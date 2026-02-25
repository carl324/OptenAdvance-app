@php
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
@endphp

@forelse($productos as $producto)
    <tr id="producto-{{ $producto->id }}" data-codigo-barras="{{ $producto->codigo_barras ?? '' }}">
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
        
        @if($isAdmin)
            {{-- Precio Compra (solo admin) --}}
            <td class="min-width">
                <span class="view truncate" 
                      data-field="precio_compra" 
                      data-bs-toggle="tooltip" 
                      data-bs-title="${{ number_format($producto->precio_compra, 0, ',', '.') }}">
                    ${{ number_format($producto->precio_compra, 0, ',', '.') }}
                </span>
                <input class="edit precio_input" data-field="precio_compra" type="text" inputmode="numeric" value="{{ number_format($producto->precio_compra, 0, ',', '.') }}" hidden>
            </td>
        @endif

        {{-- Precio Venta (siempre visible) --}}
        <td class="min-width">
            <span class="view truncate" 
                  data-field="precio_venta" 
                  data-bs-toggle="tooltip" 
                  data-bs-title="${{ number_format($producto->precio_venta, 0, ',', '.') }}">
                ${{ number_format($producto->precio_venta, 0, ',', '.') }}
            </span>
            <input class="edit precio_input" data-field="precio_venta" type="text" inputmode="numeric" value="{{ number_format($producto->precio_venta, 0, ',', '.') }}" hidden>
        </td>

        @if($isAdmin)
            {{-- Ganancia (solo admin) --}}
            <td class="min-width">
                @php
                    $ganancia = $producto->ganancia ?? 0;
                    $margen = $producto->margen_porcentaje ?? 0;
                    $color = $ganancia >= 0 ? '#28a745' : '#dc3545';
                @endphp
                <span class="view truncate" 
                      style="color: {{ $color }}; font-weight: 600;" 
                      data-bs-toggle="tooltip" 
                      data-bs-title="${{ number_format($ganancia, 0, ',', '.') }} ({{ number_format($margen, 1) }}%)">
                    ${{ number_format($ganancia, 0, ',', '.') }}
                </span>
            </td>
        @endif
        
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
        
        <td class="min-width">
          <span class="view stock_view" 
              data-field="stock" 
              data-bs-toggle="tooltip" 
              data-bs-title="{{ $producto->stock }}">
            {{ $producto->stock }}
          </span>
          <input class="edit stock_input" data-field="stock" type="text" value="{{ $producto->stock }}" data-original-stock="{{ $producto->stock }}" hidden>
        </td>
        
@if($showActions)
<td>
    <div class="producto-dropdown" id="dropdown-{{ $producto->id }}">
        <button type="button" class="dropdown-trigger" onclick="toggleDropdown({{ $producto->id }}, event)">
            <i class="lni lni-more-alt"></i>
        </button>
        <div class="dropdown-menu-custom" id="dropdown-menu-{{ $producto->id }}">
            <button type="button" onclick="abrirModalEditar({{ $producto->id }}); cerrarTodosDropdowns()">
                <i class="lni lni-pencil"></i> Editar
            </button>
            <button type="button" class="danger" onclick="eliminarProducto({{ $producto->id }}); cerrarTodosDropdowns()">
                <i class="lni lni-trash-can"></i> Eliminar
            </button>
        </div>
    </div>
    <span class="msg" id="msg-{{ $producto->id }}"></span>
</td>
@endif
    </tr>
@empty
    <tr>
        <td colspan="{{ $colspan }}" style="text-align: center; padding: 40px; color: #999;">
            <i class="lni lni-inbox" style="font-size: 32px; margin-bottom: 10px;"></i>
            <p>No hay productos registrados</p>
        </td>
    </tr>
@endforelse