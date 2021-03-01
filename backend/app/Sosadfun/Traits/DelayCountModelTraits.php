<?php
namespace App\Sosadfun\Traits;

use Cache;
use App\Jobs\UpdateModelValueChange;

trait DelayCountModelTraits{

    use DelayBatchCountModelTraits;

    public function delay_modify_attribute_for_model($model_type, $model_id, $attribute_name, $value)
    {
        $identifier = 'DelayedModelCount-'.$model_type.'-'.$model_id.'-'.$attribute_name;

		$old_value = Cache::has($identifier.'-CacheValue') ? (int)Cache::get($identifier.'-CacheValue'): 0;
		$new_value = $old_value + $value;

		if(abs($new_value)<1){
			return false;
		}

		if(!Cache::has($identifier.'-CacheInterval')){//假如距离上次cache的时间已经超过了默认时间，更新到数据库
            if($new_value<=config('constants.min_batch_count_model_value')){ // TODO here the value can be larger
                $this->delay_batch_count_model($model_type, $model_id, $attribute_name, $new_value);
            }else{
                UpdateModelValueChange::dispatch($model_type, $model_id, $attribute_name, $new_value);
            }
			Cache::put($identifier.'-CacheValue', 0, 10080);
			Cache::put($identifier.'-CacheInterval', 1, config('constants.delay_count_model_interval'));
		}else{ // 否则的话，先单独存储这个值
			Cache::put($identifier.'-CacheValue',$new_value, 10080);
		}

        return $new_value;
    }
}
