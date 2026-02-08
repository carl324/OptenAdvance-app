/**
 * OptenAdvance - Helpers para manejo seguro de modales Bootstrap 5
 * Versión: 1.0.0
 * Propósito: Prevenir errores cuando modales no existen en DOM
 */
(function(window) {
  'use strict';
  
  window.OptenHelpers = {
    
    /**
     * Espera a que Bootstrap esté disponible antes de ejecutar callback
     * @param {Function} callback - Función a ejecutar cuando Bootstrap esté listo
     * @param {Number} maxAttempts - Intentos máximos (default: 20)
     */
    waitForBootstrap: function(callback, maxAttempts) {
      if (maxAttempts === undefined) maxAttempts = 20;
      var attempts = 0;
      var checkInterval = setInterval(function() {
        if (typeof bootstrap !== 'undefined') {
          clearInterval(checkInterval);
          callback();
        } else {
          attempts++;
          if (attempts >= maxAttempts) {
            clearInterval(checkInterval);
            console.error('[OptenAdvance] Bootstrap no se cargó después de ' + (maxAttempts * 100) + 'ms');
          }
        }
      }, 100);
    },
    
    /**
     * Muestra un modal de forma segura con validaciones y fallbacks
     * @param {String} modalId - ID del modal (sin #)
     * @param {Object} options - Opciones de configuración
     * @returns {Boolean} - true si se mostró, false si falló
     */
    mostrarModalSeguro: function(modalId, options) {
      options = options || {};
      var fallbackUrl = options.fallbackUrl || null;
      var fallbackMessage = options.fallbackMessage || null;
      var onError = options.onError || null;

      // Validación 1: Bootstrap debe estar cargado
      if (typeof bootstrap === 'undefined') {
        console.error('[OptenAdvance] Bootstrap no disponible para modal: ' + modalId);
        if (fallbackUrl) {
          window.location.href = fallbackUrl;
          return false;
        }
        if (fallbackMessage) {
          alert(fallbackMessage);
          return false;
        }
        if (onError) onError('bootstrap_undefined');
        return false;
      }

      // Validación 2: El modal debe existir en el DOM
      var modalEl = document.getElementById(modalId);
      if (!modalEl) {
        console.warn('[OptenAdvance] Modal no encontrado en DOM: ' + modalId);
        if (fallbackUrl) {
          window.location.href = fallbackUrl;
          return false;
        }
        if (fallbackMessage) {
          alert(fallbackMessage);
          return false;
        }
        if (onError) onError('modal_not_found');
        return false;
      }

      // Validación 3: Intentar mostrar el modal
      try {
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        return true;
      } catch (err) {
        console.error('[OptenAdvance] Error al mostrar modal ' + modalId + ':', err);
        if (fallbackUrl) {
          window.location.href = fallbackUrl;
          return false;
        }
        if (fallbackMessage) {
          alert(fallbackMessage);
          return false;
        }
        if (onError) onError('show_failed', err);
        return false;
      }
    }
  };
})(window);
