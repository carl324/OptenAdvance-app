@forelse($clientes as $cliente)
<tr data-id="{{ $cliente->id }}">
    <td class="min-width">
        <div class="d-flex align-items-center gap-2">
            <div style="width:34px;height:34px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="lni lni-user" style="color:#3b82f6;font-size:16px;"></i>
            </div>
            <p class="text-sm fw-semibold mb-0">{{ $cliente->nombre }}</p>
        </div>
    </td>
    <td class="min-width"><p class="text-sm mb-0">{{ $cliente->telefono ?? '—' }}</p></td>
    <td class="min-width"><p class="text-sm mb-0">{{ $cliente->nit ?? '—' }}</p></td>
    <td class="min-width">
        <p class="text-sm mb-0">
           @if(is_null($cliente->cupo_credito)) Sin crédito
                  @elseif($cliente->cupo_credito === -1)
                  Sin límite
                @else
                 ${{ number_format($cliente->cupo_credito, 0, ',', '.') }}
                @endif
        </p>
    </td>
    <td class="min-width">
        @if($cliente->saldo_pendiente > 0)
            <span class="badge status-btn close-btn">
                ${{ number_format($cliente->saldo_pendiente, 0, ',', '.') }}
            </span>
        @else
            <span class="badge status-btn success-btn" >
                Al día
            </span>
        @endif
    </td>
    <td style="overflow:visible;">
        <div class="producto-dropdown">
            <button class="dropdown-trigger" onclick="toggleDropdown(this)">
                <i class="lni lni-more-alt"></i>
            </button>
            <div class="dropdown-menu-custom">
                <button onclick="window.location.href='{{ route('clientes.show', $cliente->id) }}'">
                    <i class="lni lni-eye"></i> Ver detalle
                </button>
                <button class="danger" onclick="confirmarEliminar({{ $cliente->id }}, '{{ addslashes($cliente->nombre) }}')">
                    <i class="lni lni-trash-can"></i> Eliminar
                </button>
            </div>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-4 text-gray">
        <i class="lni lni-users" style="font-size:32px;display:block;margin-bottom:8px;"></i>
        No hay clientes registrados
    </td>
</tr>
@endforelse