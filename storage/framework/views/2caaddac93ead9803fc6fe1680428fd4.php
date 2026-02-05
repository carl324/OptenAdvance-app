<?php
    $showActions = isset($showActions) ? (bool)$showActions : true;
    $colspan = 4; // id, nombre, precio, stock
    if(isset($empresa) && $empresa && $empresa->cobra_iva) $colspan += 2; // IVA + precio final
    if($showActions) $colspan += 1;
?>

<?php $__empty_1 = true; $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr id="producto-<?php echo e($producto->id); ?>">
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
        <td class="min-width">
            <span class="view truncate" 
                  data-field="precio" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="$<?php echo e(number_format($producto->precio, 0, ',', '.')); ?>">
                $<?php echo e(number_format($producto->precio, 0, ',', '.')); ?>

            </span>
            <input class="edit precio_input" data-field="precio" type="text" inputmode="numeric" value="<?php echo e(number_format($producto->precio, 0, ',', '.')); ?>" hidden>
        </td>
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
            <?php echo e($producto->stock); ?>

          </span>
          <input class="edit stock_input" data-field="stock" type="text" value="<?php echo e($producto->stock); ?>" data-original-stock="<?php echo e($producto->stock); ?>" hidden>
        </td>
        <?php if($showActions): ?>
        <td>
            <div class="action">
                <button type="button" class="icon-yelow" onclick="editarProducto(<?php echo e($producto->id); ?>)" data-bs-toggle="tooltip" data-bs-title="Editar">
                    <i class="lni lni-pencil"></i>
                </button>
                <button type="button" class="icon-red" onclick="eliminarProducto(<?php echo e($producto->id); ?>)" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                    <i class="lni lni-trash-can"></i>
                </button>
                <button type="button" class="icon-green" onclick="guardarProducto(<?php echo e($producto->id); ?>)" hidden data-bs-toggle="tooltip" data-bs-title="Guardar">
                    <i class="lni lni-checkmark-circle"></i>
                </button>
                <button type="button" class="icon-red" onclick="cancelarEdicion(<?php echo e($producto->id); ?>)" hidden data-bs-toggle="tooltip" data-bs-title="Cancelar">
                    <i class="lni lni-close"></i>
                </button>
            </div>
            <span class="msg"></span>
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
<?php endif; ?>
<?php /**PATH C:\Users\User\Documents\optenadvance\laragon\www\optenadvance\resources\views/productos/_table.blade.php ENDPATH**/ ?>