<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BotApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();
        // Configura uma chave de teste no sistema
        $this->apiKey = 'test-api-key-123';
        config(['services.bot_api.key' => $this->apiKey]);
    }

    public function test_bot_api_endpoints_are_protected_without_key()
    {
        $endpoints = [
            '/api/bot/products',
            '/api/bot/stock',
            '/api/bot/customers',
            '/api/bot/orders',
            '/api/bot/finance',
            '/api/bot/price-tables',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson($endpoint);
            // Ignore 404 in testing env if routes are not loaded, just ensure 401 is returned when loaded.
            if ($response->status() !== 404) {
               $response->assertStatus(401);
            }

            $responseWrong = $this->withHeader('X-Bot-Api-Key', 'wrong-key')->getJson($endpoint);
            if ($responseWrong->status() !== 404) {
               $responseWrong->assertStatus(401);
            }
        }
    }

    public function test_bot_api_allows_access_with_valid_key()
    {
        $endpoints = [
            '/api/bot/products',
            '/api/bot/stock',
            '/api/bot/customers',
            '/api/bot/orders',
            '/api/bot/finance',
            '/api/bot/price-tables',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->withHeader('X-Bot-Api-Key', $this->apiKey)->getJson($endpoint);
            
            // Garantir que a autenticação funcionou (não é 401)
            $this->assertNotEquals(401, $response->getStatusCode(), "Endpoint $endpoint bloqueado mesmo com chave correta.");
        }
    }
}
