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

	class Failed extends \RuntimeException {

		private $process;

		public function __construct( Process $process ) {
			if ( $process->isSuccessful() ) {
				throw new \InvalidArgumentException( 'Expected a failed process, but the given process was successful.' );
			}

			$error = sprintf( 'The command "%s" failed.' . "\nExit Code: %s(%s)" , $process->getCommandLine() , $process->getExitCode() , $process->getExitCodeText() );

			if ( !$process->isOutputDisabled() ) {
				$error .= sprintf( "\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s" , $process->getOutput() , $process->getErrorOutput() );
			}

			parent::__construct( $error );

			$this->process = $process;
		}

		public function getProcess() {
			return $this->process;
		}
	}
