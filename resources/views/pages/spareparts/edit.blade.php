@extends('layouts.master')

@section('title', 'Edit Sparepart')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Sparepart
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('spareparts.update', $sparepart->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Sparepart <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $sparepart->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code_part" class="form-label fw-semibold">Kode Part <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code_part') is-invalid @enderror" id="code_part" name="code_part" value="{{ old('code_part', $sparepart->code_part) }}" required>
                            @error('code_part')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="selling_price" class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $sparepart->selling_price) }}" step="0.01" min="0" required>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $sparepart->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="discount_percentage" class="form-label fw-semibold">Persentase Diskon (%)</label>
                            <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage', $sparepart->discount_percentage) }}" min="0" max="100" step="0.01">
                            @error('discount_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="discount_start_date" class="form-label fw-semibold">Tanggal Mulai Diskon</label>
                            <input type="date" class="form-control @error('discount_start_date') is-invalid @enderror" id="discount_start_date" name="discount_start_date" value="{{ old('discount_start_date', $sparepart->discount_start_date ? $sparepart->discount_start_date->format('Y-m-d') : '') }}">
                            @error('discount_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="discount_end_date" class="form-label fw-semibold">Tanggal Berakhir Diskon</label>
                            <input type="date" class="form-control @error('discount_end_date') is-invalid @enderror" id="discount_end_date" name="discount_end_date" value="{{ old('discount_end_date', $sparepart->discount_end_date ? $sparepart->discount_end_date->format('Y-m-d') : '') }}">
                            @error('discount_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('spareparts.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Perbarui Sparepart</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
