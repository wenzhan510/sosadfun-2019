<?php

namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use App\Models\Message;
use App\Models\MessageBody;
use App\Models\PublicNotice;
use DB;
use Carbon\Carbon;
use CacheUser;

class StoreMessage extends FormRequest
{
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return true;
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'sendTo' => 'numeric',
            'sendTos' => 'array',
            'body' => 'required|string|max:20000',
            'title' => 'string|max:1000'
        ];
    }

    public function userSend()
    {
        $this->validateSendTo(Request('sendTo'), auth('api')->id());
        $this->isDuplicateMessage(Request('body'), auth('api')->id());
        $messages = $this->generateMessages([Request('sendTo')], Request('body'));
        return $messages[0];
    }

    public function adminSend()
    {
        $this->validateSendTos(Request('sendTos'), auth('api')->id());
        $this->isDuplicateMessage(Request('body'), auth('api')->id());
        return $messages = $this->generateMessages(Request('sendTos'), Request('body'));
    }

    public function generateMessages($sendTos, $body)
    {
        $messages = DB::transaction(function() use($sendTos, $body){
            $messageBodyId = $this->generateMessageBody($body);
            $messages = $this->generateMessageRecords($sendTos, $messageBodyId);

            if (!auth('api')->user()->isAdmin()){
                auth('api')->user()->info->decrement('message_limit');
            }
            return $messages;
        });

        return $messages;
    }

    public function generateMessageBody($body)
    {
        $messageBody = MessageBody::create(['body' => $body]);
        return $messageBody->id;
    }

    public function generateMessageRecords($sendTos, $bodyId)
    {
        $created_at = Carbon::now();
        foreach ($sendTos as $sendTo) {
            $message_datas[] = [
                'poster_id' => auth('api')->id(),
                'receiver_id' => $sendTo,
                'body_id' => $bodyId,
                'created_at'=> $created_at,
            ];
        }
        Message::insert($message_datas);
        return $messages = Message::where('body_id', $bodyId)->get();
    }

    public function generatePublicNotice()
    {
        if(!auth('api')->user()->isAdmin()){abort(403);}

        $notice_data = request()->only('body','title');
        $notice_data['user_id'] = auth('api')->id();
        $public_notice = DB::transaction(function() use($notice_data) {
            $public_notice = PublicNotice::create($notice_data);
            DB::table('system_variables')->update(['latest_public_notice_id' => $public_notice->id]);
            return $public_notice;
        });

        return $public_notice;
    }

    private function validateSendTos($sendTos, $selfId)
    {
        if(!auth('api')->user()->isAdmin()){abort(403);}
        $newSendTos = \App\Models\User::whereIn('id', $sendTos)
        ->where('id', '<>', $selfId)
        ->whereNull('deleted_at')
        ->select('id')
        ->get()
        ->pluck('id')
        ->toArray();
        $unavailable = array_diff($sendTos, $newSendTos);//未来可以考虑将这个信息返回？也或许不需要……
        if($unavailable){abort(404,json_encode($unavailable));}
    }

    private function validateSendTo($sendToId, $selfId)
    {
        if((!auth('api')->user()->isAdmin())
            &&(auth('api')->user()->info->message_limit <= 0)){
                abort(410,'message limit violation');
        }
        $sendToUser = CacheUser::user($sendToId);
        if(!$sendToUser){abort(404);}
        if($selfId == $sendToId){
            // $sendToId is of type integer, $selfId is of type string
            abort(411,'cannot send message to oneself');
        }
        if($sendToUser->info->no_stranger_msg&&!$sendToUser->isFollowing(auth('api')->id())){abort(413,'receiver refuse to get message');}
    }

    private function isDuplicateMessage($body, $selfId)
    {
        $last_message = Message::where('poster_id', $selfId)
        ->orderBy('created_at', 'desc')
        ->first();
        if(!empty($last_message) && (strcmp($last_message->message_body->body, $body) === 0)){abort(409, 'cannot send duplicate message');}
    }
}
