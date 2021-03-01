@extends('layouts.default')
@section('title', '通过邮箱获得邀请链接注册-第四步-完成注册邀请问卷')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <!-- 导航 -->
            <div class="">
                <a type="btn btn-lg btn-danger sosad-button" href="{{ route('home') }}"><span class="glyphicon glyphicon-home"></span><span>{{__('pageview.homepage')}}</span></a>
                /
                <a href="{{ route('register_by_invitation') }}">邀请注册</a>
                /
                <a href="{{route('register.by_invitation_email.submit_email_form')}}">通过邮箱获得邀请链接注册</a>
                /
                【步骤4】完成注册邀请问卷
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    @include('auth.registration.by_invitation_email._steps')
                </div>

                <div class="panel-body">
                    @include('shared.errors')
                    <h2>【步骤4】完成注册邀请问卷</h2>
                    <form action="{{ route('register.by_invitation_email.submit_essay') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input name="email" value="{{$application->email}}" class="hidden">
                            <input value="{{$application->email}}" class="form-control" disabled>
                            <h6 class="warning-tag">（邮箱无法更改，如果这不是你的当前可用邮箱，请勿继续。）</h6>
                            <br>
                            <div class="font-4">
                                感谢你回答以上问题。废文网欢迎志同道合的朋友加入，但是如果理念不合，我们觉得没有必要强留。一直以来，废文网致力于打造一个比较自由的创作与阅读天地，想要加入这里的你，想必也对文学怀抱着一份热爱，接下来
                                <span class="">
                                    {!! StringProcess::wrapSpan($application->quiz()? $application->quiz()->body:'缓存未能更新，请稍后重新访问本页面') !!}
                                </span>
                            </div>
                            <div class="font-6 grayout">
                                （友情提醒，简答题目从当前题库中随机抽取，请<u><b>仔细审题</b></u>。为免数据丢失，建议你在其他地方写完问题后，复制粘贴到本页面提交。请务必<code>认真、独立</code>完成问卷的每一项内容，不抄袭，不敷衍。恶意注册的邮箱和IP将进入<u><b>黑名单</b></u>。）
                            </div>
                            <label class="control-label">申请正文：</label>
                            <div class="">
                                <textarea name="application" id="application" rows="20" class="form-control" placeholder="{{StringProcess::wrapSpan($application->quiz()? $application->quiz()->body:'缓存未能更新，请稍后重新访问本页面')}}">{{ old('application') }}</textarea>
                                <button href="#" type="button" onclick="wordscount('application');return false;" class="pull-right sosad-button-control addon-button">字数统计</button>
                            </div>
                        </div>
                        <a href="#" data-toggle="modal" data-target="#submitEssay" class="btn btn-lg btn-info sosad-button">提交申请</a>

                        <div class="modal fade" id="submitEssay" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="form-group">
                                        <h6>申请问卷一经提交【无法修改】，切勿匆忙作答！请确认完成后再提交。如果你暂时无法完成问卷，可以关闭页面，稍后只要从入口输入同一个邮箱即可继续完成问卷（无需重新做题）。</h6>
                                        <h6>请注意，如发生<span class="warning-tag">抄袭，换邮箱重复申请</span>等情况，邮箱和IP将进入黑名单。</h6>
                                        <label>如果你确定已完成问卷，且之前未曾以【其他邮箱】完成过问卷，请在此处输入<code>已完成</code>字样，确认问卷已最终完成：</label>
                                        <input type="text" name="finished" class="form-control" value="">
                                    </div>

                                    <button type="submit" class="btn btn-primary sosad-button btn-lg">
                                        确认提交申请
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
