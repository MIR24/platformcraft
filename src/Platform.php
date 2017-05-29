<?php
namespace Barantaran;

use GuzzleHttp\Client;

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
        $date = new \DateTime();
        $time = $date->getTimestamp();

        $urlBase = $this->point."/".$this->version."/objects?apiuserid=".$this->apiUserId."&timestamp=".$time;
        $message = "POST+".$urlBase;

        $hash = hash_hmac("sha256", $message, $this->HMACKey);

        $url = "https://".$urlBase."&hash=".$hash;

        $file = fopen($filePath, 'r');


        $r = $this->client->request('POST', $url,
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
            "url"=>$url,
            "response" => $r->getBody()->getContents()
            ];

        return $result;
    }
}
