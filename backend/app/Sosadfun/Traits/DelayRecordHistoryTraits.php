<?php

namespace App\Sosadfun\Traits;

use App\Jobs\SaveViewHistoryData;
use App\Jobs\SaveUserActivityData;
use App\Jobs\SaveUserLoginData;

use Cache;

trait DelayRecordHistoryTraits
{
	public function delay_record_user_activity_history($user_id, $ip, $created_at){
		if(!Cache::has('TodayUserActivity'.$user_id)){
			$this->delay_record_history('DelayRecordUserActivityHistory', [
				'user_id' => $user_id,
				'ip' => $ip,
				'created_at' => $created_at,
			]);
			Cache::put('TodayUserActivity'.$user_id,1,1440);//每个用户1天(?)内只记录一次IP活动
		}

	}

	public function delay_record_thread_view_history($user_id, $thread_id, $created_at){

		if(!Cache::has('TodayUserViewThread-UID'.$user_id.'-TID'.$thread_id)){
			$this->delay_record_history('DelayRecordThreadViewHistory', [
				'user_id' => $user_id,
				'thread_id' => $thread_id,
				'created_at' => $created_at,
			]);
			Cache::put('TodayUserViewThread-UID'.$user_id.'-TID'.$thread_id,1,10080);//每个用户7天(?)内每本书的阅读只记录一次
		}
	}

	public function delay_record_user_login_history($user_id, $ip, $device, $created_at){
		$this->delay_record_history('DelayRecordUserLoginHistory', [
			'user_id' => $user_id,
			'ip' => $ip,
			'device' => $device,
			'created_at' => $created_at,
		]);
	}

	public function delay_record_history($case='', $data){
		$count = Cache::has($case.'-CacheCount')? (int)Cache::get($case.'-CacheCount'): 0;

		$old_data = Cache::has($case.'-CacheData') ? (array)Cache::get($case.'-CacheData'): [];
		$new_data = $old_data;
		array_push($new_data, $data);

		$count_interval = config('constants.delay_record_history_count_interval');
		if($case==='DelayRecordUserLoginHistory'){
			$count_interval = config('constants.delay_record_history_count_interval_long');
		}

		if($count>=$count_interval){

			if($case==='DelayRecordUserActivityHistory'){
				SaveUserActivityData::dispatch($new_data);
			}
			if($case==='DelayRecordThreadViewHistory'){
				SaveViewHistoryData::dispatch($new_data);
			}
			if($case==='DelayRecordUserLoginHistory'){
				SaveUserLoginData::dispatch($new_data);
			}

			Cache::forget($case.'-CacheData');
			Cache::put($case.'-CacheCount', 0, 10080);
		}else{
			Cache::put($case.'-CacheCount', $count+1, 10080);
			Cache::put($case.'-CacheData', $new_data, 10080);
		}

	}
}
