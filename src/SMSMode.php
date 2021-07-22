<?php

namespace SMSMode;

use SMSMode\Exception\RequestException;
use SMSMode\Request\Client;
use SMSMode\Request\Message;
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
        int $messageClasse = Message::CLASSE_MSG__PRO
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
     * @param Message $message
     *
     * @return Response
     */
    public function sendSMS(Message $message)
    {
        $this->prepareMessage($message);

        $requestBody = $message->buildRequestBody();
        var_dump($requestBody);
        $result = $this->client->requestExecute("sendSMS", $requestBody);

        return new Response($result);
    }

    /**
     * Delete message by Id
     *
     * @param string $smsID
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteSMS(string $smsID): array
    {
        $result = $this->client->requestExecute("deleteSMS", [
            "smsID" => $smsID
        ]);

        return $this->simpleResponse($result);
    }

    /**
     * @param string $smsID
     *
     * @return Response
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

    /**
     * @param string $response
     * @return array
     */
    private function simpleResponse(string $response): array
    {
        $responseData = [
            "status" => false,
            "message" => null,
            "code" => null,
        ];

        $items = explode("|", $response);
        if(!$items || count($items) < 2) {
            return $responseData;
        }

        $responseData['code'] = (int)$items[0];
        $responseData['message'] = trim($items[1]);

        if($responseData['code'] === 0) {
            $responseData['status'] = true;
        }

        return $responseData;
    }

    /**
     * @param Message $message
     */
    private function prepareMessage(Message $message)
    {
        if(!$message->hasSender()) {
            $message->setSender($this->sender);
        }
    }
}
