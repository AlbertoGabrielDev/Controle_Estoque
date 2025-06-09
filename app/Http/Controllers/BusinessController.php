<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Places as GooglePlaces;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class BusinessController extends Controller
{
    public function index()
    {

        if (empty(env('GOOGLE_API_KEY'))) {
            abort(500, 'Chave do Google Maps não configurada');
        }

        return Inertia::render('Marketing/BotMaps', [
            'googleMapsApiKey' => env('GOOGLE_API_KEY'),
        ]);
    }

    public function extractFromUrl(Request $request)
    {
        $urlData = $this->parseGoogleMapsUrl($request->maps_url);

        if (!$urlData) {
            return response()->json(['error' => 'URL do Google Maps inválida'], 400);
        }

        $apiKey = env('GOOGLE_API_KEY');
        $allBusinesses = [];
        $nextPageToken = null;
        $maxPages = 3;
        $maxResults = 60;
        do {
            $params = [
                'location' => "{$urlData['latitude']},{$urlData['longitude']}",
                'radius' => $urlData['radius'] ?? 1500,
                'key' => $apiKey,
            ];

            // Adiciona parâmetros opcionais
            if (!empty($urlData['query'])) {
                $params['keyword'] = $urlData['query'];
            }
            if (!empty($urlData['type'])) {
                $params['type'] = $urlData['type'];
            }
            if ($nextPageToken) {
                $params['pagetoken'] = $nextPageToken;
                sleep(2);
            }

            $nearbyResponse = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', $params);

            if (!$nearbyResponse->ok()) {
                return response()->json(['error' => 'Erro ao consultar a API do Google Places.'], 500);
            }

            $results = $nearbyResponse->json()['results'] ?? [];
            $nextPageToken = $nearbyResponse->json()['next_page_token'] ?? null;

            foreach ($results as $place) {
                $detailsResponse = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                    'place_id' => $place['place_id'],
                    'fields' => 'formatted_phone_number,international_phone_number,website,opening_hours,formatted_address,business_status',
                    'key' => $apiKey,
                ]);

                $details = $detailsResponse->json()['result'] ?? [];

                $allBusinesses[] = [
                    'id' => $place['place_id'],
                    'name' => $place['name'] ?? null,
                    'address' => $place['vicinity'] ?? $details['formatted_address'] ?? null,
                    'latitude' => $place['geometry']['location']['lat'] ?? null,
                    'longitude' => $place['geometry']['location']['lng'] ?? null,
                    'rating' => $place['rating'] ?? null,
                    'total_ratings' => $place['user_ratings_total'] ?? null,
                    'open_now' => $place['opening_hours']['open_now'] ?? null,
                    'phone' => $details['formatted_phone_number'] ?? $details['international_phone_number'] ?? null,
                    'website' => $details['website'] ?? null,
                    'business_status' => $details['business_status'] ?? null,
                    'photo' => $place['photos'][0]['photo_reference'] ?? null,
                    'types' => $place['types'] ?? [],
                ];

                if (count($allBusinesses) >= $maxResults) {
                    $nextPageToken = null;
                    break 2; 
                }
            }

            $maxPages--;
        } while ($nextPageToken && $maxPages > 0);

        return response()->json([
            'businesses' => $allBusinesses,
            'next_page_token' => $nextPageToken,
            'search_metadata' => $urlData,
        ]);
    }

    private function parseGoogleMapsUrl($url)
    {
        // Extrai coordenadas da URL (formato: @lat,lng,zoom)
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            $data = [
                'latitude' => (float)$matches[1],
                'longitude' => (float)$matches[2],
            ];

            // Tenta extrair o zoom (pode conter z no final)
            if (preg_match('/@[^,]+,([^,]+),(\d+\.?\d*)z/', $url, $zoomMatches)) {
                // Converte zoom para raio aproximado em metros
                $data['radius'] = $this->zoomToRadius((float)$zoomMatches[2]);
            } else {
                $data['radius'] = 1000; // Raio padrão
            }

            // Extrai query/search se existir
            if (preg_match('/\/search\/([^\/]+)\//', $url, $queryMatches)) {
                $data['query'] = urldecode(str_replace('+', ' ', $queryMatches[1]));
            } elseif (preg_match('/query=([^&]+)/', $url, $queryMatches)) {
                $data['query'] = urldecode(str_replace('+', ' ', $queryMatches[1]));
            }

            // Extrai type se existir
            if (preg_match('/type=([^&]+)/', $url, $typeMatches)) {
                $data['type'] = $typeMatches[1];
            }

            return $data;
        }

        return null;
    }

    private function zoomToRadius($zoom)
    {
        $zoomLevels = [
            20 => 50,
            19 => 100,
            18 => 200,
            17 => 400,
            16 => 800,
            15 => 1500,
            14 => 3000,
            13 => 6000,
            12 => 12000,
            11 => 25000,
            10 => 50000,
            9 => 100000,
        ];

        return $zoomLevels[round($zoom)] ?? 1000;
    }
}
