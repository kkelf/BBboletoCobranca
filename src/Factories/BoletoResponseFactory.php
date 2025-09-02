<?php

namespace BBboletoCobranca\Factories;

use StdClass;
use BBboletoCobranca\Requests\BoletoRequest;
use BBboletoCobranca\Responses\BoletoResponse;

class BoletoResponseFactory
{
	/**
	 *
	 * @param BBboletoCobranca\Requests\BoletoRequest
	 * @param StdClass
	 */
	public function make(BoletoRequest $boletoRequest, StdClass $objectBoleto)
	{
		$logradouro = $boletoRequest->getBeneficiario()->getLogradouro();
		$municipio = $boletoRequest->getBeneficiario()->getMunicipio();
		$uf = $boletoRequest->getBeneficiario()->getUf();

		$response = new BoletoResponse;
		$response->setNossoNumero($boletoRequest->getNossoNumero())
			->setInicioNossoNumero('000')
			->setNumeroDocumento($boletoRequest->getNossoNumero())
			->setVencimento($boletoRequest->getDataVencimento()->format('d/m/Y'))
			->setEmissao($boletoRequest->getDataEmissao()->format('d/m/Y'))
			->setProcessamento($boletoRequest->getDataEmissao()->format('d/m/Y'))
			->setValor($boletoRequest->getValorOriginal())
			->setConvenio($boletoRequest->getConvenio())
			->setCarteira($boletoRequest->getCarteira())
			->setVariacaoCarteira($boletoRequest->getVariacaoCarteira())
			->setPagador($boletoRequest->getPagador())
			->setDemonstrativo($boletoRequest->getDescricaoTipoTitulo())
			->setInstrucoes($boletoRequest->getInstrucoes())
			->setAceite($boletoRequest->getAceite())
			->setAgencia($objectBoleto->beneficiario->agencia)
			->setConta($objectBoleto->beneficiario->contaCorrente)
			->setContaDigito('0')
			->setNomeBeneficiario($boletoRequest->getBeneficiario()->getNome())
			->setDocumento($boletoRequest->getBeneficiario()->getDocumento())
			->setEndereco($logradouro ? $logradouro : $objectBoleto->beneficiario->logradouro)
			->setCidade($municipio ? $municipio : $objectBoleto->beneficiario->cidade)
			->setUf($uf ? $uf : $objectBoleto->beneficiario->uf)
			->setPixQrCode($objectBoleto->qrCode->emv)
			->setLinhaDigitavel($objectBoleto->linhaDigitavel);

		return $response;
	}
}
