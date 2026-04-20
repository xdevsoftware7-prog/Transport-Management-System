/**
 * APP.JS — OBTRANS TMS
 *
 * Ce fichier gère :
 *   - Le collapse / expand de la sidebar
 *   - La persistence du state (localStorage)
 *
 * POUR AJOUTER UN COMPORTEMENT :
 *   Ajouter une nouvelle section clairement commentée.
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ════════════════════════════════════════
       1. SIDEBAR : COLLAPSE / EXPAND
    ════════════════════════════════════════ */

    const sidebar     = document.getElementById('sidebar');
    const mainWrapper = document.getElementById('mainWrapper');
    const collapseBtn = document.getElementById('collapseBtn');
    const STORAGE_KEY = 'sidebar_collapsed';

    /**
     * Applique le state collapsed sans animation (au chargement initial).
     */
    function applyCollapsed(collapsed, animate) {
        if (!animate) {
            sidebar.style.transition     = 'none';
            mainWrapper.style.transition = 'none';
        }

        if (collapsed) {
            sidebar.classList.add('collapsed');
            mainWrapper.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
            mainWrapper.classList.remove('collapsed');
        }

        if (!animate) {
            // Force reflow pour que la suppression de transition prenne effet instantanément
            sidebar.offsetHeight;
            sidebar.style.transition     = '';
            mainWrapper.style.transition = '';
        }
    }

    /**
     * Toggle le sidebar et sauvegarde le choix.
     */
    function toggleSidebar() {
        const isCollapsed = sidebar.classList.contains('collapsed');
        applyCollapsed(!isCollapsed, true);
        localStorage.setItem(STORAGE_KEY, !isCollapsed ? '1' : '0');
    }

    // Restaurer le state au chargement (sans animation)
    const savedState = localStorage.getItem(STORAGE_KEY);
    if (savedState === '1') {
        applyCollapsed(true, false);
    }

    // Attacher le listener sur le bouton
    if (collapseBtn) {
        collapseBtn.addEventListener('click', toggleSidebar);
    }

    /* ════════════════════════════════════════
       2. AJOUTER VOS COMPORTEMENTS ICI
       Exemple :
         document.getElementById('monElement')
           .addEventListener('click', maFonction);
    ════════════════════════════════════════ */

});