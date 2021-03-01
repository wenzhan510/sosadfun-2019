<?php

namespace App\Http\Middleware;

use Closure;
use App\Sosadfun\Traits\FindThreadTrait;

class FilterThread
{
    use FindThreadTrait;
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        $thread = $this->findThread($request->route('thread'));
        if(!$thread){abort(404);}
        $channel= $thread->channel();
        if(!$channel){ abort(404);}

        if($channel->is_public&&$thread->is_public&&!$thread->is_bianyuan){// 公共非边
            return $next($request);
        }

        if($channel->is_public&&$thread->is_public&&$thread->is_bianyuan){// 公共边
            if(auth('api')->check()&&!auth('api')->user()->activated){
                abort(416, '请激活邮箱后再访问');
            }
            return $next($request);
        }

        if(!auth('api')->check()){ //并非公共的，都需要登陆
            abort(401, '请先登陆再访问');
        }

        if($thread->user_id===auth('api')->id()||auth('api')->user()->isAdmin()){ //本人，或管理，可以看
            return $next($request);
        }

        if($thread->is_public&&$thread->channel()->type==='homework'){
            if(auth('api')->user()->no_homework){abort(497);}
            $homework_registration = $thread->find_homework_registration_via_thread();
            if(!$homework_registration){abort(403, '本帖暂无权限访问');}
            if(auth('api')->user()->canSeeHomework($homework_registration->homework)){
                return $next($request);
            }
            return response()->error([
                'message' => "暂不具有阅读权限，需购买才可阅读",
                'homework_registration' => [
                    'id' => $homework_registration->id,
                    'type' => 'homework_registration',
                    'homework_id' => $homework_registration->homework_id,
                    ],
            ],417);
        }

        if(!$thread->is_public){
            abort(413,'文章或讨论处于隐藏状态');
        }
        abort(403,'权限不足');
    }
}
