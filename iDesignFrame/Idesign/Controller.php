<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/9
	 * Time: 6:10
	 */
	namespace Idesign;
	abstract class Controller {

		protected static $_config = array();


		protected static function display( $templateFile = null ) {
			Template::parse( self::getTemplate( $templateFile ) );
		}

		protected static function assign( $key , $val = null ) {
			if ( $val ) {
				Template::assign( $key , $val );
				return;
			}
			Template::assign( $key );
		}

		protected static function theme( $val = null ) {
			if ( $val ) {
				App::$_var['CONFIG']['skin_name'] = $val;
			}
			return isset( App::$_var['SKIN_NAME'] ) ? App::$_var['SKIN_NAME'] : App::$_var['CONFIG']['skin_name'];
		}

		protected static function fetch( $templateFile = null ) {
			return Template::fetch( self::getTemplate( $templateFile ) );
		}

		protected static function layout( $templateFile = null ) {
			Template::parse( self::getLayout( $templateFile ) );
			unset( App::$_var['LAYOUT_NAME'] );
		}

		/**
		 *  创建静态页面
		 * @access   protected
		 * @htmlfile 生成的静态文件名称
		 * @htmlpath 生成的静态文件路径
		 * @param string $templateFile 指定要调用的模板文件
		 *                             默认为空 由系统自动定位模板文件
		 * @return string
		 */
		protected static function buildHtml( $htmlFile = '' , $htmlPath = '' , $templateFile = '' ) {
			$content  = self::fetch( $templateFile );
			$htmlPath = !empty( $htmlPath ) ? $htmlPath : HTML_PATH;
			$htmlFile = $htmlPath . $htmlFile . App::$_var['CONFIG']['html_file_suffix'];
			Storage::put( $htmlFile , $content , 'html' );
			return $content;
		}

		/**
		 * 操作错误跳转的快捷方法
		 * @access protected
		 * @param string $message 错误信息
		 * @param string $jumpUrl 页面跳转地址
		 * @param mixed  $ajax    是否为Ajax方式 当数字时指定跳转时间
		 * @return void
		 */
		protected static function error( $message = '' , $jumpUrl = '' , $template = null , $ajax = false ) {
			self::dispatchJump( $message , 0 , $jumpUrl , $template , $ajax );
		}

		/**
		 * 操作成功跳转的快捷方法
		 * @access protected
		 * @param string $message 提示信息
		 * @param string $jumpUrl 页面跳转地址
		 * @param mixed  $ajax    是否为Ajax方式 当数字时指定跳转时间
		 * @return void
		 */
		protected static function success( $message = '' , $jumpUrl = '' , $template = null , $ajax = false ) {
			self::dispatchJump( $message , 1 , $jumpUrl , $template , $ajax );
		}

		/**
		 * Ajax方式返回数据到客户端
		 * @access protected
		 * @param mixed  $data        要返回的数据
		 * @param String $type        AJAX返回数据格式
		 * @param int    $json_option 传递给json_encode的option参数
		 * @return void
		 */
		protected static function ajaxReturn( $data , $type = '' , $json_option = 0 ) {
			if ( empty( $type ) ) {
				$type = App::$_var['CONFIG']['default_ajax_return'];
			}
			switch ( strtoupper( $type ) ) {
				case 'JSON' :
					// 返回JSON数据格式到客户端 包含状态信息
					header( 'Content-Type:application/json; charset=utf-8' );
					exit( json_encode( $data , $json_option ) );
				case 'XML'  :
					// 返回xml格式数据
					header( 'Content-Type:text/xml; charset=utf-8' );
					exit( xml_encode( $data ) );
				case 'JSONP':
					// 返回JSON数据格式到客户端 包含状态信息
					header( 'Content-Type:application/json; charset=utf-8' );
					$handler = isset( $_GET[ App::$_var['CONFIG']['var_jsonp_handler'] ] ) ? $_GET[ App::$_var['CONFIG']['var_jsonp_handler'] ] : App::$_var['CONFIG']['default_jsonp_handler'];
					exit( $handler . '(' . json_encode( $data , $json_option ) . ');' );
				case 'EVAL' :
					// 返回可执行的js脚本
					header( 'Content-Type:text/html; charset=utf-8' );
					exit( $data );
				default     :
					// 用于扩展其他返回格式数据
					Hook::listen( 'ajax_return' , $data );
			}
		}

		/**
		 * Action跳转(URL重定向） 支持指定模块和延时跳转
		 * @access protected
		 * @param string  $url    跳转的URL表达式
		 * @param array   $params 其它URL参数
		 * @param integer $delay  延时跳转的时间 单位为秒
		 * @param string  $msg    跳转提示信息
		 * @return void
		 */
		protected static function redirect( $url , $params = array() , $delay = 0 , $msg = '' ) {
			$url = U( $url , $params );
			redirect( $url , $delay , $msg );
		}

		/**
		 * 默认跳转操作 支持错误导向和正确跳转
		 * 调用模板显示 默认为public目录下面的success页面
		 * 提示页面为可配置 支持模板标签
		 * @param string  $message 提示信息
		 * @param Boolean $status  状态
		 * @param string  $jumpUrl 页面跳转地址
		 * @param mixed   $ajax    是否为Ajax方式 当数字时指定跳转时间
		 * @access private
		 * @return void
		 */
		private static function dispatchJump( $message , $status = 1 , $jumpUrl = '' , $template = null , $ajax = false ) {
			if ( true === $ajax || IS_AJAX ) {// AJAX提交
				$data           = is_array( $ajax ) ? $ajax : array();
				$data['info']   = $message;
				$data['status'] = $status;
				$data['url']    = $jumpUrl;
				self::ajaxReturn( $data );
			}
			if ( is_int( $ajax ) ) {
				self::assign( 'waitSecond' , $ajax );
			}
			if ( !empty( $jumpUrl ) ) {
				self::assign( 'jumpUrl' , $jumpUrl );
			}
			// 提示标题
			self::assign( 'msgTitle' , $status ? L( '_OPERATION_SUCCESS_' ) : L( '_OPERATION_FAIL_' ) );
			//如果设置了关闭窗口，则提示完毕后自动关闭窗口
			if ( self::get( 'closeWin' ) ) {
				self::assign( 'jumpUrl' , 'javascript:window.close();' );
			}
			self::assign( 'status' , $status );   // 状态
			//保证输出不受静态缓存影响
			C( 'html_cache_on' , false );
			if ( $status ) { //发送成功信息
				self::assign( 'message' , $message );// 提示信息
				// 成功操作后默认停留1秒
				if ( !isset( self::$waitSecond ) ) {
					self::assign( 'waitSecond' , '1' );
				}
				// 默认操作成功自动返回操作前页面
				if ( !self::get( 'jumpUrl' ) ) {
					self::assign( "jumpUrl" , $_SERVER["HTTP_REFERER"] );
				}
				if ( !$template ) {
					self::display( 'Default' . DS . C( 'default_module' ) . DS . C( 'tmpl_action_success' ) );
				} else {
					self::display( $template );
				}

			} else {
				self::assign( 'error' , $message );// 提示信息
				//发生错误时候默认停留3秒
				if ( !isset( self::$waitSecond ) ) {
					self::assign( 'waitSecond' , '3' );
				}
				// 默认发生错误的话自动返回上页
				if ( !self::get( 'jumpUrl' ) ) {
					self::assign( 'jumpUrl' , "javascript:history.back(-1);" );
				}
				if ( !$template ) {
					self::display( 'Default' . DS . C( 'default_module' ) . DS . C( 'tmpl_action_error' ) );
				} else {
					self::display( $template );
				}

				// 中止执行  避免出错后继续执行
				exit;
			}
		}

		/**
		 * 取得模板显示变量的值
		 * @access protected
		 * @param string $name 模板显示变量
		 * @return mixed
		 */
		public static function get( $key = null ) {
			if ( $key ) {
				return Template::get( $key );
			}
		}

		protected static function getTemplate( $templateFile = null ) {
			if ( !$templateFile ) {
				App::$_var['SKIN_NAME'] = empty( App::$_var['CONFIG']['skin_name'] ) ? 'Default' : App::$_var['CONFIG']['skin_name'];
				if ( 2 == GROUP_MODE ) {
					App::$_var['GROUP_NAME'] = '';
				}
				$templateFile = ITEM_ROOT . App::$_var['GROUP_NAME'] . DS . SKIN_DIR . DS . App::$_var['SKIN_NAME'] . DS . App::$_var['MODULE_NAME'] . DS . App::$_var['CTR_NAME'] . DS . App::$_var['ACT_NAME'] . SKIN_EXT;
			} else {
				$templateFile = str_replace( array( '/' , '\\' ) , DS , $templateFile );
				if ( strpos( $templateFile , DS ) ) {
					App::$_var['SKIN_NAME'] = strstr( $templateFile , DS , true );
					$templateFile           = ITEM_ROOT . App::$_var['GROUP_NAME'] . DS . SKIN_DIR . DS . $templateFile . SKIN_EXT;
				} else {
					if ( 2 == GROUP_MODE ) {
						App::$_var['GROUP_NAME'] = '';
					}
					App::$_var['SKIN_NAME'] = empty( App::$_var['CONFIG']['skin_name'] ) ? 'Default' : App::$_var['CONFIG']['skin_name'];
					$templateFile           = ITEM_ROOT . App::$_var['GROUP_NAME'] . DS . SKIN_DIR . DS . App::$_var['SKIN_NAME'] . DS . App::$_var['MODULE_NAME'] . DS . App::$_var['CTR_NAME'] . DS . $templateFile . SKIN_EXT;
				}
			}
			if ( !is_file( $templateFile ) ) {
				throw new MyException( $templateFile . '不存在！' );
			}
			return $templateFile;
		}

		protected static function getLayout( $templateFile = null ) {
			App::$_var['SKIN_NAME']   = empty( App::$_var['CONFIG']['skin_name'] ) ? 'Default' : App::$_var['CONFIG']['skin_name'];
			App::$_var['LAYOUT_NAME'] = empty( App::$_var['CONFIG']['layout_name'] ) ? 'Layout' : App::$_var['CONFIG']['layout_name'];

			if ( !$templateFile ) {
				if ( 2 == GROUP_MODE ) {
					App::$_var['GROUP_NAME'] = '';
				}
				$templateFile = ITEM_ROOT . App::$_var['GROUP_NAME'] . DS . SKIN_DIR . DS . App::$_var['SKIN_NAME'] . DS . App::$_var['LAYOUT_NAME'] . DS . App::$_var['MODULE_NAME'] . DS . App::$_var['CTR_NAME'] . DS . App::$_var['ACT_NAME'] . SKIN_EXT;

			} else {
				$templateFile = str_replace( array( '/' , '\\' ) , DS , $templateFile );
				if ( strpos( $templateFile , DS ) ) {
					$_templatefile            = explode( DS , $templateFile );
					App::$_var['SKIN_NAME']   = $_templatefile[0];
					App::$_var['LAYOUT_NAME'] = $_templatefile[1];;
					$templateFile = ITEM_ROOT . App::$_var['GROUP_NAME'] . DS . SKIN_DIR . DS . $templateFile . SKIN_EXT;
				} else {
					if ( 2 == GROUP_MODE ) {
						App::$_var['GROUP_NAME'] = '';
					}
					$templateFile = ITEM_ROOT . App::$_var['GROUP_NAME'] . DS . SKIN_DIR . DS . App::$_var['SKIN_NAME'] . DS . App::$_var['LAYOUT_NAME'] . DS . App::$_var['MODULE_NAME'] . DS . App::$_var['CTR_NAME'] . DS . $templateFile . SKIN_EXT;
				}
			}
			if ( !is_file( $templateFile ) ) {
				throw new MyException( $templateFile . '不存在！' );
			}
			return $templateFile;
		}


	}