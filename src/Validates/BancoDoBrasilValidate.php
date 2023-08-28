<?php 
/**
 * Created by PhpStorm.
 * User: ewerson
 * Date: 12/06/2018
 * Time: 11:14
 */
namespace BBboletoCobranca\Validates;

use Exception;

/**
 * Class BancoDoBrasilValidate
 * @package 
 */
class BancoDoBrasilValidate
{
    /**
     * @param array $config
     * @throws ValidationException
     */
    public function config($config = [])
    {
        if(empty($config))
            throw new Exception('Necessário passar os dados de configuração para geração do boleto');

        if(!data_get($config, 'clientId'))
            throw new Exception('A configuração clientId é obrigatória');

        if(!data_get($config, 'clientSecret'))
            throw new Exception('A configuração clientSecret é obrigatória');
    }
}
