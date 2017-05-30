<?php
namespace Barantaran\Platformcraft;

use GuzzleHttp\Client;
use Barantaran\Platformcraft\Utility as Util;

class Platform
{
    protected $apiUserId;
    protected $HMACKey;
    protected $point = "api.platformcraft.ru";
    protected $version = 1;
    protected $client;

    function __construct($apiUserId, $HMACKey)
    {
        $this->apiUserId = $apiUserId;
        $this->HMACKey = $HMACKey;
        $this->client = new Client();
    }

    public function postObject($filePath, $name = "file")
    {
        $urlBase = $this->getPointBase() . "/objects?" . Util::getIdentityString($this->apiUserId);
        $message = "POST+".$urlBase;

        $hash = Util::getHash($message, $this->HMACKey);

        $urlFull = "https://".$urlBase."&hash=".$hash;

        $file = fopen($filePath, 'r');


        $response = $this->client->request('POST', $urlFull,
            [
                'multipart' => [
                    [
                        "name" => $name,
                        "contents" => $file
                    ]
                ]
            ]
        );

        $result = [
            "url"=>$urlFull,
            "response" => $response->getBody()->getContents()
            ];

        return $result;
    }

    protected function getPointBase()
    {
        return $this->point."/".$this->version;
    }
}
