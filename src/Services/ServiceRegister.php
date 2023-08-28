<?php

namespace BBboletoCobranca\Services;

use StdClass;
use BBboletoCobranca\Entities\OAuthEntity;
use BBboletoCobranca\Entities\InstrucoesEntity;
use BBboletoCobranca\Entities\PagadorEntity;
use BBboletoCobranca\Entities\BeneficiarioEntity;
use BBboletoCobranca\Exceptions\PagadorException;
use BBboletoCobranca\Exceptions\BoletoException;
use BBboletoCobranca\Exceptions\BeneficiarioException;
use BBboletoCobranca\Factories\BoletoResponseFactory;
use BBboletoCobranca\Requests\BoletoRequest;
use BBboletoCobranca\Responses\BoletoResponse;
use BBboletoCobranca\Rest\Config;
use BBboletoCobranca\Rest\Clients\BoletoClient;
use BBboletoCobranca\Rest\Factories\BoletoFactory;

/**
 *
 *
 *
 */
class ServiceRegister
{

	/**
	 *
	 * @var BBboletoCobranca\Rest\Factories\BoletoFactory
	 */
	private $boletoFactory;

	/**
	 *
	 * @var BBboletoCobranca\Factories\BoletoResponseFactory
	 */
	private $boletoResponseFactory;

	/**
	 *
	 * @param BBboletoCobranca\Rest\Factories\BoletoFactory
	 */
	function __construct()
	{
		$this->boletoFactory = new BoletoFactory;
		$this->boletoResponseFactory = new BoletoResponseFactory;
	}

	/**
	 *
	 * @param BBboletoCobranca\Requests\BoletoRequest
	 * @param [StdClass]
	 */
	private function setResponse(BoletoRequest $boletoRequest, StdClass $objectBoleto)
	{
		return $this->boletoResponseFactory->make($boletoRequest, $objectBoleto);
	}

	/**
	 *
	 * @param BBboletoCobranca\Requests\BoletoRequest
	 * @param BBboletoCobranca\Entities\OAuthEntity
	 */
	public function register(BoletoRequest $boletoRequest, OAuthEntity $oAuthEntity)
	{

		if (!$boletoRequest->getPagador() instanceof PagadorEntity)
			throw new PagadorException();

		if(!$boletoRequest->getBeneficiario() instanceof BeneficiarioEntity)
			throw new BeneficiarioException();

		$boletoBody = $this->boletoFactory->make($boletoRequest);

		try {
			$client = new BoletoClient($oAuthEntity);
			$body = $client->register($boletoBody);
		} catch (\Exception $e) {
			throw new BoletoException($e->getMessage());
		}

		return $this->setResponse($boletoRequest, (object) $body);
	}
}
