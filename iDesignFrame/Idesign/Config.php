<?php

	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/2
	 * Time: 23:54
	 */
	namespace Idesign;

	class Config {

		private static $_config = array();

		/**
		 * 解析配置文件或内容
		 *
		 * @param string $config 配置文件路径或内容
		 * @param string $type   配置解析类型
		 * @param string $range  作用域
		 */
		public static function parse( $config , $type = '' ) {
			if ( empty( $type ) ) {
				$type = pathinfo( $config , PATHINFO_EXTENSION );
			}
			$class = ( false === strpos( $type , '\\' ) ) ? '\\Idesign\\Config\\Driver\\' . ucwords( $type ) : $type;
			self::set( call_user_func( array( $class , __FUNCTION__ ) , $config ) , '' );
		}


		/**
		 * 检测配置是否存在
		 *
		 * @param string $name  配置参数名（支持二级配置 .号分割）
		 * @param string $range 作用域
		 * @return bool
		 */
		public static function has( $name ) {

			$name = strtolower( $name );

			if ( !strpos( $name , '.' ) ) {
				return isset( self::$_config[ $name ] );
			} else {
				// 二维数组设置和获取支持
				$name = explode( '.' , $name );
				return isset( self::$_config[ $name[0] ][ $name[1] ] );
			}
		}

		public static function get( $name = null ) {
			return \Idesign\App::get( $name );
		}

		public static function set( $name = null , $value = null ) {
			if ( !is_null( $value ) ) {
				\Idesign\App::set( $name , $value );
			}
		}

		/**
		 * 获取配置参数 为空则获取所有配置
		 *
		 * @param string $name  配置参数名（支持二级配置 .号分割）
		 * @param string $range 作用域
		 * @return mixed
		 */
		public static function getSelf( $name = null ) {
			// 无参数时获取所有
			if ( empty( $name ) ) {
				return self::$_config;
			}

			$name = strtolower( $name );

			if ( !strpos( $name , '.' ) ) {
				return isset( self::$_config[ $name ] ) ? self::$_config[ $name ] : null;
			} else {
				// 二维数组设置和获取支持
				$name = explode( '.' , $name );
				return isset( self::$_config[ $name[0] ][ $name[1] ] ) ? self::$_config[ $name[0] ][ $name[1] ] : null;
			}
		}

		/**
		 * 设置配置参数 name为数组则为批量设置
		 *
		 * @param string $name  配置参数名（支持二级配置 .号分割）
		 * @param mixed  $value 配置值
		 * @param string $range 作用域
		 * @return mixed
		 */
		public static function setSelf( $name , $value = null ) {

			if ( is_string( $name ) ) {
				$name = strtolower( $name );
				if ( !strpos( $name , '.' ) ) {
					self::$_config[ $name ] = $value;
				} else {
					// 二维数组设置和获取支持
					$name                                  = explode( '.' , $name );
					self::$_config[ $name[0] ][ $name[1] ] = $value;
				}
				return;
			} elseif ( is_array( $name ) ) {
				// 批量设置
				if ( !empty( $value ) ) {
					return self::$_config[ $value ] = array_change_key_case( $name );
				} else {
					return self::$_config = array_change_key_case( $name );
				}
			} else {
				// 为空直接返回 已有配置
				return self::$_config;
			}
		}

		public static function load( $name = null ) {
			if ( empty( $name ) ) {
				return;
			}

			$name = strtolower( trim( $name ) );

			if ( strpos( $name , '_' ) ) {
				$res = str_replace( ' ' , DS . CONF_DIR . DS , ucwords( str_replace( '_' , ' ' , $name ) ) );
			} else {
				$res = ucwords( $name ) . DS . CONF_DIR;
			}

			if ( isset( self::$_config[ $name ] ) ) {
				return self::$_config[ $name ];
			}
			if ( 2 == GROUP_MODE ) {
				$res = ltrim( $res , ITEM_NAME . DS );
			}

			$file = ITEM_ROOT . $res . DS . CONF_NAME;

			if ( !is_file( $file ) ) {
				throw new MyException( $file . '不存在!' );
			}

			self::$_config[ $name ] = require $file;

			return self::$_config[ $name ];
		}

		public static function initConfig() {
			$_common = self::load( 'common' );

			foreach ( $_common['group'] as $val ) {
				if ( empty( $val ) ) {
					continue;
				}
				$_group = self::load( $val );

				foreach ( $_group['module_name'] as $_val ) {
					if ( empty( $_val ) ) {
						continue;
					}
					self::load( $val . '_' . $_val );
				}
			}

			if ( RUNTIME_SWITCH && !$GLOBALS['RUNTIME']['iD_Write'] ) {
				$GLOBALS['RUNTIME']['allConfig'] = self::$_config;
			}

			return self::$_config;
		}


		public static function multiLoad( array $name ) {

			$_key = $res = array();

			foreach ( $name as $key => $_val ) {
				if ( empty( $_val ) ) {
					continue;
				}

				$_val = strtolower( $_val );

				if ( strpos( $_val , '_' ) ) {
					$_key[] = $_val;
					$res[]  = str_replace( ' ' , DS . CONF_DIR . DS , ucwords( str_replace( '_' , ' ' , $_val ) ) );
				} else {
					$_key[] = $_val;
					$res    = ucwords( $name ) . DS . CONF_DIR;
				}
			}

			if ( isset( $_key[0] ) ) {

				foreach ( $_key as $key => $val ) {
					if ( isset( self::$_config[ $val ] ) ) {
						$res[ $val ] = self::$_config[ $val ];
						continue;
					}
					$_name = $res[ $key ];

					if ( 2 == GROUP_MODE ) {
						$_name = ltrim( $_name , ITEM_NAME . DS );
					}

					$file = ITEM_ROOT . $_name . DS . CONF_NAME;

					if ( !is_file( $file ) ) {
						throw new MyException( $file . '不存在!' );
						continue;
					}
					self::$_config[ $val ] = require $file;
					unset( $res[ $key ] );
					$res[ $val ] = self::$_config[ $val ];
				}
			}

			return $res;
		}

		/**
		 * 重置配置参数
		 */
		public static function reset() {
			self::$_config = array();
		}

	}