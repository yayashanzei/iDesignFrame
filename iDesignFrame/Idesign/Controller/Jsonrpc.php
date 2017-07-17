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
	 * ThinkPHP JsonRPC控制器类
	 */
	abstract class Jsonrpc {

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
			\Idesign\Loader::import( 'vendor.jsonrpc.jsonRPCServer' );
			// 启动server
			\jsonRPCServer::handle( $this );
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
