@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    @php
        $allMenus = (new \App\Helpers\Helpers())->getAllMenus() ?? [];
    @endphp
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="card px-0">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 text-start">
                            {{ __('Pages') }}
                        </div>
                        <div class="col-md-6 text-end">
                            <a type="button" target="_blank" class="btn btn-primary col mx-2" href="{{route('page.create')}}" style="width:20em">
                                Create Page
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-responsive table-striped table-bordered overflow-scroll">
                        <thead class="mt-2">
                            <tr>
                                <th scope="col">SL No.</th>
                                <th scope="col">Title</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                                <th scope="col">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>


@endsection
@php
    $route = route('page.index');
@endphp
@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable with AJAX
            //This consist of Pagination, Search, deactivate, activate, delate modal with delete button
            //For Edit redirect to New page is suggested instead of ajax
            //Continue on Admin Footer.blade.php (if required only)
            //Mostly controller in dex need modification for DB fields
            //Rest of function like activate de activate deled are mostly same
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ $route }}', // URL to fetch data
                    type: 'GET', // Request type
                    data: function(d) {
                        // You can customize any additional request parameters here
                        return $.extend({}, d, {
                            // Example: Add custom data
                            // 'customData': 'value'
                        });
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, // SL No.
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'menu',
                        name: 'menu'
                    },
                    {
                        data: 'contentTitle',
                        name: 'contentTitle'
                    },
                    {
                        data: 'updated_at.display',
                        name: 'updated_at'
                    }, // To show formatted date
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    }, // Render status
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, // Render action buttons,
                    {
                        data: 'delete',
                        name: 'delete',
                        orderable: false,
                        searchable: false
                    } // Render action buttons

                ],
                order: [
                    [2, 'desc']
                ], // Default ordering by 'updated_at' column
                search: true, // Enable search
                pageLength: 10, // Default number of rows per page
            });

            // Activate button click
            $(document).on('click', '.btnActivate', function() {
                formData = $(this).data('id');
                route = "{{ route('page.status', ['status' => 'enable', 'id' => '__ID__']) }}".replace(
                    '__ID__', formData);
                type = 'POST';
                ajaxForm(fromID = null, type, route, formData, table, request = 'button');
            });

            // Deactivate button click
            $(document).on('click', '.btnDeActivate', function() {
                formData = $(this).data('id');
                route = "{{ route('page.status', ['status' => 'disable', 'id' => '__ID__']) }}".replace(
                    '__ID__', formData);
                type = 'POST';
                ajaxForm(fromID = null, type, route, formData, table, request = 'button');
            });

            //DELETE MODAL AJAX
            $(document).on('click', '.btnDeleteModal', function() {
                formData = $(this).data('id');
                const modalButton = `<button style="width:100%" data-status="delete" data-id="${formData}" type="button" class="mx-2 col btn btn-danger btnDelete">
                    <i class="bi bi-archive-fill nav-icon"></i>
                </button>`;
                $('#btnDeleteModal .deletBtnDiv .row').html(modalButton);
            });

            //DELETE BUTTON
            $(document).on('click', '.btnDelete', function() {
                formData = $(this).data('id');
                route = "{{ route('page.delete', ['id' => '__ID__']) }}".replace('__ID__', formData);
                type = 'DELETE';
                ajaxForm(fromID = null, type, route, formData, table, request = 'button');
                $('#btnDeleteModal').modal('hide');
            });

        });
    </script>
@endpush
