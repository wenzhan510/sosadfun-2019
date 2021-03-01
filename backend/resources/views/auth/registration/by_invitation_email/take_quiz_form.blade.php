@extends('layouts.default')
@section('title', '通过邮箱获得邀请链接注册-第二步-回答注册测试题')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        @include('shared.errors')
        <!-- 导航 -->
        <div class="">
            <a type="btn btn-lg btn-danger sosad-button" href="{{ route('home') }}"><span class="glyphicon glyphicon-home"></span><span>{{__('pageview.homepage')}}</span></a>
            /
            <a href="{{ route('register_by_invitation') }}">邀请注册</a>
            /
            <a href="{{route('register.by_invitation_email.submit_email_form')}}">通过邮箱获得邀请链接注册</a>
            /
            【步骤2】回答注册测试题
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                @include('auth.registration.by_invitation_email._steps')
            </div>
            <div class="panel-body">
                <h2>【步骤2】回答注册测试题</h2>
                <h3 class="warning-tag">（{{config('constants.registration_quiz_total')}}题中只需答对{{config('constants.registration_quiz_correct')}}题即可进入下一步）</h3>
                <h4>你好！欢迎你前来废文！因为当前排队人数较多，为了避免误入、囤号和机器批量注册，保证真正的申请者能够进入排队队列，请先回答下列问题哦!</h4>

                <form method="POST" action="{{ route('register.by_invitation_email.take_quiz') }}">
                    {{ csrf_field() }}
                    <input name="email" value="{{$application->email}}" class="hidden">
                    <input value="{{$application->email}}" class="form-control" disabled>
                    <h6 class="warning-tag">（邮箱无法更改，如果这不是你的当前可用邮箱，请勿继续。）</h6>
                    @foreach ($quizzes as $quiz_key=> $quiz)
                    <div class="h4">
                        <span><strong>第{{ $quiz_key+1 }}题：</strong></span>
                    </div>
                    <div class="h4">
                        {!! StringProcess::wrapSpan($quiz->body) !!}
                    </div>
                    @if($quiz->hint)
                    <div class="grayout h6">
                        {!! StringProcess::wrapSpan($quiz->hint) !!}
                    </div>
                    @endif
                    <!-- 各色选项 -->
                    <div class="">
                        @foreach($quiz->random_options as $option_key=>$quiz_option)
                        <!-- 选项本体 -->
                        <div class="">
                            <label><input type="radio" name="quiz-answer[{{ $quiz->id }}]" value="{{$quiz_option->id}}"><span>选项{{ $option_key+1 }}：</span><span>{!! StringProcess::wrapSpan($quiz_option->body) !!}</span></label>
                        </div>
                        @endforeach
                    </div>
                    <hr>
                    @endforeach
                    <h6 class="warning-tag">（为保证注册公平，避免机器恶意注册，页面含有防批量注册机制，五分钟只能回答一次问题，请核实后再提交回答，请勿直接“返回”前页面重新提交。）</h6>
                    <button type="submit" class="btn btn-md btn-danger sosad-button">提交回答</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
