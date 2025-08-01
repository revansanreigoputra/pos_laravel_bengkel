@extends('layouts.master')
@section('title', 'Laporan Sparepart')

@section('styles')
    <style>
        .main-card {}

        /* Custom styling for top navigation tabs to match reference */
        .nav-pills .nav-link {
            padding: 10px 25px;
            background-color: #f7f9fc;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            /* Rounded pill shape */
            color: #6a7f8e;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .nav-pills .nav-link.active {
            background-color: #e0e9fa !important;
            /* Light blue for active tab */
            border-color: #b1d4fa !important;
            /* Matching border color */
            color: #407bff !important;
            /* Primary blue for active text */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-2px);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #eaf1f9;
            border-color: #d1e0f0;
        }

        /* Search and Filter Area */
        .filter-area .form-control,
        .filter-area .form-select {
            height: calc(2.8rem + 2px);
            /* Consistent height */
            border-radius: 8px;
            font-size: 1rem;
        }

        .filter-area .btn {
            height: calc(2.8rem + 2px);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
        }

        .filter-area .btn-primary {
            /* For Search button */
            background-color: #407bff;
            border-color: #407bff;
        }

        .filter-area .btn-success {
            /* For Export button */
            background-color: #1abc9c;
            border-color: #1abc9c;
        }


        /* Status Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            /* Pill shape */
            font-weight: 600;
            font-size: 0.8rem;
            /* Smaller font size */
            white-space: nowrap;
            display: inline-block;
        }

        .status-badge.available {
            background-color: #e6f7ed;
            color: #28a745;
            border: 1px solid #d4edda;
        }

        .status-badge.expired {
            background-color: #fcebeb;
            color: #dc3545;
            border: 1px solid #f5c6cb;
        }

        .status-badge.empty {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        /* Table Action Buttons */
        .action-buttons .btn {
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-right: 8px;
            /* Space between buttons */
            box-shadow: none;
            /* Remove default bootstrap shadow */
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .action-buttons .btn-info-custom {
            background-color: #e0e9fa;
            /* Light blue */
            color: #407bff;
            border: 1px solid #e0e9fa;
            /* Matching border */
        }

        .action-buttons .btn-info-custom:hover {
            background-color: #c7dbf8;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        .action-buttons .btn-success-custom {
            background-color: #d1f2eb;
            /* Light teal */
            color: #1abc9c;
            border: 1px solid #d1f2eb;
        }

        .action-buttons .btn-success-custom:hover {
            background-color: #b8e8de;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        .action-buttons .btn-danger-custom {
            background-color: #fcebeb;
            /* Light red */
            color: #dc3545;
            border: 1px solid #fcebeb;
        }

        .action-buttons .btn-danger-custom:hover {
            background-color: #f8d7da;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }


        /* Pagination */
        .pagination-custom {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-top: 25px;
            gap: 8px;
        }

        .pagination-custom .page-item .page-link {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            text-decoration: none;
            color: #6a7f8e;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: #f7f9fc;
        }

        .pagination-custom .page-item.active .page-link {
            background-color: #407bff !important;
            border-color: #407bff !important;
            color: white !important;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for active page */
        }

        .pagination-custom .page-item .page-link:hover:not(.active) {
            background-color: #eaf1f9;
            border-color: #d1e0f0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
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
                    <option value="sparepart 1">sparepart  A  </option>
                    <option value="sparepart 2">sparepart  B  </option>
                    <option value="sparepart 3">sparepart  C  </option>
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
