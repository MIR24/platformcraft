<?php
namespace Barantaran\Platformcraft;

use Barantaran\Platformcraft\PlatformType as PlatformType;
use Barantaran\Platformcraft\Utility as Util;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;

class PlatformWrap
{
    protected $apiUserId;
    protected $HMACKey;
    protected $point = "api.platformcraft.ru";
    protected $version = 1;
    protected $client;
    protected $error = null;


    public function __construct($apiUserId, $HMACKey)
    {
        if (empty($apiUserId) || empty($HMACKey)) {
            return false;
        }

        $this->apiUserId = $apiUserId;
        $this->HMACKey = $HMACKey;
        $this->client = new Client();
    }

    protected function getAccessPointUrl($pointType = PlatformType::OBJ_ACCESS_PNT, $requestType = 'POST', $objectId = null)
    {
        if ($objectId) {
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

    protected function sendRequest($type, $accessPointUrl, $additional = null)
    {
        try {
            if ($additional) {
                $response = $this->client->request($type, $accessPointUrl, $additional);
            } else {
                $response = $this->client->request($type, $accessPointUrl);
            }
            return json_decode($response->getBody(), 1);
        } catch (ClientException $e) {
            $response['request'] = Psr7\str($e->getRequest());
            $response['message'] = $e->getMessage();
            if ($e->hasResponse()) {
                $response['code'] = $e->getResponse()->getStatusCode();
                $response['response'] = Psr7\str($e->getResponse());
            }
                return $response;
        } catch (RequestException $e) {
            $response['request'] = Psr7\str($e->getRequest());
            $response['message'] = $e->getMessage();
            if ($e->hasResponse()) {
                $response['code'] = $e->getResponse()->getStatusCode();
                $response['response'] = Psr7\str($e->getResponse());
            }
            return $response;
        } catch (\Exception $e) {
            return ['code' => 'unknown', 'message' => $e->getMessage()];
        }
    }
}
