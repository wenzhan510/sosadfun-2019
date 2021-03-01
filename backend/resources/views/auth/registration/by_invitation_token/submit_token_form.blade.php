@extends('layouts.default')
@section('title', '通过邀请码注册')

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
                通过邀请码注册
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">通过邀请码注册</div>
                <div class="panel-body">
                    @include('shared.errors')
                    <form action="{{ route('register.by_invitation_token.submit_token') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="control-label">邀请码：</label>
                            <div class="">
                                <input type="text" class="form-control" name="invitation_token" value="{{ old('invitation_token') }}" placeholder="邀请码">
                            </div>
                        </div>
                        @if (env('NOCAPTCHA_SITEKEY'))
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
                            </div>
                        </div>
                        @endif
                        <h6 class="warning-tag">（为保证注册公平，避免机器恶意注册，本页面含有防批量注册机制，五分钟内只能提交一次邀请码。请核实后再提交邀请码，避免反复提交邀请码。）</h6>
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-danger sosad-button">
                                提交邀请码
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
