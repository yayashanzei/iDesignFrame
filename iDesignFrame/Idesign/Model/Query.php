<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/26
	 * Time: 14:48
	 * copy from thinkphp
	 */
	namespace Idesign\Model;

	use Idesign\Model;

	class Query extends Model {

		/**
		 * 启动事务
		 * @access public
		 * @return void
		 */
		public function startTrans() {
			$this->commit();
			$this->db->startTrans();
			return;
		}

		/**
		 * 提交事务
		 * @access public
		 * @return boolean
		 */
		public function commit() {
			return $this->db->commit();
		}

		/**
		 * 事务回滚
		 * @access public
		 * @return boolean
		 */
		public function rollback() {
			return $this->db->rollback();
		}

		/**
		 * 批处理执行SQL语句
		 * 批处理的指令都认为是execute操作
		 * @access public
		 * @param array $sql SQL批处理指令
		 * @return boolean
		 */
		public function patchQuery( $sql = array() ) {
			if ( !is_array( $sql ) ) {
				return false;
			}
			// 自动启动事务支持
			$this->startTrans();
			try {
				foreach ( $sql as $_sql ) {
					$result = $this->execute( $_sql );
					if ( false === $result ) {
						// 发生错误自动回滚事务
						$this->rollback();
						return false;
					}
				}
				// 提交事务
				$this->commit();
			} catch ( \Idesign\Myexception $e ) {
				$this->rollback();
				return false;
			}
			return true;
		}
	}
