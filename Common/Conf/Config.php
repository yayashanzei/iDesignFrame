<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2015/11/2
	 * Time: 22:35
	 */

	return array(
		'frame_name'             => 'iDesignFrame' ,
		'group'                  => array( 'Idnote' => 'Idnote' , 'Somy' => 'Somy' ) ,
		'auto_load'              => array( 'Lib' => 'Lib' , 'Extension' => 'Extension' ) ,

		'debug'                  => true ,

		'template_type'          => 'Heredoc' ,
		'template'               => array( 'LD' => '{' , 'RD' => '}' ) ,

		'tmpl_action_error'      => 'error' , // 默认错误跳转对应的模板文件
		'tmpl_action_success'    => 'sucess' , // 默认成功跳转对应的模板文件


		'app_dir_name'           => 'App' ,
		'default_group'          => 'Somy' ,
		// 默认模块名
		'default_module'         => 'Home' ,
		'default_ctr'            => 'Index' ,
		'default_act'            => 'index' ,

		'skin_dir'               => 'Skin' ,
		'skin_name'              => 'Default' ,
		'layout_name'            => 'Layout' ,
		'skin_ext'               => '.htm' ,
		'data_dir'               => 'Data' ,

		'app_use_namespace'      => true ,    // 应用类库是否使用命名空间
		'conf_parse'             => '' ,


		/* URL设置 */
		'url_case_insensitive'   => true ,   // 默认false 表示URL区分大小写 true则表示不区分大小写
		'url_pathinfo_depr'      => '/' ,    // PATHINFO模式下，各参数之间的分割符号
		'url_pathinfo_fetch'     => 'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL' , // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
		'url_request_uri'        => 'REQUEST_URI' , // 获取当前页面地址的系统变量 默认为REQUEST_URI
		'url_html_suffix'        => 'html' ,  // URL伪静态后缀设置
		'url_deny_suffix'        => 'ico|png|gif|jpg' , // URL禁止访问的后缀设置
		'url_params_bind'        => true , // URL变量绑定到Action方法参数
		'url_params_bind_type'   => 0 , // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
		'url_params_filter'      => false , // URL变量绑定过滤
		'url_params_filter_type' => '' , // URL变量绑定过滤方法 如果为空 调用DEFAULT_FILTER
		'url_map_rules'          => array() , // URL映射定义规则

		'url_router_on'          => false ,   // 是否开启URL路由
		'url_route_rules'        => array() , // 默认路由规则 针对模块
		'url_mode'               => URL_COMMON ,      // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
		// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式

		/* 默认设定 */
		'default_m_layer'        => 'Model' , // 默认的模型层名称
		'default_c_layer'        => 'Controller' , // 默认的控制器层名称
		'default_v_layer'        => 'View' , // 默认的视图层名称
		'default_lang'           => 'zh-cn' , // 默认语言
		'default_theme'          => '' ,    // 默认模板主题名称
		'default_charset'        => 'utf-8' , // 默认输出编码
		'default_timezone'       => 'PRC' ,    // 默认时区
		'default_ajax_return'    => 'JSON' ,  // 默认AJAX 数据返回格式,可选JSON XML ...
		'default_jsonp_handler'  => 'jsonpReturn' , // 默认JSONP格式返回的处理方法
		'default_filter'         => 'htmlspecialchars' , // 默认参数过滤方法 用于I函数...

		/* SESSION设置 */
		'session_auto_start'     => true ,    // 是否自动开启Session
		'session_options'        => array() , // session 配置数组 支持type name id path expire domain 等参数
		'session_type'           => '' , // session hander类型 默认无需设置 除非扩展了session hander驱动
		'session_prefix'         => 'idn_' , // session 前缀
		'session_table'          => 'idn_session' , // session 表名
		//'VAR_SESSION_ID'      =>  'session_id',     //sessionID的提交变量

		/* Cookie设置 */
		'cookie_expire'          => 0 ,       // Cookie有效期
		'cookie_domain'          => '' ,      // Cookie有效域名
		'cookie_path'            => '/' ,     // Cookie路径
		'cookie_prefix'          => '' ,      // Cookie前缀 避免冲突
		'cookie_secure'          => false ,   // Cookie安全传输
		'cookie_httponly'        => '' ,      // Cookie httponly设置

		/* 错误设置 */
		'error_message'          => '页面错误！请稍后再试～' ,//错误显示信息,非调试模式有效
		'error_page'             => '' ,    // 错误定向页面
		'show_error_msg'         => false ,    // 显示错误信息
		'trace_max_record'       => 100 ,    // 每个级别的错误信息 最大记录数

		/* 数据缓存设置 */
		'data_cache_time'        => 0 ,      // 数据缓存有效期 0表示永久缓存
		'data_cache_compress'    => false ,   // 数据缓存是否压缩缓存
		'data_cache_check'       => false ,   // 数据缓存是否校验缓存
		'data_cache_prefix'      => '' ,     // 缓存前缀
		'data_cache_type'        => 'File' ,  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
		'data_cache_path'        => 'FileCache' ,// 缓存路径设置 (仅对File方式缓存有效)
		'data_cache_key'         => '' ,    // 缓存文件KEY (仅对File方式缓存有效)
		'data_cache_subdir'      => false ,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
		'data_path_level'        => 1 ,        // 子目录缓存级别

		/* 日志设置 */
		'log_record'             => false ,   // 默认不记录日志
		'log_type'               => 'File' , // 日志记录类型 默认为文件方式
		'log_level'              => 'EMERG,ALERT,CRIT,ERR' ,// 允许记录的日志级别
		'log_file_size'          => 2097152 ,    // 日志文件大小限制
		'log_exception_record'   => false ,    // 是否记录异常信息日志

		'file_upload_type'       => 'Local' ,    // 文件上传方式
		'data_crypt_type'        => 'ID' ,    // 数据加密方式


		// 应用模式状态
		'app_status'             => '' ,
		// 扩展配置文件
		'extra_config_list'      => array( 'database' , 'route' ) ,
		// 默认输出类型
		'default_return_type'    => 'html' ,
		// 默认语言
		'default_lang'           => 'zh-cn' ,
		// response是否返回方式
		'response_return'        => false ,
		// 默认AJAX 数据返回格式,可选JSON XML ...
		'default_ajax_return'    => 'JSON' ,
		// 默认JSONP格式返回的处理方法
		'default_jsonp_handler'  => 'jsonpReturn' ,
		// 默认JSONP处理方法
		'var_jsonp_handler'      => 'callback' ,
		// 默认时区
		'default_timezone'       => 'PRC' ,
		// 是否开启多语言
		'lang_switch_on'         => false ,
		// 支持的多语言列表
		'lang_list'              => array( 'zh-cn' ) ,
		// 语言变量
		'lang_detect_var'        => 'lang' ,
		// 语言cookie变量
		'lang_cookie_var'        => 'think_lang' ,

		// +----------------------------------------------------------------------
		// | 模块设置
		// +----------------------------------------------------------------------


		// 禁止访问模块
		'deny_module_list'       => array( '' , 'runtime' ) ,
		// 默认控制器名
		'default_controller'     => 'index' ,
		// 默认操作名
		'default_action'         => 'index' ,
		// 默认的空控制器名
		'empty_controller'       => 'error' ,
		// 操作方法后缀
		'action_suffix'          => '' ,
		// 操作绑定到类
		'action_bind_class'      => false ,

		// +----------------------------------------------------------------------
		// | URL设置
		// +----------------------------------------------------------------------

		// PATHINFO变量名 用于兼容模式
		'var_pathinfo'           => 's' ,
		// 兼容PATH_INFO获取
		'pathinfo_fetch'         => array( 'ORIG_PATH_INFO' , 'REDIRECT_PATH_INFO' , 'REDIRECT_URL' ) ,
		// pathinfo分隔符
		'pathinfo_depr'          => '/' ,
		// 获取当前页面地址的系统变量 默认为REQUEST_URI
		'url_request_uri'        => 'REQUEST_URI' ,
		// 基础URL路径
		'base_url'               => $_SERVER["SCRIPT_NAME"] ,
		// URL伪静态后缀
		'url_html_suffix'        => '.html' ,
		// URL普通方式参数 用于自动生成
		'url_common_param'       => false ,
		// url变量绑定
		'url_params_bind'        => true ,
		// URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
		'url_parmas_bind_type'   => 0 ,
		//url地址的后缀
		'url_deny_suffix'        => '' ,
		// 是否开启路由
		'url_route_on'           => true ,
		// 是否强制使用路由
		'url_route_must'         => false ,
		// URL模块映射
		'url_module_map'         => array() ,
		// 域名部署
		'url_domain_deploy'      => false ,
		// 域名部署规则
		'url_domain_rules'       => array() ,

		// +----------------------------------------------------------------------
		// | 视图及模板设置
		// +----------------------------------------------------------------------

		// 默认跳转页面对应的模板文件
		'dispatch_jump_tmpl'     => ID_PATH . 'tpl/dispatch_jump.tpl' ,
		// 默认的模板引擎
		'template_engine'        => 'think' ,

		// +----------------------------------------------------------------------
		// | 异常及错误设置
		// +----------------------------------------------------------------------

		// 异常页面的模板文件
		'exception_tmpl'         => ID_PATH . 'tpl/think_exception.tpl' ,
		// 错误显示信息,非调试模式有效
		'error_message'          => '页面错误！请稍后再试～' ,
		// 错误定向页面
		'error_page'             => '' ,
		// 显示错误信息
		'show_error_msg'         => false ,

		// +----------------------------------------------------------------------
		// | 日志设置
		// +----------------------------------------------------------------------

		'log'                    => array(
			'type' => 'File' ,
			'path' => 'Log' ,
		) ,

		// +----------------------------------------------------------------------
		// | 缓存设置
		// +----------------------------------------------------------------------

		'cache'                  => array(
			'type'   => 'File' ,
			'path'   => 'Cache' ,
			'prefix' => '' ,
			'expire' => 0 ,
		) ,

		// +----------------------------------------------------------------------
		// | 会话设置
		// +----------------------------------------------------------------------

		// 是否使用session
		'use_session'            => true ,
		'session'                => array(
			'id'         => '' ,
			'prefix'     => 'idesign' ,
			'type'       => '' ,
			'auto_start' => true ,
		) ,

		// +----------------------------------------------------------------------
		// | 数据库设置
		// +----------------------------------------------------------------------

		// 是否启用多状态数据库配置 如果启用的话 需要跟随app_status配置不同的数据库信息
		'use_db_switch'          => false ,
		'db_fields_strict'       => true ,
		'database'               => array(
			// 数据库类型
			'type'        => 'mysql' ,
			// 数据库连接DSN配置
			'dsn'         => '' ,
			// 服务器地址
			'hostname'    => 'localhost' ,
			// 数据库名
			'database'    => 'qibosyb' ,
			// 数据库用户名
			'username'    => 'root' ,
			// 数据库密码
			'password'    => 'root' ,
			// 数据库连接端口
			'hostport'    => '3306' ,
			// 数据库连接参数
			'params'      => array() ,
			// 数据库编码默认采用utf8
			'charset'     => 'utf8' ,
			// 数据库表前缀
			'prefix'      => '' ,
			// 数据库调试模式
			'debug'       => true ,
			// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
			'deploy'      => 0 ,
			// 数据库读写是否分离 主从式有效
			'rw_separate' => false ,
			// 读写分离后 主服务器数量
			'master_num'  => 1 ,
			// 指定从服务器序号
			'slave_no'    => '' ,
		) ,


	);