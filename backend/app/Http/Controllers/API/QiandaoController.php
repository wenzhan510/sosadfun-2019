<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CheckinResource;
use App\Http\Resources\UserInfoResource;
use App\Sosadfun\Traits\QiandaoTrait;
use Auth;
use Cache;
use Carbon;

class QiandaoController extends Controller
{
    use QiandaoTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function qiandao()
    {
        $user = auth('api')->user();
        $info = $user->info;
        // a new day starts at 22:00
        if($info->qiandao_at > Carbon::today()->subHours(2)->toDateTimeString()) {
            abort(409, '已领取奖励，请勿重复签到');
        }
        $info = $user->info;
        $checkin_result = $this->checkin($user);
        return response()->success(new CheckinResource($checkin_result));
    }

    public function complement_qiandao()
    {
        // 补签
        $user = auth('api')->user();
        $info = $user->info;
        if($info->qiandao_reward_limit <= 0){
            abort(412, '补签额度不足');
        }

        if($info->qiandao_continued >= $info->qiandao_last){
            abort(412, '你的连续签到天数不少与上次断签天数，无需补签');
        }

        if($info->qiandao_last == 0){
            abort(412, '未发现断签，无需补签');
        }

        $this->complement_checkin($user);
        return response()->success(new UserInfoResource($info));
    }
}
