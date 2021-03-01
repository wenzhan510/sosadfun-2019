<?php
namespace App\Observers;

use Cache;
use App\Models\User;

/**
 * User observer
 */
class UserObserver
{
    public function saved(User $user)
    {
        Cache::forget("cachedUser.{$user->id}");
    }

}
