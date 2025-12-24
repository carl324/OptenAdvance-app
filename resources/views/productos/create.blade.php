
<!DOCTYPE html>
<html>
<head>
    <title>Registrar Producto</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<form id="form-producto">
    <input type="text" name="nombre" placeholder="Nombre del producto" required minlength="3">
    <input type="number" name="precio" placeholder="Precio" step="0.01" min="0.01" required>
    <input type="number" name="stock" placeholder="Stock inicial" min="0" required>

    <button type="submit" id="btn-guardar">Agregar Producto</button>
</form>

<p id="mensaje" style="color:red; display:none;"></p>

<div id="producto-existente" style="display:none; margin-top:10px; border:1px solid #ccc; padding:8px;">
    <strong>Producto existente:</strong>
    <div><b>Nombre:</b> <span id="ex-nombre"></span></div>
    <div><b>Precio:</b> <span id="ex-precio"></span></div>
    <div><b>Stock:</b> <span id="ex-stock"></span></div>
</div>

<script>
const form = document.getElementById('form-producto');
const btn = document.getElementById('btn-guardar');
const msg = document.getElementById('mensaje');
const boxExistente = document.getElementById('producto-existente');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    msg.style.display = 'none';
    boxExistente.style.display = 'none';

    btn.disabled = true;
    btn.innerText = 'Guardando...';

    const data = {
        nombre: form.nombre.value.trim(),
        precio: form.precio.value,
        stock: form.stock.value
    };

    try {
        const res = await fetch('/productos', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();

        if (!res.ok) throw result;

        // éxito
        msg.style.display = 'block';
        msg.style.color = 'green';
        msg.innerText = 'Producto registrado correctamente';

        form.reset();

    } catch (error) {
        msg.style.display = 'block';
        msg.style.color = 'red';

        // Validaciones
        if (error.errors) {
            msg.innerText = Object.values(error.errors)[0][0];
        }
        // Producto duplicado (409)
        else if (error.message === 'El producto ya existe' && error.producto) {
            msg.innerText = 'Este producto ya está registrado';

            document.getElementById('ex-nombre').innerText = error.producto.nombre;
            document.getElementById('ex-precio').innerText = error.producto.precio;
            document.getElementById('ex-stock').innerText = error.producto.stock;

            boxExistente.style.display = 'block';
        }
        else {
            msg.innerText = 'Error inesperado';
        }
    } finally {
        btn.disabled = false;
        btn.innerText = 'Agregar Producto';
    }
});
</script>


</body>
</html>


