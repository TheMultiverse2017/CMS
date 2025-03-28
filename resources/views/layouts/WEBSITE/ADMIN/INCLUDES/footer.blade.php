<!-- jQuery (must be included first) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.css" rel="stylesheet">
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.js"></script>

{{-- SLICK CAROUSEL --}}
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

{{-- BOOTSTART ICONS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        // getMenus();
        $('.textarea, #textarea').summernote({
            placeholder: 'Contents',
            tabsize: 10,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        // Function to set a cookie
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000); // Cookie expiration in days
            document.cookie = `${name}=${value}; expires=${date.toUTCString()}; path=/`;
        }

        // Function to get a cookie
        function getCookie(name) {
            const cookies = document.cookie.split("; ");
            for (let cookie of cookies) {
                const [key, value] = cookie.split("=");
                if (key === name) {
                    return value;
                }
            }
            return null;
        }

        // Load the theme from the cookie or default to 'light'
        let currentTheme = getCookie("theme") || "light";
        let newTheme = currentTheme === "light" ? "dark" : "light";

        // Apply the theme on page load
        document.documentElement.setAttribute("data-bs-theme", currentTheme);

        // Update the icon on page load
        const themeToggleBtn = document.getElementById("theme-toggle");
        const icon = themeToggleBtn.querySelector("i");
        if (currentTheme === "light") {
            icon.classList.add("bi-moon-stars-fill"); // Moon icon for dark mode
            icon.classList.remove("bi-sun-fill");
        } else {
            icon.classList.add("bi-sun-fill"); // Sun icon for light mode
            icon.classList.remove("bi-moon-stars-fill");
        }

        // Add click event listener to the theme toggle button
        themeToggleBtn.addEventListener("click", function() {
            // Toggle the theme
            newTheme = currentTheme === "light" ? "dark" : "light";
            currentTheme = newTheme;

            // Apply the new theme
            document.documentElement.setAttribute("data-bs-theme", newTheme);

            // Save the theme to a cookie
            setCookie("theme", newTheme, 30); // Save for 30 days

            // Update the icon
            if (newTheme === "light") {
                icon.classList.remove("bi-sun-fill");
                icon.classList.add("bi-moon-stars-fill"); // Moon icon for dark mode
            } else {
                icon.classList.remove("bi-moon-stars-fill");
                icon.classList.add("bi-sun-fill"); // Sun icon for light mode
            }
        });
    })

    //fromID is needed when form submit only
    function ajaxForm(fromID = null, type, route, formData, table = null, request = null, requestFrom = null) {

        const notyf = new Notyf();
        var token = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token
            }
        });
        //form submit, non form action, load data , redirect

        //fromID is only form form submit
        var message = '';
        // notyf.success('Validation Error');
        // notyf.error('Something went Wrong, Please try again');

        if (request == 'button') {
            $.ajax({
                type: type,
                url: route,
                data: formData,
                success: function(response) {
                    message = response.message;
                    if (response.success) {
                        $('#datatable').DataTable().clear().draw();
                        $('#datatable').DataTable().ajax.reload(null, false);
                        notyf.success(message);
                    } else {
                        notyf.success(message);
                    }
                },
                error: function(xhr, status, error) {
                    notyf.error('Something went Wrong, Please try again');
                }
            });
        } else if (request == 'modal') {
            $.ajax({
                type: type,
                url: route,
                data: formData,
                success: function(response) {
                    if (response.data) {
                        const data = response.data;
                        const files = response.files;
                        const thumbnail = response.thumbnail ?? null;
                        if (requestFrom == 'bannerEditModal') {
                            // Render response data to the form
                            $(`${fromID} #menu`).val(data.menu ?? '');
                            $(`${fromID} #title`).val(data.title ?? '');
                            if (files) {
                                fileListContainer = $(`${fromID} #files`);
                                // Clear any existing content in the file list container
                                fileListContainer.empty();
                                loadFiles(fileListContainer, files);
                            }
                        } else if (requestFrom == 'navigationEditModal') {

                            // Render response data to the form
                            document.querySelector(`${fromID} .menu`).style.display = 'block'; // Show #menu
                            document.querySelector(`${fromID} #menudropdown`).style.display =
                                'block'; // Show #menudropdown
                            document.querySelector(`${fromID} .submenu`).style.display =
                                'block'; // Show #submenu

                            var mainMenu = data.mainMenu;

                            if (mainMenu) {
                                document.querySelector(`${fromID} .menu`).style.display =
                                    'block'; // Show #menu
                                document.querySelector(`${fromID} #menudropdown`).style.display =
                                    'none'; // Hide #menudropdown
                                document.querySelector(`${fromID} .submenu`).style.display =
                                    'none'; // Hide #submenu
                                $(`${fromID} #menu`).val(data.navigationMenus.menu ?? '');
                                $(`${fromID} #footer`).prop('checked', data.navigationMenus.footer ??
                                    false);
                            } else {
                                document.querySelector(`${fromID} .menu`).style.display =
                                    'none'; // Hide #menu
                                document.querySelector(`${fromID} #menudropdown`).style.display =
                                    'block'; // Show #menudropdown
                                document.querySelector(`${fromID} .submenu`).style.display =
                                    'block'; // Show #submenu
                                $(`${fromID} #menudropdown`).val(data.navigationMenus.id ?? '');
                                $(`${fromID} #menudropdown`).find(
                                    `option[value="${data.navigationMenus.id}"]`).text(data
                                    .navigationMenus.menu ?? '');
                                $(`${fromID} #submenu`).val(data.navigationSUbMenus.menu ?? '');
                                $(`${fromID} #footer`).prop('checked', data.navigationSUbMenus.footer ??
                                    false);
                            }

                            // if (thumbnail) {
                            //     fileListContainer = $(`${fromID} #thumbnailFiles`);
                            //     // Clear any existing content in the file list container
                            //     fileListContainer.empty();
                            //     loadFiles(fileListContainer, thumbnail);
                            // }
                            // if (files) {
                            //     fileListContainer = $(`${fromID} #files`);
                            //     // Clear any existing content in the file list container
                            //     fileListContainer.empty();
                            //     loadFiles(fileListContainer, files);
                            // }
                        } else if (requestFrom == 'SEOEditModal') {
                            // Render response data to the form
                            $(`${fromID} #menu`).val(data.menu ?? '');
                            // $(`${fromID} #metaTitle`).val(data.metaTitle ?? '');
                            $(`${fromID} #metaTags`).val(data.metaTags ?? '');
                            $(`${fromID} #metaDesc`).val(data.metaDesc ?? '');
                            // initializeSummernote('textarea');
                        } else if (requestFrom == 'TestimonialEditModal') {
                            // Render response data to the form
                            $(`${fromID} #name`).val(data.name ?? '');
                            $(`${fromID} #testimonial`).val(data.testimonial ?? '');
                            if (files) {
                                fileListContainer = $(`${fromID} #files`);
                                // Clear any existing content in the file list container
                                fileListContainer.empty();
                                loadFiles(fileListContainer, files);
                            }
                            initializeSummernote('textarea');
                        } else if (requestFrom == 'galleryEditModal') {
                            // Render response data to the form
                            $(`${fromID} #title`).val(data.title ?? '');
                            if (thumbnail) {
                                fileListContainer = $(`${fromID} #thumbnailFiles`);
                                // Clear any existing content in the file list container
                                fileListContainer.empty();
                                loadFiles(fileListContainer, thumbnail);
                            }
                            if (files) {
                                fileListContainer = $(`${fromID} #files`);
                                // Clear any existing content in the file list container
                                fileListContainer.empty();
                                loadFiles(fileListContainer, files);
                            }
                        }
                    }

                },
                error: function(xhr, status, error) {
                    notyf.error('Something went Wrong, Please try again');
                }
            });
        } else if (request == 'deletefile') {
            $.ajax({
                type: type,
                url: route,
                data: formData,
                success: function(response) {
                    message = response.message;
                    if (response.success) {
                        const files = response.files;
                        var fileListContainer = null;
                        var requestContainer = response.requestContainer ?? null
                        if (files) {
                            if (requestContainer == 'files') {
                                fileListContainer = $(`${fromID} #files`);
                                // Clear any existing content in the file list container
                                fileListContainer.empty();
                                loadFiles(fileListContainer, files);

                            }
                            if (requestContainer == 'thumbnailFiles') {
                                thumbnailFilesContainer = $(`${fromID} #thumbnailFiles`);
                                // Clear any existing content in the file list container
                                thumbnailFilesContainer.empty();
                                loadFiles(thumbnailFilesContainer, files);
                            }

                        }
                        notyf.success(message);
                    } else {
                        notyf.success(message);
                    }
                },
                error: function(xhr, status, error) {
                    notyf.error('Something went Wrong, Please try again');
                }
            });
        }
    }

    function loadFiles(fileListContainer, files) {
        if (!Array.isArray(files)) {
            const baseUrl = "{{ url('/') }}"; // Pass Laravel's base URL
            const fullPath = `${baseUrl}/${files.filesrc}`;
            fileListContainer.append(`<div class="col-auto m-2" ><img src="${fullPath}" class="img img-thumbnail" style="height:150px;width:150px" alt="File Image"/>
    <br/>
    <button style="width:100%" data-status="deleteFile" data-id="${files.id}" type="button" class="col btn btn-danger btnDeleteFile">Remove</button>
    </div>`);
        } else {
            const baseUrl = "{{ url('/') }}";
            files.forEach(file => {
                const fullPath = `${baseUrl}/${file.filesrc}`;
                fileListContainer.append(`<div class="col-auto m-2"><img src="${fullPath}" class="img img-thumbnail col" style="height:150px;width:150px" alt="File Image"/>
    <br/>
    <button style="width:100%" data-status="deleteFile" data-id="${file.id}" type="button" class="col btn btn-danger btnDeleteFile">Remove</button>
    </div>`)
            });
        }

    }

    function initializeSummernote(selector) {
        $('.textarea, #textarea').each(function() {
            try {
                $(this).summernote('destroy');
            } catch (e) {
                // Ignore errors if summernote was not initialized
            }
        });
        $(selector).summernote({
            placeholder: 'Contents',
            tabsize: 10,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]

        });
    }

    function dateRange(daterange = null) {
        if (daterange != null) {
            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#' + daterange + ' span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#' + daterange).daterangepicker({
                startDate: start,
                endDate: end,
                maxDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ]
                }
            }, cb);

            cb(start, end);
        }
    }

    function getMenus() {
        $.ajax({
            url: "{{ route('get.menus') }}", // Laravel route for fetching menus
            method: 'GET',
            success: function(response) {
                if (response && Array.isArray(response)) {
                    let menuDropdown = $('#menudropdown');

                    // Clear existing dynamic options, keeping the default one
                    menuDropdown.find("option:not(:first)").remove();

                    response.forEach(menu => {
                        // Append new options
                        menuDropdown.append(
                            `<option value="${menu.id}">${menu.menu}</option>`
                        );
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching menus:', error);
            }
        });
    }
</script>
{{-- EDIT MODAL START --}}
<!-- Modal -->
<div class="modal fade" id="btnEditModal" tabindex="-1" aria-labelledby="createContent" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editlabel">Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editFormDiv">
            </div>
        </div>
    </div>
</div>
{{-- EDIT MODAL END --}}
{{-- DELETE MODAL START --}}
<!-- Modal -->
<div class="modal fade" id="btnDeleteModal" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="deletBtnDiv">
                    <div class="row ">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- DELETE MODAL END --}}
