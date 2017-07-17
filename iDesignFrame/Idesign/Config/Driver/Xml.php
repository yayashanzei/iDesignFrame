<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/19
	 * Time: 14:37
	 * copy from thinkphp
	 */

	namespace Idesign\Config\Driver;

	class Xml {
		public function parse( $config ) {
			if ( is_file( $config ) ) {
				$content = simplexml_load_file( $config );
			} else {
				$content = simplexml_load_string( $config );
			}
			$result = (array)$content;
			foreach ( $result as $key => $val ) {
				if ( is_object( $val ) ) {
					$result[ $key ] = (array)$val;
				}
			}
			return $result;
		}
	}
