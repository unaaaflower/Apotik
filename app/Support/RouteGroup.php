<?php

namespace App\Support;

/** Hasil Route::middleware()/prefix(): mengelompokkan route lewat ->group(function () { ... }). */
final class RouteGroup
{
    private string $prefix = '';
    private array $middleware = [];

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function middleware($middleware): self
    {
        $this->middleware = array_merge($this->middleware, (array) $middleware);
        return $this;
    }

    public function group(callable $daftar): void
    {
        Route::pushGroup($this->prefix, $this->middleware);
        $daftar();
        Route::popGroup();
    }
}
