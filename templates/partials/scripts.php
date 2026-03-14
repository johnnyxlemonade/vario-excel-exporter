
<script>

    function normalize(text) {
        return text
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
    }

    const searchInput = document.getElementById('paramSearch');

    if (searchInput) {

        const rows = document.querySelectorAll('#paramTable tbody tr');

        searchInput.addEventListener('keyup', function () {

            const query = normalize(this.value.trim());

            rows.forEach(row => {

                let text = '';

                row.querySelectorAll('td').forEach(td => {
                    text += ' ' + td.textContent;
                });

                text = normalize(text);

                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }

            });

        });
    }

</script>
