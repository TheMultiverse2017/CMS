<style>
    .commonClass:hover {
        background-color: grey;
        cursor: crosshair !important;
    }
</style>
<div class="htmlContent row" id="{{ base64_encode(str_replace(['+', '/', '='], ['-', '_', ''], date('Y-m-d H:i:s'))) }}"
    style="height: 100vh; width: auto; border: 2px solid ;">
    <div class="htmlComponents col-md-3"
        id="{{ base64_encode(str_replace(['+', '/', '='], ['-', '_', ''], date('Y-m-d H:i:s'))) }}"
        style="height: 100%;border: 2px solid ;">
        <div class="accordion pt-3" id="htmlContent">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#Containers" aria-expanded="true" aria-controls="Containers">
                        Containers
                    </button>
                </h2>
                <div id="Containers" class="accordion-collapse collapse show" data-bs-parent="#htmlContent">
                    <div class="accordion-body">
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#Components" aria-expanded="false" aria-controls="Components">
                        Components
                    </button>
                </h2>
                <div id="Components" class="accordion-collapse collapse" data-bs-parent="#htmlContent">
                    <div class="accordion-body">
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="htmlContainer col-md-9 dropZone"
        id="{{ base64_encode(str_replace(['+', '/', '='], ['-', '_', ''], date('Y-m-d H:i:s'))) }}"
        style="height: 100%; border: 2px solid ;" contenteditable="true">
    </div>
</div>
</div>
<!-- jQuery (must be included first) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<script>
    var customClassCounter = 1; // Global counter to keep track of unique class names

    $(document).ready(function() {
        fileUploadOnClick();
        getContainers();
        // getComponents();
        // getClasses();


        // Enable dragging
        $(document).on("dragstart", ".draggable", function(event) {
            event.originalEvent.dataTransfer.setData("text", $(this).text());
        });

        // Allow drop
        $(".dropZone").on("dragover", function(event) {
            event.preventDefault();
        });

        // Handle drop event
        $(".dropZone").on("drop", function(event) {
            event.preventDefault();

            let droppedContent = event.originalEvent.dataTransfer.getData("text").replace(/\s+/g, '_');

            getValue(droppedContent).then(htmlContent => {
                if (htmlContent) {
                    $(event.target).append(htmlContent);
                    console.log("Content '" + htmlContent + "' dropped");
                }
            }).catch(error => console.error("Error fetching content:", error));
        });

    });
    const notyf = new Notyf();
    var type = 'GET';
    var route = '';
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });

    function getContainers() {
        route = "{{ route('page.get.containers') }}";
        $.ajax({
            type: type,
            url: route,
            // data: key,
            success: function(response) {
                message = response.message;
                if (response.success) {

                    let container = $("#Containers .accordion-body .row");
                    container.empty(); // Clear previous content

                    $.each(response.data, function(key, value) {
                        // Append each HTML snippet as a new div
                        container.append(`
                        <div class="col-12 mb-3">
                            <button class="btn btn-outline-secondary draggable" draggable="true" style="width:100%">` +
                            key + `</button>
                        </div>
                    `);
                    });
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

    function getComponents() {
        route = "{{ route('page.get.components') }}";
        $.ajax({
            type: type,
            url: route,
            // data: key,
            success: function(response) {
                message = response.message;
                if (response.success) {
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

    function getClasses() {
        route = "{{ route('page.get.classes') }}";
        $.ajax({
            type: type,
            url: route,
            // data: key,
            success: function(response) {
                message = response.message;
                if (response.success) {
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

    function getValue(key = null) {
        return new Promise((resolve, reject) => {
            let route = "{{ route('page.get.value') }}";
            $.ajax({
                type: "GET",
                url: route,
                data: {
                    key: key
                },
                success: function(response) {
                    if (response.success) {
                        key = response.key; // Currently not in use but may be usable in future

                        // Modify response.data to make it editable
                        let editableContent = $('<div>').html(response
                            .data); // Convert to jQuery object

                        // Find and replace [[[my-custom-class]]] with unique class names
                        editableContent.find('*').each(function() {
                            let element = $(this);
                            let classAttr = element.attr('class');
                            if (classAttr && classAttr.includes('[[[my-custom-class]]]')) {
                                let newClassName = `myCustomClass${customClassCounter++}`;
                                element.attr('class', classAttr.replace(
                                    /\[\[\[my-custom-class\]\]\]/g, newClassName));
                            }
                            element.attr('contenteditable',
                                'true'); // Make all elements editable
                        });

                        resolve(editableContent.html()); // Return modified HTML
                    } else {
                        notyf.success(response.message);
                        resolve(null);
                    }
                },
                error: function() {
                    notyf.error('Something went wrong, please try again');
                    reject('AJAX request failed');
                }
            });
        });
    }

    function fileUploadOnClick() {
        // Handle clicks on images, videos, and iframes
        $(document).on('click', '.adminUiImageClass, .adminUiVideoClass, .adminUiIframeClass', function() {
            let element = $(this); // Get clicked element
            let currentClass = element.attr('class').match(/myCustomClass\d+/); // Get unique class
            let elementType = element.prop("tagName").toLowerCase(); // Get element type

            if (elementType === "img") {
                // For images, prompt file upload
                let fileInput = $('<input type="file" accept="image/*">');
                fileInput.on('change', function(event) {
                    let file = event.target.files[0];
                    if (file) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            element.attr("src", e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                });
                fileInput.trigger('click');
            } else if (elementType === "video") {
                // For videos, prompt file upload
                let fileInput = $('<input type="file" accept="video/*">');
                fileInput.on('change', function(event) {
                    let file = event.target.files[0];
                    if (file) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            element.find("source").attr("src", e.target.result);
                            element[0].load(); // Reload video
                        };
                        reader.readAsDataURL(file);
                    }
                });
                fileInput.trigger('click');
            } else if (elementType === "iframe") {
                // For iframes, prompt for a new URL
                let newSrc = prompt("Enter new iframe URL:");
                if (newSrc) {
                    element.attr('src', newSrc);
                }
            }
        });
    }
</script>
