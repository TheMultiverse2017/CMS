@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    @php
        use App\Enums\WebsiteFilesBelongsTo;
    @endphp
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-7">
                <form method="POST" action="{{ route('post.update', [$post->id]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input class="form-control" multiple name="title" value="{{ $post->title ?? null }}"
                                        type="text" id="title">
                                </div>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input class="form-control" multiple name="meta_title"
                                        value="{{ $post->meta_title ?? null }}" type="text" id="meta_title">
                                </div>
                            </div>
                        </div>

                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="meta_tags" class="form-label">Meta Tags</label>
                                    <input class="form-control" multiple name="meta_tags"
                                        value="{{ $post->meta_tags ?? null }}" type="text" id="meta_tags">
                                </div>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="meta_desc" class="form-label">Meta Desc</label>
                                    <textarea class="form-control " multiple name="meta_desc" id="meta_desc">{{ $post->meta_desc ?? null }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="intro" class="form-label">Intro</label>
                                    <textarea class="form-control textarea" multiple name="intro" id="intro">{!! $post->intro ?? null !!}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea class="form-control textarea" multiple name="content" id="content">{!! $post->content ?? null !!}</textarea>
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
                        <div class="py-3">
                            <button type="submit" style="width: 100%" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-5">
                @php
                    $files = $post->websitefiles()->where('belongsTo', WebsiteFilesBelongsTo::POST->value)->get() ?? [];
                @endphp
                <div class="row justify-content-center ">
                    @forelse ($files as $file)
                        <div class="col-md-5 m-3">
                            <img class="img-fluid img-thumbnail " style="height: 150px;width: 300px"
                                src="{{ URL::asset($file->filesrc ?? null) ?? null }}" alt="">
                            <form action="{{ route('delete.file', [$file->id]) }}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button type="submit" style="width: 100%" class="btn btn-danger">
                                    <i class="bi bi-archive-fill nav-icon"></i>
                                </button>
                            </form>

                        </div>
                    @empty
                    @endforelse

                </div>

            </div>
        </div>
    </div>
@endsection
