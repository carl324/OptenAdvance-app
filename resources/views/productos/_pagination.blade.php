@if($productos->count() > 0)
    <div class="pagination">
        <button id="btn-prev" onclick="cargarPaginaAjax({{ $productos->currentPage() - 1 }})" @if($productos->onFirstPage()) disabled @endif>
            <i class="lni lni-chevron-left"></i>
        </button>
        <span class="page-info">Página <strong id="current-page">{{ $productos->currentPage() }}</strong> de <strong id="last-page">{{ $productos->lastPage() }}</strong></span>
        <button id="btn-next" onclick="cargarPaginaAjax({{ $productos->currentPage() + 1 }})" @if($productos->currentPage() == $productos->lastPage()) disabled @endif>
            <i class="lni lni-chevron-right"></i>
        </button>
    </div>
@endif
