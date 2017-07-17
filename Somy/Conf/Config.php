<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/2
	 * Time: 19:27
	 */

	return array(
		'module_name'     => array( 'Home' => 'Home' , 'Manage' => 'Manage' ) ,
		'auto_load'       => array( 'App' => 'App' , 'App_Home' => 'App_Home' , 'App_Manage' => 'App_Manage' , 'Ido' => 'Ido' , 'Model' => 'Model' ) ,

		'default_charset' => 'utf-8' ,
		'static_domain'   => '' ,
		'static_dir'      => 'Static' ,
		'domain'          => 'http://idnote.icebrdemo.com' ,

		'img_dir'         => 'images' ,
		'js_dir'          => 'js' ,
		'css_dir'         => 'css' ,

		'url_router_on'   => true ,

		'url_route_rules' => array(
			'admin/abc$' => 'Manage/Index/login' ,
		) ,
	);