# Boleto de Cobrança Banco do Brasil via Cobranças API 

Integração com a API de Cobranças do Banco do Brasil para registro de Boletos com QR Code Pix

Projeto baseado no BBboleto e impressão baseada no novoboletophp.
Link dos projetos:
* Bbboleto: https://github.com/Ewersonfc/BBboleto
* novoboletophp: https://github.com/jefersondaniel/novoboletophp

## Instalação
### Composer
```
composer require kkelf/bbboletocobranca
```
Ou no arquivo composer.json.

```
"kkelf/bbboletocobranca": "^1.0.0"
```
## Como usar
```php

use BBboletoCobranca\BancoDoBrasil;
use BBboletoCobranca\Constants\IndicadorPix;
use BBboletoCobranca\Constants\TipoDocumento;
use BBboletoCobranca\Entities\PagadorEntity;
use BBboletoCobranca\Entities\BeneficiarioEntity;
use BBboletoCobranca\Entities\InstrucoesEntity;
use BBboletoCobranca\Requests\BoletoRequest;

$bancoDoBrasil = new BancoDoBrasil([
	'clientId' => 'xxxxxxxxxxx',
	'clientSecret' => 'xxxxxxxxx',
	'gw_dev_app_key' => 'xxxxxx',
	'logo' => 'http://placehold.it/200&text=logo',
	'production' => false,
	'formato' => 'html' // ou 'pdf'
	
]);

	
$beneficiario = new BeneficiarioEntity;
$beneficiario->setTipoDocumento(TipoDocumento::CNPJ)
	->setDocumento('92.862.701/0001-58')
	->setNome('DOCERIA BARBOSA DE ALMEIDA');



$pagador = new PagadorEntity;
$pagador->setTipoDocumento(TipoDocumento::CPF)
	->setDocumento('97965940132')
	->setNome('João da Costa Antunes')
	->setLogradouro('Avenida Dias Gomes 1970')
	->setCep(77458000)
	->setMunicipio('Sucupira')
	->setBairro('Centro')
	->setUf('TO');

$instrucoes = new InstrucoesEntity;
$instrucoes->setInstrucoes([
	'- instrução 1',
	'- instrução teste 2',
	'- instrução teste 3',
])
->setDemonstrativo("Demonstrativo teste");

$boletoRequest = new BoletoRequest();
$boletoRequest->setConvenio(3128557)
	->setCarteira(17) 
	->setVariacaoCarteira(35) 
	->setDataEmissao('2023-08-23') 
	->setDataVencimento('31/08/2023') 
	->setValorOriginal('00,21') 
	->setSeuNumero('987654321987654') // número para controle
	->setNossoNumero('0000100017') // nosso número sequencial do banco
	->setPagador($pagador)
	->setBeneficiario($beneficiario)
	->setInstrucoes($instrucoes)
	->setIndicadorPix(IndicadorPix::QRCODE_DINAMICO);

//se o formato for pdf será baixado automaticamente no register
$data = $bancoDoBrasil->register($boletoRequest);

echo $data;

```
## Instruções

Para adicionar instruções que são impressas no boleto, é necessário preencher a entidade Instruções e "setar" no BoletoRequest.

As instruções adicionadas abaixo serão impressas no boleto, elas estão relacionadas ao desconto, juros, multa e qualquer tipo de especificidade ligada a informação que deve ser apresentada ao pagador.
```php
// ... code
use BBboletoCobranca\Entities\InstrucoesEntity;

$instrucoes = new InstrucoesEntity;
$instrucoes->setInstrucoes([
	'- instrução 1',
	'- instrução teste 2',
	'- instrução teste 3',
])->setDemonstrativo("Demonstrativo teste");

$boletoRequest = new BoletoRequest();
	//... outros set's
	->setInstrucoes($instrucoes)
	// ... 

```

## Desconto
Para adicionar a instrução de desconto em seu boleto é necessário preencher a entendidade Desconto e "setar" no BoletoRequest.

Nota: O desconto é opcional, caso não tenha desconto no título não há necessidade de preencher esta entidade e tbm não há necessidade de "setar" no Request

```php
// ... code
use BBboletoCobranca\Entities\DescontoEntity;
use BBboletoCobranca\Constants\Desconto;

$desconto = new DescontoEntity;
$desconto->setTipo(Desconto::VALOR)
	->setData('10/07/2018')
	->setValor('5.00');

$boletoRequest = new BoletoRequest();
	//... outros set's
	->setDesconto($desconto)
	// ... 
```

## Juros
Para adicionar a instrução de juros em seu boleto é necessário preencher a entendidade Juros e "setar" no BoletoRequest.

Juros possui uma combinação de valores informados que se passados de forma incorreta o Banco não aceitará os dados e consequentemente não irá registrar o boleto.

Nota: O Juros é opcional, caso não tenha desconto no título não há necessiade de preencher esta entidade e tbm não há necessidade de "setar" no Request

```php
// ... code
use BBboletoCobranca\Entities\JurosEntity;
use BBboletoCobranca\Constants\Juros;

// ... code 
$juros = new JurosEntity;
$juros->setTipo(Juros::VALOR_POR_DIA_DE_ATRASO)
	->setValor('10.00');

$boletoRequest = new BoletoRequest();
	//... outros set's
	->setJuros($juros)
	// ... 
```
## Multa
Para adicionar a instrução de multa em seu boleto é necessário preencher a entendidade Multa e "setar" no BoletoRequest.

A Multa assim como o Juros possui uma combinação de valores informados que se passados de forma incorreta o Banco não aceitará os dados e consequentemente não irá registrar o boleto.

Nota: A Multa é opcional, caso não tenha desconto no título não há necessiade de preencher esta entidade e tbm não há necessidade de "setar" no Request

```php
// ... code
use BBboletoCobranca\Entities\MultaEntity;
use BBboletoCobranca\Constants\Multa;

// ... code 
$multa = new MultaEntity;
$multa->setTipo(Multa::VALOR)
	->setValor('10.00');

$boletoRequest = new BoletoRequest();
	//... outros set's
	->setMulta($multa)
	// ... 
```

## Avalista
Para adicionar avalista em seu boleto é necessário preencher a entendidade Multa e "setar" no BoletoRequest.

Nota: O Avalista é opcional, caso não tenha desconto no título não há necessiade de preencher esta entidade e tbm não há necessidade de "setar" no Request

```php
// ... code
use BBboletoCobranca\Constants\TipoDocumento;
use BBboletoCobranca\Entities\AvalistaEntity;

// ... code 
$avalista = new AvalistaEntity;
$avalista->setTipoDocumento(TipoDocumento::CNPJ)
	->setDocumento('09.123.123\0001-81')
	->setNome('Ewerson Carvalho');

$boletoRequest = new BoletoRequest();
	//... outros set's
	->setAvalista($avalista)
	// ... 
```
