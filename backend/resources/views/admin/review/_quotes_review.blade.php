@foreach ($quotes as $quote)
<div class="row text-center">
    <div class="col-xs-8">
        <div>
            <div>
                <a href="{{ route('quote.show', $quote->id) }}">{{ $quote->body }}</a>
            </div>
            <div class="font-6 font-weight-400">
                @if($quote->is_anonymous)
                <span class="grayout">{{$quote->majia ?? '匿名咸鱼'}}</span>
                @else
                <a href="{{route('user.show', $quote->user_id)}}">{{ $quote->author->name }}</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="col-xs-12">
            <button class="btn btn-md btn-success cancel-button approvebutton{{$quote->id}} quotebutton-show{{$quote->id}}"  type="button" name="button" onClick="admin_review_quote({{$quote->id}},'approve')">通过<i class="fa fa-check" aria-hidden="true"></i></button>
        </div>
        <div class="col-xs-12">
            <button class="btn btn-md btn-info cancel-button togglebutton{{$quote->id}} hidden quotebutton-review{{$quote->id}}"  type="button" name="button" onClick="reset_review_button({{$quote->id}})">重新审核</button>
        </div>
        <div class="col-xs-12">
            <button class="btn btn-md  btn-danger cancel-button disapprovebutton{{$quote->id}}  quotebutton-notshow{{$quote->id}}"  type="button" name="button" onClick="admin_review_quote({{$quote->id}},'disapprove')">不过<i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
    </div>
</div>

<hr>
@endforeach
