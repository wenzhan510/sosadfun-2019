<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use DB;
use ConstantObjects;
use CacheUser;
use App\Sosadfun\Traits\AdminManageTraits;


class AdminController extends Controller
{
    use AdminManageTraits;

    public function __construct()
    {
        $this->middleware('admin');
    }

    public function management(Post $post, Request $request)
    {
        if($request->report_post_id){
            $report_post = $this->update_report($request);
        }

        if(!$request->report_post_id||($request->report_post_id&&$request->report_summary==="approve")){
            // 如果并非举报，直接处理。如果是举报，且通过举报，处理被举报内容
            $administration = $this->content_N_user_management($request);
        }

        if($request->report_post_id&&$request->report_summary!="approve"){
            $administration = $this->report_management($request, $report_post);
        }
        // TODO 在这里返回$administration,检查前面的代码无误
    }

}
