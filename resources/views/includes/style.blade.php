<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.3.2/dist/css/tabler.min.css" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.1/datatables.min.css" rel="stylesheet"
    integrity="sha384-rOq+bSqJVRwotGgW1XZO3EINtS8WzFI0Irekl0IRWM4qvgF+bIgfIaaGfzrOvkZL" crossorigin="anonymous">

<style>
    :root {
        --tblr-primary: #00a63e;
        --tblr-font-sans-serif: "Inter";
    }

    .dropdown-item.active,
    .dropdown-item:active {
        background-color: #00a63e !important;
        color: white !important;
    }

    .form-control:focus {
        border-color: var(--tblr-primary);
        box-shadow: none;
    }

    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: left;
    }
</style>
<style>
        /* Ensure consistent margin for both */
        .dataTables_filter,
         {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
            /* spacing between label and input */
            margin-top: 1rem;
            width: 100%;
            
        }

        /* Labels styling */
        .dataTables_filter label,
        .dataTables_length label {
            display: flex;
            align-items: center;
            color: #28a745;
            font-weight: 600;
            margin: 0;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        /* Make search input full width */
        .dataTables_filter input {
            width: 100%;
            max-width: 400px;
            /* or 100% if you want it full */
            padding: 0.5rem 0.75rem;
            border: 1px solid #28a745;
            border-radius: 8px;
            background-color: #ffffff;
            color: #155724;
            font-size: 1rem; 
        }

        /* Only style the select input, keep default width */
        .dataTables_length select {
            border: 1px solid #28a745;
            background-color: #ffffff;
            color: #155724;
            border-radius: 0.375rem;
            padding: 0.375rem 0.5rem;
            margin-left: 0.5rem;
        }

        /* Responsive adjustment */
        @media (min-width: 768px) {
            .dataTables_filter {
                float: right;
                text-align: right;
            }

            .dataTables_length {
                float: left;
            }
        }
    </style>
 