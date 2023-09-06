<?php

namespace BBboletoCobranca\Rest\Factories;

use BBboletoCobranca\Entities\DescontoEntity;
use BBboletoCobranca\Entities\JurosEntity;
use BBboletoCobranca\Entities\MultaEntity;
use BBboletoCobranca\Entities\PagadorEntity;
use BBboletoCobranca\Entities\AvalistaEntity;
use BBboletoCobranca\Helpers\BancoDoBrasil as BancoDoBrasilHelper;
use BBboletoCobranca\Requests\BoletoRequest;

/**
 * Class BoletoFactory
 * @package BBboletoCobranca\Rest\Factories
 */
class BoletoFactory
{
	/**
	 * @param BoletoRequest $boletoRequest
	 * @return array
	 */
	public function make(BoletoRequest $boletoRequest)
	{
		$data = [];

		$this->addParameter($data, 'numeroConvenio', $boletoRequest->getConvenio(), 'Y');
		$this->addParameter($data, 'numeroCarteira', $boletoRequest->getCarteira(), 'Y');
		$this->addParameter($data, 'numeroVariacaoCarteira', $boletoRequest->getVariacaoCarteira(), 'Y');
		$this->addParameter($data, 'codigoModalidade', $boletoRequest->getModalidade(), 'Y');
		$this->addParameter($data, 'dataEmissao', $boletoRequest->getDataEmissao()->format('d.m.Y'), 'Y');
		$this->addParameter($data, 'dataVencimento', $boletoRequest->getDataVencimento()->format('d.m.Y'), 'Y');
		$this->addParameter($data, 'valorOriginal', $boletoRequest->getValorOriginal(), 'Y');
		$this->addParameter($data, 'valorOriginal', $boletoRequest->getValorOriginal(), 'Y');

		if ($boletoRequest->getDesconto() instanceof DescontoEntity)
			$this->addParameter($data, 'desconto', $this->setDescontoOnBody($boletoRequest->getDesconto()), 'N');

		$this->addParameter($data, 'quantidadeDiasProtesto', $boletoRequest->getDiasProtesto(), 'Y');

		if ($boletoRequest->getJuros() instanceof JurosEntity)
			$this->addParameter($data, 'jurosMora', $this->setJurosOnBody($boletoRequest->getJuros()), 'N');

		if ($boletoRequest->getMulta() instanceof MultaEntity)
			$this->addParameter($data, 'multa', $this->setMultaOnBody($boletoRequest->getMulta()), 'N');

		$this->addParameter($data, 'codigoAceite', $boletoRequest->getAceite(), 'Y');
		$this->addParameter($data, 'codigoTipoTitulo', $boletoRequest->getTipoTitulo(), 'Y');
		$this->addParameter($data, 'descricaoTipoTitulo', $boletoRequest->getDescricaoTipoTitulo(), 'N');
		$this->addParameter($data, 'indicadorPermissaoRecebimentoParcial', $boletoRequest->getPermissaoRecebimentoParcial(), 'Y');
		$this->addParameter($data, 'numeroTituloBeneficiario', $boletoRequest->getSeuNumero(), 'N');
		$this->addParameter($data, 'campoUtilizacaoBeneficiario', BancoDoBrasilHelper::chacracterLimit($boletoRequest->getCampoUtilizacaoBeneficiario(), 25), 'N');
		$this->addParameter($data, 'numeroTituloCliente', BancoDoBrasilHelper::makeNossoNumero($boletoRequest->getConvenio(), $boletoRequest->getNossoNumero()), 'Y');

		if ($boletoRequest->getPagador() instanceof PagadorEntity)
			$this->addParameter($data, 'pagador', $this->setPagadorOnBody($boletoRequest->getPagador()), 'Y');

		if ($boletoRequest->getAvalista() instanceof AvalistaEntity)
			$this->addParameter($data, 'beneficiarioFinal', $this->setAvalistaOnBody($boletoRequest->getAvalista()), 'N');

		$this->addParameter($data, 'indicadorPix', $boletoRequest->getIndicadorPix(), 'N');

		return $data;
	}

	/**
	 * @param array $data
	 * @param string $name
	 * @param mixed $value
	 * @param string $required Accepted Values: 'Y' or 'N'
	 * @return mixed
	 */
	private function addParameter(
		array &$data,
		string $name,
		mixed $value,
		string $required
	) {
		if ($value || $required === 'Y') {
			$data = array_merge($data, [$name => $value]);
		}

		return $data;
	}

