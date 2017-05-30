<?php
namespace Barantaran\Platformcraft;

use Barantaran\Platformcraft\PlatformWrap;

class Platform extends PlatformWrap
{

    function __construct($apiUserId, $HMACKey)
    {
        if(empty($apiUserId) || empty ($HMACKey)) return false;

        parent::__construct($apiUserId, $HMACKey);
    }


    public function setupVideoPlayer($videoFilePath, $name = "file")
    {
        $videoUploadResult = $this->postObject($videoFilePath, $name);

        if(!$videoUploadResult) {
            $this->error[] = ["error" => "Can't upload file to platform", "data" => $videoFilePath];
            return false;
        }

        $playerSetupResult = $this->client->request('POST', $this->getAccessPointUrl(PlatformType::PLR_PNT, 'POST'),
            [
                "json" =>
                [
                    "name" => "player" . $videoUploadResult["response"]["object"]["name"],
                    "videos" =>
                    [
                        $videoUploadResult["response"]["object"]["name"] => $videoUploadResult["response"]["object"]["id"]
                    ]
                ]
            ]
        );

        return json_decode($playerSetupResult->getBody()->getContents(),1);
    }

    public function attachImageToPlayer($imageFilePath, $playerId)
    {
        if(!$imageFilePath || !$playerId) {
           $this->error[] = ["error" => "Wrong image path or player id"];
           return false;
        }

        $imageUploadResult = $this->postObject($imageFilePath);

        if(!$imageUploadResult) {
            $this->error[] = ["error" => "Can't upload file to platform", "data" => $imageFilePath];
            return false;
        }

        $imageSetupResult = $this->client->request('PUT', $this->getAccessPointUrl(PlatformType::PLR_PNT, 'PUT', $playerId),
            [
                "json" =>
                [
                    "screen_shot_id" => $imageSetupResult["response"]["object"]["id"]
                ]
            ]
        );

        return json_decode($imageSetupResult->getBody()->getContents(),1);
    }
}
