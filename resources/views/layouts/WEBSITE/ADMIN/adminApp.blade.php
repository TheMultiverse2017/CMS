<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.WEBSITE.ADMIN.INCLUDES.header')
    @include('COMMON.INCLUDES.summernote')
    @php
        $disabledMenus = [
            // \App\Enums\DisabledMenus::GALLERY->value,
            // \App\Enums\DisabledMenus::TESTIMONIALS->value,
            // \App\Enums\DisabledMenus::SEO->value,
        ];
    @endphp
</head>

<body>
    <div id="app">
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
            id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
        </div>
        <nav class="navbar navbar-dark bg-dark sticky-top">
            <div class="container-fluid">
                <button class="navbar-toggler ms-start" type="button" data-bs-toggle="offcanvas"
                    data-bs-target=".offcanvasDarkNavbar" d aria-controls="offcanvasDarkNavbar"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="ms-end">
                    <a href="{{ route('profile.index') }}" class="btn btn-outline-warning mx-3" type="button">
                        <i class="bi bi-person-bounding-box nav-icon"></i>
                    </a>
                    <a class="btn btn-outline-warning ms-end" id="theme-toggle">
                        <i class="bi-moon-stars-fill nav-icon"></i>
                    </a>
                </div>
                <div class="offcanvas offcanvas-start text-bg-dark show offcanvasDarkNavbar" data-bs-scroll="true"
                    data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling"
                    aria-labelledby="offcanvasScrollingLabel" style="width:auto;text-align: center">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">
                            <a class="navbar-brand" href="{{ route('home') }}">
                                {{ config('app.name', 'Laravel') }}
                            </a>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                            <li class="nav-item">
                                <a class="nav-link active py-3" aria-current="page" href="{{ route('home') }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Home">
                                    <i class="bi bi-house-door-fill nav-icon"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active py-3 addIcon" data-bs-toggle="modal"
                                    data-bs-target="#addMenuModal">
                                    <i class="bi bi-plus-square-fill nav-icon"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active py-3" href="{{ route('banner.index') }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Banners">
                                    <i class="bi bi-images nav-icon"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active py-3" href="{{ route('navigation.index') }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Navigation">
                                    <i class="bi bi-list nav-icon"></i>
                                </a>
                            </li>
                            @if (!in_array('SEO', $disabledMenus))
                                <li class="nav-item">
                                    <a class="nav-link active py-3" href="{{ route('seo.index') }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="SEO">
                                        <i class="bi bi-reception-4 nav-icon"></i>
                                    </a>
                                </li>
                            @endif
                            @if (!in_array('TESTIMONIAL', $disabledMenus))
                                <li class="nav-item">
                                    <a class="nav-link active py-3" href="{{ route('testimonial.index') }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Testimonial">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-pencil-line">
                                            <path d="M12 20h9" />
                                            <path
                                                d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z" />
                                            <path d="m15 5 3 3" />
                                        </svg>
                                    </a>
                                </li>
                            @endif
                            @if (!in_array('GALLERY', $disabledMenus))
                                <li class="nav-item">
                                    <a class="nav-link active py-3" href="{{ route('gallery.index') }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Gallery">
                                        <i class="bi bi bi-file-image nav-icon"></i>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link active py-3" href="{{ route('settings.index') }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Settings">
                                    <i class="bi bi-gear-fill nav-icon"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="nav-item mx-2">
                        <a class=" btn btn-outline-danger my-2" style="width: 100%" href="{{ route('optimize:clear') }}">optimize</a>
                        {{-- <a class=" btn btn-outline-danger my-2" style="width: 100%" href="{{ route('sitemap') }}">sitemap</a> --}}
                        <a class=" btn btn-outline-danger" style="width: 100%" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                            <i class="bi bi-power nav-icon"></i>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>

                </div>
            </div>
        </nav>

        <main class="">
            @yield('content')
        </main>
    </div>

    @include('layouts.WEBSITE.ADMIN.INCLUDES.footer')
    @stack('scripts')

</body>
<!-- Menu Modal Start -->
<div class="modal fade" id="addMenuModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addMenuModalLabel">
                    <i class="bi bi-plus-square-fill nav-icon"></i>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card" style="width:;">
                    {{-- <div class="card-header">
                      Featured
                    </div> --}}
                    {{-- <ul class="list-group list-group-flush"> --}}
                    {{-- <li class="list-group-item">An item</li> --}}
                    {{-- <a class="nav-link active p-3" href="{{ route('post.index') }}" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Post">
                        <i class="bi bi-mailbox nav-icon"></i>
                    </a> --}}
                    <a class="nav-link active p-3" href="{{ route('page.index') }}" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="Pages">
                    <i class="bi bi-file-earmark-fill nav-icon"></i>
                    </a>
                    {{-- </ul> --}}
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Understood</button> --}}
            </div>
        </div>
    </div>
</div>
<!-- Menu Modal End -->

</html>
