<?php

namespace App\Support\Modules;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleRegistry
{
    public function __construct(private Filesystem $files)
    {
    }

    public function isEnabled(): bool
    {
        return (bool) config('modules.enabled', true);
    }

    public function rootPath(): string
    {
        return (string) config('modules.paths.root', base_path('Modules'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $root = $this->rootPath();
        if (!$this->files->isDirectory($root)) {
            return [];
        }

        $modules = [];

        foreach ($this->files->directories($root) as $path) {
            $folder = basename($path);
            if ($this->shouldIgnore($folder)) {
                continue;
            }

            $definition = $this->buildDefinition($path, $folder);
            if ($definition !== null) {
                $modules[] = $definition;
            }
        }

        usort(
            $modules,
            fn(array $a, array $b): int => strcmp((string) $a['name'], (string) $b['name'])
        );

        return $modules;
    }

    /**
     * @return array<int, string>
     */
    public function existingPaths(string $key): array
    {
        $paths = [];

        foreach ($this->all() as $module) {
            $path = $module['paths'][$key] ?? null;
            if (is_string($path) && $this->files->isDirectory($path)) {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildDefinition(string $path, string $folder): ?array
    {
        $manifest = $this->readManifest($path);
        if (array_key_exists('enabled', $manifest) && !$manifest['enabled']) {
            return null;
        }

        $name = (string) ($manifest['name'] ?? $folder);
        $slug = (string) ($manifest['slug'] ?? Str::kebab($folder));
        $structure = (array) config('modules.structure', []);

        $paths = [];
        foreach ($structure as $key => $relativePath) {
            if (!is_string($relativePath) || $relativePath === '') {
                continue;
            }

            $paths[$key] = $this->joinPath($path, $relativePath);
        }

        return [
            'name' => $name,
            'folder' => $folder,
            'slug' => $slug,
            'path' => $path,
            'namespace' => 'Modules\\' . $folder,
            'manifest' => $manifest,
            'paths' => $paths,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function readManifest(string $modulePath): array
    {
        $manifestPath = $this->joinPath($modulePath, 'module.json');
        if (!$this->files->exists($manifestPath)) {
            return [];
        }

        $decoded = json_decode((string) $this->files->get($manifestPath), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function shouldIgnore(string $folder): bool
    {
        if ($folder === '' || str_starts_with($folder, '.')) {
            return true;
        }

        if (str_starts_with($folder, '_')) {
            return true;
        }

        $ignored = array_map('strval', (array) config('modules.ignore', []));

        return in_array($folder, $ignored, true);
    }

    private function joinPath(string $base, string $relative): string
    {
        return rtrim($base, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relative, '/\\'));
    }
}
