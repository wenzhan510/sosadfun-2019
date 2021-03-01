<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
    * Bootstrap services.
    *
    * @return void
    */
    public function boot()
    {
        Response::macro('success', function ($data='') {
            return Response::json([
                'code'  => 200,
                'data' => $data,
            ], 200);
        });

        Response::macro('error', function ($message, $status = 400) {
            return Response::json([
                'code'  => $status,
                'data' => $message,
            ], $status);
        });
    }

    /**
    * Register services.
    *
    * @return void
    */
    public function register()
    {
        //
    }
}
