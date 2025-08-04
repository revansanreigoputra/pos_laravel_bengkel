@extends('layouts.master')

@section('title', 'Tambah Sparepart Baru')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Tambah Sparepart Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('spareparts.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nama Sparepart <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-semibold">Kategori <span
                                        class="text-danger">*</span></label>
                                <select class="form-select select2-init @error('category_id') is-invalid @enderror"
                                    id="category_id" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" data-name="{{ $category->name }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- hidden input purchase price --}}
                            <input type="hidden" name="purchase_price" value="{{ old('purchase_price', 0) }}">
                             

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('spareparts.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Sparepart</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script></script>

    <script>
        $(document).ready(function() {
            $('#category_id').select2({
                placeholder: 'Pilih Kategori',
                allowClear: true
            });
        });

         
    </script>
@endpush
