@extends('layouts.default')
@section('title', '通过邮箱获得邀请链接注册-查询注册进度')

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
                查询注册申请进度
            </div>
            @include('shared.errors')
            <div class="panel panel-default">
                <div class="panel-heading">
                    @include('auth.registration.by_invitation_email._steps')
                </div>
                <div class="panel-body font-6">
                    <h2>查询注册申请进度</h2>
                    <div class="font-6">
                        @include('auth.registration.by_invitation_email._application_basic_info')
                        @include('auth.registration.by_invitation_email._application_action')
                    </div>
                </div>
            </div>
            @if($application->quiz())
            <div class="panel panel-default">
                <div class="panel-heading">
                    问卷题目：{!! StringProcess::wrapParagraphs($application->quiz() ? $application->quiz()->body:'问题') !!}
                </div>
                <div class="panel-body font-6">
                    {!! StringProcess::wrapParagraphs($application->body) !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@stop
