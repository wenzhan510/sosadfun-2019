<?php

namespace App\Models\Traits;

use Carbon;
use Auth;

trait VoteTrait
{
	public function votes(){
		return $this->morphMany('App\Models\Vote','votable');
	}
}
