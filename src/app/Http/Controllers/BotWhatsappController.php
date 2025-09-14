<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Models\MessageTemplate;
class BotWhatsappController extends Controller
{
    function index()
    {
        $templates = MessageTemplate::all();
        return Inertia::render('Wpp/Marketing/BotWhatsapp',[
            'templates' => $templates,
        ]);
    }


    public function sendMass(Request $request)
    {
        $contacts = $request->input('contacts', []);
        $message = $request->input('message');
        $results = [];

        foreach ($contacts as $contact) {
            $personalized = str_replace(
                ['{nome}', '{telefone}'],
                [$contact['name'], $contact['phone']],
                $message
            );

            $response = Http::withToken(config('services.wppconnect.token'))
                ->post(config('services.wppconnect.base_url') . '/' . config('services.wpcponnect.session') . '/send-message', [
                    'phone' => $contact['phone'],
                    'message' => $personalized,
                ]);

            $results[] = [
                'phone' => $contact['phone'],
                'status' => $response->successful() ? 'enviado' : 'erro',
                'response' => $response->json(),
            ];
        }
        return response()->json($results);
    }
}
