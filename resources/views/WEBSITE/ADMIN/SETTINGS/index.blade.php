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
                            {{ __('Settings') }}
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary col mx-2" data-bs-toggle="modal"
                                data-bs-target="#createSettings" style="width:20em">
                                Create Settings
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($settings as $setting)
                        <div class="row">
                            <div class="col-md-11">
                                <form method="POST" action="{{ route('settings.update', [$setting->id]) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('POST')
                                    <div class="row">
                                        <div class="row py-2">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input class="form-control" value="{{ $setting->name }}" name="name"
                                                        type="text" id="name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="value" class="form-label">Value</label>
                                                    <input class="form-control" value="{{ $setting->value }}" name="value"
                                                        type="text" id="value">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="value" class="form-label"></label>
                                                    <button type="submit" style="width: 100%"
                                                        class="btn btn-primary">Update</button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-1">
                                <div class="mb-3">
                                    <label for="value" class="form-label"></label>
                                    <button class="col mt-4  btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#settingsDelete{{ $setting->id }}">
                                        <i class="bi bi-trash3-fill nav-icon"></i>
                                    </button>
                                </div>
                                {{-- DELETE MODAL START --}}
                                <!-- Modal -->
                                <div class="modal fade" id="settingsDelete{{ $setting->id }}" tabindex="-1"
                                    aria-labelledby="deleteLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteLabel">Confirm Delete
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route('settings.delete', [$setting->id]) }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    @method('delete')
                                                    <div class="row">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- DELETE MODAL END --}}
                            </div>
                        </div>
                        @empty
                        <div>
                            No Settings Available
                        </div>
                    @endforelse


                </div>
            </div>

        </div>
    </div>

    <!-- Modal Start-->
    <div class="modal fade" id="createSettings" tabindex="-1" aria-labelledby="createLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLabel">Create Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('settings.save') }}" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="row py-2">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input class="form-control" name="name" type="text" id="name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="value" class="form-label">Value</label>
                                        <input class="form-control" name="value" type="text" id="value">
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
