<?php 

namespace BBboletoCobranca\Services;

use BBboletoCobranca\NovoBoletoPHP\BoletoFactory;
use BBboletoCobranca\Helpers\BancoDoBrasil as BancoDoBrasilHelper;
use BBboletoCobranca\Responses\BoletoResponse;


class ServiceLayoutBoleto 
{
	private function makeBoleto(BoletoResponse $boletoResponse)
	{
        $instrucoes = $boletoResponse->getInstrucoes()->getInstrucoes();
        return [
			'convenio' => $boletoResponse->getConvenio(),
            'nosso_numero' => $boletoResponse->getNossoNumero(),
            'numero_documento' => $boletoResponse->getNossoNumero(),
            'data_vencimento' => $boletoResponse->getVencimento(),
            'data_documento' => $boletoResponse->getEmissao(),
            'data_processamento' => $boletoResponse->getProcessamento(),
            'valor_boleto' => number_format($boletoResponse->getValor(), 2, ',', ''),
            'carteira' => $boletoResponse->getCarteira(),
            'especie_doc' => $boletoResponse->getEspecieTitulo(),
            'sacado' => $boletoResponse->getPagador()->getNome(),
            'sacado_tipo_documento' => $boletoResponse->getPagador()->getTipoDocumento(),
            'sacado_documento' => $boletoResponse->getPagador()->getDocumento(),
            'endereco1' => $boletoResponse->getPagador()->getLogradouro().','.$boletoResponse->getPagador()->getBairro(),
            'endereco2' => $boletoResponse->getPagador()->getMunicipio().' - '.$boletoResponse->getPagador()->getUf().' - CEP '.$boletoResponse->getPagador()->getCep(),
            'demonstrativo1' => $boletoResponse->getInstrucoes()->getDemonstrativo(),
            'instrucoes1' => array_shift($instrucoes),
            'instrucoes2' => array_shift($instrucoes),
            'instrucoes3' => array_shift($instrucoes),
            'instrucoes4' => implode(', ', $instrucoes),
            'aceite' => $boletoResponse->getAceite(),
            'especie' => $boletoResponse->getMoeda(),
            'agencia' => $boletoResponse->getAgencia(),
            'conta' => $boletoResponse->getConta(),
            'conta_dv' => '',
            'identificacao' => $boletoResponse->getNomeBeneficiario(),
            'cpf_cnpj' => BancoDoBrasilHelper::formatCnpj($boletoResponse->getDocumento()),
            'endereco' => $boletoResponse->getEndereco(),
            'cidade_uf' => $boletoResponse->getCidade().'-'.$boletoResponse->getUf(),
            'cedente' => $boletoResponse->getNomeBeneficiario(),
            'logo_empresa' => $boletoResponse->getLogo(),
            'pix_qrcode' => $boletoResponse->getPixQrCode()
        ];

          
	}

	public function dataToHtml(BoletoResponse $boletoResponse)
	{
		$data = $this->makeBoleto($boletoResponse);

		try {
			$factory = new BoletoFactory([
	            'cachePath' => '/tmp/1_0_11',
	            'imageUrl' => dirname(__DIR__, 2)."/resources/images"
	        ]);

          	$html = $factory->makeBoletoAsHTML(BoletoFactory::BANCO_DO_BRASIL, $data);

            return $html;
        }catch(\Exception $e){
            return null;
        }
	}

	public function dataToPdf(BoletoResponse $boletoResponse)
	{

		$data = $this->makeBoleto($boletoResponse);

		try {
			$factory = new BoletoFactory([
	            'cachePath' => '/tmp/1_0_11',
	            'imageUrl' => dirname(__DIR__, 2)."/resources/images"
	        ]);

	      	return $factory->makeBoletoAsPDF(BoletoFactory::BANCO_DO_BRASIL, $data);
        }catch(\Exception $e){
            return null;
        }
	}
}