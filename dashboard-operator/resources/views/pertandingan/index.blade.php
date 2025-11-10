<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pertandingan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Daftar Pertandingan</h3>

                    <div class="flex gap-2">
                        <button
                            data-modal-target="modalAdd"
                            data-modal-toggle="modalAdd"
                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
                        >
                            Tambah Pertandingan
                        </button>

                        <a href="{{ route('pertandingan.export') }}"
                            class="inline-flex items-center bg-[#217346] hover:bg-[#1e6b3f] text-white px-4 py-2 rounded shadow transition transform hover:scale-105">
                                <!-- Excel icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                    fill="currentColor" class="w-5 h-5 mr-2 text-white">
                                    <path d="M4 4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h16a2
                                            2 0 0 0 2-2V6a2 2 0 0 0-2-2H4zm3.54 5.12h1.92l1.5
                                            2.64 1.5-2.64h1.93l-2.41 3.88L14.5 17h-1.93l-1.56-2.79L9.46
                                            17H7.54l2.44-3.99-2.44-3.89zM18 6v12H6V6h12z"/>
                                </svg>
                                Export Excel
                            </a>
                    </div>
                </div>


                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="py-2 px-3 border">Nama</th>
                            <th class="py-2 px-3 border">Keterangan</th>
                            <th class="py-2 px-3 border">Mulai</th>
                            <th class="py-2 px-3 border">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pertandingans as $p)
                        <tr class="text-center">
                            <td class="py-2 px-3 border">{{ $p->nama }}</td>
                            <td class="py-2 px-3 border">{{ $p->keterangan }}</td>
                            <td class="py-2 px-3 border">
                                <a href="{{ route('pertandingan.mulai', $p->id) }}"
                                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                                   Mulai
                                </a> &nbsp;

                            </td>
                            <td class="py-2 px-3 border">
                              <a href="#"
                                    onclick="deletePertandingan('{{ route('pertandingan.destroy', $p->id) }}')"
                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">
                                        <!-- Trash icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-4 h-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 7.5h12m-9 3v6m6-6v6M4.5 7.5l.867 12.142A2.25 2.25 0 007.61 21h8.78a2.25 2.25 0 002.243-1.858L19.5 7.5M9.75 4.5h4.5a.75.75 0 01.75.75V6h-6V5.25a.75.75 0 01.75-.75z" />
                                        </svg>
                                        Hapus
                                    </a>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Modal Tambah Pertandingan -->
                <div id="modalAdd" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="bg-white rounded-lg shadow-lg w-96">
                        <form method="POST" action="{{ route('pertandingan.store') }}">
                            @csrf
                            <div class="p-4 border-b">
                                <h4 class="font-semibold text-lg">Tambah Pertandingan</h4>
                            </div>
                            <div class="p-4 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Pertandingan</label>
                                    <input type="text" name="nama" class="w-full border-gray-300 rounded mt-1" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <textarea name="keterangan" class="w-full border-gray-300 rounded mt-1"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end p-4 border-t">
                                <button type="button" data-modal-hide="modalAdd" class="px-3 py-1 bg-gray-400 text-white rounded mr-2">Batal</button>
                                <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                document.querySelectorAll('[data-modal-toggle]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const target = document.getElementById(btn.dataset.modalTarget);
                        target.classList.remove('hidden');
                    });
                });
                document.querySelectorAll('[data-modal-hide]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        btn.closest('.fixed').classList.add('hidden');
                    });
                });

                function deletePertandingan(url) {
                    if (!confirm('Yakin ingin menghapus pertandingan ini? Semua data peserta & skor akan hilang!')) return;

                   fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                alert('Pertandingan berhasil dihapus!');
                                location.reload();
                            } else {
                                console.error('Delete failed:', response.status);
                                alert('Gagal menghapus pertandingan!');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan jaringan.');
                        });
                }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
