<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/5
	 * Time: 0:19
	 */

	namespace Idnote\App\Home;

		//use Idesign\App;
	//use Idesign\Cache;
	use Idesign\Controller;

	//use Idesign\Model;
	//use Idesign\iD;
	use Idesign\App;

	class Index extends Controller {
		public function index() {

			//var_dump(App::$_var);
			//self::assign( 'CONFIG' ,C() );
			//self::assign( 'title' , 'ceshi' );
			//self::assign( 'name' , 'xiaojiong' );
			//echo '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP5</b>！</p></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
			dump(App::$_var);
			//$c = self::fetch("Default/Front/Index");
			//echo $c;
			//self::display();
			//self::display('Default/Home/Index/ceshi');
			//self::success('ceshi',C('domain').'/index.php?g=cms');
			//$c = 'jlajfkasjdfkljaskldfjkasdjfkajdskf';
			//echo $c=\Idesign\Crypt::encrypt($c,'999');
			//echo \Idesign\Crypt::decrypt($c,'999');
			//print_r( App::$_var );

			//$member = M( 'document' );
			//var_dump($member->field('id')->select());


			//$_path = CACHE_DIR . 'Session' ;
			//session(array('expire'=>3600));
			//session('ceshi','nihao');
			//cookie('Ceshi','EXP');
			//print_r(App::$_var);
			//$Verify =     new \Idesign\Verify();
			//$Verify->entry();
			//$image = new \Idesign\Image();
			//$image->open('./1.jpg');
			//$width = $image->width();// 返回图片的宽度
			//echo $width;
			//$height = $image->height();// 返回图片的高度
			//$type = $image->type();// 返回图片的类型
			//$mime = $image->mime(); // 返回图片的mime类型
			//$size = $image->size(); // 返回图片的尺寸数组 0 图片宽度 1 图片高度

		}

		public function ceshi() {

			$data = json_encode( array( 'name' => 'nihao' , 'title' => 'ceshi' ) );

			if ( IS_AJAX ) {
				self::ajaxReturn( $data );
			}

			//echo 88;
			//print_r( App::$_var );
			//echo 333;
			//session('[start]');
			//var_dump(session());
			//print_r(App::$_var);
			//print_r(cookie('Ceshi'));
			//print_r($GLOBALS);

		}

		public function config() {
			echo __CLASS__ . '--' . __FUNCTION__;
		}

		public function upload() {
			$upload           = new \Idesign\Upload();// 实例化上传类
			$upload->maxSize  = 3145728;// 设置附件上传大小
			$upload->exts     = array( 'jpg' , 'gif' , 'png' , 'jpeg' );// 设置附件上传类型
			$upload->savePath = './Uploads/'; // 设置附件上传目录
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
