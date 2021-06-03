<?php

namespace SMSMode\Request;

use GuzzleHttp\Client as ClientGuzzle;

/**
 * Class Client
 * @package SMSMode\Request
 * @author Mykhailo YATSYHSYN <myyat@mirko.in.ua>
 * @copyright Mirko 2021 <https://mirko.in.ua>
 */
class Client
{
    /**
     * @var string
     */
    private $accessToken;

    /** @var string */
    private $apiURL;

    /**
     * @var ClientGuzzle
     */
    private $guzzleClient;

    /**
     * Client constructor.
     *
     * @param string $accessToken
     * @param string $apiURL
     * @param string $apiVersion
     */
    public function __construct(
        string $accessToken,
        string $apiURL,
        string $apiVersion
    ) {
        $this->accessToken = $accessToken;
        $this->apiURL = $apiURL.$apiVersion.'/';

        $this->guzzleClient = new ClientGuzzle([
            'base_uri' => $this->apiURL
        ]);
    }

    /**
     * @param string $path
     * @param array|null $params
     *
     * @return string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestExecute(string $path, ?array $params = [])
    {
        $result = $this->guzzleClient->post(
            $this->getURLByPath($path),
            [
                "body" => http_build_query($params),
            ]
        );

        return $result->getBody()->getContents();
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getURLByPath(string $path)
    {
        return $this->apiURL . $path . ".do?accessToken=".$this->accessToken;
    }
}
