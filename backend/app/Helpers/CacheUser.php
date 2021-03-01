<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use Cache;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserIntro;
use Auth;

class CacheUser{ //cache-user class
    public static function user($id){
        if(!$id||$id<=0){return;}

        return Cache::remember('cachedUser.'.$id, 60, function() use($id) {
            $user = User::on('mysql::write')->find($id);
            if($user){
                $user->load('title');
            }
            return $user;
        });
    }

    public static function info($id){
        if(!$id||$id<=0){return;}
        return Cache::remember('cachedUserInfo.'.$id, 60, function() use($id) {
            $info = UserInfo::on('mysql::write')->find($id);
            return $info;
        });
    }

    public static function clearuser($id)
    {
        Cache::forget('cachedUser.'.$id);
        Cache::forget('cachedUserInfo.'.$id);
    }

    public static function intro($id){
        if(!$id||$id<=0){return;}

        return Cache::remember('cachedUserIntro.'.$id, 60, function() use($id) {
            return UserIntro::find($id);
        });
    }

    public static function clear_intro($id){
        Cache::forget('cachedUserIntro.'.$id);
    }

    public static function AUser(){
        return self::user(auth('api')->check()?auth('api')->id():0);
    }
    public static function AInfo(){
        return self::info(auth('api')->check()?auth('api')->id():0);
    }
    public static function AIntro(){
        return self::intro(auth('api')->check()?auth('api')->id():0);
    }
}
