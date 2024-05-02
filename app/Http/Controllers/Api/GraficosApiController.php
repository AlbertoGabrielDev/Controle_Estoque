<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GraficosApiController extends Controller
{
    public function months(){
        return view('welcome');
    }
}
