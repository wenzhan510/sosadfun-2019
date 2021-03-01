<?php
namespace App\Sosadfun\Traits;

trait AdministrationTraits{
    public function findAdminRecords($id, $page=1, $is_public)
    {
        return \App\Models\Administration::with('operator')
        ->withAdministratee($id)
        ->isPublic($is_public)
        ->latest()
        ->paginate(config('preference.index_per_page'))
        ->appends(['page'=>$page]);
    }
}
