@if( ($application->submitted_at && $application->submitted_at < Carbon::now()->subDays(config('constants.application_cooldown_days'))) || $application->last_invited_at || $application->cut_in_line )
<div class="">
    @if($application->is_passed)
    <div class="">
        恭喜，你的注册申请已经通过审核，
        @if(!$application->last_invited_at)
        当前排队人数众多，尚未来得及发送邮件。你的邀请已进入发送队列，请耐心等待服务器空闲时依序发送邮件。
        @else
        上次邀请邮件发送于{{$application->last_invited_at->diffForHumans()}}，
        @if($application->last_invited_at && $application->last_invited_at < Carbon::now()->subDay(1))
        <a href="{{route('register.by_invitation_email.resend_email',['email'=>$application->email])}}" class="btn btn-sm btn-primary sosad-button">点我重新发送邀请邮件</a>
        @else
        <span>暂时不能重发邀请邮件，请仔细查收邮箱，或明日再尝试重发。</span>
        @endif
        @endif
    </div>
    @else
    <div class="">
        <div class="">
            抱歉，你的注册申请问卷被拒绝。
        </div>
        <div class="grayout font-6">
            站内每日邀请名额有限，无法通过全部的申请。如果申请中含有以下情况，将不容易通过申请：抄袭，直接复制粘贴来自网络的资源或注册首页的说明凑字数，语句极不通顺，标点符号极不规范，回答有拷贝拼凑痕迹、前后矛盾，同样的回答反复出现（重复注册嫌疑），回答和题干问题不一致（未仔细审题），回答不符合网站定位。
        </div>
        <div class="">
            如果你愿意，也可以点击下面的链接，重新回答问卷。重新回答时可以继承原先的档案编号，无需再答题和验证邮箱。
        </div>
        <a href="{{route('register.by_invitation_email.submit_essay_form', ['email' => $application->email])}}" class="btn btn-sm btn-primary sosad-button">重新回答问卷</a>
    </div>
    @endif
</div>
@else
<div class="">
    申请正在审核中。申请档案编号仅为档案识别标志，并非问卷完成的顺序，更非档案被审核的顺序。系统会按一定的规则为档案分配审核员，请耐心等待人工审核。人工审核资源有限，请不要重复申请。为保证审核公平，排队期间更换邮箱重复提交申请问卷的，邮箱和IP会直接进入<code>黑名单</code>。如想更快注册，也可以考虑：&nbsp;<a href="{{route('register_by_invitation')}}" class="btn btn-sm btn-primary sosad-button">其他注册途径</a>
</div>
@endif
