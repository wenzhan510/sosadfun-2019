<?php
namespace App\Sosadfun\Traits;

use DB;
use Cache;
use ConstantObjects;
use App\Models\Thread;
use StringProcess;

trait ThreadQueryTraits{

    public function jinghua_threads()
    {
        return Cache::remember('jinghua-threads', 10, function () {
            $jinghua_tag = ConstantObjects::find_tag_by_name('精华');
            return \App\Models\Thread::with('author','tags')
            ->isPublic()
            ->inPublicChannel()
            ->withTag($jinghua_tag->id)
            ->inRandomOrder()
            ->take(3)
            ->get();
        });
    }

    public function find_top_threads_in_channel($id)
    {
        return Cache::remember('top_threads_in_channel.'.$id, 30, function () use($id) {
            $zhiding_tag = ConstantObjects::find_tag_by_name('置顶');
            return \App\Models\Thread::with('author','tags')
            ->inChannel($id)
            ->withTag($zhiding_tag->id)
            ->get();
        });
    }

    public function process_thread_query_id($request_data)
    {
        $queryid = '';
        $selectors = ['inChannel', 'isPublic', 'inPublicChannel', 'withType', 'withBianyuan', 'withTag', 'excludeTag', 'ordered', 'page'];
        foreach($selectors as $selector){
            if(array_key_exists($selector, $request_data)&&$request_data[$selector]){
                $queryid.='-'.$selector.':'.$request_data[$selector];
            }
        }
        return $queryid;
    }

    public function sanitize_thread_request_data($request)
    {
        $request_data = $request->only('inChannel', 'isPublic', 'inPublicChannel',  'withType', 'withBianyuan', 'withTag', 'excludeTag', 'ordered', 'page');
        if((!auth('api')->check()||!auth('api')->user()->isAdmin())&&$request->isPublic){
            $request_data['isPublic']='';
        }
        if((!auth('api')->check()||!auth('api')->user()->isAdmin())&&$request->inPublicChannel){
            $request_data['inPublicChannel']='';
        }
        if((!auth('api')->check()||auth('api')->user()->level<3)&&$request->withBianyuan){
            $request_data['withBianyuan']='';
        }
        return $request_data;
    }

    public function sanitize_book_request_data($request)
    {
        $request_data = $request->only('inChannel', 'isPublic', 'inPublicChannel',  'withType', 'withBianyuan', 'withTag', 'excludeTag', 'ordered', 'page');
        if((!auth('api')->check()||auth('api')->user()->level<3)&&$request->withBianyuan){
            $request_data['withBianyuan']='';
        }
        return $request_data;
    }

    public function find_threads_with_query($query_id, $request_data)
    {
        return Cache::remember('ThreadQ.'.$query_id, 30, function () use($request_data) {
            return Thread::brief()
            ->with('author', 'tags', 'last_post')
            ->withTag(array_key_exists('withTag',$request_data)? $request_data['withTag']:'')
            ->excludeTag(array_key_exists('excludeTag',$request_data)? $request_data['excludeTag']:'')
            ->inChannel(array_key_exists('inChannel',$request_data)? $request_data['inChannel']:'')
            ->inPublicChannel(array_key_exists('inPublicChannel',$request_data)? $request_data['inPublicChannel']:'')
            ->withType(array_key_exists('withType',$request_data)? $request_data['withType']:'')
            ->isPublic(array_key_exists('isPublic',$request_data)? $request_data['isPublic']:'')
            ->withBianyuan(array_key_exists('withBianyuan',$request_data)? $request_data['withBianyuan']:'') //
            ->ordered(array_key_exists('ordered',$request_data)? $request_data['ordered']:'latest_add_component')
            ->paginate(config('preference.threads_per_page'))
            ->appends($request_data);
        });
    }

