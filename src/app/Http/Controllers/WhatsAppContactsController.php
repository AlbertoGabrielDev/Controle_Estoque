<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppContactsController extends Controller
{
    protected Client $http;
    protected string $base;

    public function __construct()
    {
        $this->base = rtrim(config('services.whatsapp_node.url'), '/');
        $this->http = new Client([
            'timeout' => 15,
            'http_errors' => false,
        ]);
    }

    public function index(Request $request)
    {
        // Busca contatos com conversa + labels disponíveis para montar a página
        $contacts = $this->getJson("{$this->base}/contacts-with-dialog", []);
        $labels   = $this->getJson("{$this->base}/labels", []);

        return Inertia::render('Contacts/Contacts', [
            'contacts' => $contacts,
            'labels'   => $labels,
        ]);
    }

    public function createLabel(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:60',
            'labelColor' => 'nullable|string|max:20',
        ]);

        $res = $this->postJson("{$this->base}/labels", $data);

        return back()->with('flash', [
            'ok' => empty($res['error']),
            'message' => $res['error'] ?? 'Etiqueta criada',
        ]);
    }

    public function assignLabel(Request $request)
    {
        $data = $request->validate([
            'labelId' => 'required',
            'type'    => 'required|in:add,remove',
            'phones'  => 'required|array|min:1',
            'phones.*'=> 'string',
        ]);

        $res = $this->postJson("{$this->base}/labels/assign", $data);

        return back()->with('flash', [
            'ok' => empty($res['error']),
            'message' => $res['error'] ?? 'Etiqueta aplicada',
        ]);
    }

    public function deleteLabel(Request $request, $id)
    {
        $res = $this->deleteJson("{$this->base}/labels/{$id}");
        return back()->with('flash', [
            'ok' => empty($res['error']),
            'message' => $res['error'] ?? 'Etiqueta removida',
        ]);
    }

    // ------------------------
    // Helpers HTTP
    // ------------------------
    protected function getJson(string $url, $fallback = [])
    {
        try {
            $r = $this->http->get($url);
            $b = json_decode((string)$r->getBody(), true);
            return is_array($b) ? $b : $fallback;
        } catch (\Throwable $e) {
            Log::warning('getJson fail', ['url' => $url, 'err' => $e->getMessage()]);
            return $fallback;
        }
    }

    protected function postJson(string $url, array $payload)
    {
        try {
            $r = $this->http->post($url, ['json' => $payload]);
            return json_decode((string)$r->getBody(), true) ?? [];
        } catch (\Throwable $e) {
            Log::warning('postJson fail', ['url' => $url, 'err' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    protected function deleteJson(string $url)
    {
        try {
            $r = $this->http->delete($url);
            return json_decode((string)$r->getBody(), true) ?? [];
        } catch (\Throwable $e) {
            Log::warning('deleteJson fail', ['url' => $url, 'err' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
