<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\QuizCollection;
use App\Models\RegistrationApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Sosadfun\Traits\RegistrationApplicationObjectTraits;
use App\Sosadfun\Traits\QuizObjectTraits;
use Validator;
use Cache;
use App\Http\Resources\QuizResource;
use App\Http\Resources\RegistrationApplicationResource;

class RegAppController extends Controller
{

    use RegistrationApplicationObjectTraits;
    use QuizObjectTraits;

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email|max:255',
        ]);
    }

    public function submit_email(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return response()->error($validator->errors(), 422);
        }

        $this->rate_limit_check(__FUNCTION__,null,request()->ip());

        $application = self::findApplicationViaEmail($request->email,$nullable=true);

        if(!$application){

            if(preg_match('/qq\.com/', $request->email)){
                return response()->error('qq邮箱拒收本站邮件，请勿使用qq邮箱。', 422);
            }

            if(preg_match('/\.con$/', $request->email)){
                return response()->error('请确认邮箱拼写正确。', 422);
            }

            $application = RegistrationApplication::UpdateOrCreate([
                'email' => $request->email,
            ],[
                'ip_address' => request()->ip(),
                'email_token' => str_random(10),
                'token' => str_random(35),
            ]);
        }

        $success['registration_application'] = new RegistrationApplicationResource($application);

        if(!$application->is_passed&&!$application->cut_in_line&&!$application->has_quizzed){
            $quizzes = $this->random_quizzes(-1, 'register', config('constants.registration_quiz_total'));
            $quiz_questions = implode(",", $quizzes->pluck('id')->toArray());
            $application ->update(['quiz_questions' => $quiz_questions]);

            $success['quizzes'] = QuizCollection::make($quizzes);
        } elseif($application->email_verified_at&&!$application->is_passed&&!$application->cut_in_line&&$application->has_quizzed&&!$application->submitted_at){
            $essay = $application->assign_essay_question();
            $success['essay'] = new QuizResource($essay);
        }

        return response()->success($success);
    }

    public function submit_quiz(Request $request)
    {
        $this->rate_limit_check(__FUNCTION__,$request->email);

        $application = self::findApplicationViaEmail($request->email);
        if($application->cut_in_line) {
            abort(409,'后续步骤已经完成，不需要再次提交测试。');
        }
        if($application->is_passed||$application->has_quizzed||$application->submitted_at){
            abort(409,'已经答过题了，无需重复答题。');
        }

        // 如果通过了
        if($this->check_quiz_passed_or_not($request->quizzes, $application->quiz_questions, config('constants.registration_quiz_correct'))){
            $application->update([
                'has_quizzed'=>1,
                'quiz_count' => $application->quiz_count+1,
                'ip_address_last_quiz' => request()->ip()
            ]);
            $essay = $application->assign_essay_question();
            $success['essay'] = new QuizResource($essay);
            $application->sendVerificationEmail();
        } else {
            $application->update([
                'quiz_questions' => null,
                'quiz_count' => $application->quiz_count+1
            ]);
        }

        $success['registration_application'] = new RegistrationApplicationResource($application);
        return response()->success($success);
    }

    public function submit_email_confirmation_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->error($validator->errors(), 422);
        }

        $this->rate_limit_check(__FUNCTION__,$request->email);

        $application = self::findApplicationViaEmail($request->email);
        if($application->email_verified_at||$application->submitted_at||$application->is_passed||$application->user_id>0||$application->cut_in_line){
            abort(409,'你已经成功验证过邮箱，不需要重复验证。');
        }
        if(!$application->has_quizzed) {
            abort(411,'未完成前序步骤，不能验证邮箱。');
        }

        if($request->token!=$application->email_token){
            abort(422,'邮箱验证码不正确，无法验证邮箱真实有效。');
        }

        $application->update([
            'email_verified_at' => Carbon::now(),
            'ip_address_verify_email' => request()->ip(),
        ]);

        return response()->success(["email" => $request->email]);
    }

    public function resend_email_verification(Request $request)
    {
        $this->rate_limit_check(__FUNCTION__,$request->email);

        $application = self::findApplicationViaEmail($request->email);
        if($application->email_verified_at||$application->submitted_at||$application->is_passed||$application->user_id>0||$application->cut_in_line){
            abort(409,'你已经成功验证过邮箱，不需要重复验证。');
        }
        if($application->send_verification_at && $application->send_verification_at>=Carbon::now()->subDay(1)) {
            abort(410,'短时间内已曾成功发信，暂时不能重复发送验证邮件。');
        }
        if(!$application->has_quizzed) {
            abort(411,'未完成前序步骤，不能发送验证邮件。');
        }

        $application->sendVerificationEmail();
        return response()->success(["email" => $request->email]);
    }

    public function submit_essay(Request $request)
    {
        $this->rate_limit_check(__FUNCTION__,$request->email);

        $validator = Validator::make($request->all(), [
            'body' => 'required|string|min:450|max:2000',
            'essay_id' => 'required|integer',
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->error($validator->errors(), 422);
        }

        $application = self::findApplicationViaEmail($request->email);
        if ($application->cut_in_line||$application->is_passed) {
            abort(409,'后续步骤已经完成，不需要再次提交申请。');
        }
        if ($application->submitted_at&&$application->submitted_at > Carbon::now()->subDays(config('constants.application_cooldown_days'))) {
            abort(409,'已经成功提交论文，等待审核中，不需要再次提交论文。');
        }
        if(!$application->has_quizzed){
            abort(411,'未完成测试题，不能提交小论文。');
        }
        if(!$application->email_verified_at){
            abort(411,'未验证邮箱，不能提交小论文。');
        }
        if($application->essay_question_id != $request->essay_id) {
            abort(444,'回答的小论文题目和记录的应该回答的题目不符合。');
        }
        $application->update([
            'body' => $request->body,
            'submitted_at' => Carbon::now(),
            'reviewer_id' => 0,
            'reviewed_at' => null,
            'submission_count' => $application->submission_count+1,
            'ip_address_submit_essay' => request()->ip(),
        ]);
        return response()->success(['registration_application' => new RegistrationApplicationResource($application)]);
    }

    public function resend_invitation_email(Request $request)
    {
        $this->rate_limit_check(__FUNCTION__,$request->email);

        $application = self::findApplicationViaEmail($request->email);
        if($application->user_id>0){
            abort(409,'你已经成功接受邀请并注册，不需要重复验证。');
        }
        if($application->last_invited_at && $application->last_invited_at>=Carbon::now()->subDay(1)) {
            abort(409,'已成功发信，暂时不能重复发送邮件。');
        }
        if(!$application->is_passed) {
            abort(411,'尚未通过申请，不能发送验证邮件。');
        }

        $application->sendInvitationEmail();
        return response()->success(["email" => $request->email]);
    }
}
