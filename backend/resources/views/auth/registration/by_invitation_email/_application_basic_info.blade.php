<div class="font-4">
    申请档案编号{{$application->id}}&nbsp;|&nbsp;
    @if(Auth::check()&&Auth::user()->isAdmin())
    <span class="{{StringProcess::check_email($application->email) ? 'warning-tag':''}}">
        {{$application->email}}
    </span>
    @else
    {{$application->email}}
    @endif
</div>
<div class="font-5">
    @if(Auth::check()&&Auth::user()->isAdmin())
    <span class="applicationreviewstatus{{ $application->id }} {{$application->is_passed ?'':'admin-symbol'}}">{{$application->is_passed? '已通过':'未通过'}}</span>
    <span class="{{$application->cut_in_line? 'warning-tag':''}}">{{$application->cut_in_line?'特殊审核':'排队中'}}</span>
    &nbsp;|&nbsp;
    @endif

    <span class="{{$application->has_quizzed? '':'admin-symbol'}}">{{$application->has_quizzed?'已答题':'未答题'}}</span>&nbsp;|&nbsp;
    <span class="{{$application->email_verified_at? '':'admin-symbol'}}">{{$application->email_verified_at?'邮箱已确认':'邮箱未确认'}}</span>&nbsp;|&nbsp;
    <span>档案创建时间：{{$application->created_at? $application->created_at->setTimezone('Asia/Shanghai'):'无记录'}}</span>&nbsp;|&nbsp;
    <span>申请提交时间：{{$application->submitted_at? $application->submitted_at->setTimezone('Asia/Shanghai'):'尚未提交'}}</span>
    @if(Auth::check()&&Auth::user()->isAdmin())
    <span>
        &nbsp;|&nbsp;
        <span>答题次数：{{$application->quiz_count}}</span>&nbsp;|&nbsp;
        <span class="{{$application->submission_count>1? 'admin-symbol':''}}">提交次数：{{$application->submission_count}}</span>&nbsp;|&nbsp;
        @if($application->ip_address)
        <span>初始IP:&nbsp;{!! StringProcess::ip_link($application->ip_address) !!}&nbsp;|&nbsp;</span>
        @endif
        @if($application->ip_address_last_quiz)
        <span>答题IP:&nbsp;{!! StringProcess::ip_link($application->ip_address_last_quiz) !!}&nbsp;|&nbsp;</span>
        @endif
        @if($application->ip_address_verify_email)
        <span>邮箱验证IP:&nbsp;{!! StringProcess::ip_link($application->ip_address_verify_email) !!}&nbsp;|&nbsp;</span>
        @endif
        @if($application->ip_address_submit_essay)
        <span>作文IP:&nbsp;{!! StringProcess::ip_link($application->ip_address_submit_essay) !!}&nbsp;|&nbsp;</span>
        @endif
        <span class="application_reviewed_or_not{{ $application->id }} {{$application->reviewer_id>0 ? '' : 'admin-symbol' }}">{{$application->reviewer_id>0 ? '已审核':'未审核'}}</span>
        @if($application->reviewer)
        <span>「<a href="{{route('user.show',$application->reviewer_id)}}">{{$application->reviewer->name}}</a>」于：{{$application->reviewed_at? $application->reviewed_at->setTimezone('Asia/Shanghai'):''}}</span>
        @endif
        @if($application->last_invited_at)
        &nbsp;|&nbsp;<span>已邀请：{{$application->last_invited_at->setTimezone('Asia/Shanghai')}}</span>
        @endif
        &nbsp;|&nbsp;
        @if($application->owner)
        <span>已注册：<a href="{{route('user.show',$application->user_id)}}">{{$application->owner->name}}</a></span>&nbsp;|&nbsp;
        @endif
        @if($application->submitted_at && $application->email_verified_at)
        <span class="{{$application->submitted_at < $application->email_verified_at->addMinutes(20)? 'warning-tag':''}}">简答题用时 {{$application->email_verified_at->diffInMinutes($application->submitted_at)}} 分钟</span>&nbsp;|&nbsp;
        @endif
    </span>
    @endif
    <span class="{{$application->is_forbidden? 'warning-tag':''}}">{{$application->is_forbidden? '邮件黑名单':''}}</span>
</div>
