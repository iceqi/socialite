<?php

namespace RingoProject\Socialite;

use RingoProject\Socialite\Two\FacebookProvider;
use RingoProject\Socialite\Two\YahooProvider;
use RingoProject\Socialite\Two\LineProvider;


class SocialiteManager extends \Laravel\Socialite\SocialiteManager
{
	protected function createFacebookDriver()
	{
		$config = $this->app['config']['services.facebook'];

		return $this->buildProvider(FacebookProvider::class, $config);
	}

	protected function createYahooDriver()
	{
		$config = $this->app['config']['services.yahoo'];

		return $this->buildProvider(YahooProvider::class, $config);
	}

	protected function createLineDriver()
	{
		$config = $this->app['config']['services.line'];

		return $this->buildProvider(LineProvider::class, $config);
	}
}
