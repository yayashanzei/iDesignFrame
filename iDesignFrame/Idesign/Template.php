<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/9
	 * Time: 6:32
	 */


	namespace Idesign;

	use Idesign\Template\Driver\Heredoc;

	class Template {

		/**
		 * 操作句柄
		 * @var string
		 * @access protected
		 */
		private static $_handler;
		private static $_layout;

		/**
		 * 初始化
		 */
		static public function init( $type = null , $options = array() ) {
			if ( !$type ) {
				$type = App::$_var['CONFIG']['template_type'];
			}
			$class = 'Idesign\\Template\\Driver\\' . ucwords( $type );

			self::$_handler[0] = $class;
		}

		static public function __callstatic( $method , $args ) {
			if ( !isset( self::$_handler[0] ) ) {
				self::init();
			}
			if ( method_exists( self::$_handler[0] , $method ) ) {
				return call_user_func_array( array( self::$_handler[0] , $method ) , $args );
			}
		}


	}