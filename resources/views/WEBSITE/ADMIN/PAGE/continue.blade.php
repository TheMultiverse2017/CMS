@extends('layouts.WEBSITE.ADMIN.adminApp')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center py-5">
            <div class="col-md-12 " id="continuePageId">
                @include('layouts.WEBSITE.ADMIN.INCLUDES.cms')
                <div class="row d-flex justify-content-end fixed-bottom">
                    <div class="col-md-2 ">
                        <button type="submit" style="width:100% " id="savePage" class="btn btn-primary">Save</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('#new-file-btn').hide();
                $(".btn-close-white").trigger("click");
            }, 100); // Small delay to ensure the button is loaded
        });
    </script>
@endsection
