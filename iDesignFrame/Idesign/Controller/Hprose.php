<?php

	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/27
	 * Time: 23:31
	 * copy from thinkphp
	 */
	namespace Idesign\Controller;

	/**
	 * ThinkPHP Hprose控制器类
	 */
	abstract class Hprose {

		protected $allowMethodList = '';
		protected $crossDomain     = false;
		protected $P3P             = false;
		protected $get             = true;
		protected $debug           = false;

		/**
		 * 架构函数
		 * @access public
		 */
		public function __construct() {
			//控制器初始化
			if ( method_exists( $this , '_initialize' ) ) {
				$this->_initialize();
			}

			//导入类库
			\Idesign\Loader::import( 'vendor.Hprose.HproseHttpServer' );
			//实例化HproseHttpServer
			$server = new \HproseHttpServer();
			if ( $this->allowMethodList ) {
				$methods = $this->allowMethodList;
			} else {
				$methods = get_class_methods( $this );
				$methods = array_diff( $methods , array( '__construct' , '__call' , '_initialize' ) );
			}
			$server->addMethods( $methods , $this );
			if ( APP_DEBUG || $this->debug ) {
				$server->setDebugEnabled( true );
			}
			// Hprose设置
			$server->setCrossDomainEnabled( $this->crossDomain );
			$server->setP3PEnabled( $this->P3P );
			$server->setGetEnabled( $this->get );
			// 启动server
			$server->start();
		}

		/**
		 * 魔术方法 有不存在的操作的时候执行
		 * @access public
		 * @param string $method 方法名
		 * @param array  $args   参数
		 * @return mixed
		 */
		public function __call( $method , $args ) { }
	}
