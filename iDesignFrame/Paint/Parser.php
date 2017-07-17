<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Paint;

	// 内容解析类
	class Parser {

		/**
		 * @var array $handler
		 */
		private static $handler = array();

		// 解析内容
		public static function parse( $content , $type ) {
			if ( !isset( self::$handler[ $type ] ) ) {
				$class                  = '\\Paint\\Parser\\Driver\\' . ucwords( $type );
				self::$handler[ $type ] = new $class();
			}
			return self::$handler[ $type ]->parse( $content );
		}

		// 调用驱动类的方法
		public static function __callStatic( $method , $params ) {
			return self::parse( $params[0] , $method );
		}
	}
