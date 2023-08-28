<?php 

namespace BBboletoCobranca\Entities;

/**
 * Class OAuthEntity
 * @package BBboletoCobranca\Entities
 */
class OAuthEntity
{
    /**
     * @var
     */

	private $typeToken;
    /**
     * @var
     */
	private $accessToken;
    /**
     * @var
     */
	private $gw_dev_app_key;

    /**
     * @var
     */
	private $environment;

    /**
     * @return mixed
     */
	public function getTypeToken()
	{
	    return $this->typeToken;
	}

    /**
     * @param $typeToken
     * @return $this
     */
	public function setTypeToken($typeToken)
	{
	    $this->typeToken = $typeToken;
	    return $this;
	}

    /**
     * @return mixed
     */
	public function getAccessToken()
	{
	    return $this->accessToken;
	}

    /**
     * @param $accessToken
     * @return $this
     */
	public function setAccessToken($accessToken)
	{
	    $this->accessToken = $accessToken;
	    return $this;
	}

    /**
     * @return mixed
     */
	public function getGwDevAppKey()
	{
	    return $this->gw_dev_app_key;
	}

    /**
     * @param $accessToken
     * @return $this
     */
	public function setGwDevAppKey($gw_dev_app_key)
	{
	    $this->gw_dev_app_key = $gw_dev_app_key;
	    return $this;
	}

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param $environment
     * @return $this
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

}