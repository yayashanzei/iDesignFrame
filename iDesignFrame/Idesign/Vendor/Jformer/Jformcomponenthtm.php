<?php

	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/12/12
	 * Time: 13:42
	 * copy from jformer
	 */
	namespace Idesign\Vendor\Jformer;
	class JFormComponentHtml {

		var $html;

		function __construct( $html ) {
			$this->id   = uniqid();
			$this->html = $html;
		}

		function getOptions() {
			return null;
		}

		function clearValue() {
			return null;
		}

		function validate() {
			return null;
		}

		function getValue() {
			return null;
		}

		function __toString() {
			return $this->html;
		}

	}