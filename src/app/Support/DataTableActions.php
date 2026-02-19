<?php

namespace App\Support;

class DataTableActions
{
    public static function edit(string $routeName, string|int $id, bool $canEdit = true): string
    {
        if (!$canEdit) {
            return '';
        }

        $url = route($routeName, $id);

        return sprintf(
            '<a href="%s" class="p-2 text-cyan-600 hover:bg-cyan-50 rounded-md inline-flex items-center" title="Editar"><i class="fas fa-edit"></i></a>',
            e($url)
        );
    }

    public static function status(
        string $routeName,
        string $modelName,
        string|int $id,
        bool|int|null $status,
        bool $canToggle = true
    ): string {
        if (!$canToggle) {
            return '';
        }

        $isActive = (bool) $status;
        $url = route($routeName, ['modelName' => $modelName, 'id' => $id]);
        $classes = $isActive
            ? 'bg-green-500 hover:bg-green-600'
            : 'bg-red-400 hover:bg-red-500';

        return sprintf(
            '<button type="button" class="toggle-status inline-flex items-center justify-center w-10 h-10 rounded-full transition %s" data-url="%s" data-active="%d" aria-pressed="%s" title="%s"><i class="fa-solid fa-power-off text-white"></i></button>',
            $classes,
            e($url),
            $isActive ? 1 : 0,
            $isActive ? 'true' : 'false',
            $isActive ? 'Desativar' : 'Ativar'
        );
    }

    public static function delete(
        string $routeName,
        string|int $id,
        string $label = 'Excluir',
        string $confirmMessage = 'Excluir este registro?'
    ): string {
        $url = route($routeName, $id);
        $token = csrf_token();

        return sprintf(
            '<form method="POST" action="%s" onsubmit="return confirm(\'%s\');" class="inline-block"><input type="hidden" name="_token" value="%s"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="px-2 py-1 text-sm rounded bg-red-50 hover:bg-red-100 text-red-700">%s</button></form>',
            e($url),
            e($confirmMessage),
            e($token),
            e($label)
        );
    }

    public static function wrap(array $actions, string $justify = 'start'): string
    {
        $html = trim(implode('', array_filter($actions)));

        if ($html === '') {
            $html = '<span class="inline-block w-8 h-8 opacity-0" aria-hidden="true">&nbsp;</span>';
        }

        return sprintf('<div class="flex gap-2 justify-%s items-center">%s</div>', e($justify), $html);
    }
}
