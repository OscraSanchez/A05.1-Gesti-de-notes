'use strict';

document.querySelectorAll('[data-busqueda-panel]').forEach((adm_panel) => {
    const adm_busqueda = adm_panel.querySelector('[data-busqueda-entrada]');
    const adm_items = [...adm_panel.querySelectorAll('[data-busqueda-item]')];
    const adm_vacio = adm_panel.querySelector('[data-busqueda-vacio]');

    if (!adm_busqueda || adm_items.length === 0) {
        return;
    }

    const adm_normalizar = (adm_texto) => adm_texto
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLocaleLowerCase();

    const adm_filtrar = () => {
        const adm_termino = adm_normalizar(adm_busqueda.value.trim());
        let adm_visibles = 0;

        adm_items.forEach((adm_item) => {
            const adm_texto = adm_normalizar(adm_item.dataset.busquedaTexto || adm_item.textContent);
            const adm_coincide = adm_texto.includes(adm_termino);
            adm_item.hidden = !adm_coincide;
            adm_visibles += adm_coincide ? 1 : 0;
        });

        if (adm_vacio) {
            adm_vacio.hidden = adm_visibles !== 0;
        }
    };

    adm_busqueda.addEventListener('input', adm_filtrar);
    adm_filtrar();
});
