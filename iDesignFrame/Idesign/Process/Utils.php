<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */


	namespace Idesign\Process;


	class Utils {

		/**
		 * 转义字符串
		 * @param string $argument
		 * @return string
		 */
		public static function escapeArgument( $argument ) {

			if ( '' === $argument ) {
				return escapeshellarg( $argument );
			}
			$escapedArgument = '';
			$quote           = false;
			foreach ( preg_split( '/(")/i' , $argument , -1 , PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ) as $part ) {
				if ( '"' === $part ) {
					$escapedArgument .= '\\"';
				} elseif ( self::isSurroundedBy( $part , '%' ) ) {
					// Avoid environment variable expansion
					$escapedArgument .= '^%"' . substr( $part , 1 , -1 ) . '"^%';
				} else {
					// escape trailing backslash
					if ( '\\' === substr( $part , -1 ) ) {
						$part .= '\\';
					}
					$quote = true;
					$escapedArgument .= $part;
				}
			}
			if ( $quote ) {
				$escapedArgument = '"' . $escapedArgument . '"';
			}
			return $escapedArgument;
		}

		/**
		 * 验证并进行规范化Process输入。
		 * @param string $caller
		 * @param mixed  $input
		 * @return string
		 * @throws \InvalidArgumentException
		 */
		public static function validateInput( $caller , $input ) {
			if ( null !== $input ) {
				if ( is_resource( $input ) ) {
					return $input;
				}
				if ( is_scalar( $input ) ) {
					return (string)$input;
				}
				throw new \InvalidArgumentException( sprintf( '%s only accepts strings or stream resources.' , $caller ) );
			}
			return $input;
		}

		private static function isSurroundedBy( $arg , $char ) {
			return 2 < strlen( $arg ) && $char === $arg[0] && $char === $arg[ strlen( $arg ) - 1 ];
		}

	}