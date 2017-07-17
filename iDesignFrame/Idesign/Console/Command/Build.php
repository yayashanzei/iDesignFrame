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
	use Idesign\Console\Input\Option;
	use Idesign\Console\Output;

	class Build extends Command {

		/**
		 * {@inheritdoc}
		 */
		protected function configure() {
			$this->setName( 'build' )
				->setDefinition( array( new Option( 'config' , null , Option::VALUE_OPTIONAL , "build.php path" ) ) )
				->setDescription( 'Build Application Dirs' );
		}

		protected function execute( Input $input , Output $output ) {

			if ( $input->hasOption( 'config' ) ) {
				$build = include $input->getOption( 'config' );
			} else {
				$build = include APP_PATH . 'build.php';
			}
			if ( empty( $build ) ) {
				$output->writeln( "Build Config Is Empty" );
				return;
			}
			\Idesign\Build::run( $build );
			$output->writeln( "Successed" );

		}
	}