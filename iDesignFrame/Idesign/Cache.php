<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/12/2
	 * Time: 19:35
	 * copy from thinkphp
	 */

	namespace Idesign;

	class Cache {
		public static $readTimes  = 0;
		public static $writeTimes = 0;

		/**
		 * 操作句柄
		 * @var object
		 * @access protected
		 */
		protected static $handler = null;

		/**
		 * 连接缓存
		 * @access public
		 * @param array $options 配置数组
		 * @return object
		 */
		public static function connect( $options = array() ) {
			$type  = !empty( $options['type'] ) ? $options['type'] : 'File';
			$class = ( !empty( $options['namespace'] ) ? $options['namespace'] : '\\Idesign\\Cache\\Driver\\' ) . ucwords( $type );
			unset( $options['type'] );
			self::$handler = new $class( $options );
			return self::$handler;
		}

		public static function __callStatic( $method , $params ) {
			return call_user_func_array( array( self::$handler , $method ) , $params );
		}
	}