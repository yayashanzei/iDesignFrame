<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Output\Formatter;

	class Style {

		private static $availableForegroundColors = array(
			'black'   => array( 'set' => 30 , 'unset' => 39 ) ,
			'red'     => array( 'set' => 31 , 'unset' => 39 ) ,
			'green'   => array( 'set' => 32 , 'unset' => 39 ) ,
			'yellow'  => array( 'set' => 33 , 'unset' => 39 ) ,
			'blue'    => array( 'set' => 34 , 'unset' => 39 ) ,
			'magenta' => array( 'set' => 35 , 'unset' => 39 ) ,
			'cyan'    => array( 'set' => 36 , 'unset' => 39 ) ,
			'white'   => array( 'set' => 37 , 'unset' => 39 ) ,
		);
		private static $availableBackgroundColors = array(
			'black'   => array( 'set' => 40 , 'unset' => 49 ) ,
			'red'     => array( 'set' => 41 , 'unset' => 49 ) ,
			'green'   => array( 'set' => 42 , 'unset' => 49 ) ,
			'yellow'  => array( 'set' => 43 , 'unset' => 49 ) ,
			'blue'    => array( 'set' => 44 , 'unset' => 49 ) ,
			'magenta' => array( 'set' => 45 , 'unset' => 49 ) ,
			'cyan'    => array( 'set' => 46 , 'unset' => 49 ) ,
			'white'   => array( 'set' => 47 , 'unset' => 49 ) ,
		);
		private static $availableOptions          = array(
			'bold'       => array( 'set' => 1 , 'unset' => 22 ) ,
			'underscore' => array( 'set' => 4 , 'unset' => 24 ) ,
			'blink'      => array( 'set' => 5 , 'unset' => 25 ) ,
			'reverse'    => array( 'set' => 7 , 'unset' => 27 ) ,
			'conceal'    => array( 'set' => 8 , 'unset' => 28 ) ,
		);

		private $foreground;
		private $background;
		private $options = array();

		/**
		 * 初始化输出的样式
		 * @param string|null $foreground 字体颜色
		 * @param string|null $background 背景色
		 * @param array       $options    格式
		 * @api
		 */
		public function __construct( $foreground = null , $background = null , array $options = array() ) {
			if ( null !== $foreground ) {
				$this->setForeground( $foreground );
			}
			if ( null !== $background ) {
				$this->setBackground( $background );
			}
			if ( count( $options ) ) {
				$this->setOptions( $options );
			}
		}

		/**
		 * 设置字体颜色
		 * @param string|null $color 颜色名
		 * @throws \InvalidArgumentException
		 * @api
		 */
		public function setForeground( $color = null ) {
			if ( null === $color ) {
				$this->foreground = null;

				return;
			}

			if ( !isset( static::$availableForegroundColors[ $color ] ) ) {
				throw new \InvalidArgumentException( sprintf( 'Invalid foreground color specified: "%s". Expected one of (%s)' , $color , implode( ', ' , array_keys( static::$availableForegroundColors ) ) ) );
			}

			$this->foreground = static::$availableForegroundColors[ $color ];
		}

		/**
		 * 设置背景色
		 * @param string|null $color 颜色名
		 * @throws \InvalidArgumentException
		 * @api
		 */
		public function setBackground( $color = null ) {
			if ( null === $color ) {
				$this->background = null;

				return;
			}

			if ( !isset( static::$availableBackgroundColors[ $color ] ) ) {
				throw new \InvalidArgumentException( sprintf( 'Invalid background color specified: "%s". Expected one of (%s)' , $color , implode( ', ' , array_keys( static::$availableBackgroundColors ) ) ) );
			}

			$this->background = static::$availableBackgroundColors[ $color ];
		}

		/**
		 * 设置字体格式
		 * @param string $option 格式名
		 * @throws \InvalidArgumentException When the option name isn't defined
		 * @api
		 */
		public function setOption( $option ) {
			if ( !isset( static::$availableOptions[ $option ] ) ) {
				throw new \InvalidArgumentException( sprintf( 'Invalid option specified: "%s". Expected one of (%s)' , $option , implode( ', ' , array_keys( static::$availableOptions ) ) ) );
			}

			if ( !in_array( static::$availableOptions[ $option ] , $this->options ) ) {
				$this->options[] = static::$availableOptions[ $option ];
			}
		}

		/**
		 * 重置字体格式
		 * @param string $option 格式名
		 * @throws \InvalidArgumentException
		 */
		public function unsetOption( $option ) {
			if ( !isset( static::$availableOptions[ $option ] ) ) {
				throw new \InvalidArgumentException( sprintf( 'Invalid option specified: "%s". Expected one of (%s)' , $option , implode( ', ' , array_keys( static::$availableOptions ) ) ) );
			}

			$pos = array_search( static::$availableOptions[ $option ] , $this->options );
			if ( false !== $pos ) {
				unset( $this->options[ $pos ] );
			}
		}

		/**
		 * 批量设置字体格式
		 * @param array $options
		 */
		public function setOptions( array $options ) {
			$this->options = array();

			foreach ( $options as $option ) {
				$this->setOption( $option );
			}
		}

		/**
		 * 应用样式到文字
		 * @param string $text 文字
		 * @return string
		 */
		public function apply( $text ) {
			$setCodes   = array();
			$unsetCodes = array();

			if ( null !== $this->foreground ) {
				$setCodes[]   = $this->foreground['set'];
				$unsetCodes[] = $this->foreground['unset'];
			}
			if ( null !== $this->background ) {
				$setCodes[]   = $this->background['set'];
				$unsetCodes[] = $this->background['unset'];
			}
			if ( count( $this->options ) ) {
				foreach ( $this->options as $option ) {
					$setCodes[]   = $option['set'];
					$unsetCodes[] = $option['unset'];
				}
			}

			if ( 0 === count( $setCodes ) ) {
				return $text;
			}

			return sprintf( "\033[%sm%s\033[%sm" , implode( ';' , $setCodes ) , $text , implode( ';' , $unsetCodes ) );
		}
	}