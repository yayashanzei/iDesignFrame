<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/12/11
	 * Time: 13:29
	 */

	namespace Idnote\App\Manage;

	use Idesign\App;
	use Idesign\Controller;
	use Idesign\Vendor\Jformer;
	use Idesign\iD;

	class Index extends Controller {

		public function index() {

			/*if ( !cookie( 'isLogin' ) ) {
				header( 'Location: /manage/login' );
			}*/
			$globalLayout = App::$_var['CONFIG']['skin_name'] . DS . App::$_var['CONFIG']['layout_name'] . DS . App::$_var['MODULE_NAME'] . DS;
			self::layout( $globalLayout . 'global' );

			self::assign( array(
				              'title' => '后台管理' , 'defaultCss' => 'index.css' ,
			              ) );


			self::display();
		}

		public function login() {

			/*$rules = array(
				array( 'username' , '' , '帐号名称已经存在！' , 0 , 'unique' , 3 ) , // 在新增的时候验证name字段是否唯一
			);
			$_auto = array(
				array( 'username' , 'admin' ) , // 对update_time字段在更新的时候写入当前时间戳
			);

			$user = M( 'members' );

			$data["username"] = 'admin';

			if ( !$user->validate( $rules )->create($data,1) ) {
				exit( $user->getError() );
			} else {
				$user->add();
			}*/

			$globalLayout = App::$_var['CONFIG']['skin_name'] . DS . App::$_var['CONFIG']['layout_name'] . DS . App::$_var['MODULE_NAME'] . DS;
			self::layout( $globalLayout . 'global' );
			self::assign( array(
				              'title'      => '后台登录' ,
				              'defaultCss' => 'login.css' ,
			              ) );

			self::display();
			/*$formLogin = self::setLoginForm();
			$formLogin->processRequest( true );*/

		}

		public function execLogin( $formValues ) {
			$formValues = $formValues->loginFormPage->loginFormSection;
			if ( $formValues->username == 'admin' && $formValues->password == 'admin' ) {
				$response = array( 'successPageHtml' => '<p>登录成功</p>' );
			} else {
				$response = array( 'failureNoticeHtml' => '用户名或者密码不正确！' , 'failureJs' => "$('#password').val('').focus();" );
			}
			return $response;
		}

		public function setLoginForm() {

			$formLogin = new Jformer\JFormer( 'loginForm' , array(
				'title'                      => '管理登录' ,
				'submitButtonText'           => '登录' ,
				'submitProcessingButtonText' => '登录..' ,
				'action'                     => 'index.php?m=manage&a=login' ,
				'onSubmitFunctionServerSide' => array( __CLASS__ , 'execLogin' ) ,
			) );

			$formPage    = new Jformer\JFormPage( $formLogin->id . 'Page' , array() );
			$formSection = new Jformer\JFormSection( $formLogin->id . 'Section' , array() );
			$formSection->addJFormComponentArray( array(
				                                      new Jformer\JFormComponentSingleLineText( 'username' , '用户名' , array(
					                                      'validationOptions' => array( 'required' , 'username' ) ,
					                                      'tip'               => '<p>the demo login</p>' ,
				                                      ) ) ,
				                                      new Jformer\JFormComponentSingleLineText( 'password' , '密　码' , array(
					                                      'validationOptions' => array( 'required' , 'password' ) ,
					                                      'tip'               => '<p>the tip of password</p>' ,
				                                      ) ) ,

			                                      ) );
			$formPage->addJFormSection( $formSection );
			$formLogin->addJFormPage( $formPage );

			return $formLogin;
		}


	}
