<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 10:54
	 * copy from thinkphp
	 */
	namespace Idesign;

	class ORM {
		protected static $instance = array();
		protected static $config   = array();

		/**
		 * 设置数据对象的值
		 * @access public
		 * @param string $name  名称
		 * @param mixed  $value 值
		 * @return void
		 */
		public function __set( $name , $value ) {
			self::__callStatic( '__set' , array( $name , $value ) );
		}

		/**
		 * 获取数据对象的值
		 * @access public
		 * @param string $name 名称
		 * @return mixed
		 */
		public function __get( $name ) {
			return self::__callStatic( '__get' , array( $name ) );
		}

		/**
		 * 检测数据对象的值
		 * @access public
		 * @param string $name 名称
		 * @return boolean
		 */
		public function __isset( $name ) {
			return self::__callStatic( '__isset' , array( $name ) );
		}

		/**
		 * 销毁数据对象的值
		 * @access public
		 * @param string $name 名称
		 * @return void
		 */
		public function __unset( $name ) {
			self::__callStatic( '__unset' , array( $name ) );
		}

		public function __call( $method , $params ) {
			return self::__callStatic( $method , $params );
		}

		public static function __callStatic( $method , $params ) {
			$name = basename( get_called_class() );
			if ( !isset( self::$instance[ $name ] ) ) {
				// 自动实例化模型类
				self::$instance[ $name ] = new \Idesign\Model( $name , static::$config );
			}
			return call_user_func_array( array( self::$instance[ $name ] , $method ) , $params );
		}
	}
