@php
    $templateSelect = (new \App\Helpers\Helpers())->addNewSection() ?? [];
@endphp
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
</script>
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>

{{-- BOOTSTART ICONS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<style>
    .sectionClass:hover {
        border: 2px solid red;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-2" id="btnAddNewSectionDiv">
            <select class="form-select" id="addNewSectionSelect" aria-label="Default select example">
                <option value="" selected>Open this select menu</option>
                @foreach ($templateSelect as $k => $v)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2" id="btnAddNewSectionBtnDiv">
            <button class="btn btn-primary" id="btnAddNewSectionBtn">Add</button>
        </div>
    </div>
    <div class="row my-5" id=htmlContent">
        @foreach ($getPageItems as $item)
            <section id="sectionId{{ $item->id }}" class="sectionClass my-3" data-id="{{ $item->id }}">
                <div class="row justify-content-end mt-5" id="sectionIdDeleteBtnDiv{{ $item->id }}"
                    data-deletebtndivid="{{ $item->id }}">
                    <button style="width: 50px;" class="btn btn-danger sectionClassDeleteBtn"
                        id="sectionIdDeleteBtn{{ $item->id }}" data-deletebtndid="{{ $item->id }}">
                        <i class="bi bi-x-square-fill"></i>
                    </button>
                </div>
                @php
                    $checkFiles = json_decode($item->content, true);
                @endphp
                @if (isset($checkFiles['files']))
                    @php
                        $templates = (new \App\Helpers\Helpers())->templates($item->elementType) ?? [];
                        $websitefiles = $item->websitefiles()->get();
                        $carouselItemsHtml = '';
                        $class_active = true;
                    @endphp
                    @foreach ($websitefiles as $file)
                        @php
                            $activeClass = $class_active ? 'active' : '';
                            $class_active = false;
                            $carouselItemsHtml .=
                                '
                        <div class="carousel-item ' .
                                $activeClass .
                                '">
                            <img src="' .
                                URL::asset($file->filesrc) .
                                '" class="d-block w-100" alt="">
                        </div>
                        ';
                            $finalCarouselHtml = str_replace('[[carousel_items]]', $carouselItemsHtml, $templates);
                        @endphp
                    @endforeach
                    {!! $finalCarouselHtml !!}
                @else
                    {!! $checkFiles['content'] !!}
                @endif
            </section>
        @endforeach
    </div>
</div>
{{-- Template Modal Start --}}
<!-- Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="templateModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form enctype="multipart/form-data">

                </form>
            </div>
        </div>
    </div>
</div>
{{-- Template Modal End --}}
<script>
    const notyf = new Notyf();
    var templateItem = '';
    var message = '';
    var templateModalFormContent = '';
    var htmlContent = '';
    var isValid = false;

    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('btnAddNewSectionBtn').addEventListener('click', function() {
            templateItem = $("#addNewSectionSelect").val();
            if (templateItem == '') {
                message = 'Please select element to add.';
                notyf.error(message);
            } else {
                getElementForModal(templateItem);
            }
        });
    });

    function getElementForModal(templateItem) {
        htmlContent = '';
        let modalElement = document.getElementById('templateModal');
        let modalElementTitle = document.getElementById('templateModalLabel');
        let modal = new bootstrap.Modal(modalElement);
        modal.show();
        modalElementTitle.textContent = 'Add ' + templateItem;
        let sortOrder = {{ $item->contentSortOrder ?? 0 }} + 1; // Increment the sort order
        let options = ''; // Variable to hold generated options

        for (let i = 1; i <= sortOrder; i++) {
            if (i === sortOrder) {
                options += `<option value="${i}" selected>${i}</option>`;
            } else {
                options += `<option value="${i}">${i}</option>`;
            }
        }
        $.ajax({
            type: "GET",
            url: "{{ route('page.getTemplate') }}",
            data: {
                type: templateItem
            },
            success: function(response) {
                // Clear the form first
                $('#templateModal form').empty();
                $('#templateModal form').append(`
                    <div class="row">
                        <div class="col-md-10" id="sectionTitleDiv">
                            <input class="form-control my-3" name="title" required type="text" placeholder="Title" id="title">
                        </div>
                        <div class="col-md-2" id="sectionSortOrderDiv">
                            <select class="form-select my-3" id="sectionSortOrder" name="sectionSortOrder" aria-label="Default select example">
                                <option value="">Select Sort Order</option>
                               ${options}
                            </select>
                        </div>
                    </div>
                `);
                // Loop through htmlContent array and append to form
                response.html.forEach(function(item, index) {
                    if (item.key === 'content') {
                        // initialize summernote on the textarea after appending to DOM
                        setTimeout(() => {
                            $('#templateModal textarea').summernote();
                        }, 300);
                    }
                    let fieldHtml = `
                    <div class="mb-3">
                        <label class="form-label">${item.key.charAt(0).toUpperCase() + item.key.slice(1)}</label>
                        ${item.value}
                    </div>`;
                    $('#templateModal form').append(fieldHtml);
                });
                $('#templateModal form').append(`
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-primary" id="modalSubmit">Add</button>
                            </div>
                        </div>
                    `);

            },
            error: function() {
                notyf.error('Something went wrong, please try again.');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Delegate the click event to the parent modal container
        document.querySelector('#templateModal').addEventListener('click', function(e) {
            // Check if the clicked element is the submit button
            if (e.target && e.target.id === 'modalSubmit') {
                e.preventDefault(); // Prevent the default button action (form submit)
                var token = $('meta[name="csrf-token"]').attr('content');
                elementType = templateItem;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });
                var menuId = '{{ $menuId }}';
                const form = document.querySelector('#templateModal form');
                const formData = new FormData(form); // Collect the form data
                // Append the menuId to FormData
                formData.append('menuId', menuId);
                formData.append('elementType', elementType);
                // Submit the form using AJAX
                $.ajax({
                    type: "post",
                    url: "{{ route('page.addPageElement') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        let message = response.message;
                        if (response.success) {
                            notyf.success(message);
                            form.reset(); // Optionally reset the form after submission
                            window.location.reload();
                        } else {
                            notyf.error(message);
                        }
                    },
                    error: function() {
                        notyf.error('Something went wrong, please try again.');
                    }
                });
            }
        });


        document.querySelectorAll('.sectionClassDeleteBtn').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submit or default behavior

                var token = $('meta[name="csrf-token"]').attr('content');
                var pageElementId = this.getAttribute('data-deletebtndid'); // Get pageElementId from clicked button

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('page.deletePageElement') }}",
                    data: {
                        pageElementId: pageElementId
                        // You can also send elementType if needed
                    },
                    success: function(response) {
                        let message = response.message;
                        if (response.success) {
                            notyf.success(message);
                            window.location.reload();
                        } else {
                            notyf.error(message);
                        }
                    },
                    error: function() {
                        notyf.error('Something went wrong, please try again.');
                    }
                });
            });
        });
    });


    // === SUMMERNOTE INIT ===
    function initSummernote(element) {
        $(element).summernote({
            placeholder: 'Contents',
            tabsize: 10,
            height: 300,
            focus: true,
            disableDragAndDrop: true,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
        });
    }
</script>
