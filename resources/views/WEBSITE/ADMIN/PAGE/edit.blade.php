@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    @php
        $allMenus = (new \App\Helpers\Helpers())->getAllMenus() ?? [];
        use App\Enums\WebsiteFilesBelongsTo;
    @endphp
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-12 ">
                <form method="POST" action="{{ route('page.save') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-12 py-2">
                                <div class="mb-3">
                                    <label for="menu" class="form-label">Menu</label>
                                    <select required class="form-select " aria-label="Default select example" name="menu"
                                        id="menu">
                                        <option selected value="">Open this select menu</option>
                                        @if (!empty($allMenus))
                                            @forelse ($allMenus as $menu)
                                                <option value="{{ $menu->id ?? null }}"
                                                    @if ($menu->id == $page->menu) selected @endif>
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
                                        <input class="form-control" multiple name="title" value="{{$page->title ?? null}}" type="text" id="title">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="row py-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">META DESCRIPTION</label>
                                        <textarea class="form-control" multiple name="metaDesc" id="metaDesc" cols="30" rows="">{!! $page->metaDesc!!}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row py-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">META TAGS</label>
                                        <textarea class="form-control" multiple name="mentTags"id="mentTags" cols="30" rows="">{!! $page->metaTags!!}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 ">
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Content</label>
                                    <textarea class="form-control" name="content" id="content" cols="100" rows="20">{!! $page->content!!}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="py-3">
                            <button type="submit" style="width:100% " class="btn btn-primary">Save</button>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
@endsection
