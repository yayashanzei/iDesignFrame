<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/11
	 * Time: 10:07
	 */

	namespace Idesign\Template\Driver;

	use Idesign\App;
	use Idesign\Storage;
	use Idesign\MyException;

	class EasyParse {

		private static $_cplDir;
		private static $_tplString;
		private static $_compileCheck;
		private static $_TP = array();
		private static $_leftDelimiter;
		private static $_rightDelimiter;
		private static $_layout;

		public static function parse( $tplFile , $fetch = false ) {

			if ( isset( App::$_var['LAYOUT_NAME'] ) ) {
				self::$_layout = $tplFile;
				return;
			}
			self::$_cplDir         = CACHE_DIR . SKIN_DIR . DS . App::$_var['SKIN_NAME'] . DS . App::$_var['MODULE_NAME'] . DS . 'EasyParse' . DS . App::$_var['CTR_NAME'] . DS;
			self::$_leftDelimiter  = isset( App::$_var['CONFIG']['template']['LD'] ) ? App::$_var['CONFIG']['template']['LD'] : '{';
			self::$_rightDelimiter = isset( App::$_var['CONFIG']['template']['RD'] ) ? App::$_var['CONFIG']['template']['RD'] : '}';

			$fileName = basename( $tplFile );
			$cplFile  = self::$_cplDir . $fileName . ".php";
			if ( self::isCompiled( $tplFile , $cplFile ) ) {
				if ( $fetch ) {
					return $cplFile;
				}
				require $cplFile;
				return;
			}

			if ( !empty( self::$_layout ) ) {
				self::$_string = str_replace( 'Layout::Content' , file_get_contents( $tplFile ) , file_get_contents( self::$_layout ) );
			} else {
				self::$_string = file_get_contents( $tplFile );
			}

			if ( strlen( trim( self::$_tplString ) ) == 0 ) {
				return;
			}
			self::parseTemplate( $cplFile );
			if ( $fetch ) {
				return $cplFile;
			}

			require $cplFile;
		}

		public static function fetch( $templateFile = null ) {
			ob_start();
			require self::parse( $templateFile , $fetch = true );
			$res = ob_get_clean();
			return $res;
		}

		public static function parseTemplate( $cplFile ) {

			$L = self::$_leftDelimiter;

			$R = self::$_rightDelimiter;

			$_script = preg_match_all( "/<\s*script.*>.*<\s*\/\s*script\s*>/Uism" , self::$_tplString , $script , PREG_SET_ORDER );
			$_style  = preg_match_all( "/<\s*style.*>.*<\s*\/\s*style\s*>/Uism" , self::$_tplString , $style , PREG_SET_ORDER );

			self::replace( $L , $R );

			self::parseScript( '~~~script~~~' , $_script , $script , $L , $R );
			self::parseScript( '~~~style~~~' , $_style , $style , $L , $R );

			Storage::put( $cplFile , self::$_tplString );

		}

		private static function parseScript( $search , $matchCount , $matchValue , $L , $R ) {
			$_res     = null;
			$_search  = array(
				"/$L\s*\\$([a-zA-Z_0-9]*)\s*$R/ism" , "/$L\s*\\$([a-zA-Z_0-9]*)((?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*(?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*(?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*(?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*)\s*$R/ism" ,
			);
			$_replace = array(
				"<?php echo self::\$_TP['$1']; ?>" , "<?php echo isset(self::\$_TP['$1']$2)?self::\$_TP['$1']$2:self::\$_TP['$1']$2; ?>" ,
			);
			if ( $matchCount ) {
				self::$_tplString = explode( $search , self::$_tplString );
				foreach ( self::$_tplString as $key => $val ) {
					if ( isset( $matchValue[ $key ] ) ) {
						$matchValue[ $key ] = preg_replace( $_search , $_replace , $matchValue[ $key ] );
						$_res               = $_res . $val . $matchValue[ $key ][0];
						continue;
					}
					$_res = $_res . $val;
				}
				self::$_tplString = $_res;
			}
		}

		private static function replace( $L , $R ) {
			$search           = array(
				"/^(\xef\xbb\xbf)/" , "/(<\s*script.*>.*<\s*\/\s*script\s*>)/Uism" , "/(<\s*style.*>.*<\s*\/\s*style\s*>)/Uism" , "/$L\s*((?:foreach)|(?:if)|(?:for)|(?:elseif)|(?:else))(.*?)$R/ism" , "/(?:foreach\()\s*\\$([a-zA-Z_0-9]+)\s*as/ism" , "/$L\s*((?:\/\s*foreach)|(?:\/\s*if)|(?:\/\s*for))\s*$R/ism" , "/<\?php elseif/ism" , "/<\?php else\(\)/ism" , "/<\?php for\(\s*(\\$[a-zA-Z0-9_]*)\s*=\s*([a-zA-Z0-9_\\$]*)\s*to\s*([a-zA-Z0-9_\\$]*)\s*\)/ism" ,

			);
			$replace          = array(
				'' , '~~~script~~~' , '~~~style~~~' , '<?php $1($2){ ?>' , "foreach( (isset(self::\$_TP['$1'])?self::\$_TP['$1']:\$$1) as" , '<?php } ?>' , '<?php }elseif' , '<?php }else' , '<?php for($1=$2;$1<=$3;$1++)' ,
			);
			self::$_tplString = preg_replace( $search , $replace , self::$_tplString );
			self::replaceCallback( $L , $R );
			$search           = array(
				"/$L\s*\\$([a-zA-Z_0-9]*)\s*$R/ism" , "/$L\s*\\$([a-zA-Z_0-9]*)((?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*(?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*(?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*(?:\[\s*[a-zA-Z_0-9'\"]*\s*\])*)\s*$R/ism" ,
			);
			$replace          = array(
				"<?php echo self::\$_TP['$1']; ?>" , "<?php echo isset(\$$1$2)?\$$1$2:self::\$_TP['$1']$2; ?>" ,
			);
			self::$_tplString = preg_replace( $search , $replace , self::$_tplString );

		}

		private static function replaceCallback( $L , $R ) {
			self::$_tplString = preg_replace_callback( "/(?:if\(|elseif\().*\)/Uism" , function ( $matchs ) {
				return preg_replace( "/\\$([a-zA-Z_0-9]*)/ism" , "(isset(self::\$_TP['$1'])?self::\$_TP['$1']:\$$1)" , $matchs[0] );
			} , self::$_tplString );

			self::$_tplString = preg_replace_callback( "/(\?>)(.*?)(<\?php)/ism" , function ( $matchs ) use ( $L , $R ) {
				return $matchs[1] . preg_replace( "/$L\s*\\$([a-zA-Z_0-9]*)\s*$R/ism" , "<?php echo (isset(\$$1)?\$$1:self::\$_TP['$1']); ?>" , $matchs[2] ) . $matchs[3];
			} , self::$_tplString );

			self::$_tplString = preg_replace_callback( "/(for\(\\$[a-zA-Z0-9_]*=)([a-zA-Z0-9_\\$]*)(;\\$[a-zA-Z0-9_]*[><=]+)([a-zA-Z0-9_\\$]*)/ism" , function ( $matchs ) {
				return $matchs[1] . preg_replace( "/\\$([a-zA-Z_0-9]*)/ism" , "(isset(self::\$_TP['$1'])?self::\$_TP['$1']:\$$1)" , $matchs[2] ) . $matchs[3] . preg_replace( "/\\$([a-zA-Z_0-9]*)/ism" , "(isset(self::\$_TP['$1'])?self::\$_TP['$1']:\$$1)" , $matchs[4] );
			} , self::$_tplString );
		}

		public static function assign( $key , $val = null ) {
			if ( $val ) {
				self::$_TP[ $key ] = $val;
				return;
			}
			if ( is_array( $key ) ) {
				foreach ( $key as $_key => $_val ) {
					self::$_TP[ $_key ] = $_val;
				}
				return;
			}
			throw new MyException( 'parm error!' );
		}

		public static function get( $key = null ) {
			return isset( self::$_TP[ $key ] ) ? self::$_TP[ $key ] : null;
		}

		public static function isCompiled( $tplFile , $cplFile ) {
			if ( DEBUG || self::$_compileCheck ) {
				return false;
			}
			if ( filemtime( $cplFile ) >= filemtime( $tplFile ) ) {
				return true;
			}
			return false;

		}

	}