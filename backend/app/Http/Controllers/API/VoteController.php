<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVote;
use App\Http\Resources\VoteResource;
use App\Sosadfun\Traits\FindModelTrait;
use App\Http\Resources\PaginateResource;
use Cache;
use CacheUser;

class VoteController extends Controller
{
    use FindModelTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    private function findVotableModel($request)
    {
        if (!array_key_exists('votable_type', $request)
        || !array_key_exists('votable_id', $request)){
            return false;
        }
        return $this->findModel(
            $request['votable_type'],
            $request['votable_id'],
            array('post','quote','status','thread')
        );
    }

    private function findVoteRecord($request)
    {
        return $request['votable_type'].$request['votable_id'].$request['attitude_type'];
    }

    public function index(Request $request)
    {
        $type = $request->votable_type;
        $model=$this->findVotableModel($request->all());
        if(!$model){abort(404);}

        $page = is_numeric($request->page)? 'P'.$request->page:'P1';
        $votes = Cache::remember('upvoteindex.'.$request->votable_type.$request->votable_id.$page, 15, function () use($request){
            $votes = Vote::with('author')
            ->withType($request->votable_type)
            ->withId($request->votable_id)
            ->withAttitude('upvote') // TODO 非管理员只能看到赞，管理可以看到其他评分
            ->orderBy('created_at','desc')
            ->paginate(config('preference.votes_per_page'))
            ->appends($request->only(['votable_type','votable_id']));
            return $votes;
        });

        return response()->success([
            'votes' => VoteResource::collection($votes),
            'paginate' => new PaginateResource($votes),
            'request_data' => $request->only('votable_type','votable_id'),
        ]);
    }

    public function store(StoreVote $form)
    {
        $voted_model=$this->findVotableModel($form->all());
        if(empty($voted_model)){abort(404);} //检查被投票的对象是否存在
        if(Cache::has('VoteRecord'.auth('api')->id().$this->findVoteRecord($form->all()))){abort(409);}

        $vote = $form->generateVote($voted_model);

        Cache::put('VoteRecord'.auth('api')->id().$this->findVoteRecord($form->all()),1,1440);

        return response()->success(new VoteResource($vote->load('author')));
    }


    public function destroy(Vote $vote)
    {
        if(!$vote){
            abort(404);
        }
        if($vote->user_id!=auth('api')->id()){
            abort(403);
        }
        $vote->delete();
        // TODO：有待补充递减被投票用户的票数（分值系统一起做），同时应该给投票人以惩罚
        return response()->success('deleted');
    }

    public function received($id, Request $request)
    {
        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        $info->clear_column('upvote_reminders');
        $votes = Vote::with('votable','author')
        ->where('receiver_id',$user->id)
        ->withAttitude('upvote')
        ->orderBy('created_at','desc')
        ->paginate(config('preference.votes_per_page'));
        return response()->success([
            'votes' => VoteResource::collection($votes),
            'paginate' => new PaginateResource($votes),
        ]);

    }

    public function sent($id, Request $request)
    {
        $user = CacheUser::user($id);
        $info = CacheUser::info($id);
        $votes = Vote::with('votable','receiver')
        ->where('user_id',$user->id)
        ->orderBy('created_at','desc')
        ->paginate(config('preference.votes_per_page'));
        return response()->success([
            'votes' => VoteResource::collection($votes),
            'paginate' => new PaginateResource($votes),
        ]);
    }
}
