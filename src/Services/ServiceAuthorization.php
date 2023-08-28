<?php 

namespace BBboletoCobranca\Services;

use BBboletoCobranca\Entities\OAuthEntity;
use BBboletoCobranca\Clients\BancoDoBrasilAuthorizationClient;
use Illuminate\Support\Arr;

/**
*
*
*
*/
class ServiceAuthorization
{
	public function authorize(array $config)
	{
		$authorize = (new BancoDoBrasilAuthorizationClient($config))
			->__callBancoDoBrasil();
		
		$oAuthEntity = new OAuthEntity;
		$oAuthEntity->setAccessToken($authorize->access_token)
		    ->setEnvironment(Arr::get($config, 'production', false))
		    ->setGwDevAppKey(Arr::get($config, 'gw_dev_app_key', false));

		return $oAuthEntity;
		
	}
}
