/* =========================================================
   HEALTHWARE · FACTURACIÓN: emisión con pago único
   Conceptos dinámicos (servicio, cantidad, precio), total en vivo
   y validación mínima antes de enviar. El envío AJAX lo hace el
   script del módulo (facturas.js) sobre #formNuevo.
   ========================================================= */
(() => {
    'use strict';

    const dinero = (n) => `$ ${Number(n || 0).toFixed(2)}`;

    document.addEventListener('DOMContentLoaded', () => {
        const formulario = document.querySelector('form[data-hw-factura]');
        if (!formulario) return;

        const contenedor = formulario.querySelector('[data-hw-lineas]');
        const plantilla = document.getElementById('hwLineaPlantilla');
        const botonAgregar = formulario.querySelector('[data-hw-agregar-linea]');
        const modal = formulario.closest('.modal');
        if (!contenedor || !plantilla) return;

        let indice = 0;

        const total = () => {
            let suma = 0;
            contenedor.querySelectorAll('[data-hw-linea]').forEach((linea) => {
                const cantidad = parseFloat(linea.querySelector('[data-hw-cantidad]')?.value) || 0;
                const precio = parseFloat(linea.querySelector('[data-hw-precio]')?.value) || 0;
                suma += cantidad * precio;
            });
            return Math.round(suma * 100) / 100;
        };

        function recalcular() {
            const valor = total();
            formulario.querySelectorAll('[data-hw-subtotal], [data-hw-total]').forEach((nodo) => { nodo.textContent = dinero(valor); });
            [['[data-hw-campo-subtotal]', valor], ['[data-hw-campo-total]', valor], ['[data-hw-campo-pagado]', valor]].forEach(([selector, v]) => {
                const campo = formulario.querySelector(selector);
                if (campo) campo.value = v.toFixed(2);
            });
        }

        function agregar() {
            const contenido = document.createElement('div');
            contenido.innerHTML = plantilla.innerHTML.replaceAll('__I__', String(indice++)).trim();
            const linea = contenido.firstElementChild;
            contenedor.appendChild(linea);
            recalcular();
            return linea;
        }

        function reiniciar() {
            contenedor.innerHTML = '';
            indice = 0;
            agregar();
        }

        botonAgregar?.addEventListener('click', agregar);

        contenedor.addEventListener('click', (evento) => {
            const quitar = evento.target.closest('[data-hw-quitar-linea]');
            if (!quitar) return;
            quitar.closest('[data-hw-linea]')?.remove();
            recalcular();
        });

        contenedor.addEventListener('change', (evento) => {
            if (evento.target.matches('[data-hw-servicio]')) {
                const linea = evento.target.closest('[data-hw-linea]');
                const precio = linea?.querySelector('[data-hw-precio]');
                const referencia = evento.target.selectedOptions[0]?.dataset.precio;
                if (precio && referencia !== undefined) precio.value = Number(referencia).toFixed(2);
            }
            recalcular();
        });
        contenedor.addEventListener('input', recalcular);

        // Validación mínima antes de que el módulo envíe por AJAX (fase de captura)
        formulario.addEventListener('submit', (evento) => {
            let error = '';
            if (!contenedor.querySelector('[data-hw-linea]')) {
                error = 'Agrega al menos un concepto a la factura.';
            } else if (total() <= 0) {
                error = 'El total de la factura debe ser mayor que cero.';
            }
            if (!error) return;

            evento.preventDefault();
            evento.stopImmediatePropagation();
            const resumen = formulario.querySelector('[data-form-errors]');
            if (resumen) {
                resumen.classList.remove('d-none');
                resumen.textContent = error;
            }
        }, true);

        modal?.addEventListener('show.bs.modal', () => { if (!contenedor.children.length) reiniciar(); });
        modal?.addEventListener('hidden.bs.modal', () => window.setTimeout(reiniciar, 0));
        formulario.addEventListener('reset', () => window.setTimeout(reiniciar, 0));

        reiniciar();
    });
})();
