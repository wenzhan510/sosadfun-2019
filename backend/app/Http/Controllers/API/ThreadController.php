<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\Post;
use App\Http\Requests\StoreThread;
use App\Http\Resources\ThreadBriefResource;
use App\Http\Resources\ThreadInfoResource;
use App\Http\Resources\ThreadProfileResource;
use App\Http\Resources\PostIndexResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PaginateResource;
use App\Sosadfun\Traits\ThreadQueryTraits;
use App\Sosadfun\Traits\ThreadObjectTraits;
use App\Sosadfun\Traits\PostObjectTraits;
use App\Sosadfun\Traits\RecordRedirectTrait;
use App\Sosadfun\Traits\DelayRecordHistoryTraits;
use Cache;
use Carbon;
use ConstantObjects;
use CacheUser;

class ThreadController extends Controller
{
    use ThreadQueryTraits;
    use ThreadObjectTraits;
    use PostObjectTraits;
    use RecordRedirectTrait;
    use DelayRecordHistoryTraits;

    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show', 'show_profile', 'channel_index', 'thread_index']);
        $this->middleware('filter_thread')->only(['show', 'show_profile']);
    }

    public function test($id)
    {
        return view('welcome');
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $request_data = $this->sanitize_thread_request_data($request);

        if($request_data&&!auth('api')->check()){abort(401);}

        $query_id = $this->process_thread_query_id($request_data);

        $threads = $this->find_threads_with_query($query_id, $request_data);

        return response()->success([
            'threads' => ThreadInfoResource::collection($threads),
            'paginate' => new PaginateResource($threads),
            'request_data' => $request_data,
        ]);
    }

    public function thread_index(Request $request)
    {
        if($request->page&&!auth('api')->check()){abort(401);}

        $page = is_numeric($request->page)? $request->page:'1';
        $time = 10;
        if($page==1){$time=2;}
        $threads = Cache::remember('thread_index_P'.$page, $time, function () use($page) {
            return $threads = Thread::with('author', 'tags', 'last_post')
            ->isPublic()
            ->inPublicChannel()
            ->withoutType('book')
            ->ordered()
            ->paginate(config('preference.threads_per_page'))
            ->appends(['page'=>$page]);
        });
        $simple_threads = $this->jinghua_threads();

        return response()->success([
            'simple_threads' => ThreadInfoResource::collection($simple_threads),
            'threads' => ThreadInfoResource::collection($threads),
            'paginate' => new PaginateResource($threads),
        ]);
    }

    public function channel_index($channel, Request $request)
    {
        if(!auth('api')->check()&&$request->page){abort(401);}

        $channel = collect(config('channel'))->keyby('id')->get($channel);

        if($channel->id===config('constants.list_channel_id')&&$request->channel_mode==='review'){
            $request_data = $this->sanitize_review_posts_request_data($request);
            $query_id = $this->process_review_posts_query_id($request_data);
            $posts = $this->find_review_posts_with_query($query_id, $request_data);
            return response()->success([
                'posts' => PostIndexResource::collection($posts),
                'paginate' => new PaginateResource($posts),
                'request_data' => $request_data,
            ]);
        }

        $primary_tags = ConstantObjects::extra_primary_tags_in_channel($channel->id);

        $queryid = 'channel-index'
        .'-ch'.$channel->id
        .'-withBianyuan'.$request->withBianyuan
        .'-withTag'.$request->withTag
        .'-ordered'.$request->ordered
        .(is_numeric($request->page)? 'P'.$request->page:'P1');

        $time = 30;
        if(!$request->withTag&&!$request->ordered&&!$request->page){$time=2;}

        $threads = Cache::remember($queryid, $time, function () use($request, $channel) {
            return $threads = Thread::with('author', 'tags', 'last_post')
            ->isPublic()
            ->inChannel($channel->id)
            ->withBianyuan($request->withBianyuan)
            ->withTag($request->withTag)
            ->ordered($request->ordered)
            ->paginate(config('preference.threads_per_page'))
            ->appends($request->only('withBianyuan', 'ordered', 'withTag','page'));
        });

        $simplethreads = $this->find_top_threads_in_channel($channel->id);

        return response()->success([
            'channel' => $channel,
            'threads' => ThreadInfoResource::collection($threads),
            'primary_tags' => $primary_tags,
            'request_data' => $request->only('withBianyuan', 'ordered', 'withTag','page'),
            'simplethreads' => ThreadBriefResource::collection($simplethreads),
            'paginate' => new PaginateResource($threads),
        ]);

    }
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(StoreThread $form)//
    {
        $channel = collect(config('channel'))->keyby('id')->get($form->channel_id);
        if(!$channel||!auth('api')->user()){abort(404);}

        if(auth('api')->user()->no_posting){abort(403,'禁言中');}

        if($channel->type==='book'&&(auth('api')->user()->level<1||auth('api')->user()->quiz_level<1)&&!auth('api')->user()->isAdmin()){abort(403,'发布书籍，必须用户等级1以上，答题等级1以上');}

        if($channel->type<>'book'&&(auth('api')->user()->level<4||auth('api')->user()->quiz_level<2)&&!auth('api')->user()->isAdmin()){abort(403,'发布非书籍主题，必须用户等级4以上，答题等级2以上');}

        if(!$channel->is_public&&!auth('api')->user()->canSeeChannel($channel->id)){abort(403,'不能访问这个channel');}

        if(!auth('api')->user()->isAdmin()&&Cache::has('created-thread-' . auth('api')->id())){abort(410,"不能短时间频繁建立新主题");}

        //针对创建清单进行一个数值的限制
        if($channel->type==='list'){
            $list_count = Thread::where('user_id', auth('api')->id())->withType('list')->count();
            if($list_count > auth('api')->user()->user_level){abort(410,'额度不足，不能创建更多清单');}
        }
        if($channel->type==='box'){
            $box_count = Thread::where('user_id', auth('api')->id())->withType('box')->count();
            if($box_count >=1){abort(410,'目前每个人只能建立一个问题箱');}
        }

        $thread = $form->generateThread($channel);

        Cache::put('created-thread-' . auth('api')->id(), true, 10);

        if($channel->type==='list'&&auth('api')->user()->info->default_list_id===0){
            auth('api')->user()->info->update(['default_list_id'=>$thread->id]);
        }
        if($channel->type==='box'&&auth('api')->user()->info->default_box_id===0){
            auth('api')->user()->info->update(['default_box_id'=>$thread->id]);
        }

        $thread = $this->threadProfile($thread->id);

        return response()->success(new ThreadProfileResource($thread));
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $thread
    * @return \Illuminate\Http\Response
    */
    public function show($id, Request $request)
    {
        $show_config = $this->decide_thread_show_config($request);
        if($show_config['show_profile']){
            $thread = $this->threadProfile($id);
        }else{
            $thread = $this->findThread($id);
        }
        $thread->delay_count('view_count', 1);
        if(auth('api')->check()){
            $this->delay_record_thread_view_history(auth('api')->id(), $thread->id, Carbon::now());
        }

        $request_data = $this->sanitize_thread_post_request_data($request);

        if($request_data&&!auth('api')->check()){abort(401);}

        $query_id = $this->process_thread_post_query_id($request_data);

        $posts = $this->find_thread_posts_with_query($thread, $query_id, $request_data);

        $withReplyTo = '';
        if($request->withReplyTo>0){
            $withReplyTo = $this->findPost($request->withReplyTo);
            if($withReplyTo&&$withReplyTo->thread_id!=$thread->id){
                $withReplyTo = '';
            }
        }
        $inComponent = '';
        if($request->inComponent>0){
            $inComponent = $this->findPost($request->inComponent);
            if($inComponent&&$inComponent->thread_id!=$thread->id){
                $inComponent = '';
            }
        }

        return response()->success([
            'thread' => new ThreadProfileResource($thread),
            'withReplyTo' => $withReplyTo,
            'inComponent' => $inComponent,
            'posts' => PostResource::collection($posts),
            'request_data' => $request_data,
            'paginate' => new PaginateResource($posts),
        ]);

    }

    public function show_profile($id, Request $request)
    {
        if($request->review_redirect){
            $this->recordRedirectReviewCount($request->review_redirect);
        }
        $thread = $this->threadProfile($id);
        $posts = $this->threadProfilePosts($id);
        $thread->delay_count('view_count', 1);
        if(auth('api')->check()){
            $this->delay_record_thread_view_history(auth('api')->id(), $thread->id, Carbon::now());
        }
        return response()->success([
            'thread' => new ThreadProfileResource($thread),
            'posts' => PostResource::collection($posts),
        ]);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update($id, StoreThread $form) //TODO
    {
        $thread = Thread::on('mysql::write')->find($id);
        $thread = $form->updateThread($thread);
        $this->clearThread($id);
        $thread = $this->threadProfile($id);
        return response()->success(new ThreadProfileResource($thread));
    }


    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        if(!auth('api')->user()->isAdmin()&&(auth('api')->id()!=$thread->user_id||!$thread->channel()->allow_deletion||$thread->is_locked)){abort(403);}

        $thread->apply_to_delete();

        $this->clearThread($id);
        $thread = $this->threadProfile($id);

        return response()->success([
            'thread' => new ThreadProfileResource($thread),
        ]);
    }

    public function update_tag($id, Request $request)
    {
        $thread = Thread::on('mysql::write')->find($id);
        $user = auth('api')->user();
        if(!$thread||$thread->user_id!=$user->id||($thread->is_locked&&!$user->isAdmin())){abort(403);}

        $thread->drop_none_tongren_tags();//去掉所有本次能选的tag的范畴内的tag
        $thread->tags()->syncWithoutDetaching($thread->tags_validate($request->tags));//并入新tag. tags应该输入一个array of tags，前端进行一定的过滤

        $this->clearThread($id);
        $thread = $this->threadProfile($id);

        return response()->success([
            'thread' => new ThreadProfileResource($thread),
        ]);
    }
}
