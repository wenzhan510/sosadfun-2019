<?php

namespace App\Models\Traits;

trait TypeValueChangeTrait
{
	public function type_value_change($type='',$value=0)
    {
        if(!in_array($type, $this->count_types)){return;}
		$new_value = $this->{$type}+$value;
        $this->update([
            $type => $new_value
        ]);
        return $new_value;
    }
}
