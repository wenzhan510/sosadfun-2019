<?php
namespace App\Observers;

use App\Models\RegistrationApplication;
use Cache;


/**
 * User observer
 */
class RegAppObserver
{
    public function saved(RegistrationApplication $regapp)
    {
        Cache::forget('findApplicationViaEmail.'.$regapp->email);
    }

}
