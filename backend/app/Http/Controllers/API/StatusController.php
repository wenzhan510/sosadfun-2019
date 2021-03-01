<?php

namespace App\Http\Controllers\API;

use App\Models\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\NewStatus;
use App\Http\Requests\StoreStatus;
use App\Http\Resources\StatusResource;
use App\Http\Resources\PaginateResource;
use App\Sosadfun\Traits\StatusObjectTraits;
use Carbon;
use Cache;

class StatusController extends Controller
{
    use StatusObjectTraits;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $queryid = 'statusesIndex.'
        .(is_numeric($request->page)? 'P'.$request->page:'P1');

        $statuses = Cache::remember($queryid, 1, function () use($request) {
            return Status::with('author.title','attachable','last_reply')
            ->isPublic()
            ->isDirect()
            ->ordered()
            ->paginate(config('preference.statuses_per_page'))
            ->appends($request->only('page'));
        });

        return response()->success([
            'statuses' => StatusResource::collection($statuses),
            'paginate' => new PaginateResource($statuses),
        ]);
    }

    public function follow_status()
    {
        $statuses = Status::with('author.title','attachable')
        ->join('followers','followers.user_id','=','statuses.user_id')
        ->where('followers.follower_id','=',auth('api')->id())
        ->isPublic()
        ->isDirect()
        ->ordered()
        ->select('statuses.*')
        ->paginate(config('preference.statuses_per_page'));

        return response()->success([
            'statuses' => StatusResource::collection($statuses),
            'paginate' => new PaginateResource($statuses),
        ]);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreStatus $form)//
    {
        if(auth('api')->user()->no_posting){abort(412,'禁言中，不能发动态');}
        if(auth('api')->user()->level<4){abort(412,'等级不足，不能发动态');}
        $previous_status_count = Status::withUser(auth('api')->id())->isDirect()->laterThen(Carbon::now()->subHours(12))->count();

        if($previous_status_count>=3){abort(410,'短时间内不能发过多动态');}

        $status = $form->storeStatus();

        event(new NewStatus($status));

        $msg = $status->reward_check();

        $status = $this->statusProfile($status->id);

        return response()->success([
            'status' => new StatusResource($status),
        ]);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Status  $status
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $status = $this->statusProfile($id);
        if(!$status){abort(404);}
        if(!$status->is_public&&!auth('api')->user()->isAdmin()&&auth('api')->id()!=$status->user_id){abort(403,'动态隐藏，非本人无法查看');}
        return response()->success([
            'status' => new StatusResource($status),
        ]);
    }


    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Status  $status
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Status $status)
    {
        //
    }

    public function no_reply($id)
    {
        if(auth('api')->user()->no_posting){abort(412,'禁言中，不能修改动态');}

        $status = Status::on('mysql::write')->find($id);

        if(!auth('api')->user()->isAdmin()&&auth('api')->id()!=$status->user_id){abort(403,'不是自己的动态');}

        $status->no_reply = 1;
        $status->save();

        $this->clearStatus($status->id);

        $status = $this->statusProfile($status->id);

        return response()->success([
            'status' => new StatusResource($status),
        ]);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Status  $status
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        $status = Status::on('mysql::write')->find($id);
        if(!$status){abort(404);}
        if($status->user_id != auth('api')->id()){abort(403);}
        auth('api')->user()->retract('delete_status');
        $status->delete();
        $this->clearStatus($id);
        return response()->success('deleted status');
    }


}
