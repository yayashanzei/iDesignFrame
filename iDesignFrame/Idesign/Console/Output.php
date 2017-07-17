<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console;

	use Idesign\Console\Output\Formatter;
	use Idesign\Console\Output\Stream;

	class Output extends Stream {

		/** @var Stream */
		private $stderr;

		public function __construct() {
			$outputStream = 'php://stdout';
			if ( !$this->hasStdoutSupport() ) {
				$outputStream = 'php://output';
			}

			parent::__construct( fopen( $outputStream , 'w' ) );

			$this->stderr = new Stream( fopen( 'php://stderr' , 'w' ) , $this->getFormatter() );
		}

		/**
		 * {@inheritdoc}
		 */
		public function setDecorated( $decorated ) {
			parent::setDecorated( $decorated );
			$this->stderr->setDecorated( $decorated );
		}

		/**
		 * {@inheritdoc}
		 */
		public function setFormatter( Formatter $formatter ) {
			parent::setFormatter( $formatter );
			$this->stderr->setFormatter( $formatter );
		}

		/**
		 * {@inheritdoc}
		 */
		public function setVerbosity( $level ) {
			parent::setVerbosity( $level );
			$this->stderr->setVerbosity( $level );
		}

		/**
		 * {@inheritdoc}
		 */
		public function getErrorOutput() {
			return $this->stderr;
		}

		/**
		 * {@inheritdoc}
		 */
		public function setErrorOutput( Output $error ) {
			$this->stderr = $error;
		}

		/**
		 * 检查当前环境是否支持控制台输出写入标准输出。
		 * @return bool
		 */
		protected function hasStdoutSupport() {
			return ( 'OS400' != php_uname( 's' ) );
		}
	}