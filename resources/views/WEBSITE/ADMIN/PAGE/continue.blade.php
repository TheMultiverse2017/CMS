@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center py-5">
            <div class="col-md-12 ">
                <form method="POST" action="{{ route('page.save') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <input hidden multiple name="menu" type="text" value="{{ $menu }}" id="menu">
                    <input hidden multiple name="title" type="text" value="{{ $pageTitle }}" id="title">
                    <textarea hidden multiple name="metaDesc" id="metaDesc">{!! $metaDesc !!}</textarea>
                    <textarea hidden multiple name="metaTags" id="metaTags">{!! $metaTags !!}</textarea>

                    @include('layouts.WEBSITE.ADMIN.INCLUDES.cms')

                    <div class="py-3">
                        <button type="submit" style="width:100% " class="btn btn-primary">Save</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
    <script>
        $(document).on("click", ".btn-close-white", function() {
            console.log("Close button clicked!");
        });

        // Automatically trigger click when the page loads
        $(document).ready(function() {
            setTimeout(function() {
                $(".btn-close-white").trigger("click");
            }, 100); // Small delay to ensure the button is loaded
        });
    </script>
@endsection
