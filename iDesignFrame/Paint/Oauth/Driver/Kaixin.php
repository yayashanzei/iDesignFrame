<?php
	/**
	 * Created by PhpStorm.
	 * User: icebr:ice_br2046@163.com
	 * Date: 2016/1/27
	 * Time: 23:31
	 * copy from thinkphp
	 */

	namespace Paint\Oauth\Driver;

	use Paint\Oauth\Driver;

	class Kaixin extends Driver {
		/**
		 * 获取requestCode的api接口
		 * @var string
		 */
		protected $getRequestCodeURL = 'http://api.kaixin001.com/oauth2/authorize';

		/**
		 * 获取access_token的api接口
		 * @var string
		 */
		protected $getAccessTokenURL = 'https://api.kaixin001.com/oauth2/access_token';

		/**
		 * API根路径
		 * @var string
		 */
		protected $apiBase = 'https://api.kaixin001.com/';

		/**
		 * 组装接口调用参数 并调用接口
		 * @param  string $api    开心网API
		 * @param  string $param  调用API的额外参数
		 * @param  string $method HTTP请求方法 默认为GET
		 * @return json
		 */
		public function call( $api , $param = '' , $method = 'GET' , $multi = false ) {
			/* 开心网调用公共参数 */
			$params = array(
				'access_token' => $this->token['access_token'] ,
			);

			$data = $this->http( $this->url( $api , '.json' ) , $this->param( $params , $param ) , $method );
			return json_decode( $data , true );
		}

		/**
		 * 解析access_token方法请求后的返回值
		 * @param string $result 获取access_token的方法的返回值
		 */
		protected function parseToken( $result ) {
			$data = json_decode( $result , true );
			if ( $data['access_token'] && $data['expires_in'] && $data['refresh_token'] ) {
				$data['openid'] = $this->getOpenId();
				return $data;
			} else {
				throw new \Idesign\MyException( "获取开心网ACCESS_TOKEN出错：{$data['error']}" );
			}

		}

		/**
		 * 获取当前授权应用的openid
		 * @return string
		 */
		public function getOpenId() {
			if ( !empty( $this->token['openid'] ) ) {
				return $this->token['openid'];
			}

			$data = $this->call( 'users/me' );
			return !empty( $data['uid'] ) ? $data['uid'] : null;
		}

		/**
		 * 获取当前登录的用户信息
		 * @return array
		 */
		public function getOauthInfo() {
			$data = $this->call( 'users/me' );

			if ( !empty( $data['uid'] ) ) {
				$userInfo['type']   = 'KAIXIN';
				$userInfo['name']   = $data['uid'];
				$userInfo['nick']   = $data['name'];
				$userInfo['avatar'] = $data['logo50'];
				return $userInfo;
			} else {
				throw new \Idesign\MyException( "获取开心网用户信息失败：{$data['error']}" );
			}
		}

	}
