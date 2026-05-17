<?php if($productos->count() > 0): ?>
    <div class="pagination">
        <button id="btn-prev" onclick="cargarPaginaAjax(<?php echo e($productos->currentPage() - 1); ?>)" <?php if($productos->onFirstPage()): ?> disabled <?php endif; ?>>
            <i class="lni lni-chevron-left"></i>
        </button>
        <span class="page-info">Página <strong id="current-page"><?php echo e($productos->currentPage()); ?></strong> de <strong id="last-page"><?php echo e($productos->lastPage()); ?></strong></span>
        <button id="btn-next" onclick="cargarPaginaAjax(<?php echo e($productos->currentPage() + 1); ?>)" <?php if($productos->currentPage() == $productos->lastPage()): ?> disabled <?php endif; ?>>
            <i class="lni lni-chevron-right"></i>
        </button>
    </div>
<?php endif; ?>
<?php /**PATH C:\OptenAdvance\app\www\resources\views\productos\_pagination.blade.php ENDPATH**/ ?>