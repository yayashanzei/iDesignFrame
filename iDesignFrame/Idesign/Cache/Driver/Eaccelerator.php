<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/20
	 * Time: 15:10
	 * copy from thinkphp
	 */

	namespace Idesignesign\Cache\Driver;
	/**
	 * Eaccelerator缓存驱动
	 * @author    liu21st <liu21st@gmail.com>
	 */
	class Eaccelerator {

		protected $options = array(
			'prefix' => '' ,
			'expire' => 0 ,
			'length' => 0 ,
		);

		/**
		 * 架构函数
		 * @param array $options 缓存参数
		 * @access public
		 */
		public function __construct( $options = array() ) {
			if ( !empty( $options ) ) {
				$this->options = array_merge( $this->options , $options );
			}
		}

		/**
		 * 读取缓存
		 * @access public
		 * @param string $name 缓存变量名
		 * @return mixed
		 */
		public function get( $name ) {
			\Idesign\Cache::$readTimes++;
			return eaccelerator_get( $this->options['prefix'] . $name );
		}

		/**
		 * 写入缓存
		 * @access public
		 * @param string  $name   缓存变量名
		 * @param mixed   $value  存储数据
		 * @param integer $expire 有效时间（秒）
		 * @return boolen
		 */
		public function set( $name , $value , $expire = null ) {
			\Idesign\Cache::$writeTimes++;
			if ( is_null( $expire ) ) {
				$expire = $this->options['expire'];
			}
			$name = $this->options['prefix'] . $name;
			eaccelerator_lock( $name );
			if ( eaccelerator_put( $name , $value , $expire ) ) {
				if ( $this->options['length'] > 0 ) {
					// 记录缓存队列
					$queue = eaccelerator_get( '__info__' );
					if ( !$queue ) {
						$queue = array();
					}
					if ( false === array_search( $name , $queue ) ) {
						array_push( $queue , $name );
					}

					if ( count( $queue ) > $this->options['length'] ) {
						// 出列
						$key = array_shift( $queue );
						// 删除缓存
						eaccelerator_rm( $key );
					}
					eaccelerator_put( '__info__' , $queue );
				}
				return true;
			}
			return false;
		}

		/**
		 * 删除缓存
		 * @access public
		 * @param string $name 缓存变量名
		 * @return boolen
		 */
		public function rm( $name ) {
			return eaccelerator_rm( $this->options['prefix'] . $name );
		}

		/**
		 * 清除缓存
		 * @access public
		 * @return boolen
		 */
		public function clear() {
			return;
		}
	}
