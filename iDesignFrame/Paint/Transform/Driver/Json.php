<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/27
	 * Time: 23:31
	 * copy from thinkphp
	 */

	namespace Paint\Transform\Driver;

	class Json {
		public function encode( $data ) {
			return json_encode( $data , JSON_UNESCAPED_UNICODE );
		}

		public function decode( $data , $assoc = true ) {
			return json_decode( $data , $assoc );
		}
	}
