# Implementación: Búsqueda Server-Side en Reportes

## Resumen de Cambios

Se ha implementado búsqueda **server-side** en la vista de reportes, reemplazando el filtrado client-side anterior.

---

## 📋 Cambios Backend

### Archivo: `app/Http/Controllers/ReporteController.php`

**Modificación en método `apiData(Request $request)`:**

#### Búsqueda en Movimientos de Inventario
```php
->when($search, function ($q) use ($search) {
    // Buscar en nombre del producto o cantidad
    return $q->whereHas('producto', function ($sq) use ($search) {
        $sq->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($search) . '%']);
    })->orWhereRaw('CAST(cantidad AS CHAR) LIKE ?', ['%' . $search . '%']);
})
```

**Características:**
- Búsqueda case-insensitive en nombre de producto
- También busca por cantidad (cast a string)
- Usa `whereHas()` para búsqueda en relación

#### Búsqueda en Ventas
```php
->when($search, function ($q) use ($search) {
    // Buscar en número de factura o cliente
    return $q->whereHas('factura', function ($sq) use ($search) {
        $sq->whereRaw('LOWER(numero) LIKE ?', ['%' . strtolower($search) . '%'])
           ->orWhereRaw('LOWER(cliente_nombre) LIKE ?', ['%' . strtolower($search) . '%']);
    });
})
```

**Características:**
- Búsqueda case-insensitive en número de factura
- Búsqueda case-insensitive en nombre del cliente
- Usa `orWhere` para múltiples campos

#### Conservación de Parámetros
```php
$data = $query->paginate(15)->appends($request->query());  // ← Conserva búsqueda en paginación
```

---

## 🎯 Cambios Frontend

### Archivo: `resources/views/reportes/index.blade.php`

#### 1. Nueva Función: `buscarEnTabla(searchTerm)`

**Responsabilidades:**
- Envía búsqueda al servidor
- Resetea a página 1
- Actualiza tabla con resultados filtrados
- Actualiza paginación

```javascript
async function buscarEnTabla(searchTerm) {
  try {
    currentPage = 1;  // Resetear a página 1
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    const params = new URLSearchParams({
      tipo: currentType,
      fecha_inicio: dateFrom,
      fecha_fin: dateTo,
      page: currentPage,
      search: searchTerm  // ← Búsqueda server-side
    });

    const response = await fetch(`/api/reportes?${params}`, {
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      }
    });

    const data = await response.json();

    if (data.success) {
      allData = data.data;
      actualizarTabla(data.data);
      actualizarPaginacion(data.pagination);
    } else {
      mostrarError('No hay datos disponibles');
    }
  } catch (error) {
    mostrarError('Error al buscar los datos');
  }
}
```

#### 2. Event Listener con Debounce (300ms)

**ANTES:**
```javascript
document.getElementById('searchInput').addEventListener('input', filtrarTabla);
```

**DESPUÉS:**
```javascript
const searchInput = document.getElementById('searchInput');
let searchTimeout;
searchInput.addEventListener('input', function(e) {
  clearTimeout(searchTimeout);
  const searchTerm = this.value.trim();
  searchTimeout = setTimeout(() => {
    buscarEnTabla(searchTerm);  // ← Búsqueda server-side con debounce
  }, 300);
});
```

---

## ✅ Comportamiento Funcional

### Escenario 1: Búsqueda en Ventas
```
Usuario escribe "juan" en búsqueda (reportes de ventas)
    ↓
Debounce espera 300ms
    ↓
Envía: GET /api/reportes?tipo=ventas&search=juan&page=1&fecha_inicio=...&fecha_fin=...
    ↓
Backend: whereHas('factura', ...) busca "juan" en numero Y cliente_nombre
    ↓
Retorna JSON con ventas que coincidan
    ↓
UI actualiza tabla + paginación
```

### Escenario 2: Búsqueda en Inventario
```
Usuario escribe "coca" en búsqueda (reportes de inventario)
    ↓
Debounce espera 300ms
    ↓
Envía: GET /api/reportes?tipo=movimientos&search=coca&page=1&...
    ↓
Backend: whereHas('producto', ...) busca "coca" en nombre + cantidad
    ↓
Retorna JSON con movimientos que coincidan
    ↓
UI actualiza tabla + paginación
```

