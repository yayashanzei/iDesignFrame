<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/4
	 * Time: 23:16
	 */
	namespace Idesign;
	class App {
		public static $_var;

		public static function run() {

			self::$_var = self::dispatcher();
			self::initEnv();

			$class = implode( '\\' , array_filter( array( self::$_var['GROUP_NAME'] , self::$_var['APP_DIR_NAME'] , self::$_var['MODULE_NAME'] , self::$_var['CTR_NAME'] ) ) );
			if ( 1 == self::$_var['URL_MODE'] ) {
				if ( !class_exists( $class ) ) {
					throw new MyException( " class or interface not exists" );
				}
				if ( !method_exists( $class , self::$_var['ACT_NAME'] ) ) {
					throw new MyException( "method not exists" );
				}
			}

			self::doInvoke( new $class , self::$_var['ACT_NAME'] );
		}

		public static function doInvoke( $class , $act ) {
			$method = new \ReflectionMethod( $class , $act );
			if ( $method->isPublic() && !$method->isStatic() ) {
				if ( $method->getNumberOfParameters() > 0 && C( 'url_params_bind' ) ) {
					switch ( $_SERVER['REQUEST_METHOD'] ) {
						case 'POST':
							$vars = array_merge( $_GET , $_POST );
						break;
						case 'PUT':
							parse_str( file_get_contents( 'php://input' ) , $vars );
						break;
						default:
							$vars = $_GET;
					}
					$params         = $method->getParameters();
					$paramsBindType = C( 'url_params_bind_type' );
					foreach ( $params as $param ) {
						$name = $param->getName();
						if ( 1 == $paramsBindType && !empty( $vars ) ) {
							$args[] = array_shift( $vars );
						} elseif ( 0 == $paramsBindType && isset( $vars[ $name ] ) ) {
							$args[] = $vars[ $name ];
						} elseif ( $param->isDefaultValueAvailable() ) {
							$args[] = $param->getDefaultValue();
						} else {
							E( L( '_PARAM_ERROR_' ) . ':' . $name );
						}
					}
					// 开启绑定参数过滤机制
					if ( C( 'url_params_safe' ) ) {
						$filters = C( 'url_params_filter' ) ? : C( 'default_filter' );
						if ( $filters ) {
							$filters = explode( ',' , $filters );
							foreach ( $filters as $filter ) {
								$args = array_map_recursive( $filter , $args ); // 参数过滤
							}
						}
					}
					array_walk_recursive( $args , 'iD_filter' );
					$method->invokeArgs( $class , $args );
					return;
				}
				$method->invoke( $class , $act );
			}
		}


		public static function dispatcher() {
			$request = Request::instance();
			return array(
				'GROUP_NAME'    => $request->getGroupName() ,
				'MODULE_NAME'   => $request->getModuleName() ,
				'CTR_NAME'      => $request->getCtrName() ,
				'ACT_NAME'      => $request->getActName() ,
				'PATH_INFO_VAR' => $request->getPathInfoVar() ,
				'REQUEST_URI'   => $request->getRequestUri() ,
				'FULL_URL'      => $request->getUrl() ,
				'URL_MODE'      => URL_MODE ,
				'GROUP_MODE'    => GROUP_MODE ,
				'APP_DIR_NAME'  => $request->getAppDir() ,
				'CONFIG'        => $request->getConfig() ,
				'R_INSTANCE'    => $request ,
			);
		}

		private static function initEnv() {

			defined( 'CACHE_DIR' ) or define( 'CACHE_DIR' , ITEM_ROOT .RUNTIME_DIR.DS. self::$_var['GROUP_NAME'] . DS . ( empty( self::$_var['CONFIG']['cache']['path'] ) ? 'Cache' : self::$_var['CONFIG']['cache']['path'] ) . DS );
			defined( 'DATA_DIR' ) or define( 'DATA_DIR' , empty( self::$_var['CONFIG']['data_dir'] ) ? 'Data' : self::$_var['CONFIG']['data_dir'] );
			defined( 'DEBUG' ) or define( 'DEBUG' , empty( self::$_var['CONFIG']['debug'] ) ? false : self::$_var['CONFIG']['debug'] );
			defined( 'SKIN_DIR' ) or define( 'SKIN_DIR' , empty( self::$_var['CONFIG']['skin_dir'] ) ? 'Skin' : self::$_var['CONFIG']['skin_dir'] );
			defined( 'SKIN_EXT' ) or define( 'SKIN_EXT' , empty( self::$_var['CONFIG']['skin_ext'] ) ? '.htm' : self::$_var['CONFIG']['skin_ext'] );
			defined( 'CONF_PARSE' ) or define( 'CONF_PARSE' , empty( self::$_var['CONFIG']['conf_parse'] ) ? '' : self::$_var['CONFIG']['conf_parse'] );
			defined( 'LOG_PATH ' ) or define( 'LOG_PATH' , CACHE_DIR . ( empty( self::$_var['CONFIG']['log']['path'] ) ? 'Log' : self::$_var['CONFIG']['log']['path'] ) . DS );
			define( 'NOW_TIME' , $_SERVER['REQUEST_TIME'] );

			self::$_var['CONFIG']['log']['path']   = LOG_PATH;
			self::$_var['CONFIG']['cache']['path'] = CACHE_DIR;

			self::$_var['Charset']      = empty( self::$_var['CONFIG']['default_charset'] ) ? 'utf-8' : self::$_var['CONFIG']['default_charset'];
			self::$_var['Domain']       = empty( self::$_var['CONFIG']['domain'] ) ? '' : self::$_var['CONFIG']['domain'];
			self::$_var['StaticDomain'] = empty( self::$_var['CONFIG']['static_domain'] ) ? '' : self::$_var['CONFIG']['static_domain'];

			self::$_var['ImgDir'] = empty( self::$_var['CONFIG']['img_dir'] ) ? 'images' : self::$_var['CONFIG']['img_dir'];
			self::$_var['JsDir']  = empty( self::$_var['CONFIG']['js_dir'] ) ? 'js' : self::$_var['CONFIG']['js_dir'];
			self::$_var['CssDir'] = empty( self::$_var['CONFIG']['css_dir'] ) ? 'css' : self::$_var['CONFIG']['css_dir'];

			if ( !self::$_var['StaticDomain'] && !empty( self::$_var['CONFIG']['static_dir'] ) && self::$_var['Domain'] ) {
				self::$_var['StaticDir'] = self::$_var['Domain'] . '/' . self::$_var['CONFIG']['static_dir'];
			} else if ( self::$_var['StaticDomain'] ) {
				self::$_var['StaticDir'] = self::$_var['StaticDomain'];
			} else {
				throw new MyException( 'static dir not fingure!' );
			}

			if ( !empty( self::$_var['StaticDir'] ) ) {
				self::$_var['_WWW']    = self::$_var['Domain'];
				self::$_var['_STATIC'] = self::$_var['StaticDir'];
				self::$_var['_IMG']    = self::$_var['StaticDir'] . '/' . self::$_var['CONFIG']['skin_name'] . '/' . self::$_var['GROUP_NAME'] . '/' . self::$_var['MODULE_NAME'] . '/' . self::$_var['ImgDir'];
				self::$_var['_JS']     = self::$_var['StaticDir'] . '/' . self::$_var['CONFIG']['skin_name'] . '/' . self::$_var['GROUP_NAME'] . '/' . self::$_var['MODULE_NAME'] . '/' . self::$_var['JsDir'];
				self::$_var['_CSS']    = self::$_var['StaticDir'] . '/' . self::$_var['CONFIG']['skin_name'] . '/' . self::$_var['GROUP_NAME'] . '/' . self::$_var['MODULE_NAME'] . '/' . self::$_var['CssDir'];
			}
		}

		public static function getConfig( $name = null ) {
			// 无参数时获取所有
			if ( empty( $name ) ) {
				return self::$_var['CONFIG'];
			}
			// 优先执行设置获取或赋值
			if ( strpos( $name , '.' ) ) {
				// 二维数组设置和获取支持
				$name = explode( '.' , $name );
				return isset( self::$_var['CONFIG'][ $name[0] ][ $name[1] ] ) ? self::$_var['CONFIG'][ $name[0] ][ $name[1] ] : null;
			}

			return isset( self::$_var['CONFIG'][ $name ] ) ? self::$_var['CONFIG'][ $name ] : null;

		}

		public static function get( $name = null , $config = true ) {
			if ( $config ) {
				return self::getConfig( $name );
			}
			// 无参数时获取所有
			if ( empty( $name ) ) {
				return self::$_var;
			}
			// 优先执行设置获取或赋值
			if ( strpos( $name , '.' ) ) {
				// 二维数组设置和获取支持
				$name = explode( '.' , $name );
				return isset( self::$_var[ $name[0] ][ $name[1] ] ) ? self::$_var[ $name[0] ][ $name[1] ] : null;
			}

			return isset( self::$_var[ $name ] ) ? self::$_var[ $name ] : null;
		}

		public static function set( $key , $val ) {

			if ( strpos( $key , '.' ) ) {
				$key   = explode( '.' , $key );
				$count = count( $key ) - 1;
				for ( $i = $count ; $i > -1 ; $i-- ) {
					if ( $i == $count ) {
						$$key[ $i ] = array( $key[ $i ] => $val );
					} else {
						$$key[ $i ] = array( $key[ $i ] => $$key[ $i + 1 ] );
						if ( $i == 0 ) {
							$res = $$key[ $i ];
						}
					}
				}
				if ( isset( self::$_var[ $key[0] ] ) ) {
					self::$_var[ $key[0] ] = multiMerge( self::$_var[ $key[0] ] , $res[ $key[0] ] );
					return;
				}
				self::$_var[ $key[0] ] = $res[ $key[0] ];
				return;
			}

			self::$_var[ $key ] = $val;
		}


	}