<?php
namespace App\Observers;

use Cache;
use App\Models\UserInfo;

/**
 * User observer
 */
class UserInfoObserver
{
    public function saved(UserInfo $userinfo)
    {
        Cache::forget("cachedUserInfo.{$userinfo->user_id}");
    }
}
