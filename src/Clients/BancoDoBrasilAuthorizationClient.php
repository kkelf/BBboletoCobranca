<?php

namespace BBboletoCobranca\Clients;


use BBboletoCobranca\Exceptions\OAuthException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

/**
 * Class BancoDoBrasilAuthorizationClient
 * @package BBboletoCobranca\Clients
 */
class BancoDoBrasilAuthorizationClient
{

	const OAUTH_HM = 'https://oauth.sandbox.bb.com.br/oauth/token';

	const OAUTH_PRODUCTION = 'https://oauth.bb.com.br/oauth/token';

	const SCOPE = 'cobrancas.boletos-requisicao';

	const GRANT_TYPE = 'client_credentials';

    /**
     * @var Client
     */
	private $httpClient;
	private $clientId;
	private $clientSecret;
	private $oAuthUrl;

    /**
     *
     * Constructor method
     * @param array $config
     */
	function __construct(array $config)
	{
		$this->httpClient = new Client(['verify' => false]); //Remover o verify em produção

		$this->clientId = Arr::get($config, 'clientId', null);
		$this->clientSecret = Arr::get($config, 'clientSecret', null);
		$this->oAuthUrl = Arr::get($config, 'production', false) == false? self::OAUTH_HM : self::OAUTH_PRODUCTION;
	}

    /**
     * @return mixed
     */
	public function __callBancoDoBrasil()
	{
		return $this->__authorize();
	}

    /**
     * @return bool
     * @throws OAuthException
     */
    private function __authorize()
    {
        try {
            $responseOauth = $this->httpClient->post($this->oAuthUrl, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Authorization' => 'Basic '.base64_encode($this->clientId . ':' . $this->clientSecret)
                ],
                'form_params' => [
                    'scope' => self::SCOPE,
                    'grant_type' => self::GRANT_TYPE
                ]
            ]);

            $oauth = json_decode($responseOauth->getBody());

            if ($oauth && !empty($oauth->access_token)) {
              	return $oauth;
            }
        } catch(\Exception $e) {
			throw new OAuthException();
        }

        throw new OAuthException();
    }
}
