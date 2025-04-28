<?php

namespace App\Helpers;

use App\Models\Analytics;
use App\Models\Website\Admin\Navigation;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

    function addNewSection()
    {
        $items = [
            'CONTENT' => [
                'content' => '<textarea name="content" class="form-control my-3" row="30" id="contentTextarea" placeholder="Content"></textarea>',
            ],
            'CAROUSEL' => [
                'image' => '<input type="file" name="files[]" multiple class="form-control my-3"  id="files" placeholder="Images"></input>',
            ],

        ];

        return $items;
    }
    function addNewSectionModalInputs($key = null)
    {
        $allInputs = $this->addNewSection();
        $inputKeysAndValues = [];

        // Check if the provided key exists in the array
        if (!empty($key) && array_key_exists($key, $allInputs)) {
            $selectedInput = $allInputs[$key];  // Get the array of inputs for the given key

            // Loop through the selected input array to get both keys and values
            foreach ($selectedInput as $inputKey => $inputValue) {
                // Add the key-value pair to the result array
                $inputKeysAndValues[] = ['key' => $inputKey, 'value' => $inputValue];
            }
        }

        // Return the array of keys and values, or an empty array if the key was not found
        return $inputKeysAndValues ?? [];
    }

    function templates($key)
    {
        $templates = [
            'CONTENT' => '<div class="row"></div>',

            'CAROUSEL' => '
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner">
                    [[carousel_items]]
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
            ',
        ];


        $selectedTemplate = '';

        if (!empty($key)) {
            $selectedTemplate = $templates[$key];
        }

        return $selectedTemplate ?? '';
    }
}
