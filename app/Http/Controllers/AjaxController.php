<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Rack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function cabinetsByLocation(Request $request)
    {
        $locationId = $request->input('location_id');

        return Cabinet::where('ruangarsip_id', $locationId)
            ->select('lemari_id as id', 'lemari_nama')
            ->get();
    }

    public function racksByCabinet(Request $request)
    {
        $cabinetId = $request->input('cabinet_id');

        return Rack::where('lemari_id', $cabinetId)->select('rak_id as id', 'rak_nama')->get();
    }

    public function searchClassifications(Request $request)
    {
        $term = trim($request->query('q', ''));

        $query = DB::table('klasifikasi');

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('kode', 'like', "%{$term}%")
                    ->orWhere('nama', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('kode')->limit(30)->get(['id', 'kode', 'nama']);
    }
}
