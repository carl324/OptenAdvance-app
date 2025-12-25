<!DOCTYPE html>
<html>
<head>
    <title>Registrar Producto</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { margin-bottom: 25px; color: #333; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input, select { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 15px; }
        input:focus, select:focus { outline: none; border-color: #4CAF50; }
        small { color: #666; font-size: 13px; }
        
        .precio-preview { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 15px; }
        .precio-item { display: flex; justify-content: space-between; padding: 8px 0; }
        .precio-label { color: #666; }
        .precio-valor { font-weight: bold; color: #333; font-size: 18px; }
        .precio-final { border-top: 2px solid #ddd; margin-top: 10px; padding-top: 10px; }
        .precio-final .precio-valor { color: #4CAF50; font-size: 22px; }
        
        button { width: 100%; padding: 14px; background: #4CAF50; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        button:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .mensaje { padding: 12px 15px; border-radius: 6px; margin-bottom: 15px; display: none; }
        .mensaje.success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4CAF50; }
        .mensaje.error { background: #ffebee; color: #c62828; border-left: 4px solid #f44336; }
        .mensaje.show { display: block; }
        
        .producto-existente { background: #fff3e0; padding: 15px; border-radius: 6px; border-left: 4px solid #ff9800; margin-top: 15px; display: none; }
        .producto-existente.show { display: block; }
        .producto-existente strong { display: block; margin-bottom: 10px; color: #e65100; }
    </style>
</head>
<body>

<div class="container">
    <h1>📦 Registrar Producto</h1>

    <p id="mensaje" class="mensaje"></p>

    <form id="form-producto">
        <div class="form-group">
            <label for="nombre">Nombre del producto *</label>
            <input type="text" id="nombre" name="nombre" required minlength="3" placeholder="Ej: Coca Cola 350ml">
        </div>

        <div class="form-group">
            <label for="precio">Precio base (sin IVA) *</label>
            <input type="text" id="precio" name="precio" required placeholder="$0" inputmode="numeric" autocomplete="off">
            <input type="hidden" id="precio-valor" name="precio_valor">
        </div>

        <div class="form-group">
            <label for="iva">IVA (%) *</label>
            <input type="number" id="iva" name="iva" step="0.01" min="0" max="100" value="19" required placeholder="Ej: 19">
            <small>Ejemplos comunes: 0%, 5%, 19%</small>
        </div>

        <!-- Vista previa de precios -->
        <div class="precio-preview">
            <div class="precio-item">
                <span class="precio-label">Precio base:</span>
                <span class="precio-valor" id="preview-base">$0</span>
            </div>
            <div class="precio-item">
                <span class="precio-label">IVA (<span id="preview-iva-pct">19</span>%):</span>
                <span class="precio-valor" id="preview-iva">$0</span>
            </div>
            <div class="precio-item precio-final">
                <span class="precio-label">Precio con IVA:</span>
                <span class="precio-valor" id="preview-total">$0</span>
            </div>
        </div>

        <div class="form-group">
            <label for="stock">Stock inicial *</label>
            <input type="number" id="stock" name="stock" min="0" required placeholder="0">
        </div>

        <button type="submit" id="btn-guardar">Agregar Producto</button>
    </form>

    <div id="producto-existente" class="producto-existente">
        <strong>⚠️ Producto existente:</strong>
        <div><b>Nombre:</b> <span id="ex-nombre"></span></div>
        <div><b>Precio base:</b> <span id="ex-precio"></span></div>
        <div><b>IVA:</b> <span id="ex-iva"></span></div>
        <div><b>Precio con IVA:</b> <span id="ex-precio-iva"></span></div>
        <div><b>Stock:</b> <span id="ex-stock"></span></div>
    </div>
</div>

<script>
// ========================================
// MENSAJES CENTRALIZADOS - Fácil de cambiar para diseño final
// ========================================
const MENSAJES = {
    exito: '✅ Producto registrado correctamente',
    errorGenerico: '❌ Error inesperado',
    productoExiste: '⚠️ Este producto ya está registrado',
    procesando: 'Guardando...',
    btnDefault: 'Agregar Producto'
};

// ========================================
// FORMATO DE PRECIO TIPO NEQUI
// ========================================
function formatearInputPrecio(input) {
    // Quitar todo excepto números
    let valor = input.value.replace(/\D/g, '');
    
    if (valor === '' || valor === '0') {
        input.value = '';
        return 0;
    }
    
    // Formatear con puntos y $
    let numero = parseInt(valor);
    input.value = '$' + numero.toLocaleString('es-CO');
    
    return numero;
}

// Formato para mostrar precios
function formatoPrecio(precio) {
    return '$' + Math.round(precio).toLocaleString('es-CO');
}

// ========================================
// ELEMENTOS DEL DOM
// ========================================
const form = document.getElementById('form-producto');
const inputPrecio = document.getElementById('precio');
const inputPrecioValor = document.getElementById('precio-valor');
const selectIva = document.getElementById('iva');
const inputStock = document.getElementById('stock');
const btnGuardar = document.getElementById('btn-guardar');
const mensaje = document.getElementById('mensaje');
const boxExistente = document.getElementById('producto-existente');

// ========================================
// FORMATEAR PRECIO MIENTRAS ESCRIBE (TIPO NEQUI)
// ========================================
inputPrecio.addEventListener('input', function(e) {
    const valorNumerico = formatearInputPrecio(this);
    inputPrecioValor.value = valorNumerico;
    actualizarPreview();
});

inputPrecio.addEventListener('focus', function() {
    // Si está vacío, limpiar el placeholder visual
    if (this.value === '' || this.value === '$0') {
        this.value = '';
    }
});

// ========================================
// ACTUALIZAR VISTA PREVIA EN TIEMPO REAL
// ========================================
function actualizarPreview() {
    const precioBase = parseFloat(inputPrecioValor.value) || 0;
    const ivaPorcentaje = parseFloat(selectIva.value) || 0;
    const montoIva = precioBase * (ivaPorcentaje / 100);
    const precioConIva = precioBase + montoIva;

    document.getElementById('preview-base').textContent = formatoPrecio(precioBase);
    document.getElementById('preview-iva-pct').textContent = ivaPorcentaje;
    document.getElementById('preview-iva').textContent = formatoPrecio(montoIva);
    document.getElementById('preview-total').textContent = formatoPrecio(precioConIva);
}

selectIva.addEventListener('input', actualizarPreview);
selectIva.addEventListener('change', actualizarPreview);

// ========================================
// ENVIAR FORMULARIO
// ========================================
form.addEventListener('submit', async (e) => {
    e.preventDefault();

    mensaje.style.display = 'none';
    boxExistente.classList.remove('show');

    btnGuardar.disabled = true;
    btnGuardar.innerText = MENSAJES.procesando;

    const data = {
        nombre: form.nombre.value.trim(),
        precio: parseFloat(inputPrecioValor.value) || 0,
        iva: parseFloat(form.iva.value),
        stock: parseInt(form.stock.value)
    };

    // Validación adicional
    if (data.precio === 0) {
        mensaje.className = 'mensaje error show';
        mensaje.innerText = 'El precio debe ser mayor a 0';
        btnGuardar.disabled = false;
        btnGuardar.innerText = MENSAJES.btnDefault;
        return;
    }

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

        // ✅ ÉXITO
        mensaje.className = 'mensaje success show';
        mensaje.innerText = MENSAJES.exito;

        // Limpiar formulario
        form.reset();
        inputPrecio.value = '';
        inputPrecioValor.value = '0';
        selectIva.value = '19';
        actualizarPreview();

    } catch (error) {
        mensaje.className = 'mensaje error show';

        // Validaciones del servidor
        if (error.errors) {
            mensaje.innerText = Object.values(error.errors)[0][0];
        }
        // Producto duplicado
        else if (error.message === 'El producto ya existe' && error.producto) {
            mensaje.innerText = MENSAJES.productoExiste;

            document.getElementById('ex-nombre').innerText = error.producto.nombre;
            document.getElementById('ex-precio').innerText = formatoPrecio(error.producto.precio);
            document.getElementById('ex-iva').innerText = error.producto.iva + '%';
            document.getElementById('ex-precio-iva').innerText = formatoPrecio(error.producto.precio_con_iva);
            document.getElementById('ex-stock').innerText = error.producto.stock;

            boxExistente.classList.add('show');
        }
        else {
            mensaje.innerText = MENSAJES.errorGenerico;
        }
    } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerText = MENSAJES.btnDefault;
    }
});

// Inicializar preview
actualizarPreview();
</script>

</body>
</html>