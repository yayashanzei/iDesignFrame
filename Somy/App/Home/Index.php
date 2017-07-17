<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/5
	 * Time: 0:19
	 */

	namespace Somy\App\Home;

	use Idesign\App;
	use Idesign\Cache;
	use Idesign\Controller;
	use Idesign\Debug;
	use Idesign\Log;
	use Idesign\Model;
	use Paint\Verify;

	class Index extends Controller {
		public function index() {
echo __CLASS__.'=='.__FUNCTION__;
			//Debug::dump( Debug::getFile(true));
			//trigger_error('ceshi',1);

			//var_dump(App::$_var);
			//self::assign( 'CONFIG' ,C() );

			//echo '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP5</b>！</p></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
//echo   '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';

			//self::assign( 'title' , 'ceshi' );
			//self::assign( 'name' , 'xiaojiong' );
			//echo self::get('title');
			//self::display();

			//cookie('ceshi','nihao');

			//			S( array() );
			//			S( 'ceshi' , 'nihao' );
			//			dump( S( 'ceshi' ) );

			Log::init( array( 'type' => 'Trace' ) );
			//echo __CLASS__ . '--' . __FUNCTION__;
			//$a = new Verify();
			//$member = D( 'qb_label' );
			//dump($member->where( 'lid < 20' )->select());
			//session_start();
			//echo session('ceshi');
			//trace( 'ceshi' , 1 );
			Log::save();

			//$c = self::fetch("Default/Front/Index");
			//echo $c;

			//self::display('Default/Home/Index/ceshi');

			//self::success('ceshi','/index.php');

			//$c = 'jlajfkasjdfkljaskldfjkasdjfkajdskf';
			//echo $c = \Paint\Transform::encode($c,'Base64');
			//echo \Paint\Transform::decode($c,'Base64');

			//$Verify =     new \Paint\Verify();
			//$Verify->entry();

			//\Paint\Image::init();
			//\Paint\Image::open('./1.jpg');
			//echo \Paint\Image::width();

		}

		public function ceshi() {


			echo __CLASS__ . '--' . __FUNCTION__;

			$data = json_encode( array( 'name' => 'nihao' , 'title' => 'ceshi' ) );

			if ( IS_AJAX ) {
				self::ajaxReturn( $data );
			}

			//session_start();
			//session('ceshi','nihao');
			//session(null,'');
			//var_dump(session(''));
			//var_dump(session('ceshi'));

			//dump(cookie('ceshi'));

		}

		public function config() {
			echo __CLASS__ . '--' . __FUNCTION__;
		}

		public function upload() {
			$upload           = new \Paint\Upload();// 实例化上传类
			$upload->maxSize  = 3145728;// 设置附件上传大小
			$upload->exts     = array( 'jpg' , 'gif' , 'png' , 'jpeg' );// 设置附件上传类型
			$upload->savePath = './Upload/'; // 设置附件上传目录
			// 上传文件
			$info = $upload->upload();
			if ( !$info ) {
				// 上传错误提示错误信息
				var_dump( $upload->getError() );
			} else {
				// 上传成功
				echo '上传成功！';
			}
		}


	}
