<?php

namespace App\Http\Controllers\API;

use App\Models\Quote;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuote;
use App\Sosadfun\Traits\QuoteObjectTraits;
use Carbon\Carbon;
use Cache;
use App\Http\Resources\QuoteResource;
use App\Http\Resources\PaginateResource;

class QuoteController extends Controller
{
    use QuoteObjectTraits;

    public function __construct()
    {
        $this->middleware('auth:api')->except('index');
        $this->middleware('admin')->only('review_index', 'review');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = is_numeric($request->page)? $request->page:'1';
        $ordered = is_string($request->ordered)? $request->ordered:"latest_created";
        $quotes = Cache::remember('quotes.'. $ordered .'.P'. $page, 10, function () use ($ordered){
            return (Quote::with('author')
            ->withReviewState('Passed')
            ->ordered($ordered)
           ->paginate(config('preference.quotes_per_page')));
        });
        return response()->success([
            'quotes' => QuoteResource::collection($quotes),
            'paginate' => new PaginateResource($quotes)
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(StoreQuote $form)//
     {
       if(auth('api')->user()->level<3){
            abort(412, "你的等级不足，暂不能提交题头。");
        }

        $last_quote = Quote::where('user_id', auth('api')->id())->orderBy('created_at','desc')->first();

        if($last_quote&&$last_quote->created_at>Carbon::now()->subDay(1)){
            abort(410, '一人一天只能提交一次题头');
        }

        $quote = $form->generateQuote();
        $quote->load('author');
        return response()->success(new QuoteResource($quote));
     }

    /**
     * Display the specified resource.
     *
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
     public function show($id)
     {
         // TODO 展示题头内容
         // $quote = $this->quoteProfile($id);
         // if(!$quote){abort(404);}
         //
         // $user = Auth::check()? CacheUser::Auser():'';
         // $info = Auth::check()? CacheUser::Ainfo():'';
         // return view('quotes.show', compact('user','info','quote'));
     }

     public function userQuote($id, Request $request)
     {
         if(auth('api')->id() != $id && !auth('api')->user()->isAdmin()) {abort(403);}

         $quotes = Quote::with('author')
         ->where('user_id', $id)
         ->ordered('latest_created')
         ->paginate(config('preference.quotes_per_page'));

         return response()->success([
             'quotes' => QuoteResource::collection($quotes),
             'paginate' => new PaginateResource($quotes),
         ]);
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Quote  $quote
     * @return \Illuminate\Http\Response
     */
     public function destroy($id)
     {
        if(auth('api')->user()->level<3){
            abort(412, "你的等级不足，暂不能删除题头。");
        }
        $quote=Quote::find($id);
        if(!$quote)
            abort(404, "找不到该题头。");
        if($quote->user_id!=auth('api')->id())
            abort(403, "你无权删除该题头。");
         $quote->delete();
         $this->clearQuote($id);
         return response()->success([
             'message' =>[
                 'success' => "成功删除题头",
             ],
            'quote_id' => $id,
        ]);
     }


     public function review_index(Request $request)
    {
        // $state = $request->withReviewState?? 'all';
        // $order = $request->order?? 'created_at';
        // $quotes = Quote::with('author','reviewer','admin_reviews.author')
        // ->withReviewState($state)
        // ->orderBy($order, 'desc')
        // ->paginate(config('preference.quotes_review_per_page'))
        // ->appends(['withReviewState'=>$state]);
        // return response()->success([
        //     'success' => $state,
        //     'quote' => $quotes,
        // ]);
     //   return view('quotes.review_index', compact('quotes'))->with('quote_review_tab', $state);
    }

    public function review(Quote $quote, Request $request)
    {
        $attitude = $request->attitude;

        switch ($attitude):
            case "approve"://通过题头
            if(!$quote->approved){
                $quote->approved = 1;
                $quote->reviewed = 1;
                $quote->reviewer_id = auth('api')->id();
                $quote->save();
            }else{
                abort(404,"该题头不处于待通过状态");
            }
            break;
            case "disapprove"://不通过题头(已经通过了的，不允许通过；或没有评价过的，不允许通过)
            if((!$quote->reviewed)||($quote->approved)){
                $quote->approved = 0;
                $quote->reviewed = 1;
                $quote->reviewer_id = auth('api')->id();
                $quote->save();
            }else{
                abort(404,"该题头不处于待不通过状态");
            }
            break;
            default:
                abort(599);
        endswitch;
        return response()->success([
            'success' => "成功审核题头",
            'quote' => $quote,
        ]);
    }
}
