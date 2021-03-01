<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon;
use Cache;
use DB;
use App\Models\Helpfaq;
use App\Http\Resources\FAQResource;
use Illuminate\Validation\Rule;

use App\Sosadfun\Traits\FAQObjectTraits;


class FAQController extends Controller
{
    use FAQObjectTraits;

    public function __construct()
    {
        $this->middleware('admin')->except('index');
    }

    public function index()
    {
        $faqs = $this->all_faqs();
        return response()->success(FAQResource::collection($faqs));
    }

    public function store(Request $request)
    {
        $faq_keys = $this->get_faq_keys();
        $validatedData = $request->validate([
            'key' => ['required', Rule::in($faq_keys)],
            'question' => 'required|string|min:1|max:180',
            'answer'=>'required|string|min:1|max:2000',
        ]);
        $faq = Helpfaq::create($request->only('key','question','answer'));
        $this->clear_all_faqs();
        return response()->success(new FAQResource($faq));
    }

    public function update(Request $request, $id)
    {
        $faq = Helpfaq::find($id);
        if (!$faq) { abort(404, 'FAQ不存在'); }
        $validatedData = $request->validate([
            'question' => 'required|string|min:1|max:180',
            'answer'=>'required|string|min:1|max:2000',
        ]);
        $faq->update($request->only('question','answer'));
        $this->clear_all_faqs();
        return response()->success(new FAQResource($faq));
    }

    public function destroy($id)
    {
        $faq = Helpfaq::find($id);
        if (!$faq){ abort(404, 'FAQ不存在'); }
        $faq->delete();
        $this->clear_all_faqs();
        return response()->success([
            'message' =>[
                'success' => "成功删除FAQ",
            ],
           'faq_id' => $id,
       ]);
    }

}
