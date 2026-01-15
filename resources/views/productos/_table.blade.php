@forelse($productos as $producto)
    <tr id="producto-{{ $producto->id }}">
        <td class="min-width">
            <p>{{ $producto->id }}</p>
        </td>
        <td class="min-width">
            <span class="view truncate truncate-long" 
                  data-field="nombre" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="{{ $producto->nombre }}">
                {{ $producto->nombre }}
            </span>
            <input class="edit" data-field="nombre" type="text" value="{{ $producto->nombre }}" hidden>
        </td>
        <td class="min-width">
            <span class="view truncate" 
                  data-field="precio" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="${{ number_format($producto->precio, 0, ',', '.') }}">
                ${{ number_format($producto->precio, 0, ',', '.') }}
            </span>
            <input class="edit precio_input" data-field="precio" type="text" inputmode="numeric" value="{{ number_format($producto->precio, 0, ',', '.') }}" hidden>
        </td>
        @if($empresa && $empresa->cobra_iva)
            <td class="min-width">
              <span class="view truncate" 
                  data-field="iva" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="{{ $producto->iva > 0 ? $producto->iva . '%' : '-' }}">
                {{ $producto->iva > 0 ? $producto->iva . '%' : '-' }}
              </span>
              <input class="edit iva_input" data-field="iva" type="number" step="1" value="{{ $producto->iva }}" hidden>
            </td>
            <td class="min-width">
                <span class="view truncate precio_con_iva_span" 
                      data-field="precio_con_iva" 
                      data-bs-toggle="tooltip" 
                      data-bs-title="${{ number_format($producto->precio_con_iva, 0, ',', '.') }}">
                    ${{ number_format($producto->precio_con_iva, 0, ',', '.') }}
                </span>
                <input class="edit" data-field="precio_con_iva" type="text" value="{{ number_format($producto->precio_con_iva, 0, ',', '.') }}" hidden readonly>
            </td>
        @endif
        <td class="min-width text-center">
          <span class="view stock_view" 
              data-field="stock" 
              data-bs-toggle="tooltip" 
              data-bs-title="{{ $producto->stock }}">
            {{ $producto->stock }}
          </span>
          <input class="edit stock_input" data-field="stock" type="text" value="{{ $producto->stock }}" data-original-stock="{{ $producto->stock }}" hidden>
        </td>
        <td>
            <div class="action">
                <button type="button" class="icon-yelow" onclick="editarProducto({{ $producto->id }})" data-bs-toggle="tooltip" data-bs-title="Editar">
                    <i class="lni lni-pencil"></i>
                </button>
                <button type="button" class="icon-red" onclick="eliminarProducto({{ $producto->id }})" data-bs-toggle="tooltip" data-bs-title="Eliminar">
                    <i class="lni lni-trash-can"></i>
                </button>
                <button type="button" class="icon-green" onclick="guardarProducto({{ $producto->id }})" hidden data-bs-toggle="tooltip" data-bs-title="Guardar">
                    <i class="lni lni-checkmark-circle"></i>
                </button>
                <button type="button" class="icon-red" onclick="cancelarEdicion({{ $producto->id }})" hidden data-bs-toggle="tooltip" data-bs-title="Cancelar">
                    <i class="lni lni-close"></i>
                </button>
            </div>
            <span class="msg"></span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
            <i class="lni lni-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
            <p>No hay productos registrados</p>
        </td>
    </tr>
@endforelse
