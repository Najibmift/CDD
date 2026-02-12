<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-column" style="height: 90vh;">

    <div class="bg-white py-3 px-4 shadow-sm" style="position: sticky; top: 0; z-index: 10;">
        <div class="position-relative mx-auto" style="max-width: 600px;">
            <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="🔎 Cari Nomor Kontainer..." style="border-radius: 50px; padding-left: 20px; padding-right: 60px;" aria-label="Cari Nomor Kontainer">
            <button id="searchButton" class="position-absolute top-50 end-0 translate-middle-y me-2 btn btn-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" aria-label="Tombol Cari">
                <i class="bi bi-search fs-5"></i>
            </button>
        </div>
    </div>

    <div id="scrollableContent" class="flex-grow-1 overflow-auto px-4" style="padding-top: 1.5rem; padding-bottom: 4rem;">
        <div id="loadingIndicator" class="text-center my-5 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
            <p class="mt-2">Sedang memuat data kontainer...</p>
        </div>

        <div id="noResultsMessage" class="text-center my-5 d-none">
            <i class="bi bi-emoji-frown fs-1 text-muted"></i>
            <p class="mt-2 fs-5 text-muted">Oops! Tidak ada kontainer yang cocok.</p>
            <p class="text-muted">Coba kata kunci lain atau periksa kembali nomor kontainer Anda.</p>
        </div>

        <div id="errorMessage" class="alert alert-danger text-center my-5 d-none" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> Aduh! Terjadi kesalahan saat memuat data. Coba lagi nanti.
        </div>

        <div id="kontainerData" class="d-flex flex-column align-items-center gap-4">
        </div>
    </div>

    <div class="bg-white py-2 px-4 shadow-sm" style="position: sticky; bottom: 0; z-index: 10;">
        <nav id="paginationContainer" aria-label="Navigasi halaman">
            <ul id="pagination" class="pagination justify-content-center mb-0">
            </ul>
        </nav>
    </div>

</div>

