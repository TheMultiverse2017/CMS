<?php

namespace App\Helpers;

use App\Models\Analytics;
use App\Models\Website\Admin\Navigation;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class Helpers
{

    function analytics($previous_url = null, $current_url = null, $title = null)
    {
        $ignoredRoutes = [
            rtrim(route('get.menus'), '/'),
        ];
        if (in_array($current_url, $ignoredRoutes)) {
            return;
        }
        if (empty($title) && !empty($current_url)) {
            $path = parse_url($current_url, PHP_URL_PATH); // Get path part of URL
            $segments = array_filter(explode('/', trim($path, '/'))); // Remove empty segments

            if (!empty($segments)) {
                $title = urldecode(end($segments)); // Get last segment & decode URL
            } else {
                $title = ''; // Default value if no valid segment exists
            }
        }
        $request = Request();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Store the session ID in the PHP session if it doesn't already exist
        if (!isset($_SESSION['session_id'])) {

            $session_id = bin2hex(random_bytes(16));
            $session = Analytics::where('session_id', $session_id)->first();

            if ($session != null) {
                do {
                    // Generate a secure random session ID
                    $session_id = bin2hex(random_bytes(16));
                    // Check if the session ID already exists in the database
                    $session = Analytics::where('session_id', $session_id)->first();
                } while ($session != null);
            }
            $_SESSION['session_id'] = $session_id;
        }
        $user_agent = json_encode(self::getDevice(), true) ?? null;
        $ip_address = $request->ip() ?? null;

        $checkSessionExist = Analytics::where('session_id', $_SESSION['session_id'])->first();
        if (!$checkSessionExist) {
            // new user - new session - new data
            Analytics::create([
                'url' => $current_url,
                'title' => $title ?? null, // Assuming $title is defined
                'session_id' => $_SESSION['session_id'],
                'ip_address' => $ip_address,
                'user_agent' => $user_agent,
                'start_time' => now(),
                'end_time' => null, // Set this when the session ends
                'duration' => null, // Calculate and update later if needed
            ]);
        } else {
            // existing user - old session - update data
            // check if on same page
            // if on same page do nothing
            // if page changed update end time and duration of previous url
            // then add new url data with start time
            // dd($previous_url, $current_url);
            if ($previous_url != $current_url) {
                $getData = Analytics::where('url', $previous_url)
                    ->where('session_id', $_SESSION['session_id'])
                    ->latest('updated_at') // Orders by 'updated_at DESC'
                    ->first();
                if ($getData) {
                    // Ensure start_time is a Carbon instance
                    $start_time = Carbon::parse($getData->start_time);

                    // Calculate the difference in seconds from start_time to now()
                    $duration = $start_time->diffInSeconds(now());

                    // Update the end_time and duration
                    $getData->update([
                        'end_time' => now(),
                        'duration' => $duration, // in seconds
                    ]);
                }
                Analytics::create([
                    'url' => $current_url,
                    'title' => $title ?? null, // Assuming $title is defined
                    'session_id' => $_SESSION['session_id'],
                    'ip_address' => $ip_address,
                    'user_agent' => $user_agent,
                    'start_time' => now(),
                    'end_time' => null, // Set this when the session ends
                    'duration' => null, // Calculate and update later if needed
                ]);
            }
        }
    }

    function getDevice()
    {
        $agent = new Agent();

        $data = [
            'is_mobile' => $agent->isMobile(),         // Check if the device is mobile
            'is_tablet' => $agent->isTablet(),         // Check if the device is a tablet
            'is_desktop' => $agent->isDesktop(),       // Check if the device is a desktop
            'browser' => $agent->browser(),            // Get the browser name
            'platform' => $agent->platform(),          // Get the operating system
            'device' => $agent->device(),              // Get the device name
            'user_agent' => $agent->getUserAgent(),    // Full user agent string
        ];

        return response()->json($data);
    }

    function getAllMenus()
    {
        $allMenus = Navigation::get();
        return $allMenus ?? [];
    }

    function getAllMainMenus()
    {
        $allMainMenus = Navigation::where('reference_id', null)->get();
        return $allMainMenus ?? [];
    }

    function getAllSubMenus()
    {
        $allSubMenus = Navigation::where('reference_id', '!=', null)->get();
        return $allSubMenus ?? [];
    }

    function getAllSubMenusForMain($menuId)
    {
        $allSubMenusFormMain = Navigation::where('reference_id', $menuId)->get();
        return $allSubMenusFormMain ?? [];
    }
    function startSession()
    {
        if (!session()->isStarted()) {
            session()->start();
        }
    }
    function allContainers($key = null)
    {
        $defaultImagePath = URL::asset('/ASSETS/DEFAULT/defaultwide.jpg');
        $commonStyle = 'style="width:auto; border: 1px solid grey;"';

        $containers = [
            // Basic HTML Elements with contenteditable attribute
            'CONTAINER' => '<div class="container commonClass [[[[[[my-custom-class]]] draggable" draggable="true" '.$commonStyle.' id="[[[my-custom-id]]]" contenteditable="true">Container</div>',
            'ROW' => '<div class="row commonClass [[[[[[my-custom-class]]] draggable" draggable="true" '.$commonStyle.' id="[[[my-custom-id]]]" contenteditable="true">Row</div>',
            'COL' => '<div class="col commonClass [[[[[[my-custom-class]]] draggable" draggable="true" '.$commonStyle.' id="[[[my-custom-id]]]" contenteditable="true">Column</div>',
            'PARAGRAPH' => '<p class="commonClass [[[[[[my-custom-class]]] draggable" draggable="true" '.$commonStyle.' id="[[[my-custom-id]]]" contenteditable="true">Your text here</p>',
            'HEADING' => '<h1 class="h1 commonClass [[[[[[my-custom-class]]] draggable" draggable="true" '.$commonStyle.' id="[[[my-custom-id]]]" contenteditable="true">Heading Text</h1>',
            'IMAGE' => '<img src="' . $defaultImagePath . '" class="img-fluid commonClass [[[[[[my-custom-class]]] adminUiImageClass draggable" draggable="true" id="[[[my-custom-id]]]" alt="[[[my-alt-text]]]">',

            // For media elements, wrap them in a div with contenteditable
            'VIDEO' => '<div contenteditable="true"><video class="commonClass [[[[[[my-custom-class]]] adminUiVideoClass" id="[[[my-custom-id]]]" controls>
                <source src="[[[my-video-src]]]" type="video/mp4">
                Your browser does not support the video tag.
            </video></div>',

            'IFRAME' => '<div contenteditable="true"><iframe src="[[[my-iframe-src]]] " class="commonClass [[[[[[my-custom-class]]] adminUiIframeClass" id="[[[my-custom-id]]]" width="100%" height="400px" frameborder="0"></iframe></div>',

        ];

        // $components = self::getComponentKeys($components);
        if ($key != null) {
            if (!empty($containers[$key])) {
                $containers = $containers[$key];
            } else {
                return null;
            }
        }

        return $containers;
    }

    function allComponents($key = null)
    {
        $components = [
            // Bootstrap Components
            'BUTTON' => '<button type="button" class="btn btn-primary commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]">Click
                Me</button>',
            'BUTTON_GROUP' => '<div class="btn-group commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]">
                <button type="button" class="btn btn-primary">Left</button>
                <button type="button" class="btn btn-primary">Middle</button>
                <button type="button" class="btn btn-primary">Right</button>
            </div>',
            'INPUT' => '<input type="text" class="form-control commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]"
                placeholder="Enter text">',
            'TEXTAREA' => '<textarea class="form-control commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]" rows="3"
                placeholder="Enter text"></textarea>',
            'FILE' => '<input type="file" class="form-control commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]">',
            'SELECT' => '<select class="form-select commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]">
                <option selected>Choose...</option>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>',
            'CHECKBOX' => '<div class="form-check">
                <input class="form-check-input commonClass [[[[[[my-custom-class]]]" type="checkbox" id="[[[my-custom-id]]]">
                <label class="form-check-label" for="[[[my-custom-id]]]">Check me</label>
            </div>',
            'RADIO' => '<div class="form-check">
                <input class="form-check-input commonClass [[[[[[my-custom-class]]]" type="radio" name="exampleRadios" id="[[[my-custom-id]]]"
                    value="option1">
                <label class="form-check-label" for="[[[my-custom-id]]]">Radio option</label>
            </div>',
            'TABLE' => '<table class="table commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]">
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
            </table>',
            'ALERT' => '<div class="alert alert-warning commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]" role="alert">
                This is a warning alert—check it out!
            </div>',
            'CARD' => '<div class="card commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]">
                <img src="[[[my-image-src]]]" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">Some quick example text.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>',
            'MODAL' => '<div class="modal fade commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]" tabindex="-1">
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
            </div>',
            'TOAST' => '<div class="toast commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]" role="alert" data-bs-autohide="false">
                <div class="toast-header">
                    <strong class="me-auto">Bootstrap</strong>
                    <small>Just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    See? Just like this.
                </div>
            </div>',
            'CAROUSEL' => '<div id="carouselExample" class="carousel slide commonClass [[[[[[my-custom-class]]]" id="[[[my-custom-id]]]"
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
            </div>',
        ];

        // $components = self::getComponentKeys($components);
        if ($key != null) {
            if (!empty($components[$key])) {
                $components = $components[$key] ?? [];
            } else {
                return null;
            }
        }
        return $components;
    }

    function allClasses($key = null)
    {
        $classes = [
            // Column Classes
            'COL_AUTO' => 'col-auto',
            'COL_1' => 'col-1',
            'COL_2' => 'col-2',
            'COL_3' => 'col-3',
            'COL_4' => 'col-4',
            'COL_5' => 'col-5',
            'COL_6' => 'col-6',
            'COL_7' => 'col-7',
            'COL_8' => 'col-8',
            'COL_9' => 'col-9',
            'COL_10' => 'col-10',
            'COL_11' => 'col-11',
            'COL_12' => 'col-12',
            'COL_XS_AUTO' => 'col-xs-auto',
            'COL_XS_1' => 'col-xs-1',
            'COL_XS_2' => 'col-xs-2',
            'COL_XS_3' => 'col-xs-3',
            'COL_XS_4' => 'col-xs-4',
            'COL_XS_5' => 'col-xs-5',
            'COL_XS_6' => 'col-xs-6',
            'COL_XS_7' => 'col-xs-7',
            'COL_XS_8' => 'col-xs-8',
            'COL_XS_9' => 'col-xs-9',
            'COL_XS_10' => 'col-xs-10',
            'COL_XS_11' => 'col-xs-11',
            'COL_XS_12' => 'col-xs-12',
            'COL_SM_AUTO' => 'col-sm-auto',
            'COL_SM_1' => 'col-sm-1',
            'COL_SM_2' => 'col-sm-2',
            'COL_SM_3' => 'col-sm-3',
            'COL_SM_4' => 'col-sm-4',
            'COL_SM_5' => 'col-sm-5',
            'COL_SM_6' => 'col-sm-6',
            'COL_SM_7' => 'col-sm-7',
            'COL_SM_8' => 'col-sm-8',
            'COL_SM_9' => 'col-sm-9',
            'COL_SM_10' => 'col-sm-10',
            'COL_SM_11' => 'col-sm-11',
            'COL_SM_12' => 'col-sm-12',
            'COL_MD_AUTO' => 'col-md-auto',
            'COL_MD_1' => 'col-md-1',
            'COL_MD_2' => 'col-md-2',
            'COL_MD_3' => 'col-md-3',
            'COL_MD_4' => 'col-md-4',
            'COL_MD_5' => 'col-md-5',
            'COL_MD_6' => 'col-md-6',
            'COL_MD_7' => 'col-md-7',
            'COL_MD_8' => 'col-md-8',
            'COL_MD_9' => 'col-md-9',
            'COL_MD_10' => 'col-md-10',
            'COL_MD_11' => 'col-md-11',
            'COL_MD_12' => 'col-md-12',
            'COL_LG_AUTO' => 'col-lg-auto',
            'COL_LG_1' => 'col-LG-1',
            'COL_LG_1' => 'col-lg-1',
            'COL_LG_2' => 'col-lg-2',
            'COL_LG_3' => 'col-lg-3',
            'COL_LG_4' => 'col-lg-4',
            'COL_LG_5' => 'col-lg-5',
            'COL_LG_6' => 'col-lg-6',
            'COL_LG_7' => 'col-lg-7',
            'COL_LG_8' => 'col-lg-8',
            'COL_LG_9' => 'col-lg-9',
            'COL_LG_10' => 'col-lg-10',
            'COL_LG_11' => 'col-lg-11',
            'COL_LG_12' => 'col-lg-12',
            'COL_XL_AUTO' => 'col-xl-auto',
            'COL_XL_1' => 'col-xl-1',
            'COL_XL_2' => 'col-xl-2',
            'COL_XL_3' => 'col-xl-3',
            'COL_XL_4' => 'col-xl-4',
            'COL_XL_5' => 'col-xl-5',
            'COL_XL_6' => 'col-xl-6',
            'COL_XL_7' => 'col-xl-7',
            'COL_XL_8' => 'col-xl-8',
            'COL_XL_9' => 'col-xl-9',
            'COL_XL_10' => 'col-xl-10',
            'COL_XL_11' => 'col-xl-11',
            'COL_XL_12' => 'col-xl-12',

            // Bootstrap Colors
            'PRIMARY' => 'primary',
            'SECONDARY' => 'secondary',
            'SUCCESS' => 'success',
            'DANGER' => 'danger',
            'WARNING' => 'warning',
            'INFO' => 'info',
            'LIGHT' => 'light',
            'DARK' => 'dark',
            'WHITE' => 'white',
            'BLACK' => 'black',
            'TRANSPARENT' => 'transparent',

            // Background Colors
            'BG_PRIMARY' => 'bg-primary',
            'BG_SECONDARY' => 'bg-secondary',
            'BG_SUCCESS' => 'bg-success',
            'BG_DANGER' => 'bg-danger',
            'BG_WARNING' => 'bg-warning',
            'BG_INFO' => 'bg-info',
            'BG_LIGHT' => 'bg-light',
            'BG_DARK' => 'bg-dark',
            'BG_WHITE' => 'bg-white',
            'BG_BLACK' => 'bg-black',
            'BG_TRANSPARENT' => 'bg-transparent',

            // Display Utilities
            'D_NONE' => 'd-none',
            'D_BLOCK' => 'd-block',
            'D_INLINE' => 'd-inline',
            'D_INLINE_BLOCK' => 'd-inline-block',
            'D_FLEX' => 'd-flex',
            'D_GRID' => 'd-grid',

            // Flex Utilities
            'FLEX_ROW' => 'flex-row',
            'FLEX_COLUMN' => 'flex-column',
            'FLEX_ROW_REVERSE' => 'flex-row-reverse',
            'FLEX_COLUMN_REVERSE' => 'flex-column-reverse',
            'FLEX_WRAP' => 'flex-wrap',
            'FLEX_NOWRAP' => 'flex-nowrap',
            'ALIGN_ITEMS_START' => 'align-items-start',
            'ALIGN_ITEMS_CENTER' => 'align-items-center',
            'ALIGN_ITEMS_END' => 'align-items-end',
            'JUSTIFY_CONTENT_START' => 'justify-content-start',
            'JUSTIFY_CONTENT_CENTER' => 'justify-content-center',
            'JUSTIFY_CONTENT_END' => 'justify-content-end',
            'JUSTIFY_CONTENT_BETWEEN' => 'justify-content-between',
            'JUSTIFY_CONTENT_AROUND' => 'justify-content-around',

            // Border Utilities
            'BORDER' => 'border',
            'BORDER_0' => 'border-0',
            'BORDER_TOP' => 'border-top',
            'BORDER_BOTTOM' => 'border-bottom',
            'BORDER_START' => 'border-start',
            'BORDER_END' => 'border-end',

            // Text Utilities
            'TEXT_START' => 'text-start',
            'TEXT_CENTER' => 'text-center',
            'TEXT_END' => 'text-end',
            'TEXT_BOLD' => 'fw-bold',
            'TEXT_ITALIC' => 'fst-italic',
            'TEXT_UPPERCASE' => 'text-uppercase',
            'TEXT_LOWERCASE' => 'text-lowercase',
            'TEXT_CAPITALIZE' => 'text-capitalize',

            // Positioning
            'POSITION_STATIC' => 'position-static',
            'POSITION_RELATIVE' => 'position-relative',
            'POSITION_ABSOLUTE' => 'position-absolute',
            'POSITION_FIXED' => 'position-fixed',
            'POSITION_STICKY' => 'position-sticky',

            // Opacity
            'OPACITY_0' => 'opacity-0',
            'OPACITY_25' => 'opacity-25',
            'OPACITY_50' => 'opacity-50',
            'OPACITY_75' => 'opacity-75',
            'OPACITY_100' => 'opacity-100',

            // Overflow
            'OVERFLOW_AUTO' => 'overflow-auto',
            'OVERFLOW_HIDDEN' => 'overflow-hidden',
            'OVERFLOW_VISIBLE' => 'overflow-visible',
            'OVERFLOW_SCROLL' => 'overflow-scroll',

            // Sizing
            'W_25' => 'w-25',
            'W_50' => 'w-50',
            'W_75' => 'w-75',
            'W_100' => 'w-100',
            'W_AUTO' => 'w-auto',
            'H_25' => 'h-25',
            'H_50' => 'h-50',
            'H_75' => 'h-75',
            'H_100' => 'h-100',
            'H_AUTO' => 'h-auto',

            // Visibility
            'VISIBLE' => 'visible',
            'INVISIBLE' => 'invisible',

            // Shadows
            'SHADOW_SM' => 'shadow-sm',
            'SHADOW' => 'shadow',
            'SHADOW_LG' => 'shadow-lg',
            'SHADOW_NONE' => 'shadow-none',

            // Floating
            'FLOAT_START' => 'float-start',
            'FLOAT_END' => 'float-end',
            'FLOAT_NONE' => 'float-none',
        ];
        if ($key != null) {
            if (isset($classes[$key])) {
                $classes = $classes[$key] ?? [];
            } else {
                return null;
            }
        }
        return $classes;
    }

    function getComponentKeys($components)
    {
        $keys = [];
        foreach ($components as $category => $items) {
            $keys = array_merge($keys, array_keys($items));
        }
        return $keys;
    }
}
