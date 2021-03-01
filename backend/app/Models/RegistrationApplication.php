<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon;
use App\Sosadfun\Traits\SwitchableMailerTraits;
use App\Sosadfun\Traits\QuizObjectTraits;
use App\Sosadfun\Traits\RegistrationApplicationObjectTraits;

class RegistrationApplication extends Model
{

    use SwitchableMailerTraits;
    use QuizObjectTraits;
    use RegistrationApplicationObjectTraits;

    protected $guarded = [];
    protected $dates = ['last_invited_at', 'submitted_at', 'created_at', 'reviewed_at', 'email_verified_at', 'send_verification_at'];
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quiz()
    {
        return $this->find_quiz_set($this->essay_question_id);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->select('id','name');
    }

    public function scopeEmailLike($query, $email)
    {
        if($email){
            return $query->where('email','like','%'.$email.'%');
        }
        return $query;
    }

    public function scopeEssayLike($query, $essay)
    {
        if($essay){
            return $query->where('body','like','%'.$essay.'%');
        }
        return $query;
    }

    public function scopeCreationIPLike($query, $ip)
    {
        if($ip){
            // return $query->where('ip_address','like', $ip.'%');
            return $query->where(function($query) use($ip){
                $query->where('ip_address','like', $ip.'%');
                $query->orWhere('ip_address_last_quiz','like', $ip.'%');
                $query->orWhere('ip_address_verify_email','like', $ip.'%');
                $query->orWhere('ip_address_submit_essay','like', $ip.'%');
            });
        }
        return $query;
    }

    public function scopeWithReviewState($query, $state = '')
    {
        if($state==='notYetReviewed'){
            $query = $query
            ->where('reviewer_id',0)
            ->where('submitted_at','<>',null)
            ->where('user_id', 0)
            ->where('submitted_at','>', Carbon::now()->subDays(7))
            ->orderBy('submitted_at', 'desc');
        }
        if($state==='notYetFinished'){
            $query = $query
            ->where('reviewer_id',0)
            ->where('submitted_at','=',null)
            ->where('user_id', 0)
            ->orderBy('created_at', 'desc');
        }
        if($state==='Reviewed'){
            $query = $query
            ->where('reviewer_id','>',0)
            ->orderBy('reviewed_at', 'desc');
        }
        if($state==='Cutinline'){
            $query = $query
            ->where('cut_in_line',1)
            ->where('is_passed',1)
            ->orderBy('reviewed_at', 'desc');
        }
        if($state==='Passed'){
            $query = $query
            ->where('cut_in_line',0)
            ->where('is_passed',1)
            ->where('user_id', 0)
            ->orderBy('reviewed_at', 'desc');
        }
        if($state==='Registered'){
            $query = $query
            ->where('user_id', '<>', 0)
            ->orderBy('reviewed_at', 'desc');
        }
        if($state==='UnPassed'){
            $query = $query
            ->where('is_passed',0)
            ->where('is_forbidden',0)
            ->where('reviewer_id','>',0)
            ->orderBy('reviewed_at', 'desc');
        }
        if($state==='BlackListed'){
            $query = $query
            ->where('is_passed',0)
            ->where('is_forbidden',1)
            ->where('reviewer_id','>',0)
            ->orderBy('reviewed_at', 'desc');
        }
        return $query;
    }

    public function scopeWithCutIn($query, $CutIn = '')
    {
        if($CutIn==='is_cut_in'){
            $query = $query->where('cut_in_line', 1);
        }
        if($CutIn==='not_cut_in'){
            $query = $query->where('cut_in_line', 0);
        }
        return $query;
    }


    public function sendInvitationEmail()
    {
        $view = 'auth.registration.by_invitation_email.invitation_email';
        $application = $this;
        $data = compact('application');
        $to = $application->email;
        $subject = "恭喜，你的废文网注册申请审核已通过";

        $this->send_email_from_ses_server($view, $data, $to, $subject);

        $this->update(['last_invited_at' => Carbon::now()]);
    }

    public function sendVerificationEmail()
    {
        if($this->token){
            $view = 'auth.registration.by_invitation_email.verification_email';
            $application = $this;
            $data = compact('application');
            $to = $application->email;
            $subject = "感谢申请注册废文网，请确认你的邮箱";

            $this->send_email_from_ses_server($view, $data, $to, $subject);

            $this->update(['send_verification_at' => Carbon::now()]);
        }
    }

    public function assign_essay_question()
    {
        $quiz_set = $this->random_quizzes(-1, 'essay', 1);
        $quiz = $quiz_set[0];
        if($quiz){
            $this->update([
                'essay_question_id' => $quiz->id,
                'submitted_at' => null,
                'body' => null,
                'reviewer_id' => 0,
                'reviewed_at' => null,
            ]);
        }
        return $quiz;
    }

    public function assign_email_token()
    {
        if(!$this->email_token&&!$this->email_verified_at){
            $this->update([
                'email_token' => str_random(10),
                'email_verified_at' =>null,
            ]);
            return true;
        }
        return false;
    }

}
