<?php

	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/1
	 * Time: 2:01
	 */
	namespace Idesign;
	class Error {

		private static $_show = true;

		public static function register() {
			set_error_handler( array( __CLASS__ , 'error' ) );
		}

		public static function registerShutdown() {
			register_shutdown_function( array( __CLASS__ , 'fatalError' ) );
		}

		public static function setShow( $show = true ) {
			self::$_show = $show;
		}

		public static function handle( $errMsg , $level , $file , $line , $context = null ) {

			if ( '.htm' == substr( $file , -4 ) ) {
				return;
			}

			if ( DEBUG ) {
				$exception = new MyException( $errMsg , $level , $file , $line );
				$exception->setIsFromError( true );
				if ( self::$_show ) {
					exit( $exception );
				}
			} else {
				header( 'HTTP/1.1 500 Internal Server Error' );
			}

		}

		public static function error() {
			if ( !IS_CLI ) {
				ob_end_clean();
			}
			$_args = func_get_args();
			if ( isset( $_args[0] ) ) {
				self::handle( $_args[1] , $_args[0] , $_args[2] , $_args[3] );
			}
		}

		public static function fatalError() {
			if ( $e = error_get_last() ) {
				ob_end_clean();
				if ( isset( $e['type'] ) ) {
					self::handle( $e['message'] , $e['type'] , $e['file'] , $e['line'] );
				} else {
					$trace = debug_backtrace();
					self::handle( $e , $trace[0]['type'] , $trace[0]['file'] , $trace[0]['line'] );
				}
			}
		}


	}