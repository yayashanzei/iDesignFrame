<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/20
	 * Time: 15:10
	 * copy from thinkphp
	 */

	namespace Idesign\Log\Alarm;
	/**
	 * 邮件通知驱动
	 */
	class Email {

		protected $config = array(
			'address' => '' ,
		);

		// 实例化并传入参数
		public function __construct( $config = array() ) {
			$this->config = array_merge( $this->config , $config );
		}

		/**
		 * 通知发送接口
		 * @access public
		 * @param string $log 日志信息
		 * @return void
		 */
		public function send( $msg = '' ) {
			error_log( $msg , 1 , $this->config['address'] );
		}

	}
