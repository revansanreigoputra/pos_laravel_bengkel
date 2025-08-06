 

@extends('layouts.master')
@section('title', 'Tambah Servis')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Data jenis servis</div>
                        <form action="{{ route('service.store') }}" method="POST">
                            @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Service</label>
                                        <input type="text" class="form-control" name="nama"
                                            value="{{ old('nama') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Jenis Kendaraan</label>
                                        <select  name="jenis_kendaraan_id" id="jenis_kendaraan_id" class="form-select select2-init" required>
                                            <option value="">-- Pilih Jenis Kendaraan --</option>
                                            @foreach ($jenisKendaraans as $jenis)
                                                <option value="{{ $jenis->id }}"
                                                    {{ old('jenis_kendaraan_id') == $jenis->id ? 'selected' : '' }}>
                                                    {{ $jenis->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Durasi Estimasi</label>
                                        <input type="text" class="form-control" name="durasi_estimasi"
                                            value="{{ old('durasi_estimasi') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Harga Standar (Rp)</label>
                                        <input type="number" class="form-control" name="harga_standar"
                                            value="{{ old('harga_standar') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status" required>
                                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                            </option>
                                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>
                                                Nonaktif</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea class="form-control" name="deskripsi">{{ old('deskripsi') }}</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endsection


        {{-- initiate select2 START --}}
        @push('addon-script')
            <script>
                $(document).ready(function() {
                    $('#jenis_kendaraan_id').select2({
                        placeholder: 'Pilih kendaraan',
                        allowClear: true
                    });
                    ActiveXObject
                });
            </script>
        @endpush