### Escenario 3: Paginación Respetando Búsqueda
```
Usuario busca "coca" → Page 1: 15 resultados
    ↓
Usuario hace clic en Página 2
    ↓
Envía: GET /api/reportes?...&search=coca&page=2
    ↓
Backend retorna próximos 15 resultados de "coca"
    ↓
UI muestra página 2 con búsqueda aún activa
```

---

## 🔄 Flujo de Datos

```
┌─────────────────────────────────────────────────────┐
│ INPUT: Usuario escribe en búsqueda                  │
└────────────────┬────────────────────────────────────┘
                 │
                 ├─→ Event: input
                 │
                 ├─→ clearTimeout() + setTimeout(300ms)
                 │
                 └─→ buscarEnTabla(searchTerm)
                    │
                    ├─→ Resetear currentPage = 1
                    │
                    ├─→ fetch('/api/reportes?search=...&page=1&...')
                    │
                    └─→ SERVIDOR (ReporteController::apiData):
                       ├─→ $search = $request->input('search')
                       │
                       ├─→ when($search, ...) aplica filtro
                       │
                       ├─→ Para ventas: whereHas('factura') con LOWER()
                       ├─→ Para inventario: whereHas('producto') + cantidad
                       │
                       ├─→ paginate(15)->appends() conserva parámetros
                       │
                       └─→ Retorna JSON:
                          {
                            "success": true,
                            "data": [{...}],
                            "stats": {...},
                            "pagination": {...}
                          }

                    CLIENTE (recibe JSON):
                    │
                    ├─→ actualizarTabla(data.data)
                    │
                    ├─→ actualizarPaginacion(data.pagination)
                    │
                    └─→ Tabla + paginación actualizadas
```

---

## 📊 Comparación: Antes vs. Después

| Aspecto | ANTES | DESPUÉS |
|---------|-------|---------|
| **Búsqueda** | Client-side (DOM filtering) | Server-side (DB query) |
| **Debounce** | ❌ No | ✅ 300ms |
| **Performance** | Lento con 1000+ registros | Rápido (índice DB) |
| **Paginación + Búsqueda** | Se pierde al paginar | ✅ Se conserva |
| **Filtros activos** | ID de números | Texto parcial |

---

## 🚀 Restricciones Respetadas

✅ **NO crear partials** - Búsqueda implementada sin partials  
✅ **NO crear carpetas nuevas** - Se usan carpetas existentes  
✅ **NO mover archivos** - Todos los archivos en su lugar  
✅ **NO cambiar diseño UI** - Visualización idéntica  
✅ **NO duplicar HTML** - Reutilización de vista  
✅ **NO librerías externas** - Usa `fetch()` nativo  

---

## 🔧 Endpoints Utilizados

### GET `/api/reportes`
```
Parámetros:
  - tipo: 'ventas' | 'movimientos'
  - fecha_inicio: YYYY-MM-DD
  - fecha_fin: YYYY-MM-DD
  - page: número
  - search: término de búsqueda

Respuesta JSON:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "fecha": "2026-01-15T10:30:00",
      "factura_numero": "FAC-001",
      "cliente_nombre": "Juan Pérez",
      "total": 50000,
      "estado": "completada"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 3,
    "total": 45,
    "per_page": 15
  }
}
```

---

## ✨ Ventajas

- ⚡ **Performance**: Filtrado en BD, no en cliente
- 📱 **UX**: Debounce evita requests excesivos
- 🔍 **Precisión**: Búsqueda case-insensitive con LOWER()
- 🧹 **Mantenimiento**: Lógica centralizada en backend
- 🔒 **Seguridad**: Búsqueda protegida en servidor

---

## 📝 Notas Técnicas

1. **Debounce 300ms**: Espera 300ms sin cambios antes de enviar solicitud
2. **when() clause**: Filtro aplicado solo si existe $search
3. **whereHas()**: Búsqueda en relaciones (producto, factura)
4. **appends()**: Mantiene parámetros en paginación
5. **LOWER()**: Búsqueda case-insensitive
6. **Reseteo página**: currentPage = 1 al buscar

