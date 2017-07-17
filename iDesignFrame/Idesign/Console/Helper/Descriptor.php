<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Helper;

	use Idesign\Console\Helper\Descriptor\Descriptor as OutputDescriptor;
	use Idesign\Console\Output;

	class Descriptor extends Helper {

		/**
		 * @var OutputDescriptor
		 */
		private $descriptor;

		/**
		 * 构造方法
		 */
		public function __construct() {
			$this->descriptor = new OutputDescriptor();
		}

		/**
		 * 描述
		 * @param Output $output
		 * @param object $object
		 * @param array  $options
		 * @throws \InvalidArgumentException
		 */
		public function describe( Output $output , $object , array $options = array() ) {
			$options = array_merge( array(
				                        'raw_text' => false ,
			                        ) , $options );

			$this->descriptor->describe( $output , $object , $options );
		}

		/**
		 * {@inheritdoc}
		 */
		public function getName() {
			return 'descriptor';
		}
	}
