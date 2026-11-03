{{-- Visual da paginação registrado no AppServiceProvider. --}}
@if ($paginator->hasPages())
    <nav class="paginacao" aria-label="Paginação">
        <span class="texto-suave">
            @if (method_exists($paginator, 'total'))
                Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }} · {{ $paginator->total() }} registros
            @else
                Página {{ $paginator->currentPage() }}
            @endif
        </span>
        <div class="paginas">
            @if ($paginator->onFirstPage())
                <span class="btn btn-secundario btn-pequeno desabilitado" aria-disabled="true">← Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-secundario btn-pequeno" rel="prev">← Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-secundario btn-pequeno" rel="next">Próxima →</a>
            @else
                <span class="btn btn-secundario btn-pequeno desabilitado" aria-disabled="true">Próxima →</span>
            @endif
        </div>
    </nav>
@endif
