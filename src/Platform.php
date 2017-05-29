<?php
namespace Platformcraft

use GuzzleHttp\Client;

class Platform
{
    protected $apiUserId;
    protected $secretKey;
    protected $HMACKey = "8uYAzx0-sQrOquBKoh4kWiqILnbUk0bpL0fWSbsYHQQ=";
    protected $point = "api.platformcraft.ru";
    protected $version = 1;

    function __construct($apiUserId, $secretKey)
    {
        $this->apiUserId = $apiUserId;
        $this->secretKey = $secretKey;
    }

    public function postObject($filePath, $name = null)
    {
        $date = new DateTime();
        $time = $date->getTimestamp();

        $urlBase = $this->point."/".$this->version."/objects?apiuserid=".$this->apiUserId."&timestamp=".$time;
        $message = "POST+".$message;

        $hash = hash_hmac("sha256", $message, $HMACKey);

        $url = "https://".$urlBase."&hash=".$hash;

        $body = fopen($filePath, 'r');
        $client = new Client();
        $r = $client->request('POST', $url, ['body' => $body]);

        $result = [
            "url"=>$url,
            "response" => $r
            ];

        return $result;
    }
}
