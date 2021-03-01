<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessage;
use App\Models\User;
use App\Models\Message;
use App\Models\PublicNotice;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MessageBodyResource;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PublicNoticeResource;
use CacheUser;
use ConstantObjects;
use App\Sosadfun\Traits\MessageObjectTraits;
class MessageController extends Controller
{
    use MessageObjectTraits;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(StoreMessage $form)
    {
        $message = $form->userSend();
        if(!$message){abort(495);}
        $message->load('message_body','poster','receiver');
        return response()->success([
            'message' => new MessageResource($message),
        ]);
    }

    public function groupmessage(StoreMessage $form)
    {
        $messages = $form->adminSend();
        if(!$messages){abort(495);}
        $messages->load('poster','receiver')->except('seen');
        $message = $messages[0];
        $message_body = $message->message_body;
        return response()->success([
            'messages' => MessageResource::collection($messages),
            'message_body' => new MessageBodyResource($message_body),
        ]);
    }

    public function publicnotice(StoreMessage $form)
    {
        if(!auth('api')->user()->isAdmin()){abort(403,'管理员才可以发公共消息');}
        $public_notice = $form->generatePublicNotice();
        if(!$public_notice){abort(495);}
        $this->refreshPulicNotices();
        $public_notice->load('author');
        return response()->success([
            'public_notice' => new PublicNoticeResource($public_notice),
        ]);
    }

    public function publicnotice_index()
    {
        $info = CacheUser::AInfo();
        $info->clear_column('public_notice_id');
        $public_notices = $this->findAllPulicNotices();

        return response()->success([
            'public_notices' => PublicNoticeResource::collection($public_notices),
        ]);
    }

    public function index($id, Request $request)
    {
        $user = CacheUser::user($id);
        if(!$user){abort(404);}

        if(auth('api')->id()!=$user->id&&!auth('api')->user()->isAdmin()){abort(403);}
        //访问的信箱需为登录用户的信箱或登录用户为管理员

        $chatWith = $request->chatWith ?? 0;
        $query = Message::with('poster.title', 'receiver.title', 'message_body');

        switch ($request->withStyle) {
            case 'sendbox': $query = $query->withPoster($user->id);
            break;
            case 'dialogue': $query = $query->withDialogue($user->id, $chatWith);
            break;
            default: $query = $query->withReceiver($user->id)->withRead($request->read);
            break;
        }
        $messages = $query->ordered($request->ordered)
        ->paginate(config('constants.messages_per_page'));
        if((request()->withStyle==='sendbox'
            || request()->withStyle==='dialogue')
            && (!auth('api')->user()->isAdmin())){
            $messages->except('seen');
        }
        return response()->success([
            'style' => $request->withStyle,
            'messages' => MessageResource::collection($messages),
            'paginate' => new PaginateResource($messages),
        ]);
    }
    public function administration_record($id, Request $request)
    {
        $user_id = 0;$is_public='';$user_name='';
        $page = is_numeric($request->page)? $request->page:'1';
        if(auth('api')->check()&&$id===auth('api')->id()){
            CacheUser::Ainfo()->clear_column('administration_reminders');
            $user_id = $id;
        }
        if(auth('api')->check()&&auth('api')->user()->isAdmin()&&$id>0&&$id!=auth('api')->id()){
            $user_id = $id;
            $is_public="include_private";
        }
        if($user_id>0){
            $user_name = CacheUser::user($user_id)->name;
        }
        $records = $this->findAdminRecords($user_id, $page, $is_public, config('preference.index_per_page'));
        // TODO return administration record resource of $records
    }
}
