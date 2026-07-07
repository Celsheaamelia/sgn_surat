<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Nomor Surat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="max-w-6xl mx-auto p-6 md:p-10">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
            <span>&gt;</span>
            <span>Letter Management</span>
            <span>&gt;</span>
            <span class="text-blue-600 font-medium">Tambah Surat</span>
        </div>

        {{-- Alert sukses --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- FORM --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 pt-8 pb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Buat Nomor Surat</h2>
                    <p class="text-sm text-gray-500 mt-1">Isi data di bawah untuk generate nomor surat baru.</p>
                </div>

                <div class="border-t border-gray-100"></div>

                <form method="POST" action="{{ route('surat.store') }}" class="px-8 py-6 space-y-5" id="suratForm">
                    @csrf

                    {{-- Judul / Perihal Surat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Perihal Surat <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="perihal"
                            id="perihal"
                            required
                            placeholder="e.g. Annual Budget Approval Request"
                            value="{{ old('perihal') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Departemen --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Departemen <span class="text-red-500">*</span>
                            </label>
                            <select name="departemen" id="departemen" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Departemen</option>
                                <option value="HRD">HRD</option>
                                <option value="FIN">Finance</option>
                                <option value="OPS">Operasional</option>
                                <option value="IT">IT</option>
                                <option value="MKT">Marketing</option>
                            </select>
                        </div>

                        {{-- Penandatangan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Penandatangan <span class="text-red-500">*</span>
                            </label>
                            <select name="signatory" id="signatory" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Penandatangan</option>
                                <option value="GM">General Manager (GM)</option>
                                <option value="DIR">Direktur (DIR)</option>
                                <option value="MGR">Manager (MGR)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Tanggal Surat --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Surat <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="tanggal"
                                id="tanggal"
                                required
                                value="{{ old('tanggal', date('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>

                        {{-- Kode Tujuan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Kode Tujuan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="kode_tujuan"
                                id="kode_tujuan"
                                required
                                placeholder="e.g. EXT, INT, BOD"
                                value="{{ old('kode_tujuan') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>

                    {{-- Nomor urut (readonly, otomatis dari server) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Urut</label>
                        <input
                            type="text"
                            value="{{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}"
                            disabled
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500"
                        >
                        <p class="text-xs text-gray-400 mt-1">Nomor urut ini otomatis, berdasarkan surat terakhir yang dibuat.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="reset"
                                class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800">
                            Reset
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg
                                       transition flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Surat
                        </button>
                    </div>
                </form>
            </div>

            {{-- PREVIEW --}}
            <div class="space-y-6">
                <div class="bg-blue-600 rounded-2xl shadow-lg p-6 text-white">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-eye"></i>
                        <h3 class="font-semibold">Live Preview</h3>
                    </div>

                    <div class="bg-blue-500/40 rounded-xl p-4 mb-4">
                        <p class="text-xs text-blue-100 tracking-wide mb-1">GENERATED NUMBER</p>
                        <p class="text-lg font-bold tracking-wide" id="previewNumber">
                            {{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}/---/---/{{ date('Y') }}/{{ date('m') }}/{{ date('d') }}
                        </p>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-100">Departemen</span>
                            <span class="font-semibold" id="previewDept">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-100">Penandatangan</span>
                            <span class="font-semibold" id="previewSign">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-100">Tahun</span>
                            <span class="font-semibold">{{ date('Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Status Sistem</h3>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Siap generate nomor #{{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar surat yang sudah dibuat --}}
        @if (count($suratList ?? []) > 0)
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Daftar Nomor Surat</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-left">
                    <tr>
                        <th class="px-8 py-3 font-medium">Nomor Surat</th>
                        <th class="px-4 py-3 font-medium">Perihal</th>
                        <th class="px-4 py-3 font-medium">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (array_reverse($suratList) as $surat)
                    <tr class="border-t border-gray-100">
                        <td class="px-8 py-3 font-medium text-blue-600">{{ $surat['nomor'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $surat['perihal'] }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $surat['tanggal'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <script>
        const deptEl = document.getElementById('departemen');
        const signEl = document.getElementById('signatory');
        const seqText = "{{ str_pad($nextSequence, 4, '0', STR_PAD_LEFT) }}";
        const year = "{{ date('Y') }}";
        const month = "{{ date('m') }}";
        const day = "{{ date('d') }}";

        function updatePreview() {
            const dept = deptEl.value || '---';
            const sign = signEl.value || '---';
            document.getElementById('previewNumber').textContent =
                `${seqText}/${dept}/${sign}/${year}/${month}/${day}`;
            document.getElementById('previewDept').textContent = dept === '---' ? '-' : dept;
            document.getElementById('previewSign').textContent = sign === '---' ? '-' : sign;
        }

        deptEl.addEventListener('change', updatePreview);
        signEl.addEventListener('change', updatePreview);
    </script>
</body>
</html>