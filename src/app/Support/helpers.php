<?php

if (! function_exists('current_unidade')) {
    /**
     * Retorna a unidade atual (ou null).
     * Preenchida pelo middleware SyncUnidade e registrada no container.
     */
    function current_unidade()
    {
        return app()->bound('current.unidade') ? app('current.unidade') : null;
    }
}
