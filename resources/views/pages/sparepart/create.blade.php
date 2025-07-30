@extends('layouts.master')
@section('title', 'Tambah Sparepart')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tambah Sparepart</h5>
                        <form action="{{ route('sparepart.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                {{--   START --}}
                                {{-- Added id="category_id" and class="select2-init" --}}
                                <select name="category_id" id="category_id" class="form-select select2-init" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                {{--   END --}}
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Sparepart</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode Sparepart</label>
                                <input type="text" class="form-control" name="code_part" required>
                            </div>  
                            <div class="mb-3">
                                {{-- <label class="form-label">Harga Jual</label> --}}
                                <input type="hidden" class="form-control" name="selling_price" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Expired Date</label>
                                <input type="date" class="form-control" name="expired_date" required>
                            </div>

                            <div class="mb-3 d-flex justify-content-between">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- initiate select2 START --}} 
@push('scripts')
    <script>
        $(document).ready(function() { 
            $('#category_id').select2({
                placeholder: 'Pilih Kategori', 
                allowClear: true  
            });
             ActiveXObject
        });
    </script>
@endpush 
