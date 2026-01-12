/**
 * ========== INICIALIZACIÓN DE TOOLTIPS ==========
 * Sistema de inicialización y gestión de Bootstrap Tooltips
 * 
 * Requiere:
 * - Bootstrap 5+ (para bootstrap.Tooltip)
 * - Popper.js (incluido en bootstrap.bundle.js)
 * 
 * Uso:
 * 1. Incluir este archivo en tu página
 * 2. Agregar data-bs-toggle="tooltip" y data-bs-title="..." a elementos
 * 3. Llamar initializeAllTooltips() en DOMContentLoaded
 * 4. Para dinámico: initializeTooltipsInRow(element)
 * 
 * Autor: POS-MVP
 * Fecha: 12 de enero de 2026
 */

/**
 * Inicializa tooltips en todos los elementos de la página
 * Evita duplicados verificando if (el._bsTooltip)
 * 
 * @returns {number} Cantidad de tooltips inicializados
 * 
 * @example
 * document.addEventListener('DOMContentLoaded', function() {
 *     const count = initializeAllTooltips();
 *     console.log(`${count} tooltips inicializados`);
 * });
 */
function initializeAllTooltips() {
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    let count = 0;
    
    tooltipElements.forEach(el => {
        // Si ya tiene tooltip inicializado, no hacer nada
        if (el._bsTooltip) {
            return;
        }
        
        try {
            // Inicializar Bootstrap tooltip
            new bootstrap.Tooltip(el, {
                placement: 'top',              // Posición: top, bottom, left, right
                trigger: 'hover focus',        // Cuándo mostrar: hover, click, focus, manual
                boundary: 'viewport',          // Evitar que salga del viewport en móvil
                delay: { show: 300, hide: 100 } // Delay para mostrar/ocultar
            });
            count++;
        } catch (e) {
            console.warn('⚠️ Error inicializando tooltip:', e.message);
        }
    });
    
    if (count > 0) {
        console.log(`✅ ${count} tooltips inicializados correctamente`);
    }
    
    return count;
}

/**
 * Inicializa tooltips solo en un elemento específico y sus hijos
 * Útil para filas dinámicas agregadas con AJAX
 * 
 * @param {HTMLElement} element - Elemento donde inicializar tooltips
 * @returns {number} Cantidad de tooltips inicializados en el elemento
 * 
 * @example
 * const newRow = document.querySelector('.mi-fila-nueva');
 * initializeTooltipsInRow(newRow);
 */
function initializeTooltipsInRow(element) {
    if (!element) {
        console.warn('⚠️ initializeTooltipsInRow: element es null/undefined');
        return 0;
    }
    
    const tooltipElements = element.querySelectorAll('[data-bs-toggle="tooltip"]');
    let count = 0;
    
    tooltipElements.forEach(el => {
        // Si ya tiene tooltip inicializado, no hacer nada
        if (el._bsTooltip) {
            return;
        }
        
        try {
            new bootstrap.Tooltip(el, {
                placement: 'top',
                trigger: 'hover focus',
                boundary: 'viewport',
                delay: { show: 300, hide: 100 }
            });
            count++;
        } catch (e) {
            console.warn('⚠️ Error inicializando tooltip en fila:', e.message);
        }
    });
    
    return count;
}

/**
 * Elimina todos los tooltips de la página
 * Útil cuando necesitas limpiar antes de reinicializar
 * 
 * @example
 * disposeAllTooltips();
 * initializeAllTooltips(); // Reinicializar sin duplicados
 */
function disposeAllTooltips() {
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    
    tooltipElements.forEach(el => {
        if (el._bsTooltip) {
            try {
                el._bsTooltip.dispose();
            } catch (e) {
                console.warn('⚠️ Error eliminando tooltip:', e.message);
            }
        }
    });
    
    console.log('✅ Todos los tooltips han sido eliminados');
}

/**
 * Reinitializa todos los tooltips
 * Llama a disposeAllTooltips y luego initializeAllTooltips
 * 
 * @example
 * reinitializeTooltips();
 */
function reinitializeTooltips() {
    disposeAllTooltips();
    setTimeout(() => {
        initializeAllTooltips();
    }, 100);
}

/**
 * Crea un sistema automático de reinicialización para AJAX
 * Envuelve una función para reinicializar tooltips después de ejecutarla
 * 
 * @param {Function} asyncFunction - Función asincrónica a envolver
 * @returns {Function} Función envuelta que reinitializa tooltips después
 * 
 * @example
 * const originalCargarPagina = window.cargarPagina;
 * window.cargarPagina = wrapWithTooltipReinit(originalCargarPagina);
 */
function wrapWithTooltipReinit(asyncFunction) {
    return async function(...args) {
        // Ejecutar función original
        await asyncFunction.apply(this, args);
        
        // Reinicializar tooltips después
        setTimeout(() => {
            initializeAllTooltips();
        }, 100);
    };
}

/**
 * Cambia la configuración de tooltips globalmente
 * 
 * @param {Object} config - Configuración de Bootstrap tooltip
 * 
 * @example
 * updateTooltipConfig({
 *     placement: 'bottom',
 *     trigger: 'click'
 * });
 */
