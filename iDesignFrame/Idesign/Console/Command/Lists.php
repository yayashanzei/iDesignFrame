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
	use Idesign\Console\Output;
	use Idesign\Console\Input\Argument as InputArgument;
	use Idesign\Console\Input\Option as InputOption;
	use Idesign\Console\Input\Definition as InputDefinition;
	use Idesign\Console\Helper\Descriptor as DescriptorHelper;

	class Lists extends Command {

		/**
		 * {@inheritdoc}
		 */
		protected function configure() {
			$this->setName( 'list' )->setDefinition( $this->createDefinition() )->setDescription( 'Lists commands' )->setHelp( <<<EOF
The <info>%command.name%</info> command lists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
			);
		}

		/**
		 * {@inheritdoc}
		 */
		public function getNativeDefinition() {
			return $this->createDefinition();
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute( Input $input , Output $output ) {

			$helper = new DescriptorHelper();
			$helper->describe( $output , $this->getConsole() , array(
				'raw_text'  => $input->getOption( 'raw' ) ,
				'namespace' => $input->getArgument( 'namespace' ) ,
			) );
		}

		/**
		 * {@inheritdoc}
		 */
		private function createDefinition() {
			return new InputDefinition( array(
				                            new InputArgument( 'namespace' , InputArgument::OPTIONAL , 'The namespace name' ) ,
				                            new InputOption( 'raw' , null , InputOption::VALUE_NONE , 'To output raw command list' ) ,
			                            ) );
		}
	}