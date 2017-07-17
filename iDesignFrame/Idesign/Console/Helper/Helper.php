<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Helper;

	use Idesign\Console\Helper\Set as HelperSet;
	use Idesign\Console\Output\Formatter;

	abstract class Helper {

		protected $helperSet = null;

		/**
		 * 设置与此助手关联的助手集。
		 * @param HelperSet $helperSet
		 */
		public function setHelperSet( HelperSet $helperSet = null ) {
			$this->helperSet = $helperSet;
		}

		/**
		 * 获取与此助手关联的助手集。
		 * @return HelperSet
		 */
		public function getHelperSet() {
			return $this->helperSet;
		}

		/**
		 * 获取名称
		 * @return string
		 */
		abstract public function getName();

		/**
		 * 返回字符串的长度
		 * @param string $string
		 * @return int
		 */
		public static function strlen( $string ) {
			if ( !function_exists( 'mb_strwidth' ) ) {
				return strlen( $string );
			}

			if ( false === $encoding = mb_detect_encoding( $string ) ) {
				return strlen( $string );
			}

			return mb_strwidth( $string , $encoding );
		}

		public static function formatTime( $secs ) {
			static $timeFormats = array(
				array( 0 , '< 1 sec' ) ,
				array( 2 , '1 sec' ) ,
				array( 59 , 'secs' , 1 ) ,
				array( 60 , '1 min' ) ,
				array( 3600 , 'mins' , 60 ) ,
				array( 5400 , '1 hr' ) ,
				array( 86400 , 'hrs' , 3600 ) ,
				array( 129600 , '1 day' ) ,
				array( 604800 , 'days' , 86400 ) ,
			);

			foreach ( $timeFormats as $format ) {
				if ( $secs >= $format[0] ) {
					continue;
				}

				if ( 2 == count( $format ) ) {
					return $format[1];
				}

				return ceil( $secs/$format[2] ) . ' ' . $format[1];
			}
			return null;
		}

		public static function formatMemory( $memory ) {
			if ( $memory >= 1024*1024*1024 ) {
				return sprintf( '%.1f GiB' , $memory/1024/1024/1024 );
			}

			if ( $memory >= 1024*1024 ) {
				return sprintf( '%.1f MiB' , $memory/1024/1024 );
			}

			if ( $memory >= 1024 ) {
				return sprintf( '%d KiB' , $memory/1024 );
			}

			return sprintf( '%d B' , $memory );
		}

		public static function strlenWithoutDecoration( Formatter $formatter , $string ) {
			$isDecorated = $formatter->isDecorated();
			$formatter->setDecorated( false );
			// remove <...> formatting
			$string = $formatter->format( $string );
			// remove already formatted characters
			$string = preg_replace( "/\033\[[^m]*m/" , '' , $string );
			$formatter->setDecorated( $isDecorated );

			return self::strlen( $string );
		}
	}