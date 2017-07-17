<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */


	namespace Idesign\Process\Pipes;


	use Idesign\Process;

	class Unix extends Pipes {

		/** @var bool */
		private $ttyMode;
		/** @var bool */
		private $ptyMode;
		/** @var bool */
		private $disableOutput;

		public function __construct( $ttyMode , $ptyMode , $input , $disableOutput ) {
			$this->ttyMode       = (bool)$ttyMode;
			$this->ptyMode       = (bool)$ptyMode;
			$this->disableOutput = (bool)$disableOutput;

			if ( is_resource( $input ) ) {
				$this->input = $input;
			} else {
				$this->inputBuffer = (string)$input;
			}
		}

		public function __destruct() {
			$this->close();
		}

		/**
		 * {@inheritdoc}
		 */
		public function getDescriptors() {
			if ( $this->disableOutput ) {
				$nullstream = fopen( '/dev/null' , 'c' );

				return array(
					array( 'pipe' , 'r' ) ,
					$nullstream ,
					$nullstream ,
				);
			}

			if ( $this->ttyMode ) {
				return array(
					array( 'file' , '/dev/tty' , 'r' ) ,
					array( 'file' , '/dev/tty' , 'w' ) ,
					array( 'file' , '/dev/tty' , 'w' ) ,
				);
			}

			if ( $this->ptyMode && Process::isPtySupported() ) {
				return array(
					array( 'pty' ) ,
					array( 'pty' ) ,
					array( 'pty' ) ,
				);
			}

			return array(
				array( 'pipe' , 'r' ) ,
				array( 'pipe' , 'w' ) , // stdout
				array( 'pipe' , 'w' ) , // stderr
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function getFiles() {
			return array();
		}

		/**
		 * {@inheritdoc}
		 */
		public function readAndWrite( $blocking , $close = false ) {

			if ( 1 === count( $this->pipes ) && array( 0 ) === array_keys( $this->pipes ) ) {
				fclose( $this->pipes[0] );
				unset( $this->pipes[0] );
			}

			if ( empty( $this->pipes ) ) {
				return array();
			}

			$this->unblock();

			$read = array();

			if ( null !== $this->input ) {
				$r = array_merge( $this->pipes , array( 'input' => $this->input ) );
			} else {
				$r = $this->pipes;
			}

			unset( $r[0] );

			$w = isset( $this->pipes[0] ) ? array( $this->pipes[0] ) : null;
			$e = null;

			if ( false === $n = @stream_select( $r , $w , $e , 0 , $blocking ? Process::TIMEOUT_PRECISION*1E6 : 0 ) ) {

				if ( !$this->hasSystemCallBeenInterrupted() ) {
					$this->pipes = array();
				}

				return $read;
			}

			if ( 0 === $n ) {
				return $read;
			}

			foreach ( $r as $pipe ) {

				$type = ( false !== $found = array_search( $pipe , $this->pipes ) ) ? $found : 'input';
				$data = '';
				while ( '' !== $dataread = (string)fread( $pipe , self::CHUNK_SIZE ) ) {
					$data .= $dataread;
				}

				if ( '' !== $data ) {
					if ( $type === 'input' ) {
						$this->inputBuffer .= $data;
					} else {
						$read[ $type ] = $data;
					}
				}

				if ( false === $data || ( true === $close && feof( $pipe ) && '' === $data ) ) {
					if ( $type === 'input' ) {
						$this->input = null;
					} else {
						fclose( $this->pipes[ $type ] );
						unset( $this->pipes[ $type ] );
					}
				}
			}

			if ( null !== $w && 0 < count( $w ) ) {
				while ( strlen( $this->inputBuffer ) ) {
					$written = fwrite( $w[0] , $this->inputBuffer , 2 << 18 ); // write 512k
					if ( $written > 0 ) {
						$this->inputBuffer = (string)substr( $this->inputBuffer , $written );
					} else {
						break;
					}
				}
			}

			if ( '' === $this->inputBuffer && null === $this->input && isset( $this->pipes[0] ) ) {
				fclose( $this->pipes[0] );
				unset( $this->pipes[0] );
			}

			return $read;
		}

		/**
		 * {@inheritdoc}
		 */
		public function areOpen() {
			return (bool)$this->pipes;
		}

		/**
		 * 创建一个新的 UnixPipes 实例
		 * @param Process         $process
		 * @param string|resource $input
		 * @return self
		 */
		public static function create( Process $process , $input ) {
			return new static( $process->isTty() , $process->isPty() , $input , $process->isOutputDisabled() );
		}
	}