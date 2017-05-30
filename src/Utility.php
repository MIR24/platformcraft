<?php

namespace Barantaran;

class Utility
{
    public static function getHash($message, $HMACKey)
    {
        return hash_hmac("sha256", $message, $HMACKey);
    }

    public static function getTimestamp()
    {
        $date = new \DateTime();
        return $date->getTimestamp();
    }

    public static function getIdentityString($apiUserId)
    {
        $time = self::getTimestamp();
        return "apiuserid=" . $apiUserId . "&timestamp=" . $time;
    }
}
