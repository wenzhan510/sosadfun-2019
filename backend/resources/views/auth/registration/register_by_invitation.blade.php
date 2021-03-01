@extends('layouts.default')
@section('title', '邀请注册')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="font-3">通过邀请码注册</span>&nbsp;&nbsp;
                    <a href="{{route('search', ['search'=>'邀请码'])}}" class="font-6">详情搜索帮助“邀请码”</a>
                </div>
                <div class="panel-body">
                    <h4>获得邀请码的渠道:</h4>
                    <h5>【公共邀请码】通过废文网微博、微信公众号等渠道，获得公共邀请码（数量有限）</h5>
                    <h6 class="grayout">微博、微信公众号会不定期开放少量公共邀请码，数量有限，先到先得。</h6>
                    <h5>【私人邀请码】通过已经注册废文的好友，获得私人邀请码</h5>
                    <h6 class="grayout">资深废文用户，可以在“个人中心-邀请好友”处创建私人邀请码，分享给好友。</h6>
                    <a href="{{route('register.by_invitation_token.submit_token_form')}}" class="btn btn-md btn-primary sosad-button">我已获得邀请码，通过邀请码注册</a>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="font-3">通过含邀请链接的邮件注册(测试中)</span>&nbsp;&nbsp;
                    <a href="{{route('search', ['search'=>'邀请链接'])}}" class="font-6">详情搜索帮助“邀请链接”</a>
                </div>
                <div class="panel-body">
                    <h4>获得邀请链接的渠道：</h4>
                    <h5>【活动邀请】参加网站活动获取注册邀请链接</h5>
                    <h6 class="grayout">通过废文网微博、微信公众号等渠道参与限时活动，将有机会直接获得专属注册链接。</h6>
                    <h5>【问卷邀请】通过填写问卷的方式获取注册邀请链接</h5>
                    <h6 class="grayout">提交邮箱、完成问卷，排队审核通过后将获得专属注册链接。</h6>
                    <div class="warning-tag font-3">（近日申请过多，人工审核工作量难以承担，于2020/01/20-2020/01/31期间暂停接收新申请。）</div>
                    <a href="{{route('register.by_invitation_email.submit_email_form')}}" class="btn btn-md btn-primary sosad-button">提交邮箱申请注册/查询我的申请状态</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
