/**
 * LayanDesa - Admin JavaScript
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();
    initDeleteConfirm();
    initAlertDismiss();
    initFormValidation();
    initTableSearch();
});

// =============================================
// Sidebar Toggle (Mobile)
// =============================================
function initSidebarToggle() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if (!toggleBtn || !sidebar) return;

    let overlay = null;

    function openSidebar() {
        sidebar.classList.add('open');
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
        overlay.addEventListener('click', closeSidebar);
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) { overlay.remove(); overlay = null; }
    }

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
}

// =============================================
// Delete Confirmation
// =============================================
function initDeleteConfirm() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function (e) {
            const msg = this.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });
}

// =============================================
// Alert Auto-Dismiss
// =============================================
function initAlertDismiss() {
    document.querySelectorAll('.admin-alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.maxHeight = '0';
            alert.style.padding = '0';
            alert.style.marginBottom = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // Manual close button
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', function () {
            const alert = this.closest('.admin-alert');
            if (alert) alert.remove();
        });
    });
}

// =============================================
// Admin Form Validation
// =============================================
function initFormValidation() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', function (e) {
            let isValid = true;

            // Remove previous errors
            this.querySelectorAll('.field-error').forEach(el => el.remove());
            this.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));

            // Validate required fields
            this.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('invalid');
                    field.style.borderColor = '#e53e3e';
                    field.style.boxShadow = '0 0 0 3px rgba(229,62,62,0.08)';

                    const err = document.createElement('div');
                    err.className = 'field-error';
                    err.style.cssText = 'color:#e53e3e;font-size:0.78rem;margin-top:4px;';
                    err.textContent = `Field ini wajib diisi.`;
                    field.parentNode.insertBefore(err, field.nextSibling);
                }
            });

            if (!isValid) {
                e.preventDefault();
                const firstInvalid = this.querySelector('.invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        });
    });
}

// =============================================
// Table Search Filter
// =============================================
function initTableSearch() {
    const searchInput = document.getElementById('tableSearch');
    const table = document.querySelector('.admin-table tbody');
    if (!searchInput || !table) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });

        // Show "no results" message
        const visible = [...rows].filter(r => r.style.display !== 'none');
        let noResultRow = table.querySelector('.no-result-row');

        if (visible.length === 0 && query) {
            if (!noResultRow) {
                noResultRow = document.createElement('tr');
                noResultRow.className = 'no-result-row';
                noResultRow.innerHTML = `<td colspan="99" style="text-align:center;padding:32px;color:#6b8a6c;">
                    Tidak ada data yang cocok dengan "<strong>${query}</strong>"
                </td>`;
                table.appendChild(noResultRow);
            }
        } else if (noResultRow) {
            noResultRow.remove();
        }
    });
}
