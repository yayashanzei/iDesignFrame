<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/9
	 * Time: 6:32
	 */


	namespace Idesign\Template\Driver;

	use Idesign\App;
	use Idesign\MyException;
	use Idesign\Storage;

	class Heredoc {

		private static $_begin;
		private static $_end;
		private static $_cplDir;
		private static $_string;
		private static $_compileCheck;
		private static $_LD;
		private static $_RD;
		private static $_layout;

		public static function parse( $file , $fetch = null ) {
			if ( isset( App::$_var['LAYOUT_NAME'] ) ) {
				self::$_layout = $file;
				return;
			}
			self::$_compileCheck = isset( App::$_var['CONFIG']['template']['compileCheck'] ) ? App::$_var['CONFIG']['template']['compileCheck'] : false;
			self::$_LD           = isset( App::$_var['CONFIG']['template']['LD'] ) ? App::$_var['CONFIG']['template']['LD'] : '{';
			self::$_RD           = isset( App::$_var['CONFIG']['template']['RD'] ) ? App::$_var['CONFIG']['template']['RD'] : '}';
			self::$_cplDir       = CACHE_DIR . SKIN_DIR . DS . App::$_var['SKIN_NAME'] . DS . App::$_var['MODULE_NAME'] . DS . 'Heredoc' . DS . App::$_var['CTR_NAME'] . DS;
			self::$_begin        = PHP_EOL . "print <<<EOT" . PHP_EOL;
			self::$_end          = PHP_EOL . "EOT;" . PHP_EOL;

			$filename = basename( $file );

			if ( self::isCompiled( $file , self::$_cplDir . $filename ) ) {
				if ( $fetch ) {
					return self::$_cplDir . $filename;
				}
				extract( App::$_var );
				require self::$_cplDir . $filename;
				return;
			}
			if ( !empty( self::$_layout ) ) {
				self::$_string = str_replace( 'Layout::Content' , file_get_contents( $file ) , file_get_contents( self::$_layout ) );
			} else {
				self::$_string = file_get_contents( $file );
			}


			if ( strpos( self::$_string , "<html" ) ) {
				self::$_string = preg_replace( "/(<html(?:.*)>)/Uism" , "$1" . PHP_EOL . "<!--" . PHP_EOL . "<?php" . self::$_begin . "-->" , self::$_string );
			} else {
				self::$_string = PHP_EOL . "<!--" . PHP_EOL . "<?php" . self::$_begin . "-->" . PHP_EOL . self::$_string;
			}
			self::$_string .= PHP_EOL . "<!--" . self::$_end . "?>" . PHP_EOL . "-->" . PHP_EOL;
			$_script       = preg_match_all( "/<\s*script.*>.*<\s*\/\s*script\s*>/Uism" , self::$_string , $script , PREG_SET_ORDER );
			$_style        = preg_match_all( "/<\s*style.*>.*<\s*\/\s*style\s*>/Uism" , self::$_string , $style , PREG_SET_ORDER );
			self::$_string = self::templateParse();
			self::$_string = self::trimParse();
			self::$_string = self::repeat();
			self::parseScript( '~~~script~~~' , $_script , $script );
			self::parseScript( '~~~style~~~' , $_style , $style );

			Storage::put( self::$_cplDir . $filename , self::$_string );
			if ( $fetch ) {
				return self::$_cplDir . $filename;
			}
			extract( App::$_var );
			require self::$_cplDir . $filename;
		}

		public static function fetch( $templateFile = null ) {
			ob_start();
			extract( App::$_var );
			require self::parse( $templateFile , $fetch = true );
			$res = ob_get_clean();
			return $res;
		}

		private static function templateParse() {
			return preg_replace( array(
				                     "/(<\s*script.*>.*<\s*\/\s*script\s*>)/Uism" , "/(<\s*style.*>.*<\s*\/\s*style\s*>)/Uism" , "/" . self::$_LD . "[\\s]*(for|foreach|if)(.*)" . self::$_RD . "/Uism" , "/" . self::$_LD . "[\\s]*(elseif|else)(.*)" . self::$_RD . "/Uism" , "/" . self::$_LD . "[\\s]*(\\/for|\\/foreach|\\/if)[\\s]*" . self::$_RD . "/Uism" ,
			                     ) , array(
				                     '~~~script~~~' , '~~~style~~~' , "<!--" . self::$_end . "$1$2{" . self::$_begin . "-->" , "<!--" . self::$_end . "}$1$2{" . self::$_begin . "-->" , "<!--" . self::$_end . "}" . self::$_begin . "-->" ,
			                     ) , self::$_string );
		}

		private static function trimParse() {
			$_begin = self::$_begin;
			$_end   = self::$_end;
			return preg_replace_callback( "/$_begin(.*)$_end/ism" , function ( $data ) use ( $_begin , $_end ) {
				$res = trim( $data[1] );
				if ( strlen( $res ) == 0 ) {
					return '';
				} else {
					$res = stripslashes( $data[1] );
					$res = $_begin . $res . $_end;
				}
				return $res;
			} , str_replace( array( '\\' ) , array( '\\\\' ) , self::$_string ) );
		}

		private static function repeat() {

			$_repeat1 = "/(if|foreach|for)(\(.*?\){)" . self::$_begin . "-->\s*<!--" . self::$_end . "(if|foreach|for)(\(.*?\){)/ism";
			$_repeat2 = "/}" . self::$_begin . "-->\s*<!--" . self::$_end . "}/ism";
			return preg_replace( array( $_repeat1 , $_repeat1 , $_repeat2 , $_repeat2 ) , array( "$1$2" . PHP_EOL . "$3$4" , "$1$2" . PHP_EOL . "$3$4" , "}}" , "}}" ) , self::$_string );
		}

		private static function parseScript( $search , $matchCount , $matchValue ) {
			$_res = null;
			if ( $matchCount ) {
				self::$_string = explode( $search , self::$_string );
				foreach ( self::$_string as $key => $val ) {
					if ( isset( $matchValue[ $key ] ) ) {
						$_res = $_res . $val . $matchValue[ $key ][0];
						continue;
					}
					$_res = $_res . $val;
				}
				self::$_string = $_res;
			}
		}

		public static function assign( $key , $val = null ) {
			if ( $val ) {
				App::$_var[ $key ] = $val;
				return;
			}
			if ( is_array( $key ) ) {
				foreach ( $key as $_key => $_val ) {
					App::$_var[ $_key ] = $_val;
				}
				return;
			}
			throw new MyException( 'parm error!' );
		}

		public static function get( $key = null ) {
			return isset( App::$_var[ $key ] ) ? App::$_var[ $key ] : null;
		}

		public static function isCompiled( $tplFile , $cplFile ) {
			if ( DEBUG || self::$_compileCheck ) {
				return false;
			}
			if ( self::$_compileCheck ) {
				return false;
			}
			if ( filemtime( $cplFile ) >= filemtime( $tplFile ) ) {
				return true;
			}
			return false;

		}


	}