    public function find_books_with_query($query_id, $request_data)
    {
        $time = 60;
        if(!array_key_exists('withTag',$request_data)&&!array_key_exists('excludeTag',$request_data)&&!array_key_exists('ordered',$request_data)&&!array_key_exists('page',$request_data)){$time=5;}
        return Cache::remember('BookQ.'.$query_id, $time, function () use($request_data) {
            return $threads = Thread::brief()
            ->with('author', 'tags', 'last_component')
            ->withTag(array_key_exists('withTag',$request_data)? $request_data['withTag']:'')
            ->excludeTag(array_key_exists('excludeTag',$request_data)? $request_data['excludeTag']:'')
            ->withType('book')
            ->inChannel(array_key_exists('inChannel',$request_data)? $request_data['inChannel']:'')
            ->withBianyuan(array_key_exists('withBianyuan',$request_data)? $request_data['withBianyuan']:'') //
            ->isPublic()
            ->ordered(array_key_exists('ordered',$request_data)? $request_data['ordered']:'latest_add_component')
            ->paginate(config('preference.threads_per_page'))
            ->appends($request_data);
        });
    }

    public function convert_book_request_data($request)
    {
        $request_data = $request->only('withBianyuan', 'ordered');
        $withTag='';
        $inChannel='';
        $excludeTag='';

        if($request->channel_id){
            $inChannel=StringProcess::concatenate_channels($request->channel_id);
        }

        if($request->book_length_tag){
            $withTag=StringProcess::concatenate_andTag($request->book_length_tag, $withTag);
        }
        if($request->book_status_tag){
            $withTag=StringProcess::concatenate_andTag($request->book_status_tag, $withTag);
        }
        if($request->sexual_orientation_tag){
            $withTag=StringProcess::concatenate_andTag($request->sexual_orientation_tag, $withTag);
        }
        if($request->withTag){
            $withTag=StringProcess::concatenate_andTag($request->withTag, $withTag);
        }

        if($request->excludeTag){
            $excludeTag=StringProcess::concatenate_excludeTag($request->excludeTag, $excludeTag);
        }

        if($inChannel){
            $request_data = array_merge(['inChannel'=>$inChannel],$request_data);
        }
        if($withTag){
            $request_data = array_merge(['withTag'=>$withTag],$request_data);
        }
        if($excludeTag){
            $request_data = array_merge(['excludeTag'=>$excludeTag],$request_data);
        }

        return $request_data;
    }

    public function sanitize_thread_post_request_data($request)
    {
        $request_data = $request->only('withType', 'withComponent', 'withFolded', 'userOnly', 'withReplyTo', 'inComponent', 'ordered', 'page');
        return $request_data;
    }

    public function process_thread_post_query_id($request_data)
    {
        $queryid = url('/');
        $selectors = ['withType', 'withComponent', 'withFolded', 'userOnly', 'withReplyTo', 'inComponent', 'ordered', 'page'];
        foreach($selectors as $selector){
            if(array_key_exists($selector, $request_data)&&$request_data[$selector]){
                $queryid.='-'.$selector.':'.$request_data[$selector];
            }
        }
        return $queryid;
    }

    public function find_thread_posts_with_query($thread, $query_id, $request_data)
    {

        return Cache::remember('ThreadPosts.'.$thread->id.$query_id, 5, function () use($thread, $request_data) {
            $posts =  \App\Models\Post::with('author.title','last_reply')

            ->where('thread_id',$thread->id)

            ->userOnly(array_key_exists('userOnly',$request_data)? $request_data['userOnly']:'')//可以只看某用户（这样选的时候，默认必须同时属于非匿名）
            ->withReplyTo(array_key_exists('withReplyTo',$request_data)? $request_data['withReplyTo']:'')//可以只看用于回复某个回帖的
            ->inComponent(array_key_exists('inComponent',$request_data)? $request_data['inComponent']:'')//可以只看从这个贴发散的全部讨论


            ->withType(array_key_exists('withType',$request_data)? $request_data['withType']:'')// posts.type 可以筛选显示比如只看post，只看comment，只看。。。
            ->withComment(array_key_exists('withComment',$request_data)? $request_data['withComment']:'')// posts.is_comment 可以选择是否显示comment

            ->withComponent(array_key_exists('withComponent',$request_data)? $request_data['withComponent']:'')// posts.type 可以选择是只看component，还是不看component只看post，还是全都看

            ->withFolded(array_key_exists('withFolded',$request_data)? $request_data['withFolded']:'')//posts.fold_state 是否显示已折叠内容

            ->ordered(array_key_exists('ordered',$request_data)? $request_data['ordered']:'')//排序方式
            ->paginate(config('preference.posts_per_page'))
            ->appends($request_data);
            $channel = $thread->channel();
            if($channel->type==='book'){
                $posts->load('info');
            }
            if($channel->type==='list'||$channel->type==='report'){
                $posts->load('info.reviewee');
            }
            return $posts;
        });
    }

