<style>
    #sectionTextarea {
        width: 100%;
        height: auto;
    }

    .is-invalid {
        border: 2px solid red !important;
    }

    .note-editor.is-invalid {
        border: 2px solid red !important;
        border-radius: 5px;
    }

    .note-modal {
        z-index: 1055 !important;
        /* Or higher depending on your modal stack */
    }

    .modal-backdrop {
        z-index: 1050 !important;
    }
</style>

<div class="container">
    <div id="sectionMainDiv">
        <div class="row">
            <div class="col-md-10" id="sectionTitleDiv">
                <input class="form-control my-3" name="sectionTitle[]" type="text" placeholder="Section Title"
                    id="sectionTitle">
            </div>
            <div class="col-md-2" id="sectionSortOrderDiv">
                <select class="form-select my-3" id="sectionSortOrder" name="sectionSortOrder[]"
                    aria-label="Default select example">
                    <option value="">Select Sort Order</option>
                    <option value="1" selected>1</option>
                </select>
            </div>
            <div class="col-md-12" id="sectionTextareaDiv">
                <textarea class="form-control my-3" name="section[]" id="sectionTextarea" placeholder="Content"></textarea>
            </div>
        </div>
    </div>
    <div class="offset-md-10" id="btnAddNewSectionDiv">
        <button class="btn btn-primary mt-3" id="btnAddNewSection" style="width: 100%">Add New section</button>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const notyf = new Notyf();
        const container = document.getElementById("sectionMainDiv");
        const btnAdd = document.getElementById("btnAddNewSection");
        const btnSave = document.getElementById("savePage");

        const pageTitle = '{{$pageTitle}}';
        const menuId = '{{$menuId}}';
        const menuName = '{{$menuName}}';
        const metaTags = '{{$metaTags}}';
        const metaDesc = '{{$metaDesc}}';

        // === INIT SUMMERNOTE ===
        $('textarea[name="section[]"]').each(function() {
            initSummernote(this);
        });

        btnAdd.addEventListener("click", function() {
            const sections = container.querySelectorAll(".row");
            const validationResult = validateSections(sections);

            if (!validationResult.valid) return;

            const nextSortOrder = getNextSortOrder(container);
            const lastSection = sections[sections.length - 1];

            const $lastTextarea = $(lastSection).find('textarea[name="section[]"]');
            const savedContent = $lastTextarea.summernote('code');
            $lastTextarea.val(savedContent); // Set the content to <textarea>
            $lastTextarea.summernote('destroy');


            const newSection = cloneAndResetSection(lastSection, nextSortOrder);
            container.appendChild(newSection);

            updateAllSelects(container, nextSortOrder);

            // Reinitialize Summernote
            $('textarea[name="section[]"]').each(function() {
                if (!$(this).next('.note-editor').length) {
                    initSummernote(this);
                }
            });
        });

        btnSave.addEventListener("click", function() {
            const sections = container.querySelectorAll(".row");
            const validationResult = validateSections(sections);

            if (!validationResult.valid) {
                notyf.error('Some fields are empty or invalid.');
                return;
            }

            const formData = {
                pageTitle: '',
                menuId: '',
                menuName: '',
                metaTags: '',
                metaDesc: '',

                sectionTitle: [],
                section: [],
                sectionSortOrder: [],
            };

            formData.pageTitle = pageTitle || '' ;
            formData.menuId = menuId || '' ;
            formData.menuName= menuName || '' ;
            formData.metaTags = metaTags || '' ;
            formData.metaDesc = metaDesc || '' ;

            validationResult.data.forEach(section => {
                formData.sectionTitle.push(section.title);
                formData.section.push(section.content);
                formData.sectionSortOrder.push(section.sortOrder);
            });

            console.log("All fields are valid, sending AJAX");
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('page.save') }}",
                data: formData,
                success: function(response) {
                    response.success ? notyf.success(response.message) : notyf.error(
                        response.message);
                },
                error: function() {
                    notyf.error('Something went wrong, please try again.');
                }
            });
        });

        // === VALIDATION FUNCTION ===
        function validateSections(sections) {
            let allValid = true;
            const sortOrderMap = new Map();
            const validData = [];

            sections.forEach(section => {
                const titleInput = section.querySelector("input[name='sectionTitle[]']");
                const sortSelect = section.querySelector("select[name='sectionSortOrder[]']");
                const textarea = section.querySelector("textarea[name='section[]']");
                const $textarea = $(textarea);
                const editor = $textarea.next('.note-editor');

                // Cleanup previous validation
                [titleInput, sortSelect].forEach(el => el?.classList.remove("is-invalid"));
                editor.removeClass("is-invalid");

                const title = titleInput?.value.trim() || '';
                const sortOrder = sortSelect?.value || '';
                const content = $textarea.summernote('isEmpty') ? '' : $textarea.summernote('code');

                // Validation
                if (!title) {
                    titleInput.classList.add("is-invalid");
                    allValid = false;
                }

                if (!sortOrder) {
                    sortSelect.classList.add("is-invalid");
                    allValid = false;
                } else {
                    if (!sortOrderMap.has(sortOrder)) {
                        sortOrderMap.set(sortOrder, []);
                    }
                    sortOrderMap.get(sortOrder).push(sortSelect);
                }

                if (!content.trim()) {
                    editor.addClass("is-invalid");
                    allValid = false;
                }

                if (title && sortOrder && content.trim()) {
                    validData.push({
                        title,
                        sortOrder,
                        content
                    });
                }
            });

            // Check for duplicate sort orders
            sortOrderMap.forEach(selects => {
                if (selects.length > 1) {
                    allValid = false;
                    selects.forEach(sel => sel.classList.add("is-invalid"));
                }
            });

            return {
                valid: allValid,
                data: validData
            };
        }

        // === GET NEXT SORT ORDER ===
        function getNextSortOrder(container) {
            let max = 0;
            container.querySelectorAll("select").forEach(select => {
                Array.from(select.options).forEach(opt => {
                    const val = parseInt(opt.value);
                    if (!isNaN(val)) max = Math.max(max, val);
                });
            });
            return max + 1;
        }

        // === CLONE & RESET SECTION ===
        function cloneAndResetSection(section, sortOrder) {
            const clone = section.cloneNode(true);
            clone.querySelector("input[name='sectionTitle[]']").value = "";
            const textarea = clone.querySelector("textarea[name='section[]']");
            textarea.value = "";

            const selectDiv = clone.querySelector("#sectionSortOrderDiv");
            selectDiv.innerHTML = "";
            const newSelect = createSelectOptions("", sortOrder, sortOrder);
            newSelect.name = "sectionSortOrder[]";
            selectDiv.appendChild(newSelect);

            return clone;
        }

        // === UPDATE ALL SELECT DROPDOWNS ===
        function updateAllSelects(container, maxOrder) {
            container.querySelectorAll("select").forEach(select => {
                const current = select.value;
                const parent = select.parentNode;
                const updatedSelect = createSelectOptions(current, maxOrder);
                updatedSelect.name = "sectionSortOrder[]";
                parent.replaceChild(updatedSelect, select);
            });
        }

        // === CREATE SELECT OPTIONS ===
        function createSelectOptions(currentValue, maxOrder, selectedValue = null) {
            const select = document.createElement("select");
            select.className = "form-select my-3";
            select.setAttribute("aria-label", "Sort order");

            const defaultOption = new Option("Select Sort Order", "");
            select.appendChild(defaultOption);

            for (let i = 1; i <= maxOrder; i++) {
                const opt = new Option(i, i);
                if ((selectedValue && i == selectedValue) || (!selectedValue && i == currentValue)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            }
            return select;
        }

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
    });
</script>
