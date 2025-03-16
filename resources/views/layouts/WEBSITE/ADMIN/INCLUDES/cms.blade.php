@php
    $allFonts = (new \App\Helpers\Helpers())->getFonts() ?? [];
@endphp
<style>
    .commonClass:hover {
        background-color: grey;
        cursor: crosshair !important;
    }
</style>
<div class="htmlContent row" id="{{ base64_encode(str_replace(['+', '/', '='], ['-', '_', ''], date('Y-m-d H:i:s'))) }}"
    style="height: 100vh; overflow-y: scroll; width: auto; border: 2px solid ;">
    <div class="htmlComponents col-md-2"
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
    <div class="htmlContainer col-md-8 dropZone"
        id="{{ base64_encode(str_replace(['+', '/', '='], ['-', '_', ''], date('Y-m-d H:i:s'))) }}"
        style="height: 100%; border: 2px solid ;" contenteditable="true">
    </div>
    <div class="htmlComponentStyle col-md-2" style="height: 100vh; overflow-y: scroll">

    </div>
</div>
</div>
<!-- jQuery (must be included first) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


<script>
    var customClassCounter = 1; // Global counter to keep track of unique class names
    var dataElement = '';
    var dataElementComponent = '';
    let fonts = @json($allFonts); // Convert PHP array to JavaScript array
    $(document).ready(function() {
        fileUploadOnClick();
        getContainers();
        // getComponents();
        // getClasses();
        // Call this function once when initializing to attach the

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

    function getComponentStyle(key = null) {
        let route = "{{ route('page.get.componentStyle') }}";

        // Clear container before making AJAX call
        let container = document.querySelector(".htmlComponentStyle");
        container.innerHTML = "";

        $.ajax({
            type: "GET",
            url: route,
            data: {
                key: key
            },
            success: function(response) {
                let message = response.message;
                if (response.success) {
                    console.log(response.data);
                    renderComponentStyle(response.data, key, fonts);
                    notyf.success(message);
                } else {
                    notyf.error(message);
                }
            },
            error: function(xhr, status, error) {
                notyf.error("Something went wrong, please try again");
            }
        });
    }

    function renderComponentStyle(properties, key, fonts = []) {
        dataElement = dataElementComponent = '';
        let container = document.querySelector(".htmlComponentStyle");
        container.innerHTML = ""; // Clear previous content

        let element = document.querySelector(".htmlContainer")?.querySelector(`[data-element="${dataElement}"]`) ||
            document.querySelector(".htmlContainer .selected");

        if (!dataElement) {
            dataElement = element?.getAttribute("data-element") || null;
        }

        if (!dataElementComponent) {
            dataElementComponent = element?.getAttribute("data-element-component") || null;
        }

        properties.forEach(property => {
            const formGroup = document.createElement("div");
            formGroup.classList.add("form-group", "my-2", "htmlStyleInputDiv");

            const label = document.createElement("label");
            label.textContent = property.replace(/-/g, " ").replace(/\b\w/g, c => c.toUpperCase());
            label.setAttribute("for", property);

            let input;

            // Handle font-family as a select box
            if (property === "font-family") {
                input = document.createElement("select");
                input.setAttribute("name", property);
                input.setAttribute("id", property);
                input.classList.add("form-control", "htmlStyleInput");

                // Populate with fonts
                fonts.forEach(font => {
                    let option = document.createElement("option");
                    option.value = font;
                    option.textContent = font;
                    option.style.fontFamily = font; // Preview the font
                    input.appendChild(option);
                });

            } else if (property === "src" && (key === "IMAGE" || key === "VIDEO")) {
                input = document.createElement("input");
                input.setAttribute("type", "file");
                input.setAttribute("accept", key === "IMAGE" ? "image/*" : "video/*");
                input.setAttribute("name", property);
                input.setAttribute("id", property);
                input.classList.add("form-control", "htmlStyleInput");

                input.addEventListener("change", function(event) {
                    const file = event.target.files[0];
                    let selectedElement = document.querySelector(".htmlContainer .selected");

                    if (file && selectedElement) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            selectedElement.setAttribute("src", e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                input = document.createElement("input");
                input.setAttribute("type", getInputType(property));
                input.setAttribute("name", property);
                input.setAttribute("id", property);
                input.classList.add("form-control", "htmlStyleInput");
            }

            formGroup.appendChild(label);
            formGroup.appendChild(input);
            container.appendChild(formGroup);
        });

        // Create and append delete button
        if (element) {
            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.classList.add("btn", "btn-danger", "deleteBtn", "mt-3", "htmlStyleInput");

            // Style the button (optional)
            Object.assign(deleteButton.style, {
                width: "100%",
                padding: "10px",
                fontSize: "16px",
                cursor: "pointer"
            });
            container.appendChild(deleteButton);
        }
    }

    function getInputType(property) {
        if (property.includes("color") || property === "background") return "color";
        if (property.includes("width") || property.includes("height") || property.includes("size") || property.includes(
                "radius") || property.includes("gap")) return "number";
        return "text";
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
                        // Remove 'selected' class from all other elements
                        $('.htmlContainer *').removeClass('selected');
                        // Find and replace [[[my-custom-class]]] with unique class names
                        editableContent.find('*').each(function() {
                            let element = $(this);
                            let classAttr = element.attr('class');
                            if (classAttr && classAttr.includes('[[[my-custom-class]]]')) {
                                let newClassName =
                                    `myCustomClass${customClassCounter++} selected`;
                                element.attr('class', classAttr.replace(
                                    /\[\[\[my-custom-class\]\]\]/g, newClassName));
                                // Add data-element attribute with key and counter
                                element.attr('data-element',
                                    `${key}-${customClassCounter-1}`);
                                element.attr('data-element-component',
                                    `${key}`);
                            }
                            element.attr('contenteditable',
                                'true'); // Make all elements editable

                            // Apply width: auto; for IMAGE, VIDEO, or IFRAME elements
                            if (["IMAGE", "VIDEO", "IFRAME"].includes(key)) {
                                element.css("width", "100%");
                            }
                        });

                        resolve(editableContent.html()); // Return modified HTML
                        getComponentStyle(key);

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
        // implement for right click :contextmenu
        //fo double click : dblclick
        $(document).on('dblclick', '.adminUiImageClass, .adminUiVideoClass, .adminUiIframeClass', function() {
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
    $(document).on('click', '.htmlContainer *', function(event) {
        event.stopPropagation(); // Prevent event bubbling

        // Remove 'selected' class from all elements before adding it to the clicked one
        $('.htmlContainer *').removeClass('selected');
        $(this).addClass('selected');
        // Get data-element-component as key
        let key = $(this).attr("data-element-component");

        if (key) {
            getComponentStyle(key);
        }
    });

    // CSS to highlight the selected element
    const style = document.createElement('style');
    style.innerHTML = `
    .selected {
        outline: 2px solid red !important; /* Highlight selected element */
        box-shadow: 0 0 5px red !important; /* Optional glow effect */
    }
`;
    document.head.appendChild(style);

    $(document).on("click", ".deleteBtn", function(event) {
        event.preventDefault(); // Prevent any default action (if inside a form)

        let selectedElement = document.querySelector(".htmlContainer .selected");

        if (selectedElement) {
            selectedElement.remove(); // Remove the selected element from DOM
            let container = document.querySelector(".htmlComponentStyle");
            container.innerHTML = ""; // Clear previous content
        }
    });

    document.addEventListener("input", function(event) {
        if (event.target.classList.contains("htmlStyleInput")) {
            let selectedElement = document.querySelector(".htmlContainer .selected");

            if (selectedElement) {
                let propertyName = event.target.getAttribute("name"); // Get the property name (e.g., width)
                let propertyValue = event.target.value.trim(); // Get the value

                if (propertyValue) {
                    selectedElement.style[propertyName] = propertyValue + (isNaN(propertyValue) ? "" : "px");
                } else {
                    selectedElement.style.removeProperty(propertyName); // Remove property if empty
                }
            }
        }
    });
</script>
