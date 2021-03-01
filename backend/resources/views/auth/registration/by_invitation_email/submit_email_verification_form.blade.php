@extends('layouts.default')
@section('title', '通过邮箱获得邀请链接注册-第三步-验证注册邮箱')
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
            【步骤3】验证注册邮箱
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                @include('auth.registration.by_invitation_email._steps')
            </div>
            <div class="panel-body">
                <h2>【步骤3】验证注册邮箱</h2>
                <h4>你好！感谢你来到废文！为了确保你的邮箱正确无误，可以接收邀请链接，请先确认你的邮箱！</h4>
                <form method="POST" action="{{ route('register.by_invitation_email.submit_email_verification') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <input name="email" value="{{$application->email}}" class="hidden">
                        <input value="{{$application->email}}" class="form-control" disabled>
                        <h6 class="warning-tag">（邮箱无法更改，如果这不是你的当前可用邮箱，请勿继续。）</h6>
                    </div>
                    <div class="form-group">
                        <label class="control-label">从邮箱收到的确认码（应为10个随机字母）：</label>
                        <div class="">
                            <input type="text" class="form-control" name="email_token" value="{{ old('email_token') }}" placeholder="邮箱确认码">
                        </div>
                        <div class="grayout">
                            <span class="">
                                邮件确认码上次发送于{{$application->send_verification_at? $application->send_verification_at->diffForHumans():'尚未发送确认码'}}，
                            </span>
                            @if(!$application->send_verification_at || $application->send_verification_at < Carbon::now()->subDay(1))
                            <a href="{{route('register.by_invitation_email.resend_email_verification',['email'=>$application->email])}}" class="btn btn-sm btn-primary sosad-button">点我【重新发送邮件确认码】</a>
                            @else
                            <span>暂时不能重发邮件确认码，请仔细检查收件箱，或等待一日后重发。</span>
                            @endif
                        </div>
                    </div>
                    <h6 class="warning-tag">（为保证注册公平，避免机器恶意注册，页面含有防批量注册机制，五分钟只能提交一次确认码，请核实后再提交确认码，勿直接“返回”前页面重新提交。）</h6>
                    <button type="submit" class="btn btn-md btn-danger sosad-button">确认邮箱</button>

                    <h6>
                        友情提醒，请【仔细】检查邮箱输入情况，确认邮箱无误。错误的邮箱将无法接收确认码，也无法接收注册邀请邮件。为了确保验证邮件正常送达，请务必使用个人<code>目前常用、可用的</code>邮箱地址。
                    </h6>
                    <h6 class="grayout">
                        ●&nbsp;友情提醒，正常的邮件形式一般为<code>abcdefg@163.com</code>，<code>123456789@qq.com</code>，而不是<code>www.abcdefg@163.com</code>，<code>www.123456789@qq.com</code>，更不是<code>123456789@qq.con</code>。请使用正确的邮件名称格式。<br>
                        ●&nbsp;请仔细检查个人收件箱/垃圾箱，修改自己的垃圾邮件设置，再重发邮件。<br>
                        ●&nbsp;友情提醒，重复发件容易被收件箱拒收，因此请你等待&nbsp;<em><u>恰当时间间隔</u></em>&nbsp;再行重发邮件。<br>
                        ●&nbsp;<code>qq邮箱</code>拒信严重，请尽量不要使用qq邮箱。<br>
                        ●&nbsp;邮箱系统在10pm-1am繁忙，如有可能，建议优先选择凌晨、午后等访问人数较少的时间段进行验证<br>
                        ●&nbsp;如果一直无法收到验证邮件，建议更换邮箱，重新申请。
                    </h6>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
