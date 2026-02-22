@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')

<div class="notification-wrapper">
    <div class="container-fluid">

        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                
            </div>
        </div>

        <div class="card-style mb-30">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-20">
                <div class="d-flex align-items-center gap-3">
                    {{-- Checkbox seleccionar todo --}}
                    @if($notifications->count() > 0)
                    <div class="form-check checkbox-style mb-0">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <label class="form-check-label text-sm text-gray" for="select-all">
                            Seleccionar todo
                        </label>
                    </div>
                    @endif
                    
                </div>

                {{-- Botón eliminar seleccionadas - minimalista --}}
                @if($notifications->count() > 0)
                <button id="btn-delete-selected" style="display:none; background:none; border:none; color:#dc2626; font-size:13px; font-weight:600; cursor:pointer; padding: 6px 12px; border-radius:6px; transition: background 0.2s;" 
                    onmouseover="this.style.background='#fee2e2'" 
                    onmouseout="this.style.background='none'">
                    <i class="lni lni-trash-can me-1"></i> Eliminar seleccionadas
                </button>
                @endif
            </div>

            {{-- Sin notificaciones --}}
            @if($notifications->isEmpty())
                <div style="text-align: center; padding: 60px 0;">
                    <i class="lni lni-checkmark-circle" style="font-size: 48px; color: #10b981; display: block; margin-bottom: 12px;"></i>
                    <h6 style="color: #1e293b; font-size: 16px; margin-bottom: 6px;">Todo al día</h6>
                    <p class="text-sm text-gray">No tienes notificaciones pendientes</p>
                </div>

            {{-- Lista --}}
            @else
                @foreach($notifications as $notification)
                    @php
                        $bgClass = match($notification->tipo) {
                            'error'   => 'danger-bg',
                            'warning' => 'warning-bg',
                            'info'    => 'info-bg',
                            default   => 'success-bg',
                        };
                        $icono = match($notification->modulo) {
    'licencia' => 'lni lni-certificate',
    'backup'   => 'lni lni-database',
    'sistema'  => 'lni lni-warning',
    default    => 'lni lni-information',
};
                    @endphp

                    <div class="single-notification {{ $notification->leida ? 'readed' : '' }}" id="notification-{{ $notification->id }}">
                        <div class="checkbox">
                            <div class="form-check checkbox-style mb-20">
                                <input class="form-check-input notification-checkbox" type="checkbox" value="{{ $notification->id }}">
                            </div>
                        </div>
                        <div class="notification">
                            <div class="image {{ $bgClass }}">
                                <i class="{{ $icono }}" style="font-size: 20px;"></i>
                            </div>
                            <div class="content">
                                <h6>{{ $notification->titulo }}</h6>
                                <p class="text-sm text-gray">{{ $notification->mensaje }}</p>
                                <span class="text-sm text-medium text-gray">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div class="action">
                            <button class="delete-btn" onclick="eliminarNotificacion({{ $notification->id }})" title="Eliminar">
                                <i class="lni lni-trash-can"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

// Seleccionar todo
document.getElementById('select-all')?.addEventListener('change', function() {
    document.querySelectorAll('.notification-checkbox').forEach(cb => cb.checked = this.checked);
    actualizarBotonEliminar();
});

// Detectar cambio en checkboxes individuales
document.querySelectorAll('.notification-checkbox').forEach(cb => {
    cb.addEventListener('change', actualizarBotonEliminar);
});

function actualizarBotonEliminar() {
    const seleccionadas = document.querySelectorAll('.notification-checkbox:checked').length;
    const btn = document.getElementById('btn-delete-selected');
    if (btn) btn.style.display = seleccionadas > 0 ? 'inline-block' : 'none';
}

// Eliminar seleccionadas
document.getElementById('btn-delete-selected')?.addEventListener('click', function() {
    const ids = [...document.querySelectorAll('.notification-checkbox:checked')].map(cb => cb.value);
    if (!ids.length) return;

    Promise.all(ids.map(id =>
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf }
        }).then(r => r.json())
    )).then(() => {
        ids.forEach(id => document.getElementById('notification-' + id)?.remove());
        actualizarBotonEliminar();
        document.getElementById('select-all').checked = false;
    });
});

// Eliminar individual
function eliminarNotificacion(id) {
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('notification-' + id).remove();
            actualizarBotonEliminar();
        }
    });
}
</script>

@endsection