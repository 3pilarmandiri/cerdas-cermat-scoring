<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scoring Pertandingan: ') }} {{ $pertandingan->nama }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <p class="text-gray-700 mb-4">{{ $pertandingan->keterangan ?? '—' }}</p>

                <table class="min-w-full border border-gray-300 text-center">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="py-2 px-3 border text-left">Kelompok</th>
                            <th class="py-2 px-3 border text-left">Nama Peserta</th>
                            <th class="py-2 px-3 border">Skor</th>
                            <th class="py-2 px-3 border">Tambah Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pertandingan->kelompoks->sortBy('kode') as $k) {{-- A -> D --}}
                        <tr id="row-{{ $k->id }}">
                            <td class="border py-2 px-3 text-left font-semibold">{{ $k->kode }}</td>
                            <td class="border py-2 px-3 text-left">{{ $k->nama_peserta }}</td>

                            {{-- CLICKABLE SCORE --}}
                            <td class="border py-2 font-bold text-blue-600 skor cursor-pointer hover:underline"
                                data-id="{{ $k->id }}"
                                title="Klik untuk melihat riwayat skor">
                                {{ $k->total_skor }}
                            </td>

                            <td class="border py-2">
                                <div class="flex justify-center items-center gap-2">
                                    <input type="number"
                                           class="border rounded px-2 py-1 w-24 nilai"
                                           id="nilai-{{ $k->id }}"
                                           placeholder="+10">
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded tambah-skor"
                                            data-id="{{ $k->id }}">
                                        Tambah
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- ======= MODAL: HISTORY ======= --}}
                <div id="modalHistory"
                     class="hidden fixed inset-0 z-50"
                     aria-hidden="true"
                     role="dialog"
                     aria-modal="true">
                    {{-- Backdrop --}}
                    <div id="modalBackdrop" class="absolute inset-0 bg-gray-900/50"></div>

                    {{-- Dialog --}}
                    <div class="relative mx-auto mt-24 w-[28rem] rounded-lg bg-white shadow-lg">
                        <div class="flex items-center justify-between border-b px-4 py-3">
                            <h4 class="font-semibold text-lg">Riwayat Skor</h4>
                            <button id="closeModal"
                                    class="text-gray-500 hover:text-gray-700 text-2xl leading-none"
                                    aria-label="Tutup">
                                &times;
                            </button>
                        </div>

                        <div class="p-4 max-h-[60vh] overflow-auto">
                            <table class="w-full text-sm border border-gray-200">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="p-2 border text-left">Waktu</th>
                                        <th class="p-2 border text-center">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTable">
                                    <tr>
                                        <td colspan="2" class="p-3 text-center text-gray-500">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- ===== END MODAL ===== --}}
            </div>
        </div>
    </div>

    {{-- ===== PAGE SCRIPT ===== --}}
    <script>
        const csrf = '{{ csrf_token() }}';

        // --- Add score (AJAX), then update all visible scores
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.tambah-skor')) {
                const btn = e.target.closest('.tambah-skor');
                const id = btn.dataset.id;
                const input = document.getElementById(`nilai-${id}`);
                const nilai = parseInt(input.value, 10);

                if (isNaN(nilai) || nilai === 0) {
                    alert('Masukkan nilai yang valid!');
                    return;
                }

                btn.disabled = true;
                const originalText = btn.textContent;
                btn.textContent = 'Proses...';

                try {
                    const res = await fetch('{{ route("skor.tambah") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ kelompok_id: id, nilai })
                    });

                    const data = await res.json();
                    if (data.success) {
                        // Update DOM scores from payload: { "A": 100, "B": 50, ... }
                        Object.entries(data.payload).forEach(([kode, skor]) => {
                            const row = Array.from(document.querySelectorAll('tbody tr')).find(r => {
                                const cellKode = r.querySelector('td:first-child');
                                return cellKode && cellKode.textContent.trim() === kode;
                            });
                            if (row) {
                                const skorCell = row.querySelector('.skor');
                                if (skorCell) {
                                    skorCell.textContent = skor;
                                    skorCell.classList.add('animate-pulse');
                                    setTimeout(() => skorCell.classList.remove('animate-pulse'), 600);
                                }
                            }
                        });
                        input.value = '';
                    } else {
                        alert('Gagal memperbarui skor.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan jaringan.');
                } finally {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }
        });

        // --- Open history modal when clicking a score
        document.addEventListener('click', async (e) => {
            const cell = e.target.closest('.skor');
            if (!cell) return;

            const kelompokId = cell.dataset.id;
            openModal(); // show immediately with loading state

            try {
                const res = await fetch('{{ url("/skor/history") }}/' + kelompokId);
                const data = await res.json();

                const tableBody = document.getElementById('historyTable');
                if (data.success && data.items.length > 0) {
                    tableBody.innerHTML = '';
                    data.items.forEach(item => {
                        tableBody.insertAdjacentHTML('beforeend', `
                            <tr>
                                <td class="border p-2">${item.waktu}</td>
                                <td class="border p-2 text-center font-semibold text-blue-600">${item.nilai}</td>
                            </tr>
                        `);
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="2" class="p-3 text-center text-gray-500">Belum ada riwayat skor.</td></tr>';
                }
            } catch (err) {
                console.error(err);
                document.getElementById('historyTable').innerHTML =
                    '<tr><td colspan="2" class="p-3 text-center text-red-500">Gagal memuat data.</td></tr>';
            }
        });

        // --- Modal helpers
        const modalEl = document.getElementById('modalHistory');
        const backdropEl = document.getElementById('modalBackdrop');
        const closeBtn = document.getElementById('closeModal');

        function openModal() {
            // set loading state
            document.getElementById('historyTable').innerHTML =
                '<tr><td colspan="2" class="p-3 text-center text-gray-500">Loading...</td></tr>';

            modalEl.classList.remove('hidden');
            modalEl.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            modalEl.classList.add('hidden');
            modalEl.setAttribute('aria-hidden', 'true');
        }

        // Close via X
        closeBtn.addEventListener('click', closeModal);

        // Close via clicking backdrop
        backdropEl.addEventListener('click', closeModal);

        // Close via ESC
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modalEl.classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>
</x-app-layout>
