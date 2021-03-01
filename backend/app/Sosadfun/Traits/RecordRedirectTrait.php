<?php

namespace App\Sosadfun\Traits;

use App\Sosadfun\Traits\DelayCountModelTraits;

trait RecordRedirectTrait
{
	use DelayCountModelTraits;

	public function recordRedirectReviewCount($id){
		return $this->delay_modify_attribute_for_model('App\Models\PostInfo', $id, 'redirect_count', 1);
	}
}
