<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($productos as $producto)
            <tr id="producto-{{ $producto->id }}">
    <td>{{ $producto->id }}</td>

    <td>
        <span class="view">{{ $producto->nombre }}</span>
        <input class="edit" data-field="nombre" type="text" value="{{ $producto->nombre }}" hidden>
    </td>

    <td>
        <span class="view">{{ $producto->precio }}</span>
        <input class="edit" data-field="precio" type="number" step="0.01" value="{{ $producto->precio }}" hidden>
    </td>

    <td>
        <span class="view">{{ $producto->stock }}</span>
        <input class="edit" data-field="stock" type="number" value="{{ $producto->stock }}" hidden>
    </td>

    <td>
        <button onclick="editarProducto({{ $producto->id }})">✏️</button>
        <button onclick="eliminarProducto({{ $producto->id }})">🗑️</button>
        <button onclick="guardarProducto({{ $producto->id }})" hidden>💾</button>
        <button onclick="cancelarEdicion({{ $producto->id }})" hidden>❌</button>

        <div class="msg" style="color:red;font-size:12px;"></div>
    </td>
</tr>

        @endforeach
    </tbody>
</table>

<script>
const csrf = '{{ csrf_token() }}';

function editarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    tr.querySelectorAll('.view').forEach(e => e.hidden = true);
    tr.querySelectorAll('.edit').forEach(e => e.hidden = false);

    toggleButtons(tr, true);
}

function cancelarEdicion(id) {
    const tr = document.getElementById(`producto-${id}`);
    tr.querySelectorAll('.view').forEach(e => e.hidden = false);
    tr.querySelectorAll('.edit').forEach(e => e.hidden = true);
    tr.querySelector('.msg').innerText = '';

    toggleButtons(tr, false);
}

function toggleButtons(tr, editing) {
    tr.querySelector('[onclick^="editarProducto"]').hidden = editing;
    tr.querySelector('[onclick^="eliminarProducto"]').hidden = editing;
    tr.querySelector('[onclick^="guardarProducto"]').hidden = !editing;
    tr.querySelector('[onclick^="cancelarEdicion"]').hidden = !editing;
}

async function guardarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    const msg = tr.querySelector('.msg');
    msg.innerText = '';

    const data = {};
    tr.querySelectorAll('.edit').forEach(input => {
        data[input.dataset.field] = input.value;
    });

    disableRow(tr, true);

    try {
        const res = await fetch(`/productos/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();
        if (!res.ok) throw result;

        tr.querySelectorAll('.view').forEach(span => {
            const field = span.nextElementSibling.dataset.field;
            span.innerText = data[field];
        });

        cancelarEdicion(id);

    } catch (e) {
        msg.innerText = e.message || 'Error al actualizar';
    } finally {
        disableRow(tr, false);
    }
}

async function eliminarProducto(id) {
    const tr = document.getElementById(`producto-${id}`);
    const msg = tr.querySelector('.msg');
    msg.innerText = 'Eliminando...';

    try {
        const res = await fetch(`/productos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            }
        });

        const result = await res.json();
        if (!res.ok) throw result;

        tr.remove();

    } catch {
        msg.innerText = 'No se pudo eliminar';
    }
}

function disableRow(tr, state) {
    tr.querySelectorAll('button, input').forEach(e => e.disabled = state);
}
</script>

