<?php
namespace Barantaran\Platformcraft;

use Barantaran\Platformcraft\PlatformWrap;

class Platform extends PlatformWrap
{

    public function __construct($apiUserId, $HMACKey)
    {
        if (empty($apiUserId) || empty($HMACKey)) {
            return false;
        }

        parent::__construct($apiUserId, $HMACKey);
    }

    public function getUrl($pointType = PlatformType::OBJ_PNT, $method = 'GET', $id = null)
    {
        return $this->getAccessPointUrl(
            $pointType,
            strtoupper($method),
            $id
        );
    }

    public function setupVideoPlayer($videoFilePath, $name = "file")
    {
        $videoUploadResult = $this->postObject($videoFilePath, $name);

        if (!$videoUploadResult) {
            $this->error[] = ["error" => "Can't upload file to platform", "data" => $videoFilePath];
            return false;
        }

        $additional = [
            "json" =>
            [
                "name" => "player" . $videoUploadResult["response"]["object"]["name"],
                "videos" =>
                [
                    $videoUploadResult["response"]["object"]["name"] => $videoUploadResult["response"]["object"]["id"]
                ]
            ]
        ];

        return $this->sendRequest('POST', $this->getAccessPointUrl(PlatformType::PLR_ACCESS_PNT, 'POST'), $additional);
    }

    public function attachImageToPlayer($imageFilePathOrCdnId, $playerId, $useCdnId = false)
    {
        if (!$imageFilePathOrCdnId || !$playerId) {
            $this->error[] = ["error" => "Wrong image path or player id"];
            return false;
        }

        if ($useCdnId) {
            $imageUploadResult = $imageFilePathOrCdnId;
        } else {
            $imageUploadResult = $this->postObject($imageFilePathOrCdnId);
            if (!$imageUploadResult) {
                $this->error[] = ["error" => "Can't upload file to platform", "data" => $imageFilePathOrCdnId];
                return false;
            }
            $imageUploadResult = $imageUploadResult["response"]["object"]["id"];
        }

        $additional = [
            "json" =>
            [
                "screen_shot_id" => $imageUploadResult
            ]
        ];

        return $this->sendRequest('PUT', $this->getAccessPointUrl(PlatformType::PLR_ACCESS_PNT, 'PUT', $playerId), $additional);
    }

    public function deleteObject($objectId)
    {
        if (!$objectId) {
            $this->error[] = ["error" => "Object id needed"];
            return false;
        }

        return $this->sendRequest('DELETE', $this->getAccessPointUrl(PlatformType::OBJ_ACCESS_PNT, 'DELETE', $objectId));
    }

    public function deletePlayer($playerId)
    {
        if (!$playerId) {
            $this->error[] = ["error" => "Player id needed"];
            return false;
        }

        return $this->sendRequest('DELETE', $this->getAccessPointUrl(PlatformType::PLR_ACCESS_PNT, 'DELETE', $playerId));
    }
}
