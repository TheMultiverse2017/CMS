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
    $(document).ready(function() {
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

            // Get the dropped content
            let droppedContent = event.originalEvent.dataTransfer.getData("text").replace(/\s+/g, '_');
            let htmlContent = getValue(droppedContent);
            // Append the dropped content into the dropZone
            $(this).append(`
            ${htmlContent}
        `);

            // Show an alert with the dropped content
            alert("Content '" + droppedContent + "' dropped");
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
        route = "{{ route('page.get.value') }}";
        $.ajax({
            type: type,
            url: route,
            data: { key: key },
            success: function(response) {
                message = response.message;
                if (response.success) {

                    // let container = $("#Containers .accordion-body .row");
                    // container.empty(); // Clear previous content

                    // $.each(response.data, function(key, value) {
                    //     // Append each HTML snippet as a new div
                    //     container.append(`
                    //     <div class="col-12 mb-3">
                    //         <button class="btn btn-outline-secondary draggable" draggable="true" style="width:100%">` + key + `</button>
                    //     </div>
                    // `);
                    // });
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
</script>
