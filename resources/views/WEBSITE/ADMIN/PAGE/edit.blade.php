@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    @php
        $allMenus = (new \App\Helpers\Helpers())->getAllMenus() ?? [];
        use App\Enums\WebsiteFilesBelongsTo;
    @endphp
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-12 ">
            </div>
        </div>
    </div>
@endsection
