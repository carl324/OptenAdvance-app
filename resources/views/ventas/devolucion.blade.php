@extends('layouts.app')

@section('content')
<form id="anularForm" method="POST" action="{{ route('ventas.devolucion.confirmar', $venta) }}">
	@csrf
	<div class="mb-3">
		<p>Está por anular la venta <strong>{{ $venta->factura->numero ?? ('#' . $venta->id) }}</strong>.</p>
		<label for="motivo">Motivo (obligatorio)</label>
		<textarea name="motivo" id="motivo" style="width:100%;min-height:80px;" required></textarea>
	</div>
	<div style="display:flex;justify-content:flex-end;gap:8px;">
		<button type="button" data-modal-close class="btn-plain">Cancelar</button>
		<button type="submit" class="btn-danger-plain">Sí, anular</button>
	</div>
</form>
@endsection
