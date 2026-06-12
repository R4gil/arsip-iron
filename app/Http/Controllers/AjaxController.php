<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Rack;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function cabinetsByLocation(Request $request)
    {
        $locationId = $request->input('location_id');

        return Cabinet::where('location_id', $locationId)->select('id', 'nama_lemari')->get();
    }

    public function racksByCabinet(Request $request)
    {
        $cabinetId = $request->input('cabinet_id');

        return Rack::where('cabinet_id', $cabinetId)->select('id', 'nama_rak')->get();
    }
}
