<?php

namespace App\Support;

/**
 * Router sederhana dengan API yang meniru Laravel:
 *
 *   Route::get('/login', [AuthController::class, 'login'])->name('login');
 *   Route::match(['GET', 'POST'], '/profil', [ProfilController::class, 'index']);
 *   Route::middleware('auth')->group(function () { ... });
 *   Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () { ... });
 */
final class Route
{
    /** @var array<int, array{methods: array, uri: string, action: mixed, middleware: array, name: ?string}> */
    private static array $routes = [];

    /** @var array<int, array{prefix: string, middleware: array}> */
    private static array $groups = [];

    public static function get(string $uri, $action): RouteDefinition
    {
        return self::add(['GET', 'HEAD'], $uri, $action);
    }

    public static function post(string $uri, $action): RouteDefinition
    {
        return self::add(['POST'], $uri, $action);
    }

    public static function match(array $methods, string $uri, $action): RouteDefinition
    {
        return self::add(array_map('strtoupper', $methods), $uri, $action);
    }

    public static function middleware($middleware): RouteGroup
    {
        return (new RouteGroup())->middleware($middleware);
    }

    public static function prefix(string $prefix): RouteGroup
    {
        return (new RouteGroup())->prefix($prefix);
    }

    private static function add(array $methods, string $uri, $action): RouteDefinition
    {
        $prefix = '';
        $middleware = [];
        foreach (self::$groups as $g) {
            $prefix .= '/' . trim($g['prefix'], '/');
            $middleware = array_merge($middleware, $g['middleware']);
        }

        $index = count(self::$routes);
        self::$routes[$index] = [
            'methods'    => $methods,
            'uri'        => '/' . trim($prefix . '/' . trim($uri, '/'), '/'),
            'action'     => $action,
            'middleware' => $middleware,
            'name'       => null,
        ];

        return new RouteDefinition($index);
    }

    /** @internal dipakai RouteGroup */
    public static function pushGroup(string $prefix, array $middleware): void
    {
        self::$groups[] = ['prefix' => $prefix, 'middleware' => $middleware];
    }

    /** @internal dipakai RouteGroup */
    public static function popGroup(): void
    {
        array_pop(self::$groups);
    }

    /** @internal dipakai RouteDefinition */
    public static function update(int $index, string $kunci, $nilai): void
    {
        if ($kunci === 'middleware') {
            self::$routes[$index]['middleware'] = array_merge(self::$routes[$index]['middleware'], (array) $nilai);
        } else {
            self::$routes[$index][$kunci] = $nilai;
        }
    }

    /** Path dari nama route (dipakai helper route()). */
    public static function path(string $nama): string
    {
        foreach (self::$routes as $r) {
            if ($r['name'] === $nama) {
                return $r['uri'];
            }
        }
        return '/';
    }

    /** Cocokkan request dengan route, jalankan middleware, lalu panggil controller. */
    public static function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $pathCocok = false;

        foreach (self::$routes as $r) {
            if ($r['uri'] !== $path) {
                continue;
            }
            $pathCocok = true;
            if (!in_array($method, $r['methods'], true)) {
                continue;
            }

            $alias = (array) config('app.middleware', []);
            foreach ($r['middleware'] as $nama) {
                if (isset($alias[$nama])) {
                    (new $alias[$nama]())->handle();
                }
            }

            $aksi = $r['action'];
            if (is_array($aksi)) {
                [$kelas, $fungsi] = $aksi;
                (new $kelas())->$fungsi();
            } else {
                $aksi();
            }
            return;
        }

        abort($pathCocok ? 405 : 404);
    }
}
