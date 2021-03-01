<?php

namespace App\Http\Controllers\API;
namespace App\Notifications;
use cache;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;
    public $token;
    /**
    * Create a new notification instance.
    *
    * @return void
    */

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
    * Get the notification's delivery channels.
    *
    * @param  mixed  $notifiable
    * @return array
    */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
    * Get the mail representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return \Illuminate\Notifications\Messages\MailMessage
    */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('废文网密码重置/重激活')
        ->line('以下是你的废文网密码重置邮件链接')
        ->action('密码重置/重激活', ("https://sosad.fun/password/reset/".$this->token),false)
        //->action('密码重置/重激活', url(route('password.reset', $this->token),false))
        ->line('如果你没有发出密码重置请求，请忽视此邮件');
    }

    /**
    * Get the array representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return array
    */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
