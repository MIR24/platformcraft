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

    public function getObject($objectId)
    {
        return $this->sendRequest('GET', $this->getAccessPointUrl(PlatformType::OBJ_ACCESS_PNT, 'GET', $objectId));
    }

    public function postObject($filePath, $name = null)
    {
        $accessPointUrl = $this->getAccessPointUrl();

        $file = fopen($filePath, 'r');

        if (!$file) {
            $this->error[] = [ 'error' => "Can't open file", 'data' => $filePath ];
            return false;
        }

        $additional = [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => $file
                ]
            ]
        ];
        if (!is_null($name)) {
            $additional['multipart'][] = [
                'name' => 'name',
                'contents' => $name
            ];
        }

        $response = $this->sendRequest('POST', $accessPointUrl, $additional);

        if ($response['code'] == 200) {
            $result = [
                'url' => $accessPointUrl,
                'response' => $response
            ];
        } else {
            $result = null;
        }

        return $result;
    }


    /*
    *   $videos = ['720p' => ['path' => '', 'name' => '']]
    *   $image = ['path' => '', 'name' => '']
    */

    public function setupVideoPlayer($videos, $image = [], $vast = null)
    {
        $uploadedVideos = [];

        foreach ($videos as $key => $video) {
            $name = isset($video['name']) ? $video['name'] : null;
            $videoUploadResult = $this->postObject($video['path'], $name);
            if (!$videoUploadResult) {
                $this->error[] = ['error' => "Can't upload file to platform", 'data' => $video['path']];
                return false;
            }
            $uploadedVideos[$key] = $videoUploadResult['response']['object']['id'];
        }

        if (!empty($image)) {
            $name = isset($image['name']) ? $image['name'] : null;
            $imageUploadResult = $this->postObject($image['path'], $name);
            if (!$imageUploadResult) {
                $this->error[] = ['error' => "Can't upload file to platform", 'data' => $image['path']];
                return false;
            }
        }

        $additional = [
            "json" =>
            [
                "name" => "player" . $videoUploadResult["response"]["object"]["name"],
                "videos" => $uploadedVideos
            ]
        ];

        if (!empty($imageUploadResult)) {
            $additional['json']['screen_shot_id'] = $imageUploadResult['response']['object']['id'];
        }

        if (!empty($vast)) {
            $additional['json']['vast_ad_tag_url'] = $vast;
        }

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

    public function getVideoTranscodeFormats()
    {
        return $this->sendRequest('GET', $this->getAccessPointUrl(PlatformType::TPS_ACCESS_PNT, 'GET'));
    }

    public function postVideoTranscodeTask($objectId, $presetIds)
    {
        $additional = [
            "json" =>
            [
                "presets" => $presetIds
            ]
        ];

        return $this->sendRequest('POST', $this->getAccessPointUrl(PlatformType::TCD_ACCESS_PNT, 'POST', $objectId), $additional);
    }

    public function getVideoTranscodeTask($taskId)
    {
        return $this->sendRequest('GET', $this->getAccessPointUrl(PlatformType::TTS_ACCESS_PNT, 'GET', $taskId));
    }

    public function getVideoTranscodeTaskList()
    {
        return $this->sendRequest('GET', $this->getAccessPointUrl(PlatformType::TTS_ACCESS_PNT, 'GET'));
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
