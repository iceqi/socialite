<?php

namespace RingoProject\Socialite\Two;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Log;

class RakutenProvider extends AbstractProvider implements ProviderInterface
{
	protected $scopeSeparator = ' ';

	protected $scopes = [
		'openid',
	];

	protected function getAuthUrl($state)
	{
		return $this->buildAuthUrlFromBase('https://accounts.id.rakuten.co.jp/auth/oauth/authorize', $state);
	}

	protected function getTokenUrl()
	{
		return 'https://api.accounts.id.rakuten.co.jp/v1/oAuth/tokens';
	}

	protected function getUserByToken($token)
	{
		$response = $this->getHttpClient()->get('https://api.accounts.id.rakuten.co.jp/v1/openid/userinfo', [
			'headers' => [
				'Authorization' => 'Bearer ' . $token,
			],
		]);

		return json_decode($response->getBody(), true);
	}

	protected function mapUserToObject(array $user)
	{
		return (new User())->setRaw($user)->map([
			'id'         => $user['sub'],
		]);
	}

	/**
	 * Basic認証が必要なのでOverWriteする。
	 * @param string $code
	 * @return mixed
	 */
	public function getAccessTokenResponse($code)
	{
		if (defined('GuzzleHttp\\ClientInterface::MAJOR_VERSION')) {
			$postKey = (version_compare(ClientInterface::MAJOR_VERSION, '6') === 1) ? 'form_params' : 'body';
		} else {
			$postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
		}

		$basic_auth_key = base64_encode($this->parameters['client_id'] . ":" . $this->parameters['client_secret']);

		$response = $this->getHttpClient()->post($this->getTokenUrl(), [
			'headers' => [
				'Authorization' => 'Basic ' . $basic_auth_key,
				'Content-Type'  => 'application/x-www-form-urlencoded',
			],
			$postKey  => $this->getTokenFields($code),
		]);

		return json_decode($response->getBody(), true);
	}

	/**
	 * TokenFieldsに過不足があるのでOverWriteする。
	 * :Basic認証のため不要
	 *    - client_id
	 *    - client_secret
	 * :必須項目追加
	 *    + grant_type
	 * @param string $code
	 * @return array
	 */
	protected function getTokenFields($code)
	{
		$res = [
			'grant_type'   => 'authorization_code',
			'client_id' => $this->parameters['client_id'],
			'client_secret' => $this->parameters['client_secret'],
			'code'         => $code,
			'redirect_uri' => $this->parameters['redirect_uri'],
		];
		return $res;
	}
}
