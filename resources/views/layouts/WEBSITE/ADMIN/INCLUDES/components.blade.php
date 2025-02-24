@php

$components = [
// Basic HTML Elements
'HTML' => [
'CONTAINER' => '''<div class="container [[[my-custom-class]]]" id="[[[my-custom-id]]]"></div>''',
'ROW' => '''<div class="row [[[my-custom-class]]]" id="[[[my-custom-id]]]"></div>''',
'COL' => '''<div class="col [[[my-custom-class]]]" id="[[[my-custom-id]]]"></div>''',
'PARAGRAPH' => '''<p class="[[[my-custom-class]]]" id="[[[my-custom-id]]]">Your text here</p>''',
'HEADING' => '''<h1 class="h1 [[[my-custom-class]]]" id="[[[my-custom-id]]]">Heading Text</h1>''',
'IMAGE' => '''<img src="[[[my-image-src]]]" class="img-fluid [[[my-custom-class]]]" id="[[[my-custom-id]]]"
    alt="[[[my-alt-text]]]">''',
'VIDEO' => '''<video class="[[[my-custom-class]]]" id="[[[my-custom-id]]]" controls>
    <source src="[[[my-video-src]]]" type="video/mp4">
    Your browser does not support the video tag.
</video>''',
'IFRAME' => '''<iframe src="[[[my-iframe-src]]]" class="[[[my-custom-class]]]" id="[[[my-custom-id]]]" width="100%"
    height="400px" frameborder="0"></iframe>''',
],

// Bootstrap Components
'COMPONENTS' => [
'BUTTON' => '''<button type="button" class="btn btn-primary [[[my-custom-class]]]" id="[[[my-custom-id]]]">Click
    Me</button>''',
'BUTTON_GROUP' => '''<div class="btn-group [[[my-custom-class]]]" id="[[[my-custom-id]]]">
    <button type="button" class="btn btn-primary">Left</button>
    <button type="button" class="btn btn-primary">Middle</button>
    <button type="button" class="btn btn-primary">Right</button>
</div>''',
'INPUT' => '''<input type="text" class="form-control [[[my-custom-class]]]" id="[[[my-custom-id]]]"
    placeholder="Enter text">''',
'TEXTAREA' => '''<textarea class="form-control [[[my-custom-class]]]" id="[[[my-custom-id]]]" rows="3"
    placeholder="Enter text"></textarea>''',
'FILE' => '''<input type="file" class="form-control [[[my-custom-class]]]" id="[[[my-custom-id]]]">''',
'SELECT' => '''<select class="form-select [[[my-custom-class]]]" id="[[[my-custom-id]]]">
    <option selected>Choose...</option>
    <option value="1">Option 1</option>
    <option value="2">Option 2</option>
    <option value="3">Option 3</option>
</select>''',
'CHECKBOX' => '''<div class="form-check">
    <input class="form-check-input [[[my-custom-class]]]" type="checkbox" id="[[[my-custom-id]]]">
    <label class="form-check-label" for="[[[my-custom-id]]]">Check me</label>
</div>''',
'RADIO' => '''<div class="form-check">
    <input class="form-check-input [[[my-custom-class]]]" type="radio" name="exampleRadios" id="[[[my-custom-id]]]"
        value="option1">
    <label class="form-check-label" for="[[[my-custom-id]]]">Radio option</label>
</div>''',
'TABLE' => '''<table class="table [[[my-custom-class]]]" id="[[[my-custom-id]]]">
    <thead>
        <tr>
            <th>#</th>
            <th>First</th>
            <th>Last</th>
            <th>Handle</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
        </tr>
    </tbody>
</table>''',
'ALERT' => '''<div class="alert alert-warning [[[my-custom-class]]]" id="[[[my-custom-id]]]" role="alert">
    This is a warning alert—check it out!
</div>''',
'CARD' => '''<div class="card [[[my-custom-class]]]" id="[[[my-custom-id]]]">
    <img src="[[[my-image-src]]]" class="card-img-top" alt="...">
    <div class="card-body">
        <h5 class="card-title">Card title</h5>
        <p class="card-text">Some quick example text.</p>
        <a href="#" class="btn btn-primary">Go somewhere</a>
    </div>
</div>''',
'MODAL' => '''<div class="modal fade [[[my-custom-class]]]" id="[[[my-custom-id]]]" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Modal body text goes here.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>''',
'TOAST' => '''<div class="toast [[[my-custom-class]]]" id="[[[my-custom-id]]]" role="alert" data-bs-autohide="false">
    <div class="toast-header">
        <strong class="me-auto">Bootstrap</strong>
        <small>Just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">
        See? Just like this.
    </div>
</div>''',
'CAROUSEL' => '''<div id="carouselExample" class="carousel slide [[[my-custom-class]]]" id="[[[my-custom-id]]]"
    data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="[[[my-image-src1]]]" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="[[[my-image-src2]]]" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="[[[my-image-src3]]]" class="d-block w-100" alt="...">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>''',
],
];


