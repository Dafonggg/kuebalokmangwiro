@extends('layouts.admin')

@section('title', 'Tambah Metode Pembayaran - Admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Tambah Metode Pembayaran</h1>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Metode Pembayaran *</label>
                <input type="text" name="name" id="name" required
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Tipe *</label>
                <select name="type" id="type" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                        onchange="toggleFields()">
                    <option value="">Pilih Tipe</option>
                    <option value="qris" {{ old('type') == 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="bank_transfer" {{ old('type') == 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="qris_fields" style="display: none;">
                <div>
                    <label for="qr_code_image" class="block text-sm font-medium text-gray-700">Gambar QR Code</label>
                    <input type="file" name="qr_code_image" id="qr_code_image" accept="image/*"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm">
                    @error('qr_code_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="bank_transfer_fields" style="display: none;">
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700">Nama Bank *</label>
                    <input type="text" name="bank_name" id="bank_name"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                           value="{{ old('bank_name') }}">
                    @error('bank_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="account_number" class="block text-sm font-medium text-gray-700">Nomor Rekening *</label>
                    <input type="text" name="account_number" id="account_number"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                           value="{{ old('account_number') }}">
                    @error('account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="account_name" class="block text-sm font-medium text-gray-700">Atas Nama *</label>
                    <input type="text" name="account_name" id="account_name"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                           value="{{ old('account_name') }}">
                    @error('account_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="display_order" class="block text-sm font-medium text-gray-700">Urutan Tampil</label>
                <input type="number" name="display_order" id="display_order" min="0"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2e4358] focus:border-[#2e4358] sm:text-sm"
                       value="{{ old('display_order', 0) }}">
                @error('display_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#2e4358] shadow-sm focus:border-[#2e4358]/50 focus:ring focus:ring-[#2e4358]/20 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.payment-methods.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="bg-[#2e4358] text-white px-4 py-2 rounded-lg hover:bg-[#1a2a3a]">
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('type').value;
        const qrisFields = document.getElementById('qris_fields');
        const bankTransferFields = document.getElementById('bank_transfer_fields');
        
        if (type === 'qris') {
            qrisFields.style.display = 'block';
            bankTransferFields.style.display = 'none';
            document.getElementById('bank_name').removeAttribute('required');
            document.getElementById('account_number').removeAttribute('required');
            document.getElementById('account_name').removeAttribute('required');
        } else if (type === 'bank_transfer') {
            qrisFields.style.display = 'none';
            bankTransferFields.style.display = 'block';
            document.getElementById('bank_name').setAttribute('required', 'required');
            document.getElementById('account_number').setAttribute('required', 'required');
            document.getElementById('account_name').setAttribute('required', 'required');
        } else {
            qrisFields.style.display = 'none';
            bankTransferFields.style.display = 'none';
            document.getElementById('bank_name').removeAttribute('required');
            document.getElementById('account_number').removeAttribute('required');
            document.getElementById('account_name').removeAttribute('required');
        }
    }

    // Trigger on page load if value is already set
    if (document.getElementById('type').value) {
        toggleFields();
    }
</script>
@endsection

