<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Command;


	use Idesign\Console\Input;
	use Idesign\Console\Input\Argument as InputArgument;
	use Idesign\Console\Input\Option as InputOption;
	use Idesign\Console\Output;
	use Idesign\Console\Helper\Descriptor as DescriptorHelper;

	class Help extends Command {

		private $command;

		/**
		 * {@inheritdoc}
		 */
		protected function configure() {
			$this->ignoreValidationErrors();

			$this->setName( 'help' )->setDefinition( array(
				                                         new InputArgument( 'command_name' , InputArgument::OPTIONAL , 'The command name' , 'help' ) ,
				                                         new InputOption( 'raw' , null , InputOption::VALUE_NONE , 'To output raw command help' ) ,
			                                         ) )->setDescription( 'Displays help for a command' )->setHelp( <<<EOF
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
			);
		}

		/**
		 * Sets the command.
		 * @param Command $command The command to set
		 */
		public function setCommand( Command $command ) {
			$this->command = $command;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute( Input $input , Output $output ) {
			if ( null === $this->command ) {
				$this->command = $this->getConsole()->find( $input->getArgument( 'command_name' ) );
			}


			$helper = new DescriptorHelper();
			$helper->describe( $output , $this->command , array(
				'raw_text' => $input->getOption( 'raw' ) ,
			) );

			$this->command = null;
		}
	}