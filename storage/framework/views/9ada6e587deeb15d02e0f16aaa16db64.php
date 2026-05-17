<?php
    $showActions = isset($showActions) ? (bool)$showActions : true;
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    
    // Calcular colspan dinámicamente
    $colspan = 3; // id, nombre, stock (siempre visibles)
    
    if($isAdmin) {
        $colspan += 2; // precio_compra + ganancia (solo admin)
    }
    
    $colspan += 1; // precio_venta (siempre visible)
    
    if(isset($empresa) && $empresa && $empresa->cobra_iva) {
        $colspan += 2; // IVA + precio final
    }
    
    if($showActions) {
        $colspan += 1; // acciones
    }
?>

<?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr id="producto-<?php echo e($producto->id); ?>" data-codigo-barras="<?php echo e($producto->codigo_barras ?? ''); ?>" data-unidad="<?php echo e($producto->unidad ?? 'Unidad'); ?>">
        <td class="min-width">
            <p><?php echo e($producto->id); ?></p>
        </td>
        <td class="min-width">
            <span class="view truncate truncate-long" 
                  data-field="nombre" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="<?php echo e($producto->nombre); ?>">
                <?php echo e($producto->nombre); ?>

            </span>
            <input class="edit" data-field="nombre" type="text" value="<?php echo e($producto->nombre); ?>" hidden>
        </td>
        
        <?php if($isAdmin): ?>
            
            <td class="min-width">
                <span class="view truncate" 
                      data-field="precio_compra" 
                      data-bs-toggle="tooltip" 
                      data-bs-title="$<?php echo e(number_format($producto->precio_compra, 0, ',', '.')); ?>">
                    $<?php echo e(number_format($producto->precio_compra, 0, ',', '.')); ?>

                </span>
                <input class="edit precio_input" data-field="precio_compra" type="text" inputmode="numeric" value="<?php echo e(number_format($producto->precio_compra, 0, ',', '.')); ?>" hidden>
            </td>
        <?php endif; ?>

        
        <td class="min-width">
            <span class="view truncate" 
                  data-field="precio_venta" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="$<?php echo e(number_format($producto->precio_venta, 0, ',', '.')); ?>">
                $<?php echo e(number_format($producto->precio_venta, 0, ',', '.')); ?>

            </span>
            <input class="edit precio_input" data-field="precio_venta" type="text" inputmode="numeric" value="<?php echo e(number_format($producto->precio_venta, 0, ',', '.')); ?>" hidden>
        </td>

        <?php if($isAdmin): ?>
            
            <td class="min-width">
                <?php
                    $ganancia = $producto->ganancia ?? 0;
                    $margen = $producto->margen_porcentaje ?? 0;
                    $color = $ganancia >= 0 ? '#28a745' : '#dc3545';
                ?>
                <span class="view truncate" 
                      style="color: <?php echo e($color); ?>; font-weight: 600;" 
                      data-bs-toggle="tooltip" 
                      data-bs-title="$<?php echo e(number_format($ganancia, 0, ',', '.')); ?> (<?php echo e(number_format($margen, 1)); ?>%)">
                    $<?php echo e(number_format($ganancia, 0, ',', '.')); ?>

                </span>
            </td>
        <?php endif; ?>
        
        <?php if($empresa && $empresa->cobra_iva): ?>
            <td class="min-width">
              <span class="view truncate" 
                  data-field="iva" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="<?php echo e($producto->iva > 0 ? $producto->iva . '%' : '-'); ?>">
                <?php echo e($producto->iva > 0 ? $producto->iva . '%' : '-'); ?>

              </span>
              <input class="edit iva_input" data-field="iva" type="number" step="1" value="<?php echo e($producto->iva); ?>" hidden>
            </td>
            <td class="min-width">
                <span class="view truncate precio_con_iva_span" 
                      data-field="precio_con_iva" 
                      data-bs-toggle="tooltip" 
                      data-bs-title="$<?php echo e(number_format($producto->precio_con_iva, 0, ',', '.')); ?>">
                    $<?php echo e(number_format($producto->precio_con_iva, 0, ',', '.')); ?>

                </span>
                <input class="edit" data-field="precio_con_iva" type="text" value="<?php echo e(number_format($producto->precio_con_iva, 0, ',', '.')); ?>" hidden readonly>
            </td>
        <?php endif; ?>
        
        <td class="min-width">
          <span class="view stock_view" 
              data-field="stock" 
              data-bs-toggle="tooltip" 
              data-bs-title="<?php echo e($producto->stock); ?>">
            <?php echo e($producto->stock); ?> <small style="color:#94a3b8;font-size:0.75rem;"><?php echo e($producto->unidad ?? 'und'); ?></small>
          </span>
          <input class="edit stock_input" data-field="stock" type="text" value="<?php echo e($producto->stock); ?>" data-original-stock="<?php echo e($producto->stock); ?>" hidden>
        </td>
        
<?php if($showActions): ?>
<td>
    <div class="producto-dropdown" id="dropdown-<?php echo e($producto->id); ?>">
        <button type="button" class="dropdown-trigger" onclick="toggleDropdown(<?php echo e($producto->id); ?>, event)">
            <i class="lni lni-more-alt"></i>
        </button>
        <div class="dropdown-menu-custom" id="dropdown-menu-<?php echo e($producto->id); ?>">
            <button type="button" onclick="abrirModalEditar(<?php echo e($producto->id); ?>); cerrarTodosDropdowns()">
                <i class="lni lni-pencil"></i> Editar
            </button>
            <button type="button" class="danger" onclick="eliminarProducto(<?php echo e($producto->id); ?>); cerrarTodosDropdowns()">
                <i class="lni lni-trash-can"></i> Eliminar
            </button>
        </div>
    </div>
    <span class="msg" id="msg-<?php echo e($producto->id); ?>"></span>
</td>
<?php endif; ?>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="<?php echo e($colspan); ?>" style="text-align: center; padding: 40px; color: #999;">
            <i class="lni lni-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
            <p>No hay productos registrados</p>
        </td>
    </tr>
<?php endif; ?><?php /**PATH C:\OptenAdvance\app\www\resources\views\productos\_table.blade.php ENDPATH**/ ?>