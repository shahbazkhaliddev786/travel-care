<?php

namespace App\Services;

use DateTime;
use DateTimeZone;
use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;

class AgoraService
{
    protected string $appId;
    protected string $appCertificate;
    protected int $tokenExpiration;

    public function __construct()
    {
        $this->appId = config('services.agora.app_id');
        $this->appCertificate = config('services.agora.app_certificate');
        $this->tokenExpiration = (int) config('services.agora.token_expiration', 3600);
    }

    public function generateRtcToken(string $channelName, int|string $uid, ?int $role = null, ?int $expireTime = null): string
    {
        $role = $role ?? RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = $expireTime ?? $this->tokenExpiration;
        $currentTimestamp = (new DateTime('now', new DateTimeZone('UTC')))->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        if (is_numeric($uid)) {
            return RtcTokenBuilder::buildTokenWithUid(
                $this->appId,
                $this->appCertificate,
                $channelName,
                (int) $uid,
                $role,
                $privilegeExpiredTs
            );
        }

        return RtcTokenBuilder::buildTokenWithUserAccount(
            $this->appId,
            $this->appCertificate,
            $channelName,
            (string) $uid,
            $role,
            $privilegeExpiredTs
        );
    }
} 