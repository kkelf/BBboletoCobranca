<?php

namespace BBboletoCobranca;

use BBboletoCobranca\Constants\Formato;
use BBboletoCobranca\Exceptions\BoletoException;
use BBboletoCobranca\Helpers\Fractal;
use BBboletoCobranca\Requests\BoletoRequest;
use BBboletoCobranca\Responses\BoletoResponse;
use BBboletoCobranca\Services\ServiceAuthorization;
use BBboletoCobranca\Services\ServiceRegister;
use BBboletoCobranca\Services\ServiceLayoutBoleto;
use BBboletoCobranca\Transformers\BoletoTransformer;
use BBboletoCobranca\Validates\BancoDoBrasilValidate;

/**
 * Class BancoDoBrasil
 * @package BBboletoCobranca
 */
class BancoDoBrasil
{
    /**
     * @var Entities\OAuthEntity 
     */
	private $authorization;

    /**
     * @var array
     */
	private $config;

	/**
	*
	* @param [array|config]
	* @return bool
	*/
  	function __construct(array $config)
	{
		$bancoDoBrasilValidate = new BancoDoBrasilValidate();
		$bancoDoBrasilValidate->config($config);

		$this->config = $config;

		$serviceAuthorization = new ServiceAuthorization();
		$this->authorization = $serviceAuthorization->authorize($config);
	}

    /**
     * @param BoletoRequest $boletoRequest
     * @return mixed
     * @throws BoletoException
     */
	public function register(BoletoRequest $boletoRequest)
	{
    	$serviveRegister = new ServiceRegister();
    	$boleto = $serviveRegister->register($boletoRequest, $this->authorization);
    	$boleto->setLogo(array_get($this->config, 'logo', 'http://placehold.it/200&text=logo'));

    	if(!$boleto instanceof BoletoResponse)
    		throw new BoletoException("Não foi possivel gerar boleto");

    	$data = null;
    	if(array_get($this->config, 'formato') == Formato::PDF) 
    		$data = (new ServiceLayoutBoleto)->dataToPdf($boleto);

    	if(array_get($this->config, 'formato') == Formato::HTML) 
    		$data = (new ServiceLayoutBoleto)->dataToHtml($boleto);

		return $data;
	}
}