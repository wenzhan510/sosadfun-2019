@extends('layouts.default')
@section('title', '管理界面')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>待办事</h4></div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>标签（Tag）管理</h4>
                <ul>
                    <li><a href="{{ route('tag.index') }}">全站标签</a></li>
                </ul>
                <h4>用户（User）管理</h4>
                <ul>
                    <li><a href="{{route('admin.searchrecordsform')}}">按用户名/邮箱/IP/时间顺序搜索各类记录</a></li>
                    <li><a href="{{route('admin.manageblacklistform')}}">修改黑名单记录</a></li>
                </ul>

                <h4>公共通知（Public Notice）管理</h4>
                <ul>
                    <li><a href="{{route('admin.sendpublicnoticeform')}}">发送公共通知</a></li>
                </ul>

                <h4>主题/书籍（thread）管理</h4>
                <ul>
                    <li><a href="{{route('threads.index')}}">全站帖子筛选</a></li>
                </ul>

                <h4>题头（Quote）管理</h4>
                <ul>
                    <li><a href="{{ route('quote.review_index', ['withReviewState'=>'notYetReviewed']) }}">题头审核</a></li>
                </ul>

                <h4>作业（Homework）管理</h4>
                <ul>
                    <li><a href="{{ route('homework.index') }}">作业列表</a></li>
                    <li><a href="{{ route('homework.create') }}">新建作业</a></li>
                </ul>

                <h4>邀请码（Invitation Token）管理</h4>
                <ul>
                    <li><a href="{{ route('invitation_tokens.index') }}">查看邀请码列表</a></li>
                </ul>

                <h4>注册申请（Registration Application）管理</h4>
                <ul>
                    <li><a href="{{ route('registration_application.review_index', ['withReviewState'=>'notYetReviewed']) }}">查看注册申请审核</a></li>
                </ul>

                <h4>赞助者（Donation）管理</h4>
                <ul>
                    <li><a href="{{ route('donation.review_patreon', ['show_review_tab'=> 'not_approved']) }}">查看赞助者申请</a></li>
                </ul>

                <h4>头衔（Title）管理</h4>
                <ul>
                    <li><a href="{{route('title.index')}}">头衔列表</a></li>
                </ul>

                <h4>答题测试（Quiz）管理</h4>
                <ul>
                    <li><a href="{{ route('quiz.review') }}">查看题库列表</a></li>
                </ul>

            </div>
        </div>
    </div>
</div>

@stop
