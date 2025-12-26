@include('shop::layouts.sidebar')
@include('appearance::layouts.sidebar')
@include('menu::layouts.sidebar')
@include('frontend::layouts.sidebar')
@include('page::layouts.sidebar')
@include('core::layout.partials.sidebar-heading', ['title' => 'Manage Contents'])
<li class="app-sidebar-menu-item has-dropdown">
    <a href="javascript:void(0);" class="menu-link d-flex align-items-center">
        <span class="menu-icon flex-shrink-0">
            <svg width="18" height="18" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 6.16C16.94 6.15 16.87 6.15 16.81 6.16C15.43 6.11 14.33 4.98 14.33 3.58C14.33 2.15 15.48 1 16.91 1C18.34 1 19.49 2.16 19.49 3.58C19.48 4.98 18.38 6.11 17 6.16Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M15.9699 13.44C17.3399 13.67 18.8499 13.43 19.9099 12.72C21.3199 11.78 21.3199 10.24 19.9099 9.30004C18.8399 8.59004 17.3099 8.35003 15.9399 8.59003" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M4.96998 6.16C5.02998 6.15 5.09998 6.15 5.15998 6.16C6.53998 6.11 7.63998 4.98 7.63998 3.58C7.63998 2.15 6.48998 1 5.05998 1C3.62998 1 2.47998 2.16 2.47998 3.58C2.48998 4.98 3.58998 6.11 4.96998 6.16Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M5.99994 13.44C4.62994 13.67 3.11994 13.43 2.05994 12.72C0.649941 11.78 0.649941 10.24 2.05994 9.30004C3.12994 8.59004 4.65994 8.35003 6.02994 8.59003" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M11 13.63C10.94 13.62 10.87 13.62 10.81 13.63C9.42996 13.58 8.32996 12.45 8.32996 11.05C8.32996 9.61997 9.47995 8.46997 10.91 8.46997C12.34 8.46997 13.49 9.62997 13.49 11.05C13.48 12.45 12.38 13.59 11 13.63Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M8.08997 16.78C6.67997 17.72 6.67997 19.26 8.08997 20.2C9.68997 21.27 12.31 21.27 13.91 20.2C15.32 19.26 15.32 17.72 13.91 16.78C12.32 15.72 9.68997 15.72 8.08997 16.78Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
        </span>
        <span class="menu-title flex-grow-1">
            {{ __('sidebar.manage_users') }}
        </span>
        <span class="menu-arrow flex-shrink-0 d-flex align-items-center justify-content-center">
            <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    </a>

    <ul class="app-sidebar-submenu">
        <li class="app-sidebar-menu-item">
            <a href="{{ route('admin.user.index') }}" class="menu-link d-flex align-items-center {{ activeRoute(['admin.user.index', 'admin.user.create', 'admin.user.edit']) }}">
                <span class="menu-title flex-grow-1">
                    {{ __('sidebar.user_list') }}
                </span>
            </a>
        </li>
        <li class="app-sidebar-menu-item">
            <a href="{{ route('admin.user.send-bulk-email') }}" class="menu-link d-flex align-items-center {{ activeRoute(['admin.user.send-bulk-email']) }}">
                <span class="menu-title flex-grow-1">
                    {{ __('sidebar.send_bulk_email') }}
                </span>
            </a>
        </li>
    </ul>
</li>

{{-- 3D Quote Mesh Repair Section --}}
<li class="app-sidebar-menu-item has-dropdown">
    <a href="javascript:void(0);" class="menu-link d-flex align-items-center">
        <span class="menu-icon flex-shrink-0">
            <svg width="18" height="18" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20.5 7.27783L12 12.0001M12 12.0001L3.49997 7.27783M12 12.0001L12 21.5001M21 16.0586V7.94153C21 7.59889 21 7.42757 20.9495 7.27477C20.9049 7.13959 20.8318 7.01551 20.7354 6.91082C20.6263 6.79248 20.4766 6.70928 20.177 6.54288L12.777 2.43177C12.4934 2.27421 12.3516 2.19543 12.2015 2.16454C12.0685 2.13721 11.9315 2.13721 11.7986 2.16454C11.6484 2.19543 11.5066 2.27421 11.223 2.43177L3.82297 6.54288C3.52345 6.70928 3.37369 6.79248 3.26463 6.91082C3.16816 7.01551 3.09515 7.13959 3.05048 7.27477C3 7.42757 3 7.59889 3 7.94153V16.0586C3 16.4013 3 16.5726 3.05048 16.7254C3.09515 16.8606 3.16816 16.9847 3.26463 17.0893C3.37369 17.2077 3.52345 17.2909 3.82297 17.4573L11.223 21.5684C11.5066 21.726 11.6484 21.8047 11.7986 21.8356C11.9315 21.863 12.0685 21.863 12.2015 21.8356C12.3516 21.8047 12.4934 21.726 12.777 21.5684L20.177 17.4573C20.4766 17.2909 20.6263 17.2077 20.7354 17.0893C20.8318 16.9847 20.9049 16.8606 20.9495 16.7254C21 16.5726 21 16.4013 21 16.0586Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="menu-title flex-grow-1">
            3D Quote
        </span>
        <span class="menu-arrow flex-shrink-0 d-flex align-items-center justify-content-center">
            <svg width="6" height="10" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 9L5 5L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    </a>

    <ul class="app-sidebar-submenu">
        <li class="app-sidebar-menu-item">
            <a href="{{ route('admin.mesh-repair.dashboard') }}" class="menu-link d-flex align-items-center {{ activeRoute(['admin.mesh-repair.dashboard']) }}">
                <span class="menu-title flex-grow-1">
                    Dashboard
                </span>
            </a>
        </li>
        <li class="app-sidebar-menu-item">
            <a href="{{ route('admin.mesh-repair.logs') }}" class="menu-link d-flex align-items-center {{ activeRoute(['admin.mesh-repair.logs']) }}">
                <span class="menu-title flex-grow-1">
                    Repair Logs
                </span>
            </a>
        </li>
        <li class="app-sidebar-menu-item">
            <a href="{{ route('admin.printing-orders.index') }}" class="menu-link d-flex align-items-center {{ activeRoute(['admin.printing-orders.index', 'admin.printing-orders.show']) }}">
                <span class="menu-title flex-grow-1">
                    Printing Orders
                </span>
            </a>
        </li>
        <li class="app-sidebar-menu-item">
            <a href="{{ route('admin.mesh-repair.settings') }}" class="menu-link d-flex align-items-center {{ activeRoute(['admin.mesh-repair.settings']) }}">
                <span class="menu-title flex-grow-1">
                    Settings
                </span>
            </a>
        </li>
    </ul>
</li>

@include('blog::layouts.sidebar')
@include('pricing::layouts.sidebar')
@include('team::layouts.sidebar')
@include('faqs::layouts.sidebar')
@include('social::layouts.sidebar')
@include('service::layouts.sidebar')
@include('portfolio::layouts.sidebar')
@include('brand::layouts.sidebar')
@include('translation::layouts.sidebar')
@include('language::layouts.layout')
@include('settings::layouts.sidebar')
@include('core::layout.partials.sidebar-heading', ['title' => __('sidebar.utility')])
@include('newsletter::layouts.sidebar')
@include('testimonial::layouts.sidebar')
@include('contactmessage::layouts.sidebar')
