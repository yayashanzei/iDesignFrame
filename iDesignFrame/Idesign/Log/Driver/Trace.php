<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/20
	 * Time: 15:10
	 * copy from thinkphp
	 */

	namespace Idesign\Log\Driver;

	/**
	 * 页面Trace调试 需要设置 'response_exit' => false 才能生效
	 */
	class Trace {
		protected $tabs   = array( 'base' => '基本' , 'file' => '文件' , 'warn|error' => '错误' , 'sql' => 'SQL' , 'info|debug|log' => '调试' );
		protected $config = array(
			'trace_file' => '' ,
		);

		// 实例化并传入参数
		public function __construct( $config = array() ) {
			$this->config['trace_file'] = ID_PATH . 'PageTrace.php';
			$this->config               = array_merge( $this->config , $config );
		}

		/**
		 * 日志写入接口
		 * @access public
		 * @param array $log 日志信息
		 * @return void
		 */
		public function save( $log = array() ) {
			if ( IS_AJAX || IS_CLI || IS_API ) {
				// ajax cli api方式下不输出
				return;
			}
			// 获取基本信息
			$current_uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$runtime     = number_format( microtime( true ) - START_TIME , 6 );
			$reqs        = number_format( 1/$runtime , 2 );

			// 页面Trace信息
			$base = array(
				'请求信息' => date( 'Y-m-d H:i:s' , $_SERVER['REQUEST_TIME'] ) . ' ' . $_SERVER['SERVER_PROTOCOL'] . ' ' . $_SERVER['REQUEST_METHOD'] . ' : ' . $current_uri ,
				'运行时间' => "{$runtime}s [ 吞吐率：{$reqs}req/s ]" ,
				'内存消耗' => number_format( ( memory_get_usage() - START_MEM )/1024 , 2 ) . 'kb' ,
				'查询信息' => \Idesign\Db::$queryTimes . ' queries ' . \Idesign\Db::$executeTimes . ' writes ' ,
				'缓存信息' => \Idesign\Cache::$readTimes . ' reads,' . \Idesign\Cache::$writeTimes . ' writes' ,
				'文件加载' => count( get_included_files() ) ,
				'配置加载' => count( \Idesign\Config::get() ) ,
				'会话信息' => 'SESSION_ID=' . session_id() ,
			);

			$info = \Idesign\Debug::getFile( true );

			// 获取调试日志
			$debug = array();
			foreach ( $log as $line ) {
				$debug[ $line['type'] ][] = $line['msg'];
			}

			// 页面Trace信息
			$trace = array();
			foreach ( $this->tabs as $name => $title ) {
				$name = strtolower( $name );
				switch ( $name ) {
					case 'base':    // 基本信息
						$trace[ $title ] = $base;
					break;
					case 'file':    // 文件信息
						$trace[ $title ] = $info;
					break;
					default:    // 调试信息
						if ( strpos( $name , '|' ) ) {
							// 多组信息
							$names  = explode( '|' , $name );
							$result = array();
							foreach ( $names as $name ) {
								$result = array_merge( $result , isset( $debug[ $name ] ) ? $debug[ $name ] : array() );
							}
							$trace[ $title ] = $result;
						} else {
							$trace[ $title ] = isset( $debug[ $name ] ) ? $debug[ $name ] : '';
						}
				}
			}
			// 调用Trace页面模板
			ob_start();
			include $this->config['trace_file'];
			echo ob_get_clean();
		}

	}
