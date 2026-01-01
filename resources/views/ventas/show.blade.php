@extends('layouts.app')

@section('content')
<div class="invoice p-3">
	<div class="d-flex justify-content-between mb-3">
		<div>
			<strong>{{ config('app.name', 'Mi Negocio') }}</strong><br>
			Dirección: -<br>
			Tel: -
		</div>
		<div class="text-end">
			<strong>Factura:</strong> {{ $factura->numero ?? '-' }}<br>
			<small>Fecha: {{ optional($factura->fecha_emision)->format('Y-m-d H:i') ?? optional($venta->fecha)->format('Y-m-d H:i') }}</small>
		</div>
	</div>

	<div class="mb-3">
		<strong>Cliente:</strong> {{ $factura->cliente_nombre ?? $venta->cliente ?? '-' }}<br>
		<strong>NIT:</strong> {{ $factura->cliente_nit ?? '-' }}
	</div>

	<table class="table table-sm">
		<thead>
			<tr>
				<th>Producto</th>
				<th class="text-end">Cantidad</th>
				<th class="text-end">Precio</th>
				<th class="text-end">Subtotal</th>
			</tr>
		</thead>
		<tbody>
			@foreach($venta->detalles as $d)
			<tr>
				<td>{{ optional($d->producto)->nombre ?? 'Producto #' . $d->producto_id }}</td>
				<td class="text-end">{{ $d->cantidad }}</td>
				<td class="text-end">{{ number_format($d->precio_unitario,2,'.','') }}</td>
				<td class="text-end">{{ number_format($d->subtotal,2,'.','') }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div class="d-flex justify-content-end">
		<div class="w-50">
			@if($empresa && $empresa->cobra_iva)
			<div class="d-flex justify-content-between">
				<div>IVA</div>
				<div>{{ number_format($factura->impuestos ?? $venta->detalles->sum('iva'),2,'.','') }}</div>
			</div>
			@endif
			<div class="d-flex justify-content-between fw-bold mt-2">
				<div>Total</div>
				<div>{{ number_format($factura->total ?? $venta->total,2,'.','') }}</div>
			</div>
		</div>
	</div>
</div>
@endsection
