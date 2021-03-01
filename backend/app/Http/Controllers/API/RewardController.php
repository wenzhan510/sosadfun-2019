<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Reward;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReward;
use App\Http\Resources\RewardResource;
use App\Sosadfun\Traits\FindModelTrait;
use App\Http\Resources\PaginateResource;

use Cache;
use CacheUser;

class RewardController extends Controller
{
    //
    use FindModelTrait;
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    private function findRewardableModel($request)
    {
        if (!array_key_exists('rewardable_type', $request)
        || !array_key_exists('rewardable_id', $request)){
            return false;
        }
        return $this->findModel(
            $request['rewardable_type'],
            $request['rewardable_id'],
            array('post', 'thread', 'quote', 'status')
        );
    }

    public function index(Request $request)
    {

        $model=$this->findRewardableModel($request->all());
        if(!$model){abort(404);}

        $page = is_numeric($request->page)? 'P'.$request->page:'P1';
        $rewards = Cache::remember('rewardindex.'.$request->rewardable_type.$request->rewardable_id.$page, 15, function () use($request){
            $rewards = \App\Models\Reward::with('author')
            ->withType($request->rewardable_type)
            ->withId($request->rewardable_id)
            ->orderBy('created_at','desc')
            ->paginate(config('preference.rewards_per_page'))
            ->appends($request->only(['rewardable_type','rewardable_id']));
            return $rewards;
        });

        return response()->success([
            'rewards' => RewardResource::collection($rewards),
            'paginate' => new PaginateResource($rewards),
            'request_data' => $request->only('rewardable_type','rewardable_id'),
        ]);
    }

    public function store(StoreReward $form)
    {
        $rewarded_model=$this->findRewardableModel($form->all());
        if(empty($rewarded_model)){abort(404);} //检查被投票的对象是否存在
        if($rewarded_model->deletion_applied_at){abort(413, '申请删除中，无法进行打赏');}

        $reward = $form->generateReward($rewarded_model);
        return response()->success(new RewardResource($reward));
    }


    public function destroy(Reward $reward)
    {
        if(!$reward){abort(404);}
        if($reward->user_id!=auth('api')->id()){abort(403);}
        $reward_id = $reward->id;
        $reward->delete();
        return response()->success([
            'success' => "成功删除已有打赏",
            'reward_id' => $reward_id,
        ]);
    }

    public function received($id, Request $request)
    {
        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        $info->clear_column('reward_reminders');
        $rewards = Reward::with('rewardable','author')
        ->where('receiver_id',$user->id)
        ->orderBy('created_at','desc')
        ->paginate(config('preference.rewards_per_page'));
        return response()->success([
            'rewards' => RewardResource::collection($rewards),
            'paginate' => new PaginateResource($rewards),
        ]);

    }

    public function sent($id, Request $request)
    {
        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        $rewards = Reward::with('rewardable','receiver')
        ->where('user_id',$user->id)
        ->orderBy('created_at','desc')
        ->paginate(config('preference.rewards_per_page'));
        return response()->success([
            'rewards' => RewardResource::collection($rewards),
            'paginate' => new PaginateResource($rewards),
        ]);
    }
}
