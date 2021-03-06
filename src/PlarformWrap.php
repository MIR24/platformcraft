<?php
namespace Barantaran\Platformcraft;

use Barantaran\Platformcraft\PlatformType as PlatformType;
use Barantaran\Platformcraft\Utility as Util;
use GuzzleHttp\Client;

class PlatformWrap
{
    protected $apiUserId;
    protected $HMACKey;
    protected $point = "api.platformcraft.ru";
    protected $version = 1;
    protected $client;
    protected $error = null;


    function __construct($apiUserId, $HMACKey)
    {
        if(empty($apiUserId) || empty ($HMACKey)) return false;

        $this->apiUserId = $apiUserId;
        $this->HMACKey = $HMACKey;
        $this->client = new Client();
    }

    protected function postObject($filePath, $name = "file")
    {
        $accessPointUrl = $this->getAccessPointUrl();

        $file = fopen($filePath, 'r');

        if(!$file) {
            $this->error[] = [ "error" => "Can't open file", "data" => $filePath ];
            return false;
        }

        $response = $this->client->request('POST', $accessPointUrl,
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
            "url"=>$accessPointUrl,
            "response" => json_decode($response->getBody()->getContents(),1)
            ];

        return $result;
    }

    protected function getAccessPointUrl($pointType = PlatformType::OBJ_PNT, $requestType = 'POST', $objectId = null)
    {
        if($objectId){
            $urlBase = $this->getPointBase() . "/$pointType/$objectId?" . Util::getIdentityString($this->apiUserId);
        } else {
            $urlBase = $this->getPointBase() . "/$pointType?" . Util::getIdentityString($this->apiUserId);
        }

        $message = "$requestType+".$urlBase;
        $hash = Util::getHash($message, $this->HMACKey);

        return "https://" . $urlBase."&hash=".$hash;
    }

    protected function getPointBase()
    {
        return $this->point."/".$this->version;
    }

    protected function getMyError()
    {
        return $this->error;
    }
}
