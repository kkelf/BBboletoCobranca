<?php 

namespace BBboletoCobranca\Entities;

use BBboletoCobranca\Constants\Desconto;
use BBboletoCobranca\Helpers\BancoDoBrasil as BancoDoBrasilHelper;

class DescontoEntity
{
	private $tipo = Desconto::SEM_DESCONTO;

	private $data;

	private $percentual;

	private $valor;

	public function getTipo()
	{
	    return $this->tipo;
	}
	 
	public function setTipo($tipo)
	{
	    $this->tipo = $tipo;
	    return $this;
	}

	public function getData()
	{
	    return $this->data;
	}
	 
	public function setData($data)
	{
	    $this->data = BancoDoBrasilHelper::generateDateTimeFromBoleto($data);
	    return $this;
	}

	public function getPercentual()
	{
	    return $this->percentual;
	}
	 
	public function setPercentual($percentual)
	{
	    $this->percentual = $percentual;
	    return $this;
	}

	public function getValor()
	{
	    return $this->valor;
	}
	 
	public function setValor($valor)
	{
	    $this->valor = $valor;
	    return $this;
	}
}

