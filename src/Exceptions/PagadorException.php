<?php 

namespace BBboletoCobranca\Exceptions;

use Throwable;

class PagadorException extends \Exception
{
    /**
     * OAuthException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if(empty($message))
            $message = 'Pagador inválido';

        parent::__construct($message, $code, $previous);
    }
}
