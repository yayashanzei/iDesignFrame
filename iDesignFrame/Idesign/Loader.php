<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/21
	 * Time: 19:08
	 * copy from thinkphp
	 */


	namespace Idesign;

	class Loader {

		private static $_loadedClass;
		private static $_nameSpace;

		public static function init() {

			spl_autoload_register( array( __CLASS__ , 'autoload' ) );
			Error::registerShutdown();
			Error::register();
			MyException::register();

			// 初始化文件存储方式
			Storage::connect( STORAGE_TYPE );

			if ( RUNTIME_SWITCH ) {
				require ITEM_ROOT . RUNTIME_FILE;

				if ( isset( $GLOBALS['RUNTIME']['iD_Write'] ) && $GLOBALS['RUNTIME']['iD_Write'] ) {
					self::$_nameSpace = $GLOBALS['RUNTIME']['nameSpace'];
					Config::setSelf( $GLOBALS['RUNTIME']['allConfig'] );
				} else {
					$GLOBALS['RUNTIME']['iD_Write'] = false;
					self::setNamespaceFConf( Config::initConfig() );
				}
			} else {
				self::setNamespaceFConf( Config::initConfig() );
			}

			App::run();
		}

		public static function autoload( $name ) {

			$_arr  = explode( '\\' , strtolower( $name ) );
			$_name = ucwords( array_pop( $_arr ) );
			$name  = implode( DS , $_arr );

			if ( isset( self::$_loadedClass[ $name ] ) ) {
				self::$_loadedClass[ $name ]++;
				return true;
			}

			if ( isset( $_arr[0] ) && ( 'idesign' == $_arr[0] || 'paint' == $_arr[0] ) ) {
				$file = ID_PATH . str_replace( ' ' , DS , ucwords( implode( ' ' , $_arr ) ) ) . DS . $_name . ID_EXT;
			} elseif ( isset( self::$_nameSpace[ $name ] ) ) {
				$file = self::$_nameSpace[ $name ] . $_name . ITEM_EXT;
			} else {
				return false;
			}
			if ( !is_file( $file ) ) {
				return false;
			}
			require $file;

			if ( !class_exists( $name , false ) && !interface_exists( $name , false ) ) {
				return false;
			}
			self::$_loadedClass[ $name ] = 1;

			return true;
		}


		public static function setNamespaceFConf( array $group ) {

			foreach ( $group as $key => $val ) {
				if ( !isset( $val['auto_load'] ) || empty( $val ) || strpos( $key , '_' ) || empty( $val['auto_load'] ) ) {
					continue;
				}
				if ( is_string( $val['auto_load'] ) ) {
					$val['auto_load'][] = $val['auto_load'];
				}
				foreach ( $val['auto_load'] as $autoload ) {
					$path[] = $key . DS . str_replace( '_' , DS , $autoload );
				}
			}

			if ( !isset( $path[0] ) ) {
				return;
			}

			$path = array_unique( $path );

			foreach ( $path as $_path ) {

				$_path = strtolower( $_path );

				if ( 2 == GROUP_MODE ) {
					$_path = str_replace( strstr( $_path , DS , true ) . DS , '' , $_path );
				}
				self::$_nameSpace[ $_path ] = ITEM_ROOT . str_replace( ' ' , DS , ucwords( strtr( $_path , DS , ' ' ) ) ) . DS;
			}

			if ( RUNTIME_SWITCH && !$GLOBALS['RUNTIME']['iD_Write'] ) {
				$GLOBALS['RUNTIME']['nameSpace'] = self::$_nameSpace;
			}

		}

		public static function setNamespace( $path = null ) {

			if ( !empty( $path ) && !is_array( $path ) ) {
				$path[] = $path;
			}

			$path = array_unique( $path );

			foreach ( $path as $_path ) {

				$_path = strtolower( $_path );

				if ( 2 == GROUP_MODE ) {
					$_path = str_replace( strstr( $_path , DS , true ) . DS , '' , $_path );
				}

				self::$_nameSpace[ $_path ] = ITEM_ROOT . str_replace( ' ' , DS , ucwords( strtr( $_path , DS , ' ' ) ) ) . DS;

			}

		}

		public static function getLoadedClass() {
			return self::$_loadedClass;
		}

		public static function getNameSpace() {
			return self::$_nameSpace;
		}


		/**
		 * 导入所需的类库 同java的Import 本函数有缓存功能
		 * @param string $class   类库命名空间字符串
		 * @param string $baseUrl 起始路径
		 * @param string $ext     导入的文件扩展名
		 * @return boolean
		 */
		public static function import( $class , $baseUrl = '' , $ext = EXT ) {
			static $_file = array();
			$class = str_replace( array( '.' , '#' ) , array( '/' , '.' ) , $class );
			if ( isset( $_file[ $class . $baseUrl ] ) ) {
				return true;
			} else {
				$_file[ $class . $baseUrl ] = true;
			}

			$class_strut = explode( '/' , $class );
			if ( empty( $baseUrl ) ) {
				if ( '@' == $class_strut[0] || MODULE_NAME == $class_strut[0] ) {
					//加载当前项目应用类库
					$class   = substr_replace( $class , '' , 0 , strlen( $class_strut[0] ) + 1 );
					$baseUrl = MODULE_PATH;
				} elseif ( in_array( $class_strut[0] , array( 'traits' , 'think' , 'behavior' , 'org' , 'com' ) ) ) {
					// org 第三方公共类库 com 企业公共类库
					$baseUrl = LIB_PATH;
				} elseif ( in_array( $class_strut[0] , array( 'vendor' ) ) ) {
					$baseUrl = THINK_PATH;
				} else {
					// 加载其他项目应用类库
					$class   = substr_replace( $class , '' , 0 , strlen( $class_strut[0] ) + 1 );
					$baseUrl = APP_PATH . $class_strut[0] . DS;
				}
			}
			if ( substr( $baseUrl , -1 ) != DS ) {
				$baseUrl .= DS;
			}
			// 如果类存在 则导入类库文件
			$filename = $baseUrl . $class . $ext;
			if ( is_file( $filename ) ) {
				include $filename;
				return true;
			}
			return false;
		}

		/**
		 * 实例化一个没有模型文件的Model（对应数据表）
		 * @param string $name    Model名称 支持指定基础模型 例如 MongoModel:User
		 * @param array  $options 模型参数
		 * @return Model
		 */
		public static function table( $name = '' , $options = array() ) {
			static $_model = array();

			if ( strpos( $name , ':' ) ) {
				list( $class , $name ) = explode( ':' , $name );
			} else {
				$class = 'Idesign\\Model';
			}
			$guid = $name . '_' . $class;
			if ( !isset( $_model[ $guid ] ) ) {
				$_model[ $guid ] = new $class( $name , $options );
			}

			return $_model[ $guid ];
		}

		/**
		 * 实例化（分层）模型
		 * @param string $name  Model名称
		 * @param string $layer 业务层名称
		 * @return Object
		 */
		public static function model( $name = '' , $layer = MODEL_LAYER ) {
			if ( empty( $name ) ) {
				return new Model;
			}
			static $_model = array();
			if ( isset( $_model[ $name . $layer ] ) ) {
				return $_model[ $name . $layer ];
			}
			if ( strpos( $name , '/' ) ) {
				list( $module , $name ) = explode( '/' , $name , 2 );
			} else {
				$module = App::$_var['MODULE_NAME'];
			}
			$class = $module . '\\' . $layer . '\\' . self::parseName( str_replace( '/' , '\\' , $name ) , 1 );
			$name  = basename( $name );
			if ( class_exists( $class ) ) {
				$model = new $class( $name );
			} else {
				$class = COMMON_MODULE . strstr( $class , '\\' );
				if ( class_exists( $class ) ) {
					$model = new $class( $name );
				} else {
					Log::record( '实例化不存在的类：' . $class , 'notic' );
					$model = new Model( $name );
				}
			}
			$_model[ $name . $layer ] = $model;
			return $model;
		}


		/**
		 * 实例化（分层）控制器 格式：[模块名/]控制器名
		 * @param string $name  资源地址
		 * @param string $layer 控制层名称
		 * @param string $empty 空控制器名称
		 * @return Object|false
		 */
		public static function controller( $name , $layer = '' , $empty = '' ) {
			static $_instance = array();
			$layer = $layer ? : CONTROLLER_LAYER;
			if ( isset( $_instance[ $name . $layer ] ) ) {
				return $_instance[ $name . $layer ];
			}
			if ( strpos( $name , '/' ) ) {
				list( $module , $name ) = explode( '/' , $name );
			} else {
				$module = MODULE_NAME;
			}
			$class = $module . '\\' . $layer . '\\' . self::parseName( str_replace( '.' , '\\' , $name ) , 1 );
			if ( class_exists( $class ) ) {
				$action                      = new $class;
				$_instance[ $name . $layer ] = $action;
				return $action;
			} elseif ( $empty && class_exists( $module . '\\' . $layer . '\\' . $empty ) ) {
				$class = $module . '\\' . $layer . '\\' . $empty;
				return new $class;
			} else {
				return false;
			}
		}

		/**
		 * 远程调用模块的操作方法 参数格式 [模块/控制器/]操作
		 * @param string       $url   调用地址
		 * @param string|array $vars  调用参数 支持字符串和数组
		 * @param string       $layer 要调用的控制层名称
		 * @return mixed
		 */
		public static function action( $url , $vars = array() , $layer = CONTROLLER_LAYER ) {
			$info   = pathinfo( $url );
			$action = $info['basename'];
			$module = '.' != $info['dirname'] ? $info['dirname'] : CONTROLLER_NAME;
			$class  = self::controller( $module , $layer );
			if ( $class ) {
				if ( is_string( $vars ) ) {
					parse_str( $vars , $vars );
				}
				return call_user_func_array( array( & $class , $action . Config::get( 'action_suffix' ) ) , $vars );
			} else {
				return false;
			}
		}

		/**
		 * 实例化数据库
		 * @param mixed   $config 数据库配置
		 * @param boolean $lite   是否采用lite方式连接
		 * @return object
		 */
		public static function db( $config , $lite = false ) {
			return Db::instance( $config , $lite );
		}


		/**
		 * 取得对象实例 支持调用类的静态方法
		 *
		 * @param string $class  对象类名
		 * @param string $method 类的静态方法名
		 *
		 * @return mixed
		 * @throws MyException
		 */
		public static function instance( $class , $method = '' ) {
			static $_instance = array();
			$identify = $class . $method;
			if ( !isset( $_instance[ $identify ] ) ) {
				if ( class_exists( $class ) ) {
					$o = new $class();
					if ( !empty( $method ) && method_exists( $o , $method ) ) {
						$_instance[ $identify ] = call_user_func_array( array( & $o , $method ) , array() );
					} else {
						$_instance[ $identify ] = $o;
					}

				} else {
					throw new MyException( 'class not exist :' . $class , 10007 );
				}

			}
			return $_instance[ $identify ];
		}

		/**
		 * 字符串命名风格转换
		 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
		 * @param string  $name 字符串
		 * @param integer $type 转换类型
		 * @return string
		 */
		public static function parseName( $name , $type = 0 ) {
			if ( $type ) {
				return ucfirst( preg_replace_callback( '/_([a-zA-Z])/' , function ( $match ) { return strtoupper( $match[1] ); } , $name ) );
			} else {
				return strtolower( trim( preg_replace( "/[A-Z]/" , "_\\0" , $name ) , "_" ) );
			}
		}
	}
