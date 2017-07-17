<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/19
	 * Time: 14:37
	 * copy from thinkphp
	 */

	namespace Idesign\Config\Driver;

	class Ini {
		public static function parse( $config ) {
			if ( is_file( $config ) ) {
				return parse_ini_file( $config , true );
			} else {
				return parse_ini_string( $config , true );
			}
		}
	}
