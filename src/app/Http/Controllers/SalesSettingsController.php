<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesSettingsRequest;
use App\Services\AppSettingService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SalesSettingsController extends Controller
{
    public function __construct(private AppSettingService $settings)
    {
    }

    public function index(): InertiaResponse
    {
        return Inertia::render('Settings/Sales', [
            'requireClient' => $this->settings->getBool(AppSettingService::KEY_SALES_REQUIRE_CLIENT, true),
        ]);
    }

    public function update(SalesSettingsRequest $request)
    {
        $this->settings->setBool(
            AppSettingService::KEY_SALES_REQUIRE_CLIENT,
            (bool) $request->boolean('require_client')
        );

        return back()->with('success', 'Configuracao de vendas atualizada.');
    }
}
