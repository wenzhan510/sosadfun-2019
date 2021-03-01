<div class="row text-center">
    <div class="col-xs-10">
        <div class="text-left">
            <div class="font-6 font-weight-400">
                <input type="checkbox" class="registration_applications_for_review pull-left" name="registration_applications_for_review[]" value="{{ $application->id }}">
                @include('auth.registration.by_invitation_email._application_basic_info')
            </div>
            @if($application->quiz())
            <div class="font-6">
                {!! StringProcess::wrapParagraphs($application->quiz() ? $application->quiz()->body:'问题') !!}
            </div>
            <div class="font-6 font-weight-400">
                {!! StringProcess::warn_in_application(StringProcess::wrapParagraphs($application->body)) !!}
            </div>
            @endif
        </div>
    </div>
    <div class="col-xs-2">
        <button class="brief-8 btn btn-md btn-primary cancel-button approvebutton{{$application->id}} {{$application->reviewer_id>0 ? 'hidden':''}} applicationbutton-show{{$application->id}}"  type="button" name="button" onClick="review_application({{$application->id}},'cut_in_line')">保送<i class="fa fa-check" aria-hidden="true"></i></button><br>
        <button class="brief-8 btn btn-md btn-success cancel-button approvebutton{{$application->id}} {{$application->reviewer_id>0 ? 'hidden':''}} applicationbutton-show{{$application->id}}"  type="button" name="button" onClick="review_application({{$application->id}},'pass')">通过<i class="fa fa-check" aria-hidden="true"></i></button><br>
        <button class="brief-8 btn btn-md btn-info cancel-button togglebutton{{$application->id}} {{$application->reviewer_id>0 ? '':'hidden'}} applicationbutton-review{{$application->id}}"  type="button" name="button" onClick="reset_review_button({{$application->id}})">重新审核</button>
        <button class="brief-8 btn btn-md  btn-warning cancel-button disapprovebutton{{$application->id}} {{$application->reviewer_id>0 ? 'hidden':''}} applicationbutton-notshow{{$application->id}}"  type="button" name="button" onClick="review_application({{$application->id}},'unpass')">不过<i class="fa fa-times" aria-hidden="true"></i></button><br>
        <button class="brief-8 btn btn-md  btn-danger cancel-button disapprovebutton{{$application->id}} {{$application->reviewer_id>0 ? 'hidden':''}} applicationbutton-notshow{{$application->id}}"  type="button" name="button" onClick="review_application({{$application->id}},'black_list')">黑名单<i class="fa fa-times" aria-hidden="true"></i></button>
    </div>
</div>
<hr>
