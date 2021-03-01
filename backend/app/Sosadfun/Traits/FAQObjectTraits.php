<?php
namespace App\Sosadfun\Traits;

use Cache;
use DB;

trait FAQObjectTraits{


    public function all_faqs()
    {
        return Cache::remember('all_faqs', 30, function () {
            return \App\Models\Helpfaq::all();
        });
    }

    public function clear_all_faqs()
    {
        Cache::forget('all_faqs');
    }

    public function get_faq_keys()
    {
        $faq_keys = [];
        foreach(config('faq') as $key1=>$value1)
        {
            foreach($value1['children'] as $key2 => $value2)
            {
                $combokey = $key1.'-'.$key2;
                array_push($faq_keys, $combokey);
            }
        }
        return $faq_keys;
    }

}
