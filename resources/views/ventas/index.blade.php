@extends('layouts.app')

@section('content')
<div class="container mt-4">
	<h3>Listado de ventas</h3>

	<!-- Filtros de búsqueda y fecha -->
	<div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 16px;">
		<input type="text" id="buscadorVentas" class="form-control" style="max-width: 320px;" placeholder="Buscar venta… (cliente, factura, total, estado)">
		<label style="margin-bottom:0;">Desde: <input type="date" id="fechaInicio" class="form-control" style="display:inline-block; width:auto;"></label>
		<label style="margin-bottom:0;">Hasta: <input type="date" id="fechaFin" class="form-control" style="display:inline-block; width:auto;"></label>
	</div>

	@if($ventas->isEmpty())
		<p>No hay ventas registradas.</p>
	@else
		<div class="table-responsive">
			<div id="noCoincidencias" style="display:none; padding: 18px; text-align: center; color: #b02a37; font-weight: 500;">No se encontraron ventas con ese criterio</div>
			<table class="table table-sm" id="tablaVentas">
				<thead>
					<tr>
						<th>ID</th>
						<th>Número factura</th>
						<th>Fecha</th>
						<th>Cliente</th>
						<th class="text-end">Total</th>
						@if($empresa && $empresa->cobra_iva)
						<th class="text-end">IVA / impuestos</th>
						@endif
						<th>Forma de pago</th>
						<th>Estado</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
						@foreach($ventas as $venta)
						<tr data-venta-id="{{ $venta->id }}">
							<td>{{ $venta->id }}</td>
							<td>{{ $venta->factura->numero ?? '-' }}</td>
							<td>{{ optional($venta->fecha)->format('Y-m-d H:i') ?? '-' }}</td>
							<td>{{ $venta->factura->cliente_nombre ?? 'Consumidor final' }}</td>
							<td class="text-end">{{ number_format($venta->total ?? 0, 0, ',', '.') }}</td>
							@if($empresa && $empresa->cobra_iva)
							<td class="text-end">{{ number_format(optional($venta->factura)->impuestos ?? 0, 0, ',', '.') }}</td>
							@endif
							<td>{{ $venta->factura->forma_pago ?? '-' }}</td>
							<td>{{ ucfirst($venta->estado ?? '---') }}</td>
							<td>
								@php
									$puedeAnular = (
										($venta->estado === 'completada') &&
										($venta->factura && optional($venta->factura)->fecha_emision && \Carbon\Carbon::parse($venta->factura->fecha_emision)->isSameDay(\Carbon\Carbon::now()))
									);
								@endphp
								@if($puedeAnular)
												<a href="{{ route('ventas.factura', $venta) }}" target="_blank" class="btn btn-sm btn-outline-primary">Imprimir factura</a>
												<button class="btn btn-sm btn-danger btn-anular" data-url="{{ route('ventas.devolucion.confirmar', $venta) }}">Anular</button>
								@else
												<a href="{{ route('ventas.factura', $venta) }}" target="_blank" class="btn btn-sm btn-outline-primary">Imprimir factura</a>
												<button class="btn btn-sm" disabled title="Solo se puede anular el mismo día">Solo se puede anular el mismo día</button>
								@endif
							</td>
						</tr>
						@endforeach
				</tbody>
			</table>
		</div>
	@endif
 
	<style>
	.highlight { background: #ffe066; color: #b26a00; padding: 0 2px; border-radius: 2px; }
	</style>
	<script>
	document.addEventListener('DOMContentLoaded', function(){
		// Modal simple sin librerías
		const modalHtml = `
		<div id="modalAnular" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;z-index:1000;">
		  <div style="background:#fff;padding:12px;border-radius:6px;max-width:600px;width:90%;">
			<h4>Anular venta</h4>
			<p>Esta acción anula la venta y revierte el inventario. No se puede deshacer.</p>
			<form id="formAnular">
			  <input type="hidden" name="_token" value="{{ csrf_token() }}">
			  <div style="margin-bottom:8px;"><label>Motivo (obligatorio)</label><br><textarea name="motivo" required style="width:100%;min-height:80px;"></textarea></div>
			  <div style="display:flex;justify-content:flex-end;gap:8px;"><button type="button" id="cancelAnular">Cancelar</button><button type="submit" id="confirmAnular">Sí, anular</button></div>
			</form>
			<div id="anularMessage" style="margin-top:8px"></div>
		  </div>
		</div>`;

		document.body.insertAdjacentHTML('beforeend', modalHtml);
		const modal = document.getElementById('modalAnular');
		const form = document.getElementById('formAnular');
		const cancelBtn = document.getElementById('cancelAnular');
		const confirmBtn = document.getElementById('confirmAnular');
		const messageBox = document.getElementById('anularMessage');

		let currentUrl = null;
		let currentRow = null;

		function openModal(url, row){ currentUrl = url; currentRow = row; modal.style.display = 'flex'; }
		function closeModal(){ modal.style.display = 'none'; form.reset(); messageBox.innerHTML = ''; currentUrl = null; currentRow = null; }

		cancelBtn.addEventListener('click', closeModal);

		// Attach click handlers to anular buttons
		document.querySelectorAll('.btn-anular').forEach(btn => {
			btn.addEventListener('click', function(){
				const url = btn.dataset.url || btn.getAttribute('data-url');
				const row = btn.closest('tr');
				openModal(url, row);
			});
		});

		form.addEventListener('submit', async function(e){
			e.preventDefault();
			if (!currentUrl) return;
			confirmBtn.disabled = true;
			messageBox.textContent = '';
			const fd = new FormData(form);
			try {
				const res = await fetch(currentUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
				const json = await res.json();
				if (res.ok && json.success) {
					// actualizar fila: estado y deshabilitar botón
					if (currentRow) {
						const estadoCell = currentRow.querySelectorAll('td')[7];
						if (estadoCell) estadoCell.textContent = 'Anulada';
						const btnCell = currentRow.querySelectorAll('td')[8];
						if (btnCell) {
							btnCell.innerHTML = '<span style="color:#b02a37">Anulada</span>';
						}
					}
					messageBox.style.color = '#0f5132';
					messageBox.textContent = json.message || 'Venta anulada correctamente';
					setTimeout(closeModal, 900);
				} else {
					messageBox.style.color = '#842029';
					messageBox.textContent = json.message || 'Error al anular';
				}
			} catch (err) {
				messageBox.style.color = '#842029';
				messageBox.textContent = 'Error de red al intentar anular';
			} finally {
				confirmBtn.disabled = false;
			}
		});

		// --- FILTRO Y BUSCADOR ---
		const tabla = document.getElementById('tablaVentas');
		const buscador = document.getElementById('buscadorVentas');
		const fechaInicio = document.getElementById('fechaInicio');
		const fechaFin = document.getElementById('fechaFin');
		const noCoincidencias = document.getElementById('noCoincidencias');

		// Guardar texto original de cada celda para restaurar
		const filas = Array.from(tabla.querySelectorAll('tbody tr'));
		filas.forEach(tr => {
			Array.from(tr.children).forEach(td => {
				td.setAttribute('data-original', td.textContent);
			});
		});


		function limpiarResaltado(tr) {
			Array.from(tr.children).forEach((td, idx) => {
				// No modificar la celda de acciones (última columna)
				if (td.hasAttribute('data-original') && idx !== tr.children.length - 1) {
					td.innerHTML = td.getAttribute('data-original');
				}
			});
		}

		function resaltarCoincidencia(texto, filtro) {
			if (!filtro) return texto;
			const regex = new RegExp('('+filtro.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')+')', 'gi');
			return texto.replace(regex, '<mark class="highlight">$1</mark>');
		}

		function filtrarTabla() {
			const filtro = buscador.value.trim().toLowerCase();
			const fInicio = fechaInicio.value;
			const fFin = fechaFin.value;
			let visibles = 0;

			filas.forEach(tr => {
				limpiarResaltado(tr);
				let mostrar = true;

				// Filtrar por texto
				if (filtro) {
					mostrar = false;
					Array.from(tr.children).forEach((td, idx) => {
						// No buscar en la celda de acciones
						if (idx === tr.children.length - 1) return;
						const texto = td.getAttribute('data-original') || '';
						if (texto.toLowerCase().includes(filtro)) {
							mostrar = true;
						}
					});
				}

				// Filtrar por fecha
				if (mostrar && (fInicio || fFin)) {
					// La fecha está en la columna 2 (índice 2)
					const fechaTexto = tr.children[2].getAttribute('data-original') || '';
					// Formato esperado: YYYY-MM-DD HH:mm
					const fechaVenta = fechaTexto.split(' ')[0];
					if (fInicio && fechaVenta < fInicio) mostrar = false;
					if (fFin && fechaVenta > fFin) mostrar = false;
				}

				// Mostrar/ocultar fila
				tr.style.display = mostrar ? '' : 'none';

				// Resaltar coincidencias
				if (mostrar && filtro) {
					Array.from(tr.children).forEach((td, idx) => {
						// No resaltar en la celda de acciones
						if (idx === tr.children.length - 1) return;
						const texto = td.getAttribute('data-original') || '';
						if (texto.toLowerCase().includes(filtro)) {
							td.innerHTML = resaltarCoincidencia(texto, filtro);
						}
					});
				}

				if (mostrar) visibles++;
			});

			noCoincidencias.style.display = visibles === 0 ? '' : 'none';
		}

		buscador.addEventListener('input', filtrarTabla);
		fechaInicio.addEventListener('change', filtrarTabla);
		fechaFin.addEventListener('change', filtrarTabla);
	});
	</script>

@endsection
