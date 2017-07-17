<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/10/31
	 * Time: 15:26
	 */

	defined( 'GROUP_NAME' ) or define( 'GROUP_NAME' , substr( strrchr( dirname( __FILE__ ) , DIRECTORY_SEPARATOR ) , 1 ) );
	defined( 'GROUP_ROOT' ) or define( 'GROUP_ROOT' , dirname( __FILE__ ) . DIRECTORY_SEPARATOR );

	/*
	 *  ITEM_ROOT 必须是这种格式 web根目录\整个项目的名称\
	 */
	defined( 'ITEM_ROOT' ) or define( 'ITEM_ROOT' , dirname( GROUP_ROOT ).DIRECTORY_SEPARATOR );

	defined( 'iD_VERSION' ) or require ITEM_ROOT . 'iDesignFrame' . DIRECTORY_SEPARATOR . 'start.php';










