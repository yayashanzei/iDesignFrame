<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/12/12
	 * Time: 13:42
	 * copy from jformer
	 */
	namespace Idesign\Vendor\Jformer;

	class JFormComponentHidden extends JFormComponent {
		/*
		 * Constructor
		 */

		function __construct( $id , $value , $optionArray = array() ) {
			// Class variables
			$this->id    = $id;
			$this->name  = $this->id;
			$this->class = 'jFormComponentHidden';

			// Initialize the abstract FormComponent object
			$this->initialize( $optionArray );

			// Prevent the value from being overwritten
			$this->value = $value;
		}

		/**
		 *
		 * @return string
		 */
		function __toString() {
			// Generate the component div without a label
			$div = $this->generateComponentDiv( false );
			$div->addToAttribute( 'style' , 'display: none;' );

			// Input tag
			$input = new JFormElement( 'input' , array(
				'type'  => 'hidden' ,
				'id'    => $this->id ,
				'name'  => $this->name ,
				'value' => $this->value ,
			) );
			$div->insert( $input );

			return $div->__toString();
		}

	}