$classes = [
    // Prefixes for Bootstrap classes
    'PRE_FIX_BTN' => 'btn-',
    'PRE_FIX_BG' => 'bg-',
    'PRE_FIX_TEXT' => 'text-',
    'PRE_FIX_BORDER' => 'border-',
    'PRE_FIX_SHADOW' => 'shadow-',
    'PRE_FIX_SPACING' => 'm- p-',
    'PRE_FIX_DISPLAY' => 'd-',
    'PRE_FIX_FLEX' => 'flex-',
    'PRE_FIX_GRID' => 'g-',

    // Column Classes
    'COL_AUTO' => 'col-auto',
    'COL_1' => 'col-1', 'COL_2' => 'col-2', 'COL_3' => 'col-3', 'COL_4' => 'col-4', 'COL_5' => 'col-5', 'COL_6' => 'col-6',
    'COL_7' => 'col-7', 'COL_8' => 'col-8', 'COL_9' => 'col-9', 'COL_10' => 'col-10', 'COL_11' => 'col-11', 'COL_12' => 'col-12',
    'COL_MD_1' => 'col-md-1', 'COL_MD_2' => 'col-md-2', 'COL_MD_3' => 'col-md-3', 'COL_MD_4' => 'col-md-4', 'COL_MD_5' => 'col-md-5',
    'COL_MD_6' => 'col-md-6', 'COL_MD_7' => 'col-md-7', 'COL_MD_8' => 'col-md-8', 'COL_MD_9' => 'col-md-9', 'COL_MD_10' => 'col-md-10',
    'COL_MD_11' => 'col-md-11', 'COL_MD_12' => 'col-md-12',

    // Bootstrap Colors
    'PRIMARY' => 'primary', 'SECONDARY' => 'secondary', 'SUCCESS' => 'success', 'DANGER' => 'danger',
    'WARNING' => 'warning', 'INFO' => 'info', 'LIGHT' => 'light', 'DARK' => 'dark', 'WHITE' => 'white', 'BLACK' => 'black',
    'TRANSPARENT' => 'transparent',

    // Background Colors
    'BG_PRIMARY' => 'bg-primary', 'BG_SECONDARY' => 'bg-secondary', 'BG_SUCCESS' => 'bg-success', 'BG_DANGER' => 'bg-danger',
    'BG_WARNING' => 'bg-warning', 'BG_INFO' => 'bg-info', 'BG_LIGHT' => 'bg-light', 'BG_DARK' => 'bg-dark',
    'BG_WHITE' => 'bg-white', 'BG_BLACK' => 'bg-black', 'BG_TRANSPARENT' => 'bg-transparent',

    // Display Utilities
    'D_NONE' => 'd-none', 'D_BLOCK' => 'd-block', 'D_INLINE' => 'd-inline', 'D_INLINE_BLOCK' => 'd-inline-block',
    'D_FLEX' => 'd-flex', 'D_GRID' => 'd-grid',

    // Flex Utilities
    'FLEX_ROW' => 'flex-row', 'FLEX_COLUMN' => 'flex-column', 'FLEX_ROW_REVERSE' => 'flex-row-reverse', 'FLEX_COLUMN_REVERSE' => 'flex-column-reverse',
    'FLEX_WRAP' => 'flex-wrap', 'FLEX_NOWRAP' => 'flex-nowrap',
    'ALIGN_ITEMS_START' => 'align-items-start', 'ALIGN_ITEMS_CENTER' => 'align-items-center', 'ALIGN_ITEMS_END' => 'align-items-end',
    'JUSTIFY_CONTENT_START' => 'justify-content-start', 'JUSTIFY_CONTENT_CENTER' => 'justify-content-center',
    'JUSTIFY_CONTENT_END' => 'justify-content-end', 'JUSTIFY_CONTENT_BETWEEN' => 'justify-content-between',
    'JUSTIFY_CONTENT_AROUND' => 'justify-content-around',

    // Border Utilities
    'BORDER' => 'border', 'BORDER_0' => 'border-0', 'BORDER_TOP' => 'border-top', 'BORDER_BOTTOM' => 'border-bottom',
    'BORDER_START' => 'border-start', 'BORDER_END' => 'border-end',

    // Text Utilities
    'TEXT_START' => 'text-start', 'TEXT_CENTER' => 'text-center', 'TEXT_END' => 'text-end', 'TEXT_BOLD' => 'fw-bold', 'TEXT_ITALIC' => 'fst-italic',
    'TEXT_UPPERCASE' => 'text-uppercase', 'TEXT_LOWERCASE' => 'text-lowercase', 'TEXT_CAPITALIZE' => 'text-capitalize',

    // Positioning
    'POSITION_STATIC' => 'position-static', 'POSITION_RELATIVE' => 'position-relative', 'POSITION_ABSOLUTE' => 'position-absolute',
    'POSITION_FIXED' => 'position-fixed', 'POSITION_STICKY' => 'position-sticky',

    // Opacity
    'OPACITY_0' => 'opacity-0', 'OPACITY_25' => 'opacity-25', 'OPACITY_50' => 'opacity-50', 'OPACITY_75' => 'opacity-75', 'OPACITY_100' => 'opacity-100',

    // Overflow
    'OVERFLOW_AUTO' => 'overflow-auto', 'OVERFLOW_HIDDEN' => 'overflow-hidden', 'OVERFLOW_VISIBLE' => 'overflow-visible', 'OVERFLOW_SCROLL' => 'overflow-scroll',

    // Sizing
    'W_25' => 'w-25', 'W_50' => 'w-50', 'W_75' => 'w-75', 'W_100' => 'w-100', 'W_AUTO' => 'w-auto',
    'H_25' => 'h-25', 'H_50' => 'h-50', 'H_75' => 'h-75', 'H_100' => 'h-100', 'H_AUTO' => 'h-auto',

    // Visibility
    'VISIBLE' => 'visible', 'INVISIBLE' => 'invisible',

    // Shadows
    'SHADOW_SM' => 'shadow-sm', 'SHADOW' => 'shadow', 'SHADOW_LG' => 'shadow-lg', 'SHADOW_NONE' => 'shadow-none',

    // Floating
    'FLOAT_START' => 'float-start', 'FLOAT_END' => 'float-end', 'FLOAT_NONE' => 'float-none',
];

@endphp