<?php if (uri_string() == 'cari-kontainer') : ?>
    <script>
        const kontainerDataEl = document.getElementById('kontainerData');
        const paginationEl = document.getElementById('pagination');
        const searchInputEl = document.getElementById('searchInput');
        const searchButtonEl = document.getElementById('searchButton');
        const loadingIndicatorEl = document.getElementById('loadingIndicator');
        const noResultsMessageEl = document.getElementById('noResultsMessage');
        const errorMessageEl = document.getElementById('errorMessage');
        const paginationContainerEl = document.getElementById('paginationContainer');

        let fetchTimeout; // Untuk debounce

        function renderKontainerCard(k) {
            // Gunakan gambar default yang lebih informatif atau clean
            const imageUrl = k.gambar || '<?= base_url('assets/images/placeholder-container.png') ?>'; // Asumsi ada placeholder
            // Atau jika tidak mau dependensi base_url di JS:
            // const imageUrl = k.gambar || 'https://via.placeholder.com/350x250/e9ecef/6c757d?text=Gambar+Kontainer';

            return `
                <div class="card shadow-sm border-0 w-100" style="max-width: 950px; transition: all 0.3s ease-in-out;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="${imageUrl}" class="img-fluid rounded-start" alt="Gambar Kontainer ${k.nomor}" style="object-fit: cover; height: 100%; min-height:180px;">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body d-flex flex-column h-100">
                                <h5 class="card-title fw-bold text-primary mb-2">${k.nomor}</h5>
                                <p class="card-text mb-1"><small class="text-muted"><i class="bi bi-calendar3 me-1"></i> ${k.waktu || 'Data tidak tersedia'}</small></p>
                                <p class="card-text mb-1"><small class="text-muted"><i class="bi bi-geo-alt-fill me-1"></i> ${k.tempat || 'Data tidak tersedia'}</small></p>
                                <p class="card-text fw-semibold mt-auto ${k.jenis_kerusakan ? 'text-danger' : 'text-success'}">
                                    ${k.jenis_kerusakan ? '<i class="bi bi-exclamation-triangle me-1"></i>' + k.jenis_kerusakan : '<i class="bi bi-check-circle me-1"></i>Tidak ada kerusakan'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPaginationLinks(current, total, keyword) {
            let html = '';
            const maxVisiblePages = 5; // Jumlah maksimal tombol halaman yang terlihat (selain prev/next/first/last)

            // Tombol "Previous"
            if (current > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" aria-label="Previous" onclick="event.preventDefault(); fetchKontainerData('${keyword}', ${current - 1})"><span aria-hidden="true">&laquo;</span></a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link" aria-hidden="true">&laquo;</span></li>`;
            }

            let startPage, endPage;
            if (total <= maxVisiblePages) {
                startPage = 1;
                endPage = total;
            } else {
                const maxPagesBeforeCurrentPage = Math.floor(maxVisiblePages / 2);
                const maxPagesAfterCurrentPage = Math.ceil(maxVisiblePages / 2) - 1;
                if (current <= maxPagesBeforeCurrentPage) {
                    startPage = 1;
                    endPage = maxVisiblePages;
                } else if (current + maxPagesAfterCurrentPage >= total) {
                    startPage = total - maxVisiblePages + 1;
                    endPage = total;
                } else {
                    startPage = current - maxPagesBeforeCurrentPage;
                    endPage = current + maxPagesAfterCurrentPage;
                }
            }

            // Tombol halaman pertama dan elipsis jika perlu
            if (startPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); fetchKontainerData('${keyword}', 1)">1</a></li>`;
                if (startPage > 2) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            // Tombol halaman
            for (let i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === current ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="event.preventDefault(); fetchKontainerData('${keyword}', ${i})">${i}</a>
                         </li>`;
            }

            // Tombol halaman terakhir dan elipsis jika perlu
            if (endPage < total) {
                if (endPage < total - 1) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); fetchKontainerData('${keyword}', ${total})">${total}</a></li>`;
            }

            // Tombol "Next"
            if (current < total) {
                html += `<li class="page-item"><a class="page-link" href="#" aria-label="Next" onclick="event.preventDefault(); fetchKontainerData('${keyword}', ${current + 1})"><span aria-hidden="true">&raquo;</span></a></li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link" aria-hidden="true">&raquo;</span></li>`;
            }

            paginationEl.innerHTML = html;
            paginationContainerEl.classList.toggle('d-none', total <= 1);
        }

        function showLoading(isLoading) {
            loadingIndicatorEl.classList.toggle('d-none', !isLoading);
            if (isLoading) {
                kontainerDataEl.innerHTML = ''; // Kosongkan data saat loading baru
                noResultsMessageEl.classList.add('d-none');
                errorMessageEl.classList.add('d-none');
                paginationContainerEl.classList.add('d-none');
            }
        }

        function fetchKontainerData(keyword = '', page = 1) {
            showLoading(true);
            // Pastikan URL controller benar. Jika tidak pakai index.php, sesuaikan.
            const apiUrl = `<?= site_url('cari-kontainer/search') ?>?keyword=${encodeURIComponent(keyword)}&page=${page}`;

            fetch(apiUrl)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(response => {
                    showLoading(false);
                    if (response.data && response.data.length > 0) {
                        kontainerDataEl.innerHTML = response.data.map(renderKontainerCard).join('');
                        noResultsMessageEl.classList.add('d-none');
                    } else {
                        kontainerDataEl.innerHTML = '';
                        noResultsMessageEl.classList.remove('d-none');
                    }
                    renderPaginationLinks(response.currentPage, response.totalPages, keyword);
                })
                .catch(error => {
                    console.error('Error fetching container data:', error);
                    showLoading(false);
                    kontainerDataEl.innerHTML = ''; // Kosongkan data jika ada error
                    errorMessageEl.classList.remove('d-none');
                    paginationContainerEl.classList.add('d-none');
                });
        }

        // Live Search dengan Debounce
        searchInputEl.addEventListener('keyup', () => {
            clearTimeout(fetchTimeout);
            fetchTimeout = setTimeout(() => {
                fetchKontainerData(searchInputEl.value.trim());
            }, 500); // Tunggu 500ms setelah user berhenti mengetik
        });

        // Search on button click
        searchButtonEl.addEventListener('click', () => {
            clearTimeout(fetchTimeout); // Batalkan timeout keyup jika ada
            fetchKontainerData(searchInputEl.value.trim());
        });

        // Allow search on Enter key press in input
        searchInputEl.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault(); // Mencegah submit form jika input ada di dalam form
                clearTimeout(fetchTimeout);
                fetchKontainerData(searchInputEl.value.trim());
            }
        });

        // Initial Load
        window.addEventListener('DOMContentLoaded', () => {
            fetchKontainerData();
        });
    </script>
<?php endif; ?>

<style>
    .card:hover {
        transform: translateY(-5px) scale(1.01);
        /* Sedikit zoom saat hover */
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        /* Shadow lebih tegas */
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        /* Primary Bootstrap color */
        border-color: #0d6efd;
    }

    .page-link {
        color: #0d6efd;
    }

    .page-link:hover {
        color: #0a58ca;
    }

    #searchInput::placeholder {
        color: #999;
    }
</style>

<?= $this->endSection() ?>