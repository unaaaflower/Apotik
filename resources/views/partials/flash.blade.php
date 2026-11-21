@php
    /*
     * Partial: pesan sukses/error sekali tampil.
     * Variabel: $pesanFlash = ['ok' => bool, 'pesan' => string]
     * atau null.
     */

    $pesanFlash = $pesanFlash ?? session()->pull('flash');
@endphp

@if (!empty($pesanFlash))
    <div class="form-message{{ $pesanFlash['ok'] ? '' : ' is-error' }}"
         role="status"
         style="text-align:left; margin-bottom:16px;">
        {{ $pesanFlash['pesan'] }}
    </div>
@endif
