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
	 * Redis缓存驱动
	 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
	 * @author    尘缘 <130775@qq.com>
	 */
	class Redis {
		protected $handler = null;
		protected $options = array(
			'host'       => '127.0.0.1' ,
			'port'       => 6379 ,
			'timeout'    => false ,
			'expire'     => 0 ,
			'persistent' => false ,
			'length'     => 0 ,
		);

		/**
		 * 架构函数
		 * @param array $options 缓存参数
		 * @access public
		 */
		public function __construct( $options = array() ) {
			if ( !extension_loaded( 'redis' ) ) {
				throw new \Idesign\MyException( '_NOT_SUPPERT_:redis' );
			}
			if ( !empty( $options ) ) {
				$this->options = array_merge( $this->options , $options );
			}
			$func          = $this->options['persistent'] ? 'pconnect' : 'connect';
			$this->handler = new \Redis;
			false === $this->options['timeout'] ?
				$this->handler->$func( $this->options['host'] , $this->options['port'] ) :
				$this->handler->$func( $this->options['host'] , $this->options['port'] , $this->options['timeout'] );
		}

		/**
		 * 读取缓存
		 * @access public
		 * @param string $name 缓存变量名
		 * @return mixed
		 */
		public function get( $name ) {
			\Idesign\Cache::$readTimes++;
			return $this->handler->get( $this->options['prefix'] . $name );
		}

		/**
		 * 写入缓存
		 * @access public
		 * @param string  $name   缓存变量名
		 * @param mixed   $value  存储数据
		 * @param integer $expire 有效时间（秒）
		 * @return boolean
		 */
		public function set( $name , $value , $expire = null ) {
			\Idesign\Cache::$writeTimes++;
			if ( is_null( $expire ) ) {
				$expire = $this->options['expire'];
			}
			$name = $this->options['prefix'] . $name;
			if ( is_int( $expire ) ) {
				$result = $this->handler->setex( $name , $expire , $value );
			} else {
				$result = $this->handler->set( $name , $value );
			}
			if ( $result && $this->options['length'] > 0 ) {
				if ( $this->options['length'] > 0 ) {
					// 记录缓存队列
					$queue = $this->handler->get( '__info__' );
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
						$this->handler->delete( $key );
					}
					$this->handler->set( '__info__' , $queue );
				}
			}
			return $result;
		}

		/**
		 * 删除缓存
		 * @access public
		 * @param string $name 缓存变量名
		 * @return boolean
		 */
		public function rm( $name ) {
			return $this->handler->delete( $this->options['prefix'] . $name );
		}

		/**
		 * 清除缓存
		 * @access public
		 * @return boolean
		 */
		public function clear() {
			return $this->handler->flushDB();
		}

	}
