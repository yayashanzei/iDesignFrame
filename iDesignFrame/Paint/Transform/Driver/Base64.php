<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/27
	 * Time: 23:31
	 * copy from thinkphp
	 */

	namespace Paint\Transform\Driver;

	/**
	 * Base64编码实现
	 *
	 */
	class Base64 {
		/**
		 * @access public
		 * @static 编解码目标
		 *     default: 原始的编(解)码
		 *     url    : URL友好的编(解)码
		 *     regex  : 正则表达式友好的编(解)码
		 */
		public static $target = 'default';

		/**
		 * Base64编码函数
		 *
		 * @param string $data   欲编码的数据
		 * @param string $target 编码目标
		 */
		public function encode( $data , $target = '' ) {
			// 当函数没有特别指定编码目标时, 使用类自身编码目标
			if ( empty( $target ) ) {
				$target = self::$target;
			}
			// 进行一次原始编码
			$data = base64_encode( $data );
			// 根据编码目标替换字符
			switch ( $target ) {
				case 'url':
					$data = str_replace( array( '+' , '/' , '=' ) , array( '-' , '_' , '' ) , $data );
				break;
				case 'regex':
					$data = str_replace( array( '+' , '/' , '=' ) , array( '!' , '-' , '' ) , $data );
				break;
				case 'default':
				default:
				break;
			}
			// 返回编码结果
			return $data;
		}

		/**
		 * Base64解码函数
		 *
		 * @param string $data   欲解码的数据
		 * @param string $target 解码目标
		 */
		public function decode( $data , $target = '' ) {
			// 当函数没有特别指定解码目标时, 使用类自身解码目标
			if ( empty( $target ) ) {
				$target = self::$target;
			}
			// 根据解码目标替换字符
			switch ( $target ) {
				case 'url':
					$data = str_replace( array( '-' , '_' ) , array( '+' , '/' ) , $data );
				break;
				case 'regex':
					$data = str_replace( array( '!' , '-' ) , array( '+' , '/' ) , $data );
				break;
				case 'default':
				default:
				break;
			}
			// 原始解码，并返回结果
			return base64_decode( $data );
		}

	}
