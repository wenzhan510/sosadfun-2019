<?php

namespace App\Models\Traits;

use App\Sosadfun\Traits\DelayCountModelTraits;

trait DelayCountTrait
{
	use DelayCountModelTraits;
	
	public function delay_count($attribute_name, $value){
		return $this->delay_modify_attribute_for_model(get_class($this), $this->getKey(), $attribute_name, $value);
	}
}
