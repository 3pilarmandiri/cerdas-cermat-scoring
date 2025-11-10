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

                        <a href="{{ route('pertandingan.export') }}">

                                Export Excel
                            </a>
                    </div>
                </div>


                <table class="min-w-full border border-gray-300">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th width="30%" class="py-2 px-3 border">Nama</th>
                            <th class="py-2 px-3 border">Keterangan</th>
                            <th width="20%" class="py-2 px-3 border">Aksi</th>
                            {{-- <th class="py-2 px-3 border">Hapus</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pertandingans as $p)
                        <tr class="text-center">
                            <td class="py-2 px-3 border">{{ $p->nama }}</td>
                            <td class="py-2 px-3 border">{{ $p->keterangan }}</td>
                            <td class="py-2 px-3 border">
                                <a href="{{ route('pertandingan.mulai', $p->id) }}"
                                    class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded transition transform hover:scale-105">
                                        <!-- Play icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                            class="w-4 h-4 mr-1 text-white">
                                            <path d="M6.5 5.5v9l8-4.5-8-4.5z" />
                                        </svg>
                                        Mulai
                                    </a>



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
                <!-- Modal Tambah Pertandingan -->
                <div id="modalAdd" tabindex="-1" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
                        <div class="p-5 border-b">
                            <h3 class="text-lg font-bold text-gray-800">Tambah Pertandingan</h3>
                        </div>

                        <form action="{{ route('pertandingan.store') }}" method="POST" class="p-5 space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-semibold">Nama Pertandingan</label>
                                <input type="text" name="nama" class="w-full border rounded px-3 py-2" required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Keterangan</label>
                                <textarea name="keterangan" class="w-full border rounded px-3 py-2"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Kelompok A</label>
                                    <input type="text" name="kelompok[A]" class="w-full border rounded px-3 py-2" placeholder="Nama peserta A" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Kelompok B</label>
                                    <input type="text" name="kelompok[B]" class="w-full border rounded px-3 py-2" placeholder="Nama peserta B" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Kelompok C</label>
                                    <input type="text" name="kelompok[C]" class="w-full border rounded px-3 py-2" placeholder="Nama peserta C" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700">Kelompok D</label>
                                    <input type="text" name="kelompok[D]" class="w-full border rounded px-3 py-2" placeholder="Nama peserta D" required>
                                </div>
                            </div>

                            <div class="flex justify-end mt-4">
                                <button type="button"
                                        data-modal-hide="modalAdd"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                    Simpan
                                </button>
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
