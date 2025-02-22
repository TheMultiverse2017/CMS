<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @php
    $allMenus = (new \App\Helpers\Helpers())->getAllMainMenus() ?? [];
    (new \App\Helpers\Helpers())->startSession();
    $previous_url = rtrim(session()->previousUrl(), '/');
    $current_url = rtrim(url()->current(), '/');
    $title = $title ?? null;
    (new \App\Helpers\Helpers())->analytics($previous_url, $current_url, $title);
    $allMenus = (new \App\Helpers\Helpers())->getAllMainMenus() ?? [];
    @endphp
    @include('layouts.WEBSITE.USER.INCLUDES.header')
    @include('COMMON.INCLUDES.summernote')
</head>

<body>
    @if (env('WEBSITE_ENABLE_LOADING_SCREEN'))
        <!-- Loading Screen -->
        <div id="loading-screen">
            <img src="{{ URL::asset(env('WEBSITE_LOADING_ASSET')) ?? null }}" alt="Loading...">
            <!-- Your loading image -->
            {{-- <p>Loading...</p> --}}
        </div>
    @endif
    <div id="app">
        <div class="row px-0 m-0 justify-content-center align-items-center" align="center">
            <div class="col-auto">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img alt="{{ env('WEBSITE_TITLE') }} - {{ $title ?? null }}" height="24"style="height: 80px"
                        src="{{ URL::asset(env('WEBSITE_LOGO')) }}">
                </a>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg bg-body-tertiary mt-0 pt-0 fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img alt="{{ env('WEBSITE_TITLE') }} - {{ $title ?? null }}" height="50"
                        src="{{ URL::asset(env('WEBSITE_LOGO')) }}">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav m-auto  " align="center">
                        @forelse ($allMenus as $menu)
                            @php
                                $subMenus = (new \App\Helpers\Helpers())->getAllSubMenusForMain($menu->id) ?? [];
                                $subMenuStatus = false;
                                if(!empty($subMenus) && count($subMenus) > 0){
                                    $subMenuStatus = true;
                                }
                            @endphp
                            <li class="nav-item mx-3 @if($subMenuStatus) dropdown @endif">
                                <a class="nav-link active  @if ($subMenuStatus) dropdown-toggle @endif"
                                    href="@if(strtolower($menu->menu) === 'home') {{ route('home') }} @else {{ route('menu', [$menu->menu]) }} @endif">{{ strtoupper($menu->menu) }}</a>
                                @if ($subMenuStatus)
                                    <ul class="dropdown-menu">
                                        @forelse ($subMenus as $subMenu)
                                            <li><a class="dropdown-item"
                                                    href="{{ route('menu', [$subMenu->menu]) }}">{{ strtoupper($subMenu->menu) ?? null }}</a>
                                            </li>
                                        @empty
                                        @endforelse

                                    </ul>
                                @endif
                            </li>
                        @empty
                        @endforelse
                    </ul>
                    <ul class="navbar-nav align-items-center">
                        <!-- Authentication Links -->
                        @guest
                            {{-- @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif --}}

                            {{-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif --}}
                        @endguest
                        @if (env('WEBSITE_ENABLE_DARK_LIGHT_FEATURE'))
                            <li class="nav-item">
                                <a class="nav-link m-2" id="theme-toggle">
                                    <i class="bi-moon-stars-fill m-2"></i>
                                </a>
                            </li>
                        @endif
                    </ul>

                </div>
            </div>
        </nav>
        <main class="">
            @yield('content')
        </main>
    </div>

    @include('layouts.WEBSITE.USER.INCLUDES.footer')

</body>

</html>
