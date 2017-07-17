<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/2
	 * Time: 19:27
	 */

	return array(
		'auto_load'         => array( 'App' => 'App' , 'Ido' => 'Ido' , 'Model' => 'Model' , 'Skin' => 'Skin' ) ,

		'template_type'     => 'Heredoc' ,

		'skin_name'         => 'Default' ,
		'default_charset'   => 'utf-8' ,
		'static_domain'     => '' ,
		'static_dir'        => 'Static' ,
		'domain'            => 'http://idnote.icebrdemo.com' ,

		'img_dir'           => 'images' ,
		'js_dir'            => 'js' ,
		'css_dir'           => 'css' ,

		'url_router_on'     => true ,

		'url_route_rules'   => array(
			'admin$'          => 'Manage/Index/login' ,
			'config/:config$' => 'Index/ceshi' ,
		) ,

		'url_domain_deploy' => true ,

		'url_domain_rules'  => array(
			'a'                => 'ceshi' ,
			'idnote.ceshi.com' => 'ceshi' ,
			'doc.thinkphp.cn'  => 'home/doc' ,
			'202.65.34.5'      => 'admin' ,
			'blog'             => 'home/blog' ,
			'*'                => 'home' ,
			'admin.blog'       => 'admin' ,
			'*.user'           => 'user',
		) ,

	);