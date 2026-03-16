<script>

    document.addEventListener('DOMContentLoaded', () => {
        
        const tooltipTriggers = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggers].forEach(el => new bootstrap.Tooltip(el));

        const normalize = text =>
            text
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');

        const searchInput = document.getElementById('paramSearch');
        if (!searchInput) return;

        const rows = document.querySelectorAll('#paramTable tbody tr[data-row="filterable"]');
        const noResultsRow = document.getElementById('noResultsRow');
        const searchTerm = document.getElementById('searchTerm');

        const filter = () => {

            const query = normalize(searchInput.value.trim());

            let visible = 0;

            rows.forEach(row => {

                const text = normalize(row.textContent);
                const match = text.includes(query);

                row.style.display = match ? '' : 'none';

                if (match) visible++;

            });

            if (visible === 0 && query !== '') {
                noResultsRow.classList.remove('d-none');
                searchTerm.textContent = searchInput.value.trim();
            } else {
                noResultsRow.classList.add('d-none');
            }

        };

        searchInput.addEventListener('input', filter);

        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                filter();
            }
        });

    });

</script>
