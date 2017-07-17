<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/10/31
	 * Time: 18:36
	 */
	namespace Idesign;

	class MyException extends \Exception {

		private static $_errorLevels = array(
			E_ERROR           => 'Error' ,
			E_WARNING         => 'Warning' ,
			E_PARSE           => 'Parsing Error' ,
			E_NOTICE          => 'Notice' ,
			E_CORE_ERROR      => 'Core Error' ,
			E_CORE_WARNING    => 'Core Warning' ,
			E_COMPILE_ERROR   => 'Compile Error' ,
			E_COMPILE_WARNING => 'Compile Warning' ,
			E_USER_ERROR      => 'User Error' ,
			E_USER_WARNING    => 'User Warning' ,
			E_USER_NOTICE     => 'User Notice' ,
			E_STRICT          => 'Runtime Notice' ,
		);
		private static $_style       = <<<EOT
		<meta charset="utf-8"/>
		<style type='text/css'>
			body{width:900px;margin:30px auto;}
			*{font-size:12px;}
			.trace{background: #ffe92c;color:#11303d;font-weight: bold;font-size: 18px;padding:5px;}
			.trace_line{line-height:20px;border-bottom:1px dashed #DFDFDF;color:#FF642C;}
			.trace_line span{width:40px;display:inline-block;text-align:right;color:#666;border-bottom:1px solid #FFE92C;}
			.trace_err{color:#f00;margin-left:24px;}
			.code{background: #8892BF;color:#11303d;font-weight: bold;font-size: 16px;padding:2px;}
			.code_err{color:#f00;height:24px;line-height:24px;border-bottom: 1px dashed #DFDFDF;}
			.code_nor{height:24px;line-height:24px;border-bottom: 1px dashed #DFDFDF;color:#666;}
			.code_nor span{background: #DFDFDF;padding:3px;}
		</style>
EOT;

		private $_isFromError = false;


		public static function register() {
			set_exception_handler( array( __CLASS__ , 'handle' ) );
		}


		public static function handle( $exception ) {
			if ( $exception instanceof MyException ) {
				if ( DEBUG ) {
					echo $exception;
				} else {
					header( 'HTTP/1.1 500 Internal Server Error' );
				}

			}
		}

		/**
		 * @return boolean
		 */
		public function isFromError() {
			return $this->_isFromError;
		}

		/**
		 * @param boolean $isFromError
		 * @return $this
		 */
		public function setIsFromError( $isFromError ) {
			$this->_isFromError = $isFromError;
			return $this;
		}

		public function __construct( $errMsg = '' , $level = 0 , $file = '' , $line = 0 , \Exception $previous = null ) {

			parent::__construct( $errMsg , $level , $previous );

			if ( !empty( $file ) ) {
				$this->file = $file;
			}
			if ( !empty( $line ) ) {
				$this->line = $line;
			}
		}

		public function __toString() {
			if ( IS_CLI ) {
				return $this->cliInfo();
			} else {
				return $this->defaultInfo();
			}
		}

		protected function defaultInfo() {
			$trace = $this->getTrace();
			krsort( $trace );
			$string = self::$_style . "<span class='trace'>Trace</span>\n<hr>";
			$type   = $this->isFromError() ? ( isset( self::$_errorLevels[ $this->code ] ) ? self::$_errorLevels[ $this->code ] : $this->code ) : $this->code;
			$class  = isset( $trace[0]['class'] ) ? $trace[0]['class'] : '--';
			$string .= "Exception at class  '<b>{$class}</b>'<br /><br />\n" . '<div class="trace_line"><span>Throw:</span> ' . $this->message . "</div>\n" . '<div class="trace_line"><span>Type:</span> ' . $type . "</div>\n" . '<div class="trace_line"><span>File:</span> ' . $this->file . "</div>\n" . '<div class="trace_line"><span>Line:</span> ' . $this->line . "</div><br />\n";

			$string .= $this->getDebugInfo();

			$rowNum = 1;
			if ( !empty( $trace ) ) {
				foreach ( $trace as $key => $val ) {
					if ( $key == 0 ) {
						continue;
					}
					$args = array();
					if ( !empty( $val['args'] ) ) {
						foreach ( $val['args'] as $v ) {
							$args[] = is_object( $v ) ? ( sprintf( 'Object(%s)' , get_class( $v ) ) ) : ( is_array( $v ) ? gettype( $v ) : "'$v'" );
						}
					}
					$args         = implode( ', ' , $args );
					$val['class'] = isset( $val['class'] ) ? $val['class'] : '';
					$val['type']  = isset( $val['type'] ) ? $val['type'] : '';
					$val['file']  = isset( $val['file'] ) ? $val['file'] : '';
					$val['line']  = isset( $val['line'] ) ? "  ($val[line])<br/><br/>\n" : '';
					$string .= "$rowNum. $val[file]$val[line]<span class='trace_err'>$val[class]$val[type]$val[function]($args) </span><br/><br/>\n";
					++$rowNum;
				}
			}

			return $string;
		}

		protected function cliInfo() {
			$trace = $this->getTrace();
			krsort( $trace );
			$string = "\n";
			$type   = $this->isFromError() ? ( isset( self::$_errorLevels[ $this->code ] ) ? self::$_errorLevels[ $this->code ] : $this->code ) : $this->code;
			$class  = isset( $trace[0]['class'] ) ? $trace[0]['class'] : '--';
			$string .= "Exception at class  '$class'\n" . "Throw: " . $this->message . "\n" . " Type: " . $type . "\n" . " File: " . $this->file . "\n" . " Line: " . $this->line . "\n\n";

			$rowNum = 1;
			if ( !empty( $trace ) ) {
				foreach ( $trace as $key => $val ) {
					if ( $key == 0 ) {
						continue;
					}
					$args = array();
					if ( !empty( $val['args'] ) ) {
						foreach ( $val['args'] as $v ) {
							$args[] = is_object( $v ) ? ( sprintf( 'Object(%s)' , get_class( $v ) ) ) : ( is_array( $v ) ? gettype( $v ) : "'$v'" );
						}
					}
					$args         = implode( ', ' , $args );
					$val['class'] = isset( $val['class'] ) ? $val['class'] : '';
					$val['type']  = isset( $val['type'] ) ? $val['type'] : '';
					$val['file']  = isset( $val['file'] ) ? $val['file'] : '';
					$val['line']  = isset( $val['line'] ) ? " ({$val['line']})\n" : '';

					$string .= "$rowNum. {$val['file']}{$val['line']}   {$val['class']}{$val['type']} {$val['function']}($args) \n";

					++$rowNum;
				}
			}

			return $string;
		}

		protected function getDebugInfo() {
			$ret          = "<span class='code' > Code</span>\n<hr>";
			$contentLines = file( $this->file );
			$total        = count( $contentLines );
			$startLine    = ( $this->line < 5 ) ? 0 : ( $this->line - 5 );
			$endLine      = $this->line + 5;
			$endLine      = ( $total >= $endLine ) ? $endLine : $total;

			for ( $i = $startLine ; $i < $endLine ; ++$i ) {
				if ( $i == ( $this->line - 1 ) ) {
					$ret .= '<div class="code_err">' . ( $i + 1 ) . ' ' . htmlspecialchars( $contentLines[ $i ] ) . "</div>\n";
				} else {
					$ret .= '<div class="code_nor"><span>' . ( $i + 1 ) . '</span> ' . htmlspecialchars( $contentLines[ $i ] ) . "</div>\n";
				}
			}

			return $ret . "<br/>\n";
		}


	}