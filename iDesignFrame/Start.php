<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/10/30
	 * Time: 17:30
	 */
	// 开始运行时间和内存使用
	define( 'START_TIME' , microtime( true ) );
	define( 'MEMORY_LIMIT_ON' , function_exists( 'memory_get_usage' ) );
	if ( MEMORY_LIMIT_ON ) {
		define( 'START_MEM' , memory_get_usage() );
	}

	if ( version_compare( PHP_VERSION , '5.3.0' , '<' ) ) {
		die( 'your php version < 5.3.0' );
	}
	// 系统信息
	if ( version_compare( PHP_VERSION , '5.4.0' , '<' ) ) {
		ini_set( 'magic_quotes_runtime' , 0 );
		define( 'MAGIC_QUOTES_GPC' , get_magic_quotes_gpc() ? true : false );
	} else {
		define( 'MAGIC_QUOTES_GPC' , false );
	}

	/*
	 * 记录开始时间
	 */
	ini_set( 'allow_url_fopen' , 0 );
	ini_set( 'magic_quotes_gpc' , 'Off' );
	ini_set( "display_errors" , "Off" );
	ini_set( "register_argc_argv" , "On" );

	error_reporting( E_ALL );
	date_default_timezone_set( 'PRC' );

	const iD_VERSION = '1.0.7';

	const URL_COMMON   = 1;
	const URL_PATHINFO = 2;
	const URL_REWRITE  = 3;
	const URL_COMPAT   = 4;

	const GROUP_COMMON = 1;
	const GROUP_SIMPLE = 2;

	const DS = DIRECTORY_SEPARATOR;

	defined( 'ID_TIME' ) or define( 'ID_TIME' , isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : time() );
	defined( 'ID_DATE' ) or define( 'ID_DATE' , date( 'Y-m-d H:i:s' , ID_TIME ) );

	defined( 'IS_API' ) or define( 'IS_API' , false ); // 是否API接口
	// 环境常量
	define( 'IS_CGI' , strpos( PHP_SAPI , 'cgi' ) === 0 ? 1 : 0 );
	define( 'IS_WIN' , strstr( PHP_OS , 'WIN' ) ? 1 : 0 );
	define( 'IS_CLI' , PHP_SAPI == 'cli' ? 1 : 0 );
	define( 'IS_AJAX' , ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false );
	define( 'REQUEST_METHOD' , IS_CLI ? '' : $_SERVER['REQUEST_METHOD'] );
	define( 'IS_GET' , REQUEST_METHOD == 'GET' ? true : false );
	define( 'IS_POST' , REQUEST_METHOD == 'POST' ? true : false );
	define( 'IS_PUT' , REQUEST_METHOD == 'PUT' ? true : false );
	define( 'IS_DELETE' , REQUEST_METHOD == 'DELETE' ? true : false );

	defined( 'MODEL_LAYER' ) or define( 'MODEL_LAYER' , 'Model' );
	defined( 'COMMON_MODULE' ) or define( 'COMMON_MODULE' , 'Common' );

	if ( function_exists( 'saeAutoLoader' ) ) {// 自动识别SAE环境
		defined( 'APP_MODE' ) or define( 'APP_MODE' , 'sae' );
		defined( 'STORAGE_TYPE' ) or define( 'STORAGE_TYPE' , 'Sae' );
	} else {
		defined( 'APP_MODE' ) or define( 'APP_MODE' , 'common' ); // 应用模式 默认为普通模式
		defined( 'STORAGE_TYPE' ) or define( 'STORAGE_TYPE' , 'File' );
	}

	defined( 'ITEM_NAME' ) or define( 'ITEM_NAME' , str_replace( array( dirname( ITEM_ROOT ) , DS ) , '' , ITEM_ROOT ) );

	defined( 'ITEM_EXT' ) or define( 'ITEM_EXT' , '.php' );

	defined( 'ID_PATH' ) or define( 'ID_PATH' , dirname( __FILE__ ) . DS );
	defined( 'ID_EXT' ) or define( 'ID_EXT' , '.php' );

	defined( 'VENDOR_PATH' ) or define( 'VENDOR_PATH' , ID_PATH . 'Idesign' . DS . 'Vendor' . DS );

	defined( 'CONF_DIR' ) or define( 'CONF_DIR' , 'Conf' );
	defined( 'CONF_NAME' ) or define( 'CONF_NAME' , 'Config.php' );

	defined( 'GROUP_MODE' ) or define( 'GROUP_MODE' , GROUP_COMMON );
	defined( 'URL_MODE' ) or define( 'URL_MODE' , URL_REWRITE );

	defined( 'RUNTIME_SWITCH' ) or define( 'RUNTIME_SWITCH' , true );

	defined('RUNTIME_DIR') or define('RUNTIME_DIR','Runtime');
	defined( 'RUNTIME_FILE' ) or define( 'RUNTIME_FILE' , RUNTIME_DIR.'/conf_runtime.php' );

	require ID_PATH . 'Idesign' . DS . 'Loader' . ID_EXT;

	\Idesign\Loader::init();


	/**
	 * 实例化控制器 格式：[模块/]控制器
	 * @param string $name  资源地址
	 * @param string $layer 控制层名称
	 * @return object
	 */
	function A( $name , $layer = CONTROLLER_LAYER ) {
		return \Idesign\Loader::controller( $name , $layer );
	}

	/**
	 * 获取和设置配置参数 支持批量定义
	 */
	function C( $name = null , $value = null ) {
		if ( is_null( $value ) ) {
			return \Idesign\App::get( $name );
		} else {
			\Idesign\App::set( $name , $value );
		}
	}

	/**
	 * 实例化Model
	 * @param string $name  Model名称
	 * @param string $layer 业务层名称
	 * @return object
	 */
	function D( $name = '' , $layer = MODEL_LAYER ) {
		return \Idesign\Loader::model( $name , $layer );
	}

	/**
	 * 抛出异常处理
	 *
	 * @param string  $msg  异常消息
	 * @param integer $code 异常代码 默认为0
	 *
	 * @throws \Idesign\MyException
	 */
	function E( $msg , $code = 0 ) {
		throw new \Idesign\MyException( $msg , $code );
	}

	// 获取输入数据 支持默认值和过滤
	function I( $key , $default = null , $filter = '' ) {
		if ( strpos( $key , '.' ) ) {
			// 指定参数来源
			list( $method , $key ) = explode( '.' , $key , 2 );
		} else {
			// 默认为自动判断
			$method = 'param';
		}
		return \Idesign\Input::$method( $key , $default , $filter );
	}

	/**
	 * 记录时间（微秒）和内存使用情况
	 * @param string  $start 开始标签
	 * @param string  $end   结束标签
	 * @param integer $dec   小数位
	 * @return mixed
	 */
	function G( $start , $end = '' , $dec = 6 ) {
		if ( '' == $end ) {
			\Idesign\Debug::remark( $start );
		} else {
			return 'm' == $dec ? \Idesign\Debug::getRangeMem( $start , $end ) : \Idesign\Debug::getRangeTime( $start , $end , $dec );
		}
	}

	// 获取多语言变量
	function L( $name , $vars = array() , $lang = '' ) {
		return \Idesign\Lang::get( $name , $vars , $lang );
	}

	/**
	 * 实例化一个没有模型文件的Model
	 * @param string $name        Model名称 支持指定基础模型 例如 MongoModel:User
	 * @param string $tablePrefix 表前缀
	 * @param mixed  $connection  数据库连接信息
	 * @return \Idesign\Model
	 */
	function M( $name = '' , $tablePrefix = '' , $connection = '' ) {
		return \Idesign\Loader::table( $name , array( 'prefix' => $tablePrefix , 'connection' => $connection ) );
	}


	/**
	 * 调用模块的操作方法 参数格式 [模块/控制器/]操作
	 * @param string       $url   调用地址
	 * @param string|array $vars  调用参数 支持字符串和数组
	 * @param string       $layer 要调用的控制层名称
	 * @return mixed
	 */
	function R( $url , $vars = array() , $layer = CONTROLLER_LAYER ) {
		return \Idesign\Loader::action( $url , $vars , $layer );
	}


	/**
	 * 缓存管理
	 * @param mixed $name    缓存名称，如果为数组表示进行缓存设置
	 * @param mixed $value   缓存值
	 * @param mixed $options 缓存参数
	 * @return mixed
	 */
	function S( $name , $value = '' , $options = null ) {
		if ( is_array( $options ) ) {
			// 缓存操作的同时初始化
			\Idesign\Cache::connect( $options );
		} elseif ( is_array( $name ) ) {
			// 缓存初始化
			return \Idesign\Cache::connect( $name );
		}
		if ( '' === $value ) {
			// 获取缓存
			return \Idesign\Cache::get( $name );
		} elseif ( is_null( $value ) ) {
			// 删除缓存
			return \Idesign\Cache::rm( $name );
		} else {
			// 缓存数据
			if ( is_array( $options ) ) {
				$expire = isset( $options['expire'] ) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
			} else {
				$expire = is_numeric( $options ) ? $options : null; //默认快捷缓存设置过期时间
			}
			return \Idesign\Cache::set( $name , $value , $expire );
		}
	}

	function U( $url , $vars = '' , $suffix = true , $domain = false ) {
		return \Idesign\Url::build( $url , $vars , $suffix , $domain );
	}

	/**
	 * 渲染输出Widget
	 * @param string $name Widget名称
	 * @param array  $data 传人的参数
	 * @return mixed
	 */
	function W( $name , $data = array() ) {
		return \Idesign\Loader::action( $name , $data , 'Widget' );
	}


	/**
	 * 浏览器友好的变量输出
	 * @param mixed   $var   变量
	 * @param boolean $echo  是否输出 默认为true 如果为false 则返回输出字符串
	 * @param string  $label 标签 默认为空
	 * @return void|string
	 */
	function dump( $var , $echo = true , $label = null ) {
		return \Idesign\Debug::dump( $var , $echo , $label );
	}

	/**
	 * 实例化数据库类
	 * @param array   $config 数据库配置参数
	 * @param boolean $lite   是否lite连接
	 * @return object
	 */
	function db( $config = array() , $lite = false ) {
		return \Idesign\Db::instance( $config , $lite );
	}

	/**
	 * 导入所需的类库 同java的Import 本函数有缓存功能
	 * @param string $class   类库命名空间字符串
	 * @param string $baseUrl 起始路径
	 * @param string $ext     导入的文件扩展名
	 * @return boolean
	 */
	function import( $class , $baseUrl = '' , $ext = ID_EXT ) {
		return \Idesign\Loader::import( $class , $baseUrl , $ext );
	}

	/**
	 * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
	 * @param string $class 类库
	 * @param string $ext   类库后缀
	 * @return boolean
	 */
	function vendor( $class , $ext = ID_EXT ) {
		return \Idesign\Loader::import( $class , VENDOR_PATH , $ext );
	}

	function session( $name , $value = '' ) {
		if ( is_array( $name ) ) {
			// 初始化
			\Idesign\Session::init( $name );
		} elseif ( is_null( $name ) ) {
			// 清除
			\Idesign\Session::clear( $value );
		} elseif ( '' === $value ) {
			// 获取
			return \Idesign\Session::get( $name );
		} elseif ( is_null( $value ) ) {
			// 删除session
			return \Idesign\Session::delete( $name );
		} else {
			// 设置session
			return \Idesign\Session::set( $name , $value );
		}
	}

	function cookie( $name , $value = '' ) {
		if ( is_array( $name ) ) {
			// 初始化
			\Idesign\Cookie::init( $name );
		} elseif ( is_null( $name ) ) {
			// 清除
			\Idesign\Cookie::clear( $value );
		} elseif ( '' === $value ) {
			// 获取
			return \Idesign\Cookie::get( $name );
		} elseif ( is_null( $value ) ) {
			// 删除session
			return \Idesign\Cookie::delete( $name );
		} else {
			// 设置session
			return \Idesign\Cookie::set( $name , $value );
		}
	}

	/**
	 * 添加Trace记录到SocketLog
	 * @param mixed  $log   log信息 支持字符串和数组
	 * @param string $level 日志级别
	 * @return void|array
	 */
	function trace( $log = '[Idesign]' , $level = 'log' ) {
		if ( '[Idesign]' == $log ) {
			return \Idesign\Log::getLog();
		} else {
			\Idesign\Log::record( $log , $level );
		}
	}


	/*
	 * behind this create by icebr
	 */
	function multiMergeRecursive() {
		$args  = func_get_args();
		$first = array_shift( $args );

		if ( !isset( $args[0] ) ) {
			return $first;
		}

		foreach ( $args as $arg ) {

			foreach ( $arg as $key1 => $value1 ) {

				if ( is_array( $value1 ) ) {
					if ( isset( $first[ $key1 ] ) ) {
						$first[ $key1 ] = multiMergeRecursive( $first[ $key1 ] , $value1 );
						continue;
					}
					$first[ $key1 ] = $value1;
				} else {
					if ( is_numeric( $key1 ) ) {
						if ( !isset( $_first ) ) {
							$_first = array_flip( $first );
						}
						if ( !isset( $_first[ $value1 ] ) ) {
							$first[] = $value1;
						}
						$_first[ $value1 ] = $value1;
						continue;
					}
					$first[ $key1 ] = $value1;
				}

			}

		}

		return $first;
	}


	/*
	 * behind this create by icebr
	 */
	function multiMerge() {
		$args  = func_get_args();
		$first = array_shift( $args );
		if ( !isset( $args[0] ) ) {
			return $first;
		}
		foreach ( $args as $arg ) {
			foreach ( $arg as $key1 => $value1 ) {
				if ( is_array( $value1 ) ) {
					if ( isset( $first[ $key1 ] ) ) {
						foreach ( $value1 as $key2 => $value2 ) {

							if ( is_array( $value2 ) ) {
								if ( isset( $first[ $key1 ][ $key2 ] ) ) {

									foreach ( $value2 as $key3 => $value3 ) {

										if ( is_array( $value3 ) ) {
											if ( isset( $first[ $key1 ][ $key2 ][ $key3 ] ) ) {

												foreach ( $value3 as $key4 => $value4 ) {
													if ( is_array( $value4 ) ) {
														if ( isset( $first[ $key1 ][ $key2 ][ $key3 ][ $key4 ] ) ) {

															foreach ( $value4 as $key5 => $value5 ) {

																if ( is_array( $value5 ) ) {
																	if ( isset( $first[ $key1 ][ $key2 ][ $key3 ][ $key4 ][ $key5 ] ) ) {

																		continue;
																	}
																	$first[ $key1 ][ $key2 ][ $key3 ][ $key4 ][ $key5 ] = $value5;
																	continue;
																}
																if ( is_numeric( $key5 ) ) {
																	if ( !in_array( $value5 , $first[ $key1 ][ $key2 ][ $key3 ][ $key4 ] ) ) {
																		$first[ $key1 ][ $key2 ][ $key3 ][ $key4 ][] = $value5;
																	}
																	continue;
																}
																$first[ $key1 ][ $key2 ][ $key3 ][ $key4 ][ $key5 ] = $value5;
															}
															continue;
														}
														$first[ $key1 ][ $key2 ][ $key3 ][ $key4 ] = $value4;
														continue;
													}
													if ( is_numeric( $key4 ) ) {
														if ( !in_array( $value4 , $first[ $key1 ][ $key2 ][ $key3 ] ) ) {
															$first[ $key1 ][ $key2 ][ $key3 ][] = $value4;
														}
														continue;
													}
													$first[ $key1 ][ $key2 ][ $key3 ][ $key4 ] = $value4;
												}
												continue;
											}
											$first[ $key1 ][ $key2 ][ $key3 ] = $value3;
											continue;
										}
										if ( is_numeric( $key3 ) ) {
											if ( !in_array( $value3 , $first[ $key1 ][ $key2 ] ) ) {
												$first[ $key1 ][ $key2 ][] = $value3;
											}
											continue;
										}
										$first[ $key1 ][ $key2 ][ $key3 ] = $value3;
									}
									continue;
								}
								$first[ $key1 ][ $key2 ] = $value2;
								continue;
							}
							if ( is_numeric( $key2 ) ) {
								if ( !in_array( $value2 , $first[ $key1 ] ) ) {
									$first[ $key1 ][] = $value2;
								}
								continue;
							}
							$first[ $key1 ][ $key2 ] = $value2;
						}
						continue;
					}
					$first[ $key1 ] = $value1;
					continue;
				}
				if ( is_numeric( $key1 ) ) {
					if ( !in_array( $value1 , $first ) ) {
						$first[] = $value1;
					}
					continue;
				}
				$first[ $key1 ] = $value1;
			}
		}

		return $first;
	}

?>