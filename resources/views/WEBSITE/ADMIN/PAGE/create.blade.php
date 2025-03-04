@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    @php
        $allMenus = (new \App\Helpers\Helpers())->getAllMenus() ?? [];
        use App\Enums\WebsiteFilesBelongsTo;
        $dateTimeToString = base64_encode(date('Y-m-d H:i:s', round(microtime(true) * 1000) / 1000));
    @endphp
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-12 ">
                {{-- <form method="POST" action="{{ route('page.save') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST') --}}
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Basics
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12 py-2">
                                            <div class="mb-3">
                                                <label for="menu" class="form-label">Menu</label>
                                                <select required class="form-select " aria-label="Default select example"
                                                    name="menu" id="menu">
                                                    <option selected value="">Open this select menu</option>
                                                    @if (!empty($allMenus))
                                                        @forelse ($allMenus as $menu)
                                                            <option value="{{ $menu->id ?? null }}">
                                                                {{ $menu->menu ?? null }}
                                                            </option>

                                                        @empty
                                                        @endforelse
                                                    @endif
                                                </select>
                                                @error('menu')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row py-2">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Title</label>
                                                    <input class="form-control" multiple name="title" type="text"
                                                        id="title">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="row py-2">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">META DESCRIPTION</label>
                                                    <textarea class="form-control" multiple name="metaDesc" id="metaDesc" cols="30" rows=""></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row py-2">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">META TAGS</label>
                                                    <textarea class="form-control" multiple name="mentTags"id="mentTags" cols="30" rows=""></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Content
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="col-md-12 ">
                                    <div class="row py-2">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Content</label>
                                                {{-- <textarea class="form-control" name="content" id="content" cols="100" rows="20"> --}}
                                                @include('layouts.WEBSITE.ADMIN.INCLUDES.cms')
                                                {{-- </textarea> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-3">
                    <button type="submit" style="width:100% " class="btn btn-primary">Save</button>
                </div>
            </div>
            {{-- </form> --}}
        </div>
    </div>
    </div>
@endsection
