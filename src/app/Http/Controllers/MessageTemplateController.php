<?php

namespace App\Http\Controllers;

use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $templates = MessageTemplate::all();
        return Inertia::render('ConfigSidebar/MessageTemplates', [
            'templates' => $templates
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'body' => 'required'
        ]);
      
        MessageTemplate::create($request->all());
        return redirect()->back();
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $request->validate([
            'name' => 'required',
            'body' => 'required'
        ]);
        $messageTemplate->update($request->all());
        return redirect()->back();
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();
        return redirect()->back();
    }
}
