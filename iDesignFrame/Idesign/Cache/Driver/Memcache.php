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
	 * Memcache缓存驱动
	 * @author    liu21st <liu21st@gmail.com>
	 */
	class Memcache {
		protected $handler = null;
		protected $options = array(
			'host'       => '127.0.0.1' ,
			'port'       => 11211 ,
			'expire'     => 0 ,
			'timeout'    => false ,
			'persistent' => false ,
			'length'     => 0 ,
		);

		/**
		 * 架构函数
		 * @param array $options 缓存参数
		 * @access public
		 */
		public function __construct( $options = array() ) {
			if ( !extension_loaded( 'memcache' ) ) {
				throw new \Idesign\MyException( '_NOT_SUPPERT_:memcache' );
			}
			if ( !empty( $options ) ) {
				$this->options = array_merge( $this->options , $options );
			}
			$this->handler = new \Memcache;
			// 支持集群
			$hosts = explode( ',' , $this->options['host'] );
			$ports = explode( ',' , $this->options['port'] );

			foreach ( (array)$hosts as $i => $host ) {
				$port = isset( $ports[ $i ] ) ? $ports[ $i ] : $ports[0];
				false === $options['timeout'] ?
					$this->handler->addServer( $host , $port , $this->options['persistent'] , 1 ) :
					$this->handler->addServer( $host , $port , $this->options['persistent'] , 1 , $this->options['timeout'] );
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
			return $this->handler->get( $this->options['prefix'] . $name );
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
			if ( $this->handler->set( $name , $value , 0 , $expire ) ) {
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
				return true;
			}
			return false;
		}

		/**
		 * 删除缓存
		 *
		 * @param    string  $name 缓存变量名
		 * @param bool|false $ttl
		 *
		 * @return bool
		 */
		public function rm( $name , $ttl = false ) {
			$name = $this->options['prefix'] . $name;
			return false === $ttl ?
				$this->handler->delete( $name ) :
				$this->handler->delete( $name , $ttl );
		}

		/**
		 * 清除缓存
		 * @access public
		 * @return bool
		 */
		public function clear() {
			return $this->handler->flush();
		}
	}
