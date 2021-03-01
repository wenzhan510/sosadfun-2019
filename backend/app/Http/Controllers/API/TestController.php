<?php

namespace App\Http\Controllers\API;

use App\Jobs\TestJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Store a new podcast.
     *
     * @param  Request  $request
     * @return Response
     */
    public function test($id)
    {
        return view('welcome');
    }
}
