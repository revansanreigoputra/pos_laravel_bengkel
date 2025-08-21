 <!DOCTYPE html>
 <html>

 <head>
     <title>{{ $reportTitle }}</title>
     <style>
         body {
             font-family: DejaVu Sans, sans-serif;
             font-size: 12px;
             margin: 20px;
         }

         .header {
             text-align: center;
             margin-bottom: 10px;
         }

         .header h2 {
             margin: 0;
             font-size: 18px;
         }

         .header small {
             display: block;
             font-size: 12px;
             margin-top: 4px;
             color: #666;
         }

         .date {
             text-align: right;
             font-size: 12px;
             margin-bottom: 10px;
             color: #444;
         }

         table {
             width: 100%;
             border-collapse: collapse;
             font-size: 11px;
         }

         th {
             background-color: #f0f0f0;
             font-weight: bold;
         }

         th,
         td {
             border: 1px solid #888;
             padding: 6px;
             text-align: center;
         }

         tfoot td {
             font-style: italic;
             text-align: left;
             border: none;
         }
     </style>
 </head>

 <body>
    <div class="header">
        <h2>{{ \App\Models\BengkelSetting::getSettings()->nama_bengkel }}</h2>
        <h4 >{{ $reportTitle }}</h4>
    </div>
     <p>Rentang data: {{ $startDate ?? 'Semua' }} - {{ $endDate ?? ' ' }}</p>

     {{-- Conditional rendering for each tab --}}

     @if ($tab === 'available')
         @include('components.available-sparepart')
     @elseif ($tab === 'expired')
         @include('components.expired-sparepart')
     @elseif ($tab === 'empty')
         @include('components.empty-sparepart')
     @endif
     <br>
     <footer>
         <div style="text-align: right; font-size: 11px;">
             &copy; {{ date('Y') }} Bengkelku
         </div>
     </footer>
 </body>

 </html>
