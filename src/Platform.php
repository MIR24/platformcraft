<?php
<<<<<<< HEAD
namespace Barantaran\Platformcraft;

use GuzzleHttp\Client;
use Barantaran\Utility as Util;
=======
namespace Barantaran;

use GuzzleHttp\Client;
>>>>>>> master

class Platform
{
    protected $apiUserId;
    protected $HMACKey;
    protected $point = "api.platformcraft.ru";
    protected $version = 1;
<<<<<<< HEAD
    protected $client;
=======
>>>>>>> master

    function __construct($apiUserId, $HMACKey)
    {
        $this->apiUserId = $apiUserId;
        $this->HMACKey = $HMACKey;
<<<<<<< HEAD
        $this->client = new Client();
=======
>>>>>>> master
    }

    public function postObject($filePath, $name = "file")
    {
<<<<<<< HEAD
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
=======
        $date = new \DateTime();
        $time = $date->getTimestamp();

        $urlBase = $this->point."/".$this->version."/objects?apiuserid=".$this->apiUserId."&timestamp=".$time;
        $message = "POST+".$urlBase;

        $hash = hash_hmac("sha256", $message, $this->HMACKey);

        $url = "https://".$urlBase."&hash=".$hash;

        $file = fopen($filePath, 'r');

        $client = new Client();

        $r = $client->request('POST', $url, 
            [
                'multipart' => [
                    [
                        "name" => $name, 
>>>>>>> master
                        "contents" => $file
                    ]
                ]
            ]
        );

        $result = [
<<<<<<< HEAD
            "url"=>$urlFull,
            "response" => $response->getBody()->getContents()
=======
            "url"=>$url,
            "response" => $r->getBody()->getContents()
>>>>>>> master
            ];

        return $result;
    }
<<<<<<< HEAD

    protected function getPointBase()
    {
        return $this->point."/".$this->version;
    }
=======
>>>>>>> master
}
