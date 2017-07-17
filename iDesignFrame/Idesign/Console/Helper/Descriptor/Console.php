<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Helper\Descriptor;


	use Idesign\Console\Command\Command;
	use Idesign\Console as ThinkConsole;

	class Console {

		const GLOBAL_NAMESPACE = '_global';

		/**
		 * @var ThinkConsole
		 */
		private $console;

		/**
		 * @var null|string
		 */
		private $namespace;

		/**
		 * @var array
		 */
		private $namespaces;

		/**
		 * @var Command[]
		 */
		private $commands;

		/**
		 * @var Command[]
		 */
		private $aliases;

		/**
		 * 构造方法
		 * @param ThinkConsole $console
		 * @param string|null  $namespace
		 */
		public function __construct( ThinkConsole $console , $namespace = null ) {
			$this->console   = $console;
			$this->namespace = $namespace;
		}

		/**
		 * @return array
		 */
		public function getNamespaces() {
			if ( null === $this->namespaces ) {
				$this->inspectConsole();
			}

			return $this->namespaces;
		}

		/**
		 * @return Command[]
		 */
		public function getCommands() {
			if ( null === $this->commands ) {
				$this->inspectConsole();
			}

			return $this->commands;
		}

		/**
		 * @param string $name
		 * @return Command
		 * @throws \InvalidArgumentException
		 */
		public function getCommand( $name ) {
			if ( !isset( $this->commands[ $name ] ) && !isset( $this->aliases[ $name ] ) ) {
				throw new \InvalidArgumentException( sprintf( 'Command %s does not exist.' , $name ) );
			}

			return isset( $this->commands[ $name ] ) ? $this->commands[ $name ] : $this->aliases[ $name ];
		}

		private function inspectConsole() {
			$this->commands   = array();
			$this->namespaces = array();

			$all = $this->console->all( $this->namespace ? $this->console->findNamespace( $this->namespace ) : null );
			foreach ( $this->sortCommands( $all ) as $namespace => $commands ) {
				$names = array();

				/** @var Command $command */
				foreach ( $commands as $name => $command ) {
					if ( !$command->getName() ) {
						continue;
					}

					if ( $command->getName() === $name ) {
						$this->commands[ $name ] = $command;
					} else {
						$this->aliases[ $name ] = $command;
					}

					$names[] = $name;
				}

				$this->namespaces[ $namespace ] = array( 'id' => $namespace , 'commands' => $names );
			}
		}

		/**
		 * @param array $commands
		 * @return array
		 */
		private function sortCommands( array $commands ) {
			$namespacedCommands = array();
			foreach ( $commands as $name => $command ) {
				$key = $this->console->extractNamespace( $name , 1 );
				if ( !$key ) {
					$key = '_global';
				}

				$namespacedCommands[ $key ][ $name ] = $command;
			}
			ksort( $namespacedCommands );

			foreach ( $namespacedCommands as &$commandsSet ) {
				ksort( $commandsSet );
			}
			// unset reference to keep scope clear
			unset( $commandsSet );

			return $namespacedCommands;
		}
	}