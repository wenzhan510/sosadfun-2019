@extends('layouts.default')
@section('title', '主题贴高级管理')

@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        @include('shared.errors')
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2>管理讨论帖/书籍</h2>
                <h2><a href="{{route('thread.show',$thread->id)}}">{{ $thread->title }}</a></h2>

            </div>
            <div class="panel-body">
                <form action="{{ route('admin.threadmanagement',$thread->id)}}" method="POST">
                    {{ csrf_field() }}
                    <div class="admin-symbol">
                        <h3>管理员权限专区</h3>
                    </div>
                    @if(!$thread->is_locked)
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="1">锁帖</label>
                    </div>
                    @else
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="2">解锁</label>
                    </div>
                    @endif

                    @if($thread->is_public)
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="3">转私密</label>
                    </div>
                    @else
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="4">转公开</label>
                    </div>
                    @endif

                    @if(!$thread->is_bianyuan)
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="15">转边缘</label>
                    </div>
                    @else
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="16">转非边缘</label>
                    </div>
                    @endif

                    @if(!$thread->no_reply)
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="21">禁止回复</label>
                    </div>
                    @else
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="22">允许回复</label>
                    </div>
                    @endif

                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="40">帖子上浮（顶帖）</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="41">帖子下沉（踩贴）</label>
                    </div>

                    @if(!$thread->recommended)
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="42">添加推荐</label>
                    </div>
                    @else
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="43">取消推荐</label>
                    </div>
                    @endif

                    @if(!$thread->tags->contains('tag_name', '精华'))
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="44">添加精华</label>
                    </div>
                    @else
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="45">取消精华</label>
                    </div>
                    @endif

                    @if(!$thread->tags->contains('tag_name', '置顶'))
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="46">添加置顶</label>
                    </div>
                    @else
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="47">取消置顶</label>
                    </div>
                    @endif

                    @if(!$thread->is_locked)
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="60">锁+隐+禁7+清</label>
                        <span class="font-6">如断头车、恶意发文升级</span>
                    </div>
                    @endif
                    <hr>
                    @if($thread->channel()->type==='list')
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="111">将全楼标记为当前编推</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="112">将全楼标记为往期编推</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="113">将全楼标记为专题推荐</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="114">将全楼标记为非编推</label>
                    </div>
                    <hr>
                    @endif

                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="121">将本文标记为当前编推</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="122">将本文标记为往期编推</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="123">将本文标记为专题推荐</label>
                    </div>
                    <div class="radio">
                        <label><input type="radio" name="controlthread" value="124">将本文标记为非编推</label>
                    </div>
                    <hr>

                    <label><input type="radio" name="controlthread" value="9">转换板块（注意，如果点选了下面其他选项，记得回头把这个选一下）</label>
                    @foreach(collect(config('channel')) as $channel)
                    <div class="">
                        <label class="radio-inline"><input type="radio" name="channel" value="{{$channel->id}}">{{__('channel_name.'.$channel->id)}}</label>
                    </div>
                    @endforeach
                    <div class="radio">
                        <label class="pull-right admin-symbol"><input type="radio" name="controlthread" value="5">删除主题</label>
                    </div>
                    <div class="form-group">
                        <label for="reason"></label>
                        <textarea name="reason"  rows="3" class="form-control" placeholder="请输入处理理由，方便查看管理记录，如“涉及举报，标题简介违规”，“涉及举报，不友善”，“边限标记不合规”。"></textarea>
                    </div>
                    <div class="form-group admin-symbol">
                        <label><input type="checkbox" name="not_public" value="true">本条管理状态不公开</label>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-danger sosad-button btn-md admin-button">确定管理</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
