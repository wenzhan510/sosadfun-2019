<?php
namespace App\Sosadfun\Traits;

use Cache;

trait QuoteObjectTraits{

    public function quoteProfile($id)
    {
        return Cache::remember('quoteProfile.'.$id, 5, function () use($id) {
            $quote = \App\Models\Quote::find($id);
            if(!$quote){
                return;
            }
            $quote->load('author','admin_reviews.author','reviewer');

            $quote->setAttribute('recent_rewards', $quote->latest_rewards());

            return $quote;
        });
    }
    public function clearQuote($id)
    {
        return Cache::forget('quoteProfile.'.$id);
    }

}
