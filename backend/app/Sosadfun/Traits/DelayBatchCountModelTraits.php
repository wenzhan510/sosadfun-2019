<?php
namespace App\Sosadfun\Traits;

use Cache;
use App\Jobs\BatchUpdateModelValueChange;

trait DelayBatchCountModelTraits{

	public function delay_batch_count_model($model_type, $model_id, $attribute_name, $value){
        $case = 'DelayBatchCount-'.$model_type.'-'.$attribute_name.'-'.$value;
		$count = Cache::has($case.'-CacheCount')? (int)Cache::get($case.'-CacheCount'): 0;

		$old_data = Cache::has($case.'-CacheData') ? (array)Cache::get($case.'-CacheData'): [];
		$new_data = $old_data;
		array_push($new_data, $model_id);

		$count_interval = config('constants.delay_record_history_count_interval');

		if($count>=$count_interval){
            BatchUpdateModelValueChange::dispatch($model_type, $new_data, $attribute_name, $value);
			Cache::forget($case.'-CacheData');
			Cache::put($case.'-CacheCount', 0, 10080);
		}else{
			Cache::put($case.'-CacheCount', $count+1, 10080);
			Cache::put($case.'-CacheData', $new_data, 10080);
		}

	}
}
