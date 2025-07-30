@extends('layouts.master')
@section('title', 'Edit Servis')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Data jenis servis</div>
                        <form action="{{ route('service.update', $service->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama"
                                    value="{{ old('nama', $service->nama) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Kendaraan</label>
                                <select class="form-select" name="jenis_kendaraan_id" required>
                                    <option value="">-- Pilih Jenis Kendaraan --</option>
                                    @foreach ($jenisKendaraans as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            {{ old('jenis_kendaraan_id', $service->jenis_kendaraan_id) == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Durasi Estimasi</label>
                                <input type="text" class="form-control" name="durasi_estimasi"
                                    value="{{ old('durasi_estimasi', $service->durasi_estimasi) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Harga Standar (Rp)</label>
                                <input type="number" class="form-control" name="harga_standar"
                                    value="{{ old('harga_standar', $service->harga_standar) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="aktif"
                                        {{ old('status', $service->status) == 'aktif' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="nonaktif"
                                        {{ old('status', $service->status) == 'nonaktif' ? 'selected' : '' }}>
                                        Nonaktif</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi">{{ old('deskripsi', $service->deskripsi) }}</textarea>
                            </div>


                            <div class="mb-3 d-flex justify-content-between">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        @endsection


        {{-- initiate select2 START --}}
        @push('scripts')
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
