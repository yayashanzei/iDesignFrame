<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/17
	 * Time: 9:43
	 * copy from thinkphp
	 */

	namespace Paint;

	// 内容解析类
	use Idesign\MyException;

	class Transform {
		private static $handler = array();

		/**
		 * 初始化解析驱动
		 * @static
		 * @access private
		 * @param  string $type 驱动类型
		 */
		private static function init( $type ) {
			if ( !isset( self::$handler[ $type ] ) ) {
				$class                  = '\\Paint\\Transform\\Driver\\' . ucwords( $type );
				self::$handler[ $type ] = new $class();
			}
		}

		/**
		 * 编码内容
		 * @static
		 * @access public
		 * @param  mixed  $content 要编码的数据
		 * @param  string $type    数据类型
		 * @param  array  $config  XML配置参数，JSON格式生成无此参数
		 * @return string          编码后的数据
		 */
		public static function encode( $content , $type , array $config = array() ) {
			self::init( $type );
			return self::$handler[ $type ]->encode( $content , $config );
		}

		/**
		 * 解码数据
		 * @param  string  $content 要解码的数据
		 * @param  string  $type    数据类型
		 * @param  boolean $assoc   是否返回数组
		 * @param  array   $config  XML配置参数，JSON格式解码无此参数
		 * @return mixed            解码后的数据
		 */
		public static function decode( $content , $type , $assoc = true , array $config = array() ) {
			self::init( $type );
			return self::$handler[ $type ]->decode( $content , $assoc , $config );
		}

		// 调用驱动类的方法
		// Transform::xmlEncode('abc')
		// Transform::jsonDecode('abc', true);
		public static function __callStatic( $method , $params ) {
			if ( empty( $params[0] ) ) {
				return '';
			}

			//获取类型
			$type = substr( $method , 0 , strlen( $method ) - 6 );

			switch ( strtolower( substr( $method , -6 ) ) ) {
				case 'encode':
					$config = empty( $params[1] ) ? array() : $params[1];
					return self::encode( $params[0] , $type , $config );
				case 'decode':
					$assoc  = empty( $params[1] ) ? true : $params[1];
					$config = empty( $params[2] ) ? array() : $params[2];
					return self::decode( $params[0] , $type , $assoc , $config );
				default:
					throw new MyException( "call to undefined method {$method}" );
			}
		}
	}
