<?php

namespace App\Support;

/** Hasil Route::get()/post()/match(): mendukung ->name() dan ->middleware(). */
final class RouteDefinition
{
    private int $index;

    public function __construct(int $index)
    {
        $this->index = $index;
    }

    public function name(string $nama): self
    {
        Route::update($this->index, 'name', $nama);
        return $this;
    }

    public function middleware($middleware): self
    {
        Route::update($this->index, 'middleware', $middleware);
        return $this;
    }
}
