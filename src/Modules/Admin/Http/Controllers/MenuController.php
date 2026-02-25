<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Admin\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
{
    $menus = Menu::with('children')
        ->whereNull('parent_id')
        ->orderBy('order')
        ->get();
      
    return view('layouts.principal', compact('menus'));
}
}
