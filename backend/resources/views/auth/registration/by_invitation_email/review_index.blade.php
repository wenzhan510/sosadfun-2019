@extends('layouts.default')
@section('title', '审核申请')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1>审核申请</h1>
                <div class="">
                    <a href="{{route('registration_application.upload_form')}}" class="btn btn-lg btn-primary sosad-button">批量上传通过邮箱</a>
                    <a href="{{route('admin.searchrecordsform')}}" class="btn btn-lg btn-primary sosad-button pull-right" target="_blank">搜索申请记录</a>
                </div>
                <br>
                <div class="">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="{{ $review_tab==='notYetReviewed'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'notYetReviewed']) }}">未审核</a></li>
                        <li role="presentation" class="{{ $review_tab==='notYetFinished'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'notYetFinished']) }}">未完成</a></li>
                        <li role="presentation" class="{{ $review_tab==='Reviewed'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'Reviewed']) }}">已审核</a></li>
                        <li role="presentation" class="{{ $review_tab==='Cutinline'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'Cutinline']) }}">快速通道</a></li>
                        <li role="presentation" class="{{ $review_tab==='Passed'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'Passed']) }}">已通过</a></li>
                        <li role="presentation" class="{{ $review_tab==='Registered'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'Registered']) }}">已注册</a></li>
                        <li role="presentation" class="{{ $review_tab==='UnPassed'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'UnPassed']) }}">已拒绝</a></li>
                        <li role="presentation" class="{{ $review_tab==='BlackListed'? 'active':'' }}"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'BlackListed']) }}">黑名单</a></li>
                        <li role="presentation" class="{{ $review_tab==='all'? 'active':'' }} pull-right"><a href="{{ route('registration_application.review_index', ['withReviewState'=>'all']) }}">全部</a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body text-center" id="registration_applications_for_review">
                {{ $registration_applications->links() }}
                <form method="POST" action="{{ route('registration_application.batch_review') }}">
                    {{ csrf_field() }}
                    <div class="col-xs-12">
                        <label class="radio-inline"><input type="radio" name="check" value="1" onclick="checkAll('registration_applications_for_review')">全选</label>
                        <label class="radio-inline"><input type="radio" name="check" value="2" onclick="uncheckAll('registration_applications_for_review')">全不选</label>
                    </div>
                    @foreach($registration_applications as $application)
                    @include('auth.registration.by_invitation_email._application_review')
                    @endforeach
                    <div class="col-xs-12">
                        <input type="submit" name="cut_in_line_all" value="选项全保送" class="btn btn-md btn-primary cancel-button">&nbsp;&nbsp;
                        <input type="submit" name="pass_all" value="选项全通过" class="btn btn-md btn-success cancel-button">&nbsp;&nbsp;
                        <input type="submit" name="unpass_all" value="选项全不通过" class="btn btn-md btn-warning cancel-button">&nbsp;&nbsp;
                        <input type="submit" name="black_list_all" value="选项全黑名单" class="btn btn-md btn-danger cancel-button">
                        <br><br>
                    </div>
                </form>

                {{ $registration_applications->links() }}
            </div>

        </div>
    </div>
</div>
@stop
