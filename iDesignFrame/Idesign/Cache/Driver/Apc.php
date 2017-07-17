<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/20
	 * Time: 15:10
	 * copy from thinkphp
	 */

	namespace Idesign\Cache\Driver;

	/**
	 * Apc缓存驱动
	 * @author    liu21st <liu21st@gmail.com>
	 */
	class Apc {

		protected $options = array(
			'expire' => 0 ,
			'prefix' => '' ,
			'length' => 0 ,
		);
		/*****************************
		 * 需要支持apc_cli模式
		 ******************************/
		/**
		 * 架构函数
		 *
		 * @param array $options 缓存参数
		 *
		 * @throws Exception
		 * @access public
		 */
		public function __construct( $options = array() ) {
			if ( !function_exists( 'apc_cache_info' ) ) {
				throw new \Idesign\MyException( '_NOT_SUPPERT_:Apc' );
			}
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
			return apc_fetch( $this->options['prefix'] . $name );
		}

		/**
		 * 写入缓存
		 * @access public
		 * @param string  $name   缓存变量名
		 * @param mixed   $value  存储数据
		 * @param integer $expire 有效时间（秒）
		 * @return bool
		 */
		public function set( $name , $value , $expire = null ) {
			\Idesign\Cache::$writeTimes++;
			if ( is_null( $expire ) ) {
				$expire = $this->options['expire'];
			}
			$name = $this->options['prefix'] . $name;
			if ( $result = apc_store( $name , $value , $expire ) ) {
				if ( $this->options['length'] > 0 ) {
					// 记录缓存队列
					$queue = apc_fetch( '__info__' );
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
						apc_delete( $key );
					}
					apc_store( '__info__' , $queue );
				}
			}
			return $result;
		}

		/**删除缓存
		 * @access public
		 *
		 * @param string $name 缓存变量名
		 *
		 * @return bool|\string[]
		 */
		public function rm( $name ) {
			return apc_delete( $this->options['prefix'] . $name );
		}

		/**
		 * 清除缓存
		 * @access public
		 * @return bool
		 */
		public function clear() {
			return apc_clear_cache();
		}
	}
