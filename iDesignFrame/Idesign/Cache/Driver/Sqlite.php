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
	 * Sqlite缓存驱动
	 * @author    liu21st <liu21st@gmail.com>
	 */
	class Sqlite {

		protected $options = array(
			'db'         => ':memory:' ,
			'table'      => 'sharedmemory' ,
			'prefix'     => '' ,
			'expire'     => 0 ,
			'length'     => 0 ,
			'persistent' => false ,
		);

		/**
		 * 架构函数
		 *
		 * @param array $options 缓存参数
		 *
		 * @throws Exception
		 * @access public
		 */
		public function __construct( $options = array() ) {
			if ( !extension_loaded( 'sqlite' ) ) {
				throw new \Idesign\MyException( '_NOT_SUPPERT_:sqlite' );
			}
			if ( !empty( $options ) ) {
				$this->options = array_merge( $this->options , $options );
			}
			$func          = $this->options['persistent'] ? 'sqlite_popen' : 'sqlite_open';
			$this->handler = $func( $this->options['db'] );
		}

		/**
		 * 读取缓存
		 * @access public
		 * @param string $name 缓存变量名
		 * @return mixed
		 */
		public function get( $name ) {
			\Idesign\Cache::$readTimes++;
			$name   = $this->options['prefix'] . sqlite_escape_string( $name );
			$sql    = 'SELECT value FROM ' . $this->options['table'] . ' WHERE var=\'' . $name . '\' AND (expire=0 OR expire >' . time() . ') LIMIT 1';
			$result = sqlite_query( $this->handler , $sql );
			if ( sqlite_num_rows( $result ) ) {
				$content = sqlite_fetch_single( $result );
				if ( function_exists( 'gzcompress' ) ) {
					//启用数据压缩
					$content = gzuncompress( $content );
				}
				return unserialize( $content );
			}
			return false;
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
			$name  = $this->options['prefix'] . sqlite_escape_string( $name );
			$value = sqlite_escape_string( serialize( $value ) );
			if ( is_null( $expire ) ) {
				$expire = $this->options['expire'];
			}
			$expire = ( 0 == $expire ) ? 0 : ( time() + $expire ); //缓存有效期为0表示永久缓存
			if ( function_exists( 'gzcompress' ) ) {
				//数据压缩
				$value = gzcompress( $value , 3 );
			}
			$sql = 'REPLACE INTO ' . $this->options['table'] . ' (var, value,expire) VALUES (\'' . $name . '\', \'' . $value . '\', \'' . $expire . '\')';
			if ( sqlite_query( $this->handler , $sql ) ) {
				if ( $this->options['length'] > 0 ) {
					// 记录缓存队列
					$this->queue( $name );
				}
				return true;
			}
			return false;
		}

		/**
		 * 删除缓存
		 * @access public
		 * @param string $name 缓存变量名
		 * @return boolean
		 */
		public function rm( $name ) {
			$name = $this->options['prefix'] . sqlite_escape_string( $name );
			$sql  = 'DELETE FROM ' . $this->options['table'] . ' WHERE var=\'' . $name . '\'';
			sqlite_query( $this->handler , $sql );
			return true;
		}

		/**
		 * 清除缓存
		 * @access public
		 * @return boolean
		 */
		public function clear() {
			$sql = 'DELETE FROM ' . $this->options['table'];
			sqlite_query( $this->handler , $sql );
			return;
		}
	}
