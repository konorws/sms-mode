<?php

namespace SMSMode;

use SMSMode\Exception\RequestException;
use SMSMode\Request\Client;
use SMSMode\Request\Response;

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

    const CLASSE_MSG__PRO = 2;
    const CLASSE_MSG__WITH_FEEDBACK = 4;

    /** @var Client */
    private $client;

    /**=
     * @var string
     */
    private $sender;
    /**
     * @var int
     */
    private $messageClasse;

    /**
     * SMSMode constructor.
     *
     * @param string $accessToken
     * @param string $senderName
     * @param int $messageClasse
     */
    public function __construct(
        string $accessToken,
        string $senderName,
        int $messageClasse = self::CLASSE_MSG__PRO
    ) {
        $this->client = new Client($accessToken, self::API_URL, self::API_VERSION);

        $this->verifySenderName($senderName);
        $this->sender = $senderName;
        $this->messageClasse = $messageClasse;
    }

    /**
     * Return balance in you account
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getBalance()
    {
        return (float)$this->client->requestExecute("credit");
    }

    /**
     * @param array $phones
     * @param string $message
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendSimple(array $phones, string $message): Response
    {
        $requestBody = [
            'message' => $message,
            'numero' => implode(",", $phones),
            'classe_msg' => $this->messageClasse,
            'emetteur' => $this->sender,
        ];

        $result = $this->client->requestExecute("sendSMS", $requestBody);

        return new Response($result);
    }

    /**
     * @param string $smsID
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkStatus(string $smsID)
    {
        $status =  $this->client->requestExecute("smsStatus", [
            'smsID' => $smsID
        ]);

        return new Response($status, $smsID);
    }

    /**
     * @param string $senderName
     *
     * @throws RequestException
     */
    protected function verifySenderName(string $senderName)
    {
        $blocks = ['MSISDN'];

        if(in_array($senderName, $blocks)) {
            throw new RequestException("Invalid config, sender name unavailable");
        }

        if(strlen($senderName) > 11) {
            throw new RequestException("Invalid config, sender name max 11 characters");
        }
    }
}
