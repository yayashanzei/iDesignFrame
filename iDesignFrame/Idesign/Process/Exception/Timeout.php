<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */


	namespace Idesign\Process\Exception;


	use Idesign\Process;

	class Timeout extends \RuntimeException {

		const TYPE_GENERAL = 1;
		const TYPE_IDLE    = 2;

		private $process;
		private $timeoutType;

		public function __construct( Process $process , $timeoutType ) {
			$this->process     = $process;
			$this->timeoutType = $timeoutType;

			parent::__construct( sprintf( 'The process "%s" exceeded the timeout of %s seconds.' , $process->getCommandLine() , $this->getExceededTimeout() ) );
		}

		public function getProcess() {
			return $this->process;
		}

		public function isGeneralTimeout() {
			return $this->timeoutType === self::TYPE_GENERAL;
		}

		public function isIdleTimeout() {
			return $this->timeoutType === self::TYPE_IDLE;
		}

		public function getExceededTimeout() {
			switch ( $this->timeoutType ) {
				case self::TYPE_GENERAL:
					return $this->process->getTimeout();

				case self::TYPE_IDLE:
					return $this->process->getIdleTimeout();

				default:
					throw new \LogicException( sprintf( 'Unknown timeout type "%d".' , $this->timeoutType ) );
			}
		}
	}