	/**
	 * @param DescontoEntity $descontoEntity
	 * @return array
	 */
	private function setDescontoOnBody(DescontoEntity $descontoEntity)
	{
		$dataDesconto = [];

		if ($descontoEntity->getTipo()) {
			$this->addParameter($dataDesconto, 'tipo', $descontoEntity->getTipo(), 'Y');
		}

		if ($descontoEntity->getData()) {
			$this->addParameter($dataDesconto, 'dataExpiracao', $descontoEntity->getData()->format('d.m.Y'), 'Y');
		}

		if ($descontoEntity->getPercentual()) {
			$this->addParameter($dataDesconto, 'porcentagem', $descontoEntity->getPercentual(), 'Y');
		}

		if ($descontoEntity->getValor()) {
			$this->addParameter($dataDesconto, 'valor', BancoDoBrasilHelper::formatMoney($descontoEntity->getValor()), 'Y');
		}

		return $dataDesconto;
	}

	/**
	 * @param JurosEntity $jurosEntity
	 * @return array
	 */
	private function setJurosOnBody(JurosEntity $jurosEntity)
	{
		$dataJuros = [];

		if ($jurosEntity->getTipo()) {
			$this->addParameter($dataJuros, 'tipo', $jurosEntity->getTipo(), 'Y');
		}

		if ($jurosEntity->getPercentual()) {
			$this->addParameter($dataJuros, 'porcentagem', $jurosEntity->getPercentual(), 'Y');
		}

		if ($jurosEntity->getValor()) {
			$this->addParameter($dataJuros, 'valor', BancoDoBrasilHelper::formatMoney($jurosEntity->getValor()), 'Y');
		}

		return $dataJuros;
	}

	/**
	 * @param MultaEntity $multaEntity
	 * @return array
	 */
	private function setMultaOnBody(MultaEntity $multaEntity)
	{
		$dataMulta = [];

		if ($multaEntity->getTipo()) {
			$this->addParameter($dataMulta, 'tipo', $multaEntity->getTipo(), 'Y');
		}

		if ($multaEntity->getData()) {
			$this->addParameter($dataMulta, 'data', $multaEntity->getData()->format('d.m.Y'), 'Y');
		}

		if ($multaEntity->getPercentual()) {
			$this->addParameter($dataMulta, 'porcentagem', $multaEntity->getPercentual(), 'Y');
		}

		if ($multaEntity->getValor()) {
			$this->addParameter($dataMulta, 'valor', BancoDoBrasilHelper::formatMoney($multaEntity->getValor()), 'Y');
		}

		return $dataMulta;
	}

	/**
	 * @param PagadorEntity $pagadorEntity
	 * @return array
	 */
	private function setPagadorOnBody(PagadorEntity $pagadorEntity)
	{
		$dataPagador = [];

		$this->addParameter($dataPagador, 'tipoInscricao', $pagadorEntity->getTipoDocumento(), 'Y');
		$this->addParameter($dataPagador, 'numeroInscricao', BancoDoBrasilHelper::numbers($pagadorEntity->getDocumento()), 'Y');
		$this->addParameter($dataPagador, 'nome', $pagadorEntity->getNome(), 'Y');
		$this->addParameter($dataPagador, 'endereco', $pagadorEntity->getLogradouro(), 'Y');
		$this->addParameter($dataPagador, 'cep', BancoDoBrasilHelper::numbers($pagadorEntity->getCep()), 'Y');
		$this->addParameter($dataPagador, 'cidade', $pagadorEntity->getMunicipio(), 'Y');
		$this->addParameter($dataPagador, 'bairro', $pagadorEntity->getBairro(), 'Y');
		$this->addParameter($dataPagador, 'uf', $pagadorEntity->getUf(), 'Y');
		$this->addParameter($dataPagador, 'telefone', BancoDoBrasilHelper::numbers($pagadorEntity->getTelefone()), 'N');

		return $dataPagador;
	}

	/**
	 * @param AvalistaEntity $avalistaEntity
	 * @return array
	 */
	private function setAvalistaOnBody(AvalistaEntity $avalistaEntity)
	{
		$dataAvalista = [];

		$this->addParameter($dataAvalista, 'tipoInscricao', $avalistaEntity->getTipoDocumento(), 'Y');
		$this->addParameter($dataAvalista, 'numeroInscricao', $avalistaEntity->getDocumento(), 'Y');
		$this->addParameter($dataAvalista, 'nome', $avalistaEntity->getNome(), 'Y');

		return $dataAvalista;
	}
}
