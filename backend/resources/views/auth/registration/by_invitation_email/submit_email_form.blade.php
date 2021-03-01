@extends('layouts.default')
@section('title', '通过邮箱获得邀请链接注册-第一步-提交用于申请注册的邮箱')

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
                【步骤1】提交用于申请注册的邮箱
            </div>
            @include('shared.errors')
            <div class="panel panel-default">
                <div class="panel-heading">
                    @include('auth.registration.by_invitation_email._steps')
                </div>
                <div class="panel-body">
                    <form action="{{ route('register.by_invitation_email.submit_email') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <h2>【步骤1】提交申请注册邮箱(测试中)</h2>
                            <h5 class="">
                                ●&nbsp;如果你没有提交过邮箱，可以在这里提交【新邮箱】。<br>
                                ●&nbsp;未完成的申请，必须从本页面输入邮箱【继续申请】，将自动前往当前申请所需的页面。<br>
                                ●&nbsp;页面含有防批量注册机制，申请中请<u>【不要】刷新，【不要】使用浏览器“返回”前页面继续提交</u>。<br>
                                ●&nbsp;如果你已完成申请，可以在这里输入邮箱【查询进度】。正常查询不属于“重复提交”。<br>
                                ●&nbsp;【站内活动】奖励的链接会直接发送到参与活动的邮箱，同样可以在这里查询和补发。<br>
                                ●&nbsp;继续下一步之前，请确保你已阅读页面顶部的<a type="button" data-toggle="collapse" data-target="#registration_by_email_steps" style="cursor: pointer;"><code>
                                    《通过邮箱申请注册邀请链接的详细步骤》
                                </code></a>。其他常见疑问，请<a href="{{route('search', ['search'=>'邀请链接'])}}" class=""><code>《搜索帮助“邀请链接”》</code></a><br>
                            </h5>
                            <hr>

                            <label class="control-label">邮箱：<span class="font-6">(暂不接受qq邮箱。近日申请过多，人工审核工作量难以承担，于2020/01/20-2020/01/31期间暂停接收新申请。）</span>
                            </label>
                            <div class="">
                                <input type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{__('pageview.email')}}">
                            </div>
                        </div>
                        @if (env('NOCAPTCHA_SITEKEY'))
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
                            </div>
                        </div>
                        @endif
                        <h6 class="warning-tag">（为保证注册公平，避免机器恶意注册，本页面含有防批量注册机制，五分钟只能提交一次邮箱，请核实后再提交邮箱，避免反复提交邮箱。）</h6>
                        <button type="submit" class="btn btn-lg btn-danger sosad-button">
                            提交邮箱申请注册/查询申请状态
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
