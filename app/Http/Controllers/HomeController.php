<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Enums\UserType;
use App\Enums\WebsiteFilesBelongsTo;
use App\Models\Website\Admin\Banner;
use App\Models\WebsiteFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;
use App\Models\Analytics;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $title = 'Home';
        if (Auth::user()) {
            if (Auth::user()->type == UserType::WebsiteAdmin->value) {
                $title = 'Admin';
                $now = Carbon::now();

                // Query total visits for different time ranges
                $visitCounts = [
                    '7_days' => Analytics::where('start_time', '>=', $now->subDays(7))->count(),
                    '30_days' => Analytics::where('start_time', '>=', $now->subDays(30))->count(),
                    '60_days' => Analytics::where('start_time', '>=', $now->subDays(60))->count(),
                    '1_year' => Analytics::where('start_time', '>=', $now->subYear())->count(),
                ];

                // If no visits, set "N/A"
                foreach ($visitCounts as $key => $value) {
                    $visitCounts[$key] = $value > 0 ? $value : 'N/A';
                }
                $request = Request();
                if ($request->ajax()) {
                    $startDate = Carbon::parse($request->start)->startOfDay();
                    $endDate = Carbon::parse($request->end)->endOfDay();
                    // dd($startDate);
                    $analyticsDatas = Analytics::whereBetween('updated_at', [$startDate, $endDate])
                        ->whereNotNull('duration') // Ignore sessions with no duration
                        ->get();

                    // Initialize arrays for top URLs
                    $urlData = [];

                    $is_mobile_count = $is_tablet_count = $is_desktop_count = $is_unknown = 0;

                    $is_mobile_duration = $is_tablet_duration = $is_desktop_duration = $is_unknown_duration = 0;

                    $deviceTypes = [
                        'Mobile' => $is_mobile_count,
                        'Tablet' => $is_tablet_count,
                        'Desktop' => $is_desktop_count,
                        'Unknown' => $is_unknown
                    ];

                    $deviceDurations = [
                        'Mobile' => $is_mobile_duration,
                        'Tablet' => $is_tablet_duration,
                        'Desktop' => $is_desktop_duration,
                        'Unknown' => $is_unknown_duration
                    ];
                    foreach ($analyticsDatas as $analyticsData) {
                        $userAgentDecode = json_decode($analyticsData->user_agent);
                        $deviceType = 'Unknown';
                        if ($userAgentDecode->original->is_mobile) {
                            $deviceType = 'Mobile';
                            $is_mobile_count++;
                            $is_mobile_duration = $analyticsData->duration + $is_mobile_duration ?? 0;
                        } elseif ($userAgentDecode->original->is_tablet) {
                            $deviceType = 'Tablet';
                            $is_tablet_count++;
                            $is_tablet_duration = $analyticsData->duration + $is_tablet_duration  ?? 0;
                        } elseif ($userAgentDecode->original->is_desktop) {
                            $deviceType = 'Desktop';
                            $is_desktop_count++;
                            $is_desktop_duration = $analyticsData->duration + $is_desktop_duration ?? 0;
                        } else {
                            $is_unknown++;
                            $is_unknown_duration = $analyticsData->duration + $is_unknown_duration ?? 0;
                        }

                        // Track top URLs
                        $url = $analyticsData->url;
                        if (!isset($urlData[$url])) {
                            $urlData[$url] = [
                                'count' => 0,
                                'duration' => 0,
                                'title' => '',
                            ];
                        }

                        $urlData[$url]['count']++; // Count visits
                        $urlData[$url]['duration'] += $analyticsData->duration; // Sum duration
                        $urlData[$url]['title'] = $analyticsData->title; // Sum duration
                    }
                    // Get the top 10 URLs
                    $topUrls = array_slice($urlData, 0, 10, true);

                    $deviceTypes = [
                        'Mobile' => $is_mobile_count,
                        'Tablet' => $is_tablet_count,
                        'Desktop' => $is_desktop_count,
                        'Unknown' => $is_unknown
                    ];
                    $deviceDurations = [
                        'Mobile' => $is_mobile_duration,
                        'Tablet' => $is_tablet_duration,
                        'Desktop' => $is_desktop_duration,
                        'Unknown' => $is_unknown_duration
                    ];

                    return response()->json(['deviceTypes' => $deviceTypes, 'deviceDurations' => $deviceDurations, 'topUrls' => $topUrls]);
                }
                return view('WEBSITE.ADMIN.home', compact('title', 'visitCounts'));
            }
        }
        $banners = WebsiteFiles::where('status', Status::ACTIVE->value)
            ->where('filesfor', WebsiteFilesBelongsTo::BANNERS->value)
            ->orderBy('updated_at', 'desc')->get();

        return view('WEBSITE.USER.home', compact('title', 'banners'));
    }

    public function getAllMenus()
    {
        $allMenus = (new Helpers())->getAllMenus() ?? [];
        return response()->json($allMenus);
    }

    public function menu($title)
    {
        $id = $title = 'Test';
        $data = [];
        // $content = use model to get content based on matching title

        //
        return view('WEBSITE.USER.testpage', compact('id', 'title', 'data'));
    }
    function sitemap(){
        $fileName = 'sitemap.xml';
        $path = public_path($fileName);

        // Delete existing sitemap if it exists
        if (File::exists($path)) {
            File::delete($path);
        }

        // Fetch URLs from the database (if applicable)
        // $pages = Post::all(); // Replace with your actual model

        // Define static pages
        // $staticPages = [
        //     'about-us' => 'about us',
        //     'privacy' => 'privacy',
        //     'terms-and-conditions' => 'terms & conditions',
        //     'disclaimer' => 'disclaimer',
        // ];

        // Start XML structure
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Add home page
        $xml .= '<url>';
        $xml .= '<loc>' . url('/') . '</loc>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Add static pages
        // foreach ($staticPages as $slug => $title) {
        //     $xml .= '<url>';
        //     $xml .= '<loc>' . url("/page/$slug") . '</loc>'; // Adjust according to your route
        //     // $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        //     $xml .= '<changefreq>monthly</changefreq>';
        //     $xml .= '<priority>0.5</priority>';
        //     $xml .= '</url>';
        // }

        // Add dynamic pages from the database
        // foreach ($pages as $page) {
        //     $xml .= '<url>';
        //     $xml .= '<loc>' . url("/post/$page->title") . '</loc>'; // Adjust URL format if necessary
        //     $xml .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
        //     $xml .= '<changefreq>weekly</changefreq>';
        //     $xml .= '<priority>0.8</priority>';
        //     $xml .= '</url>';
        // }

        // Close XML structure
        // $xml .= '</urlset>';
        // Save the sitemap.xml file in the public directory
        // File::put($path, $xml);

        notyf()->success('Your request was processed successfully.');
        return redirect()->back();

    }
}