    public function clear_thread_posts_with_query($thread_id, $request){
        $request_data = $this->sanitize_thread_post_request_data($request);
        $query_id = $this->process_thread_post_query_id($request_data);
        return Cache::forget('ThreadPosts.'.$thread_id.$query_id);
    }


    public function sanitize_review_posts_request_data($request)
    {
        $request_data = $request->only('channel_mode', 'withBianyuan', 'reviewType', 'withSummary', 'withLength',  'ordered', 'page');
        if(!Auth::check()||Auth::user()->level<3){
            $request_data['withBianyuan']='';
        }
        return $request_data;
    }

    public function process_review_posts_query_id($request_data)
    {
        $queryid = url('/');
        $selectors = ['channel_mode', 'withBianyuan', 'reviewType', 'withSummary', 'withLength',  'ordered', 'page'];
        foreach($selectors as $selector){
            if(array_key_exists($selector, $request_data)){
                $queryid.='-'.$selector.':'.$request_data[$selector];
            }
        }
        return $queryid;
    }

    public function find_review_posts_with_query($query_id, $request_data)
    {
        $time = 5;

        return Cache::remember('ReviewPosts.'.$query_id, $time, function () use($request_data) {
            $posts =  \App\Models\Post::join('post_infos','posts.id','=','post_infos.post_id')
            ->join('threads','threads.id','=','posts.thread_id')

            ->withType('review') // post.type===review
            ->withBianyuan(array_key_exists('withBianyuan',$request_data)? $request_data['withBianyuan']:'') //posts.is_bianyuan
            ->withLength(array_key_exists('withLength',$request_data)? $request_data['withLength']:'')// posts.len

            ->reviewType(array_key_exists('reviewType',$request_data)? $request_data['reviewType']:'')// post_infos.reviewee_id >0 or ===0 站内还是站外
            ->withSummary(array_key_exists('withSummary',$request_data)? $request_data['withSummary']:'')// post_infos.summary 'editorRec','recommend'

            ->isPublic() // threads.is_public

            ->ordered(array_key_exists('ordered',$request_data)? $request_data['ordered']:'latest_created')//排序方式（默认显示最新创建）
            ->select('posts.*')
            ->paginate(config('preference.reviews_per_page'))
            ->appends($request_data);

            $posts->load('simpleInfo.reviewee');

            return $posts;
        });
    }


    public function sanitize_report_case_posts_request_data($request)
    {
        $request_data = $request->only('channel_mode', 'withBianyuan', 'caseType', 'withSummary', 'ordered', 'page');
        if(!Auth::check()||Auth::user()->level<3){
            $request_data['withBianyuan']='';
        }
        return $request_data;
    }

    public function process_case_posts_query_id($request_data)
    {
        $queryid = url('/');
        $selectors = ['channel_mode', 'withBianyuan', 'caseType', 'withSummary', 'ordered', 'page'];
        foreach($selectors as $selector){
            if(array_key_exists($selector, $request_data)){
                $queryid.='-'.$selector.':'.$request_data[$selector];
            }
        }
        return $queryid;
    }


    public function find_case_posts_with_query($query_id, $request_data)
    {
        $time = 5;

        return Cache::remember('CasePosts.'.$query_id, $time, function () use($request_data) {
            $posts =  \App\Models\Post::join('post_infos','posts.id','=','post_infos.post_id')

            ->withType('case') // posts.type
            ->withBianyuan(array_key_exists('withBianyuan',$request_data)? $request_data['withBianyuan']:'') // posts.is_bianyuan

            ->caseType(array_key_exists('caseType',$request_data)? $request_data['caseType']:'') //post_infos.summary
            ->withSummary(array_key_exists('withSummary',$request_data)? $request_data['withSummary']:'') // post_infos.summary

            ->ordered(array_key_exists('ordered',$request_data)? $request_data['ordered']:'latest_created')//排序方式（默认显示最新创建）
            ->select('posts.*')
            ->paginate(config('preference.cases_per_page'))
            ->appends($request_data);

            $posts->load('simpleInfo.reviewee');

            return $posts;
        });
    }
}
