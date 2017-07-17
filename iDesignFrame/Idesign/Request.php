<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/4
	 * Time: 2:04
	 */
	namespace Idesign;

	class  Request {

		protected static $_url;
		protected static $_requestUri;
		// 全局过滤规则
		protected static $filter = null;

		protected static $_allConf;
		protected static $_pathInfoVar = array();
		protected static $_appDir;
		protected static $_groupName;
		protected static $_moduleName;
		protected static $_ctrName;
		protected static $_actName;
		protected static $_config;

		protected static $_instance;

		private function __construct() {

			self::addS( array( 'GET' => &$_GET , 'POST' => &$_POST , 'COOKIE' => &$_COOKIE , 'SESSION' => &$_SESSION , 'REQUEST' => &$_REQUEST , 'FILES' => &$_FILES ) );

			if ( IS_CLI ) {

				$_SERVER['REQUEST_URI'] = '/' . implode( '/' , $GLOBALS['argv'] );
				self::$_url             = $_SERVER['REQUEST_URI'];

			} else {

				$_SERVER['REQUEST_URI'] = implode( '/' , explode( '/' , str_replace( '\\' , '/' , ( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] ) ) ) );
				self::$_url             = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			}

			self::setRequestUri( explode( '/' , rtrim( str_replace( array( '/index.php' , '.php' , '.html' , '.htm' ) , '' , $_SERVER['REQUEST_URI'] ) , '/' ) ) );

			self::$_allConf = Config::getSelf();

			self::$_appDir = empty( self::$_allConf['common']['app_dir_name'] ) ? 'App' : self::$_allConf['common']['app_dir_name'];

			self::$_groupName = ucwords( strtolower( defined( 'GROUP_NAME' ) ? GROUP_NAME : empty( self::$_allConf['common']['default_group'] ) ? current( self::$_allConf['common']['group'] ) : self::$_allConf['common']['default_group'] ) );

			self::$_moduleName = empty( self::$_allConf[ self::$_groupName ]['default_module'] ) ? empty( self::$_allConf['common']['default_module'] ) ? 'Home' : self::$_allConf['common']['default_module'] : self::$_allConf[ self::$_groupName ]['default_module'];

			self::urlInit();

			if ( self::$_config['url_router_on'] ) {
				//Route::parseRule();
			}

			if ( self::$_config['url_domain_deploy'] ) {
				//Route::parseDomain();
			}

		}

		protected static function urlInit() {
			switch ( URL_MODE ) {
				case 1:
					self::setCommon();
				break;
				case 2:
					self::setPathInfo();
				break;
				case 3:
					self::setPathInfo();
				break;
				case 4:
					throw new MyException( 'testing now!' );
				break;

			}
		}


		protected static function setCommon() {

			if ( isset( $_REQUEST['g'] ) ) {
				self::$_groupName = ucfirst( strtolower( $_REQUEST['g'] ) );
				unset( $_REQUEST['g'] );
			}
			if ( isset( $_REQUEST['m'] ) ) {
				self::$_moduleName = ucfirst( strtolower( $_REQUEST['m'] ) );
				unset( $_REQUEST['m'] );
			}

			self::configure();

			if ( isset( $_REQUEST['c'] ) ) {
				self::$_ctrName = ucfirst( strtolower( $_REQUEST['c'] ) );
				unset( $_REQUEST['c'] );
			}
			if ( isset( $_REQUEST['a'] ) ) {
				self::$_actName = $_REQUEST['a'];
				unset( $_REQUEST['a'] );
			}
		}

		protected static function setPathInfo() {

			$request = self::$_requestUri;

			if ( !isset( $request[0] ) ) {
				self::configure();
				return;
			}

			if ( isset( self::$_allConf['common']['group'][ ucfirst( strtolower( $request[0] ) ) ] ) ) {
				if ( 1 == GROUP_MODE ) {
					self::$_groupName = ucfirst( strtolower( array_shift( $request ) ) );
				}
			}

			if ( !isset( $request[0] ) ) {
				self::configure();
				return;
			}

			if ( isset( self::$_allConf[ strtolower( self::$_groupName ) ]['module_name'][ ucwords( strtolower( $request[0] ) ) ] ) ) {
				self::$_moduleName = ucwords( strtolower( array_shift( $request ) ) );
			}

			self::configure();

			if ( !isset( $request[0] ) ) {
				return;
			}

			$_class = implode( '\\' , array_filter( array( self::$_groupName , self::$_appDir , self::$_moduleName , ucwords( strtolower( $request[0] ) ) ) ) );

			if ( class_exists( $_class ) ) {
				self::$_ctrName = ucfirst( strtolower( array_shift( $request ) ) );
			}

			if ( !isset( $request[0] ) ) {
				return;
			}

			$_class = implode( '\\' , array_filter( array( self::$_groupName , self::$_appDir , self::$_moduleName , ucwords( strtolower( self::$_ctrName ) ) ) ) );

			if ( method_exists( $_class , $request[0] ) ) {
				self::$_actName = array_shift( $request );
			}

			self::$_pathInfoVar = array_merge( self::$_pathInfoVar , $request );

		}

		public static function instance() {
			if ( !self::$_instance instanceof self ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		protected static function configure( $end = true ) {

			if ( !self::$_config['url_router_on'] ) {
				defined( 'GROUP_NAME' ) && self::$_groupName = GROUP_NAME;
				defined( 'MODULE_NAME' ) && self::$_moduleName = MODULE_NAME;
			}

			$_suffix = strtolower( self::$_groupName ) . '_' . strtolower( self::$_moduleName );
			$_lang   = empty( self::$_allConf[ $_suffix ]['default_lang'] ) ? empty( self::$_allConf['common']['default_lang'] ) ? 'zh-cn' : self::$_allConf['common']['default_lang'] : self::$_allConf[ $_suffix ]['default_lang'];

			if ( RUNTIME_SWITCH ) {
				if ( $GLOBALS['RUNTIME']['iD_Write'] && isset( $GLOBALS['RUNTIME'][ 'Config_' . $_suffix ] ) ) {
					self::$_config = $GLOBALS['RUNTIME'][ 'Config_' . $_suffix ];
					Lang::set( $GLOBALS['RUNTIME'][ 'Lang_' . $_suffix ] );
					unset( $GLOBALS['RUNTIME'] , $_suffix );
				} else {
					self::configMerge();
					$lang = require ID_PATH . 'Idesign' . DS . 'Lang' . DS . strtolower( $_lang ) . '.php';
					Lang::set( $lang );
					$GLOBALS['RUNTIME'][ 'Lang_' . $_suffix ]   = $lang;
					$GLOBALS['RUNTIME'][ 'Config_' . $_suffix ] = self::$_config;
					$GLOBALS['RUNTIME']['iD_Write']             = true;
					//Storage::put( ITEM_ROOT . RUNTIME_FILE , "<?php \$GLOBALS['RUNTIME']=unserialize(" . var_export( serialize( $GLOBALS['RUNTIME'] ) , true ) . ');' );
					Storage::put( ITEM_ROOT . RUNTIME_FILE , "<?php \$GLOBALS['RUNTIME']=" . preg_replace( '/(\s)*(\n)+(\s)*/i' , '' , var_export( $GLOBALS['RUNTIME'] , true ) ) . ";" );
					unset( $GLOBALS['RUNTIME'] , $_suffix );
				}
			} else {
				self::configMerge();
				Lang::set( include ID_PATH . 'Idesign' . DS . 'Lang' . DS . strtolower( $_lang ) . '.php' );
			}

			if ( empty( self::$_ctrName ) ) {
				self::$_ctrName = empty( self::$_config['default_ctr'] ) ? 'Index' : self::$_config['default_ctr'];
			}
			if ( empty( self::$_actName ) ) {
				self::$_actName = empty( self::$_config['default_act'] ) ? 'index' : self::$_config['default_act'];
			}

		}

		protected static function configMerge() {
			self::$_config = multiMergeRecursive( self::$_allConf['common'] , self::$_allConf[ strtolower( self::$_groupName ) ] , self::$_allConf[ strtolower( self::$_groupName . '_' . self::$_moduleName ) ] );
		}

		public static function addS( $array ) {
			foreach ( $array as $key1 => $value1 ) {
				if ( empty( $value1 ) ) {
					unset ( $array[ $key1 ] );
					continue;
				}
				if ( !is_array( $value1 ) ) {
					unset ( $array[ $key1 ] );
					$value1         = preg_replace( array( "/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i" , "/&#x/i" , "/eval/i" ) , array( '$1 ' , '& # x' , 'eva l' ) , $value1 );
					$array[ $key1 ] = addslashes( $value1 );
				} else {

					foreach ( $value1 as $key2 => $value2 ) {
						if ( empty( $value2 ) ) {
							unset ( $value1[ $key2 ] );
							continue;
						}
						if ( !is_array( $value2 ) ) {
							unset ( $value1[ $key2 ] );
							$value2                  = preg_replace( array( "/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i" , "/&#x/i" , "/eval/i" ) , array( '$1 ' , '& # x' , 'eva l' ) , $value2 );
							$array[ $key1 ][ $key2 ] = addslashes( $value2 );
						} else {

							foreach ( $value2 as $key3 => $value3 ) {
								if ( empty( $value3 ) ) {
									unset ( $value2[ $key3 ] );
									continue;
								}
								if ( !is_array( $value3 ) ) {
									unset ( $value2[ $key3 ] );
									$value3 = preg_replace( array( "/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i" , "/&#x/i" , "/eval/i" ) , array( '$1 ' , '& # x' , 'eva l' ) , $value3 );
									if ( 'tmp_name' == $key3 ) {
										continue;
									}
									$array[ $key1 ][ $key2 ][ $key3 ] = addslashes( $value3 );

								} else {
									$array[ $key1 ][ $key2 ][ $key3 ] = self::addS( $array[ $key1 ][ $key2 ][ $key3 ] );
								}
							}

						}
					}

				}
			}
			return $array;
		}

		public static function setAppDir( $val ) {
			self::$_appDir = $val;
		}

		public static function getAppDir() {
			return self::$_appDir;
		}

		public static function getConfig() {
			return self::$_config;
		}

		public static function getGroupName() {
			return self::$_groupName;
		}

		public static function setGroupName( $val ) {
			self::$_groupName = $val;
		}

		public static function getModuleName() {
			return self::$_moduleName;
		}

		public static function setModuleName( $val ) {
			self::$_moduleName = $val;
		}

		public static function getCtrName() {
			return self::$_ctrName;
		}

		public static function setCtrName( $val ) {
			self::$_ctrName = $val;
		}

		public static function getActName() {
			return self::$_actName;
		}

		public static function setActName( $val ) {
			self::$_actName = $val;
		}

		public static function getRequestUri() {
			return self::$_requestUri;
		}

		public static function setRequestUri( $var ) {
			$_temp = current( $var );

			if ( empty( $_temp ) ) {
				self::$_requestUri['s'][] = '';
				array_shift( $var );
			}

			self::$_requestUri = $var;
		}

		public static function getUrl() {
			return self::$_url;
		}

		public static function setUrl( $val ) {
			self::$_url = $val;

		}

		public static function getPathInfoVar() {
			return self::$_pathInfoVar;
		}

		public static function setPathInfoVar( $val ) {
			self::$_pathInfoVar = $val;
		}

		public static function getCommonConf() {
			return self::$_allConf['common'];
		}

		public static function setCommonConf( $key , $val ) {
			self::$_allConf['common'][ $key ] = $val;
		}

		/*copy from thinkphp*/

		/**
		 * 获取get变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function get( $name = '' , $default = null , $filter = '' ) {
			return self::getData( $name , $_GET , $filter , $default );
		}

		/**
		 * 获取post变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function post( $name = '' , $default = null , $filter = '' ) {
			return self::getData( $name , $_POST , $filter , $default );
		}

		/**
		 * 获取put变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function put( $name = '' , $default = null , $filter = '' ) {
			static $_PUT = null;
			if ( is_null( $_PUT ) ) {
				parse_str( file_get_contents( 'php://input' ) , $_PUT );
			}
			return self::getData( $name , $_PUT , $filter , $default );
		}

		/**
		 * 获取post变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function param( $name = '' , $default = null , $filter = '' ) {
			switch ( $_SERVER['REQUEST_METHOD'] ) {
				case 'POST':
					return self::post( $name , $default , $filter );
				case 'PUT':
					return self::put( $name , $default , $filter );
				default:
					return self::get( $name , $default , $filter );
			}
		}

		/**
		 * 获取request变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function request( $name = '' , $default = null , $filter = '' ) {
			return self::getData( $name , $_REQUEST , $filter , $default );
		}

		/**
		 * 获取session变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function session( $name = '' , $default = null , $filter = '' ) {
			return self::getData( $name , $_SESSION , $filter , $default );
		}

		/**
		 * 获取cookie变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function cookie( $name = '' , $default = null , $filter = '' ) {
			return self::getData( $name , $_COOKIE , $filter , $default );
		}

		/**
		 * 获取post变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function server( $name = '' , $default = null , $filter = '' ) {
			return self::getData( strtoupper( $name ) , $_SERVER , $filter , $default );
		}

		/**
		 * 获取GLOBALS变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function globals( $name = '' , $default = null , $filter = '' ) {
			return self::getData( $name , $GLOBALS , $filter , $default );
		}

		/**
		 * 获取环境变量
		 * @access public
		 * @param string $name    数据名称
		 * @param string $default 默认值
		 * @param string $filter  过滤方法
		 * @return mixed
		 */
		public static function env( $name = '' , $default = null , $filter = '' ) {
			return self::getData( strtoupper( $name ) , $_ENV , $filter , $default );
		}

		/**
		 * 获取系统变量 支持过滤和默认值
		 * @access   public
		 *
		 * @param $name
		 * @param $input
		 * @param $filter
		 * @param $default
		 *
		 * @return mixed
		 * @internal param string $method 输入数据类型
		 * @internal param array $args 参数 [key,filter,default]
		 */
		private static function getData( $name , $input , $filter , $default ) {
			if ( strpos( $name , '/' ) ) {
				// 指定修饰符
				list( $name , $type ) = explode( '/' , $name , 2 );
			} else {
				// 默认强制转换为字符串
				$type = 's';
			}
			$filters = isset( $filter ) ? $filter : self::$filter;
			if ( '' == $name ) {
				// 获取全部变量
				$data = $input;
				if ( $filters ) {
					if ( is_string( $filters ) ) {
						$filters = explode( ',' , $filters );
					}
					foreach ( $filters as $filter ) {
						$data = self::filter( $filter , $data ); // 参数过滤
					}
				}
			} elseif ( isset( $input[ $name ] ) ) {
				// 取值操作
				$data = $input[ $name ];
				if ( $filters ) {
					if ( is_string( $filters ) ) {
						if ( 0 === strpos( $filters , '/' ) ) {
							if ( 1 !== preg_match( $filters , (string)$data ) ) {
								// 支持正则验证
								return $default;
							}
						} else {
							$filters = explode( ',' , $filters );
						}
					} elseif ( is_int( $filters ) ) {
						$filters = array( $filters );
					}

					if ( is_array( $filters ) ) {
						foreach ( $filters as $filter ) {
							if ( function_exists( $filter ) ) {
								$data = is_array( $data ) ? self::filter( $filter , $data ) : $filter( $data ); // 参数过滤
							} else {
								$data = filter_var( $data , is_int( $filter ) ? $filter : filter_id( $filter ) );
								if ( false === $data ) {
									return $default;
								}
							}
						}
					}
				}
				if ( !empty( $type ) ) {
					switch ( strtolower( $type ) ) {
						case 'a': // 数组
							$data = (array)$data;
						break;
						case 'd': // 数字
							$data = (int)$data;
						break;
						case 'f': // 浮点
							$data = (float)$data;
						break;
						case 'b': // 布尔
							$data = (boolean)$data;
						break;
						case 's': // 字符串
						default:
							$data = (string)$data;
					}
				}
			} else {
				// 变量默认值
				$data = $default;
			}
			is_array( $data ) && array_walk_recursive( $data , 'self::filterExp' );
			return $data;
		}

		// 过滤表单中的表达式
		public static function filterExp( &$value ) {
			// TODO 其他安全过滤

			// 过滤查询特殊字符
			if ( preg_match( '/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i' , $value ) ) {
				$value .= ' ';
			}
		}

		public static function filter( $filter , $data ) {
			$result = array();
			foreach ( $data as $key => $val ) {
				$result[ $key ] = is_array( $val ) ? self::filter( $filter , $val ) : call_user_func( $filter , $val );
			}
			return $result;
		}


	}