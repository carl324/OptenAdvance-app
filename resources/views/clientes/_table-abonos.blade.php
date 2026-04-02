@forelse($abonos as $abono)

<tr>
  <td class="min-width">
    <p class="text-sm mb-0">{{ $abono->created_at->format('d/m/Y H:i') }}</p>
  </td>
  <td class="min-width">
    <span style="color:#16a34a;font-weight:600;">${{ number_format($abono->monto, 0, ',', '.') }}</span>
  </td>
  <td class="min-width">
    <p class="text-sm mb-0 text-capitalize">{{ $abono->forma_pago }}</p>
  </td>
  <td>
    <p class="text-sm mb-0 text-gray">{{ $abono->observacion ?? '—' }}</p>
  </td>
</tr>
@empty
<tr>
  <td colspan="4" class="text-center py-4 text-gray">
    <i class="lni lni-empty-file" style="font-size:32px;display:block;margin-bottom:8px;"></i>
    Sin abonos registrados
  </td>
</tr>
@endforelse