<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/12/12
	 * Time: 13:42
	 * copy from jformer
	 */
	namespace Idesign\Vendor\Jformer;

	class JFormComponentLikertStatement extends JFormComponent {

		/**
		 * Constructor
		 */
		function __construct( $id , $label , $choiceArray , $statementArray , $optionsArray ) {
			// General settings
			$this->id    = $id;
			$this->name  = $this->id;
			$this->class = 'jFormComponentLikertStatement';
			$this->label = $label;
			// Initialize the abstract FormComponent object
			$this->initialize( $optionsArray );
		}

		function __toString() {
			return;
		}

	}