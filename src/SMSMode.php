<?php

namespace SMSMode;

use SMSMode\Request\Client;

/**
 * Class SMSMode
 * @package SMSMode
 * @author Mykhailo YATSYHSYN <myyat@mirko.in.ua>
 * @copyright Mirko 2021 <https://mirko.in.ua>
 */
class SMSMode
{
    const API_URL = 'https://api.smsmode.com/http/';
    const API_VERSION = '1.6';

    /** @var Client */
    private $client;

    /**
     * SMSMode constructor.
     * @param string $accessToken
     */
    public function __construct(string $accessToken)
    {
        $this->client = new Client($accessToken, self::API_URL, self::API_VERSION);
    }

    /**
     * Return balance in you account
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBalance()
    {
        return (float)$this->client->requestExecute("credit");
    }
}
