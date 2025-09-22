<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4" style="z-index: auto;"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('zone.index') }}" target="_blank">
            {{-- <img src="./img/logo-ct-dark.png" class="navbar-brand-img h-100" alt="main_logo"> --}}
            <span class="ms-1 font-weight-bold">Rupa Water Foundation</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'zone.index' ? 'active' : '' }}"
                    href="{{ route('zone.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                       <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            

            {{-- Coding Rupa Content Start--}}
            {{-- Home Content-> About us , Gallery --}}
            <li class="nav-item mt-3 d-flex align-items-center">
                <div class="ps-4">
                    {{-- <i class="fab fa-laravel" style="color: #f4645f;"></i> --}}
                </div>
                <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-0">Home</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'about_us.index' ? 'active' : '' }}"
                    href="{{ route('about_us.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-badge text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">About Us</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'gallery.index' ? 'active' : '' }}"
                    href="{{ route('gallery.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-album-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Gallery</span>
                </a>
            </li>
            {{-- Employee Management --}}
            <li class="nav-item mt-3 d-flex align-items-center">
                <div class="ps-4">
                    {{-- <i class="fab fa-laravel" style="color: #f4645f;"></i> --}}
                </div>
                <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-0">Employee Management</h6>
            </li>
             <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'zone.employee.index' ? 'active' : '' }}"
                    href="{{ route('zone.employee.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Employee List</span>
                </a>
            </li>
            {{-- Zone Management --}}
            <li class="nav-item mt-3 d-flex align-items-center">
                <div class="ps-4">
                    {{-- <i class="fab fa-laravel" style="color: #f4645f;"></i> --}}
                </div>
                <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-0">Zone Management</h6>
            </li>
            
             <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'zone.location.index' ? 'active' : '' }}"
                    href="{{ route('zone.location.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-pin-3 text-dark text-sm opacity-10"></i>   <!-- Nucleo location pin -->
                    </div>
                    <span class="nav-link-text ms-1">Zone Wise Location</span>
                </a>
            </li>
          

            {{-- Inspection --}}
            <li class="nav-item mt-3 d-flex align-items-center">
                <div class="ps-4">
                    {{-- <i class="fab fa-laravel" style="color: #f4645f;"></i> --}}
                </div>
                <h6 class="ms-2 text-uppercase text-xs font-weight-bolder opacity-6 mb-0">Inspection Management</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'inspection.index' ? 'active' : '' }}"
                    href="{{ route('inspection.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-check-bold text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Inspection</span>
                </a>
            </li>
            {{-- Coding Rupa Content End--}}
        </ul>
         <ul class="navbar-nav">
             <li class="nav-item d-flex align-items-center justify-content-center w-100 px-3">
                    <form role="form" method="post" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="btn btn-primary text-white font-weight-bold w-100 d-flex align-items-center justify-content-center rounded">
                           <i class="fa fa-sign-out-alt me-sm-1"></i>
                            <span class="">Log out</span>
                           
                        </a>
                    </form>
                </li>
                
            </ul>
    </div>
    <div>
        
    </div>
   
</aside>