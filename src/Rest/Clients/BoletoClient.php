<?php

namespace BBboletoCobranca\Rest\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use BBboletoCobranca\Entities\OAuthEntity;
use BBboletoCobranca\Rest\Config;
use Exception;

class BoletoClient
{
	/**
     * @var Client
     */
	private $httpClient;
	private $endpoint;
	private $headers;
	private $query;

	/**
	 * BoletoClient constructor.
	 * @param OAuthEntity $oAuthEntity
	 */
	function __construct(OAuthEntity $oAuthEntity)
	{
		$this->httpClient = new Client([
			'verify' => false, //Remover o verify em produção
			'Content-Type' => 'application/json'
		]); 

		$this->endpoint = $oAuthEntity->getEnvironment() == false
			? Config::ENDPOINT_HM
			: Config::ENDPOINT_PRODUCTION;

		$this->headers = [
			'Authorization' => "Bearer {$oAuthEntity->getAccessToken()}"
		];

		$this->query = [
			'gw-dev-app-key' => $oAuthEntity->getGwDevAppKey()
		];
	}

	/**
	 *
	 * @param array $boletoBody
	 */
	public function register(array $boletoBody)
	{
		try {
			$responseBoleto = $this->httpClient->post("$this->endpoint/boletos", [
				'headers' => $this->headers,
				'query' => $this->query,
				'form_params' => $boletoBody
			]);
			
			$response = json_decode($responseBoleto->getBody());
			
			if ($response && !empty($response->numero)) {
				return $response;
			}
		} catch(ClientException $e) {
			$response = $e->getResponse();
			throw new Exception($response->getBody()->getContents());
		}

		throw new Exception();
	}
}
