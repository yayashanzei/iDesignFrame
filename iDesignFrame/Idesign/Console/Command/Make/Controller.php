<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Command\Make;

	use Idesign\Console\Command\Command;

	class Controller extends Command {

		public function __construct() {
			parent::__construct( "make:controller" );
		}
	}