function updateTooltipConfig(config = {}) {
    const defaultConfig = {
        placement: 'top',
        trigger: 'hover focus',
        boundary: 'viewport',
        delay: { show: 300, hide: 100 }
    };
    
    const mergedConfig = { ...defaultConfig, ...config };
    
    // Guardar en window para referencia
    window.tooltipConfig = mergedConfig;
    
    console.log('✅ Configuración de tooltips actualizada:', mergedConfig);
}

/**
 * Obtiene el tooltip instance de un elemento
 * 
 * @param {HTMLElement|string} element - Elemento o selector
 * @returns {bootstrap.Tooltip|null} Instancia del tooltip o null
 * 
 * @example
 * const tooltip = getTooltipInstance('.mi-elemento');
 * tooltip?.show(); // Mostrar manualmente
 */
function getTooltipInstance(element) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    
    if (!element) {
        console.warn('⚠️ Elemento no encontrado');
        return null;
    }
    
    return element._bsTooltip || null;
}

/**
 * Muestra manualmente un tooltip
 * 
 * @param {HTMLElement|string} element - Elemento o selector
 * 
 * @example
 * showTooltip('.mi-elemento');
 */
function showTooltip(element) {
    const tooltip = getTooltipInstance(element);
    if (tooltip) {
        tooltip.show();
    }
}

/**
 * Oculta manualmente un tooltip
 * 
 * @param {HTMLElement|string} element - Elemento o selector
 * 
 * @example
 * hideTooltip('.mi-elemento');
 */
function hideTooltip(element) {
    const tooltip = getTooltipInstance(element);
    if (tooltip) {
        tooltip.hide();
    }
}

/**
 * Alterna un tooltip (mostrar/ocultar)
 * 
 * @param {HTMLElement|string} element - Elemento o selector
 * 
 * @example
 * toggleTooltip('.mi-elemento');
 */
function toggleTooltip(element) {
    const tooltip = getTooltipInstance(element);
    if (tooltip) {
        tooltip.toggle();
    }
}

/**
 * Actualiza el contenido de un tooltip
 * 
 * @param {HTMLElement|string} element - Elemento o selector
 * @param {string} newTitle - Nuevo contenido del tooltip
 * 
 * @example
 * updateTooltipTitle('.mi-elemento', 'Nuevo contenido');
 */
function updateTooltipTitle(element, newTitle) {
    if (typeof element === 'string') {
        element = document.querySelector(element);
    }
    
    if (!element) {
        console.warn('⚠️ Elemento no encontrado');
        return;
    }
    
    // Actualizar atributo
    element.setAttribute('data-bs-title', newTitle);
    
    // Actualizar tooltip si existe
    const tooltip = element._bsTooltip;
    if (tooltip) {
        tooltip.setContent({ '.tooltip-inner': newTitle });
    }
}

/**
 * Obtiene estadísticas de tooltips en la página
 * 
 * @returns {Object} Estadísticas con total, inicializados y no inicializados
 * 
 * @example
 * const stats = getTooltipStats();
 * console.log(stats); // { total: 15, initialized: 12, uninitialized: 3 }
 */
function getTooltipStats() {
    const total = document.querySelectorAll('[data-bs-toggle="tooltip"]').length;
    let initialized = 0;
    
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        if (el._bsTooltip) {
            initialized++;
        }
    });
    
    return {
        total: total,
        initialized: initialized,
        uninitialized: total - initialized
    };
}

/**
 * Inicialización automática cuando el documento está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Cargando sistema de tooltips...');
    
    // Verificar que Bootstrap esté disponible
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Tooltip === 'undefined') {
        console.error('❌ Bootstrap 5+ no disponible. Los tooltips no funcionarán.');
        return;
    }
    
    // Inicializar tooltips
    initializeAllTooltips();
});

/**
 * Exportar funciones para uso en módulos (si aplica)
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initializeAllTooltips,
        initializeTooltipsInRow,
        disposeAllTooltips,
        reinitializeTooltips,
        wrapWithTooltipReinit,
        updateTooltipConfig,
        getTooltipInstance,
        showTooltip,
        hideTooltip,
        toggleTooltip,
        updateTooltipTitle,
        getTooltipStats
    };
}

// Hacer funciones disponibles globalmente
window.initializeAllTooltips = initializeAllTooltips;
window.initializeTooltipsInRow = initializeTooltipsInRow;
window.disposeAllTooltips = disposeAllTooltips;
window.reinitializeTooltips = reinitializeTooltips;
window.wrapWithTooltipReinit = wrapWithTooltipReinit;
window.updateTooltipConfig = updateTooltipConfig;
window.getTooltipInstance = getTooltipInstance;
window.showTooltip = showTooltip;
window.hideTooltip = hideTooltip;
window.toggleTooltip = toggleTooltip;
window.updateTooltipTitle = updateTooltipTitle;
window.getTooltipStats = getTooltipStats;
