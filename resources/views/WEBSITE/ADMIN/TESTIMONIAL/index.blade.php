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
                            {{ __('Testimonial') }}
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary col mx-2" data-bs-toggle="modal"
                                data-bs-target="#createTestimonial" style="width:20em">
                                Create Testimonial
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-responsive table-striped table-bordered overflow-scroll">
                        <thead class="mt-2">
                            <tr>
                                <th scope="col">SL No.</th>
                                <th scope="col">Name</th>
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

    <!-- Modal Start-->
    <div class="modal fade" id="createTestimonial" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLabel">Create Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('testimonial.save') }}" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="row py-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="formFile" class="form-label">Images</label>
                                        <input class="form-control" name="file[]" type="file" id="formFile">
                                    </div>
                                </div>
                            </div>
                            <div class="row py-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input class="form-control" multiple name="name" type="text" id="name">
                                    </div>
                                </div>
                            </div>
                            <div class="row py-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="testimonial" class="form-label">Testimonial</label>
                                        <textarea class="form-control textarea" multiple name="testimonial" id="testimonial"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="py-3">
                                <button type="submit" style="width: 100%" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End-->
@endsection
@php
    $route = route('testimonial.index');
@endphp
@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable with AJAX
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
                        data: 'name',
                        name: 'name'
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
                route = "{{ route('testimonial.status', ['status' => 'enable', 'id' => '__ID__']) }}"
                    .replace(
                        '__ID__', formData);
                type = 'POST';
                ajaxForm(fromID = null, type, route, formData, table, request = 'button');
            });

            // Deactivate button click
            $(document).on('click', '.btnDeActivate', function() {
                formData = $(this).data('id');
                route = "{{ route('testimonial.status', ['status' => 'disable', 'id' => '__ID__']) }}"
                    .replace(
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
                route = "{{ route('testimonial.delete', ['id' => '__ID__']) }}".replace('__ID__', formData);
                type = 'DELETE';
                ajaxForm(fromID = null, type, route, formData, table, request = 'button');
                $('#btnDeleteModal').modal('hide');
            });


            //EDIT MODAL AJAX
            $(document).on('click', '.btnEditModal', function() {
                formData = $(this).data('id');
                route = "{{ route('testimonial.edit', ['id' => '__ID__']) }}".replace('__ID__', formData);
                type = 'GET';
                modal = true;
                editModalData = `
                <form id="editForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">Images</label>
                                    <div id="files" class="row justify-content-between"  align="center">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">Images</label>
                                    <input class="form-control" multiple name="file[]" type="file" id="formFile">
                                </div>
                            </div>
                        </div>

                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control" multiple name="name" type="text" id="name">
                                </div>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="testimonial" class="form-label">Testimonial</label>
                                    <textarea class="form-control textarea" multiple name="testimonial" id="testimonial"></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="updateBtn" class="btn btn-primary" data-id="${formData}">Save changes</button>
                    </div>
                </form>`;
                ajaxForm(fromID = '#btnEditModal #editFormDiv #editForm', type, route, formData, table =
                    null, request = 'modal', requestFrom = 'TestimonialEditModal');

                $('#btnEditModal #editFormDiv').html(editModalData);
            });

            //DELETE MODAL AJAX
            $(document).on('click', '.btnDeleteFile', function() {
                formData = $(this).data('id');
                route = "{{ route('delete.file', ['id' => '__ID__']) }}".replace('__ID__', formData);
                type = 'DELETE';
                ajaxForm(fromID = '#btnEditModal #editFormDiv #editForm', type, route, formData, table,
                    request = 'deletefile');
            });

            $(document).on('click', '#updateBtn', function() {
                const notyf = new Notyf();
                const token = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                const id = $(this).data('id'); // Get the data-id from the button
                const route = "{{ route('testimonial.update', ['id' => '__ID__']) }}".replace('__ID__', id);
                const type = 'POST';

                // Reference the form element
                const form = document.querySelector('#editForm');
                const formData = new FormData(form); // Collect form data using FormData

                // Make an AJAX request
                $.ajax({
                    type: type,
                    url: route,
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Let FormData handle the content type (including files)
                    success: function(response) {
                        // Handle success response
                        const message = response.message;
                        if (response.success) {
                            notyf.success(message); // Show success notification
                            $('#btnEditModal').modal('hide'); // Close the modal
                            $('#datatable').DataTable().clear().draw();
                            $('#datatable').DataTable().ajax.reload(null, false);
                        } else {
                            notyf.error(message || 'Update completed successfully.');
                        }
                    },
                    error: function(xhr) {
                        // Handle validation errors or generic errors
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            Object.values(errors).forEach(err => notyf.error(
                                err)); // Display errors
                        } else {
                            notyf.error('Something went wrong. Please try again.');
                        }
                    }
                });
            });

        });
    </script>
@endpush
