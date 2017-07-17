<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/7
	 * Time: 17:17
	 */
	namespace Idesign;
	class Route extends Request {

		protected static $_pathInfoVar = array();

		protected static function parseRule() {

			$_rule = self::checkRule();

			if ( isset( $_rule[0] ) ) {
				$_ruleKey = $_rule['key'];
				$_rule    = explode( '/' , str_replace( '\\' , '/' , $_rule[0] ) );
			}

			if ( isset( $_rule[2] ) ) {
				parent::$_moduleName = ucwords( strtolower( array_shift( $_rule ) ) );

				if ( isset( parent::$_config['module_name'][ parent::$_moduleName ] ) ) {
					parent::$_config = multiMerge( parent::$_config , parent::$_allConf[ strtolower( parent::$_groupName . '_' . parent::$_moduleName ) ] );
				}
			}

			if ( isset( $_rule[0] ) ) {
				parent::$_ctrName = ucwords( strtolower( $_rule[0] ) );
			}

			if ( isset( $_rule[1] ) ) {
				parent::$_actName = ucwords( strtolower( $_rule[1] ) );
			}

		}

		protected static function checkRule() {

			if ( empty( parent::$_config['url_route_rules'] ) ) {
				return;
			}

			$_rs = array();

			$_keyArray = array( strtolower( parent::$_groupName ) => 0 , strtolower( parent::$_moduleName ) => 1 );

			if ( !empty( parent::$_requestUri ) ) {
				foreach ( parent::$_requestUri as $val ) {
					if ( !isset( $_keyArray[ strtolower( $val ) ] ) ) {
						self::$_pathInfoVar[] = $val;
					}
				}
			}

			self::$_pathInfoVar = parent::addS( self::$_pathInfoVar );

			if ( !empty( self::$_pathInfoVar ) ) {
				foreach ( parent::$_config['url_route_rules'] as $key => $val ) {

					if ( empty( $val ) || !is_string( $val ) ) {
						continue;
					}

					$key = str_replace( '\\' , '/' , $key );


					if ( 0 !== strpos( $key , '/' ) ) {
						$rule = explode( '/' , $key );
					}

					if ( count( $rule ) > count( self::$_pathInfoVar ) ) {
						continue;
					}
					if ( isset( $rule[0] ) ) {

						foreach ( $rule as $_key => $_rule ) {

							if ( '$' == substr( $_rule , -1 , 1 ) ) {

								if ( 0 === strpos( $_rule , ':' ) ) {
									$_rule = trim( $_rule , '$,:' );
									if ( isset( self::$_pathInfoVar[ $_key + 1 ] ) ) {
										$_rs = array();
										break;
									} else {
										$_rs[]          = $val;
										$_rs['key']     = $key;
										$_GET[ $_rule ] = self::$_pathInfoVar[ $_key ];
										return $_rs;
									}
								} else {
									$_rule = rtrim( $_rule , '$' );

									if ( isset( self::$_pathInfoVar[ $_key + 1 ] ) ) {
										$_rs = array();
										break;
									} else {
										if ( self::$_pathInfoVar[ $_key ] == $_rule ) {
											$_rs[]      = $val;
											$_rs['key'] = $key;
											//$_GET[ $_rule ] = self::$_pathInfoVar[ $_key ];
											return $_rs;
										} else {
											$_rs = array();
											break;
										}
									}

								}

							} else {

								if ( 0 === strpos( $_rule , ':' ) ) {
									$_rule = ltrim( $_rule , ':' );

									if ( !isset( self::$_pathInfoVar[ $_key + 1 ] ) || !isset( $rule[ $_key + 1 ] ) ) {
										$_rs[]          = $val;
										$_rs['key']     = $key;
										$_GET[ $_rule ] = self::$_pathInfoVar[ $_key ];
										return $_rs;
									} else {
										$_rs[ $key ][] = self::$_pathInfoVar[ $_key ];
									}

								} else {

									if ( !isset( self::$_pathInfoVar[ $_key + 1 ] ) || !isset( $rule[ $_key + 1 ] ) ) {

										if ( self::$_pathInfoVar[ $_key ] == $_rule ) {
											$_rs[ $key ]['ca'] = $val;
											//$_GET[ $_rule ]    = self::$_pathInfoVar[ $_key ];
											return $_rs;
										} else {
											$_rs = array();
											break;
										}

									} else {
										if ( self::$_pathInfoVar[ $_key ] == $_rule ) {
											//$_GET[ $_rule ] = self::$_pathInfoVar[ $_key ];
										} else {
											$_rs = array();
											break;
										}
									}
								}
							}
						}
					}
				}
			}

		}


		protected static function parseDomain() {

			$_rule = self::checkDomain();

			if ( isset( $_rule[0] ) ) {
				$_ruleKey = $_rule['key'];
				$_rule    = explode( '/' , str_replace( '\\' , '/' , $_rule[0] ) );
			}

			if ( isset( $_rule[2] ) ) {
				parent::$_moduleName = ucwords( strtolower( array_shift( $_rule ) ) );

				if ( isset( parent::$_config['module_name'][ parent::$_moduleName ] ) ) {
					parent::$_config = multiMerge( parent::$_config , parent::$_allConf[ strtolower( parent::$_groupName . '_' . parent::$_moduleName ) ] );
				}
			}

			if ( isset( $_rule[0] ) ) {
				parent::$_ctrName = ucwords( strtolower( $_rule[0] ) );
			}

			if ( isset( $_rule[1] ) ) {
				parent::$_actName = ucwords( strtolower( $_rule[1] ) );
			}

		}

		protected static function checkDomain() {

			if ( empty( parent::$_config['url_domain_rules'] ) || IS_CLI ) {
				return;
			}

			$_keyArray = array( strtolower( parent::$_groupName ) => 0 , strtolower( parent::$_moduleName ) => 1 );

			$_rs = array();


			foreach ( parent::$_config['url_domain_rules'] as $key => $val ) {

				if ( empty( $val ) || !is_string( $val ) ) {
					continue;
				}

				$key = str_replace( '\\' , '/' , $key );

				if($key == $_SERVER['HTTP_HOST'] ){
					echo $val;exit;
				}

			}


		}

	}