@extends('layouts.master')
@section('title', 'Laporan Sparepart')

@section('styles')
    <style>
        /* Custom active pill style */
        .nav-pills .nav-link.active {
            background-color: #357c3e;
            /* Bootstrap primary color */
            color: white;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Optional: Hover effect */
        .nav-pills .nav-link:not(.active):hover {
            background-color: #f8f9fa;
            color: #357c3e;
        }
    </style>

@endsection
@section('content')
    <div class="card">
        <div class="card-header">

            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-available-tab" data-bs-toggle="pill"
                        data-bs-target="#pills-available" type="button" role="tab" aria-controls="pills-available"
                        aria-selected="true">Available Stock</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-expired-tab" data-bs-toggle="pill" data-bs-target="#pills-expired"
                        type="button" role="tab" aria-controls="pills-expired" aria-selected="false">Expired
                        Stock</button>

                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-empty-tab" data-bs-toggle="pill" data-bs-target="#pills-empty"
                        type="button" role="tab" aria-controls="pills-empty" aria-selected="false">Empty Stock</button>

                </li>
            </ul>
        </div>

        <div class="card-body filter-area d-flex align-items-center mb-4">

            <select class="form-select me-3 select2-init">
                <option value="">Please choose</option>
                <option value="sparepart 1">sparepart A </option>
                <option value="sparepart 2">sparepart B </option>
                <option value="sparepart 3">sparepart C </option>
            </select>
            <button class="btn btn-primary me-3">Cari</button>s
            <button class="btn btn-success ms-auto">Export</button>
        </div>

        <div class="card-body table-responsive" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-available" role="tabpanel"
                aria-labelledby="pills-available-tab">
                <nav class="pagination-custom">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#">&lt;</a></li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><span class="page-link">...</span></li>
                        <li class="page-item"><a class="page-link" href="#">17</a></li>
                        <li class="page-item"><a class="page-link" href="#">&gt;</a></li>
                    </ul>
                </nav>
            </div>
        </div>


    @endsection

    @section('scripts')

    @endsection
