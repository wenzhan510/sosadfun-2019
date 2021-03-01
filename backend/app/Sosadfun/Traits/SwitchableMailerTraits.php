<?php
namespace App\Sosadfun\Traits;

use Carbon;
use Mail;
use Exception;
use Log;

trait SwitchableMailerTraits{

    public function send_email_from_ses_server($view, $data, $to, $subject)
    {
        $name = env('MAIL_FROM_NAME','sosad_no_reply');
        $from = env('MAIL_FROM_ADDRESS','no_reply@sosad.fun');
        try {
            $mail_status = Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
                $message->from($from, $name)->to($to)->subject($subject);
            });
        } catch (\Exception $e) {
            Log::emergency('Mail'.$e->getMessage());
            abort(550);
        }
    }
}
