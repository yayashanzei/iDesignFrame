<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Output\Formatter;


	class Stack {

		/**
		 * @var Style[]
		 */
		private $styles;

		/**
		 * @var Style
		 */
		private $emptyStyle;

		/**
		 * 构造方法
		 * @param Style|null $emptyStyle
		 */
		public function __construct( Style $emptyStyle = null ) {
			$this->emptyStyle = $emptyStyle ? : new Style();
			$this->reset();
		}

		/**
		 * 重置堆栈
		 */
		public function reset() {
			$this->styles = array();
		}

		/**
		 * 推一个样式进入堆栈
		 * @param Style $style
		 */
		public function push( Style $style ) {
			$this->styles[] = $style;
		}

		/**
		 * 从堆栈中弹出一个样式
		 * @param Style|null $style
		 * @return Style
		 * @throws \InvalidArgumentException
		 */
		public function pop( Style $style = null ) {
			if ( empty( $this->styles ) ) {
				return $this->emptyStyle;
			}

			if ( null === $style ) {
				return array_pop( $this->styles );
			}

			/**
			 * @var int   $index
			 * @var Style $stackedStyle
			 */
			foreach ( array_reverse( $this->styles , true ) as $index => $stackedStyle ) {
				if ( $style->apply( '' ) === $stackedStyle->apply( '' ) ) {
					$this->styles = array_slice( $this->styles , 0 , $index );

					return $stackedStyle;
				}
			}

			throw new \InvalidArgumentException( 'Incorrectly nested style tag found.' );
		}

		/**
		 * 计算堆栈的当前样式。
		 * @return Style
		 */
		public function getCurrent() {
			if ( empty( $this->styles ) ) {
				return $this->emptyStyle;
			}

			return $this->styles[ count( $this->styles ) - 1 ];
		}

		/**
		 * @param Style $emptyStyle
		 * @return Stack
		 */
		public function setEmptyStyle( Style $emptyStyle ) {
			$this->emptyStyle = $emptyStyle;

			return $this;
		}

		/**
		 * @return Style
		 */
		public function getEmptyStyle() {
			return $this->emptyStyle;
		}
	}