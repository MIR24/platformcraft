<?php

namespace Barantaran\Platformcraft;

class Utility
{
    public static function getHash($message, $HMACKey)
    {
        return hash_hmac("sha256", $message, $HMACKey);
    }

    public static function getIdentityString($apiUserId)
    {
        return "apiuserid=" . $apiUserId . "&timestamp=" . time();
    }
}
