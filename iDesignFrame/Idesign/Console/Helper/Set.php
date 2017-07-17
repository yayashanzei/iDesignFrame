<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/2/18
	 * Time: 11:02
	 * copy from thinkphp
	 */

	namespace Idesign\Console\Helper;


	use Idesign\Console\Command\Command;

	class Set implements \IteratorAggregate {

		private $helpers = array();
		private $command;

		/**
		 * 构造方法
		 * @param Helper[] $helpers 助手实例数组
		 */
		public function __construct( array $helpers = array() ) {
			/**
			 * @var int|string $alias
			 * @var Helper     $helper
			 */
			foreach ( $helpers as $alias => $helper ) {
				$this->set( $helper , is_int( $alias ) ? null : $alias );
			}
		}

		/**
		 * 添加一个助手
		 * @param Helper $helper 助手实例
		 * @param string $alias  别名
		 */
		public function set( Helper $helper , $alias = null ) {
			$this->helpers[ $helper->getName() ] = $helper;
			if ( null !== $alias ) {
				$this->helpers[ $alias ] = $helper;
			}

			$helper->setHelperSet( $this );
		}

		/**
		 * 是否有某个助手
		 * @param string $name 助手名称
		 * @return bool
		 */
		public function has( $name ) {
			return isset( $this->helpers[ $name ] );
		}

		/**
		 * 获取助手
		 * @param string $name 助手名称
		 * @return Helper
		 * @throws \InvalidArgumentException
		 */
		public function get( $name ) {
			if ( !$this->has( $name ) ) {
				throw new \InvalidArgumentException( sprintf( 'The helper "%s" is not defined.' , $name ) );
			}

			return $this->helpers[ $name ];
		}

		/**
		 * 设置与这个助手关联的命令集
		 * @param Command $command
		 */
		public function setCommand( Command $command = null ) {
			$this->command = $command;
		}

		/**
		 * 获取与这个助手关联的命令集
		 * @return Command
		 */
		public function getCommand() {
			return $this->command;
		}

		public function getIterator() {
			return new \ArrayIterator( $this->helpers );
		}
	}
