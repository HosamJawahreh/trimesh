<!-- header area start -->
<div id="header-sticky" class="tp-header-area tp-header-ptb tp-header-blur sticky-white-bg header-transparent tp-header-3-style">
   <div class="container container-1750">
         <div class="row align-items-center">
            <div class="col-xl-1 col-lg-5 col-5">
               <div class="tp-header-logo">
                     <a href="{{ route('home') }}">
                        <img width="180" src="{{ asset($settings->logo) }}" alt="{{ $settings->app_name }}">
                     </a>
               </div>
            </div>
            <div class="col-xl-11 col-lg-7 col-7">
               <div class="tp-header-box d-flex align-items-center justify-content-end justify-content-xl-center">
                     <div class="tp-header-menu tp-header-dropdown dropdown-white-bg d-none d-xl-flex">
                        <nav class="tp-mobile-menu-active">
                           <ul>
                              @include('frontend.layouts.all-home-pages')
                              @include('frontend.layouts.menu-items', ['menu_data' => $main_menu])
                           </ul>
                        </nav>
                     </div>

                     <div class="ss-header-right d-flex align-items-center justify-content-end gap-3 ms-auto">

                        <div class="tp-header-btn d-none d-md-flex">
                           <a class="tp-btn-yellow-green green-solid" href="{{ route('products') }}">
                              <i>
                                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M5.6316 12.7895H11.748C15.527 12.7895 16.1017 10.4155 16.7988 6.95297C16.9998 5.95427 17.1003 5.45492 16.8586 5.12224C16.6168 4.78955 16.1534 4.78955 15.2266 4.78955H3.94739" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" />
                                       <path d="M5.63158 12.7894L3.42419 2.27572C3.23675 1.52597 2.5631 1 1.79027 1H1" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" />
                                       <path d="M6.37265 12.7896H6.02618C4.8781 12.7896 3.94739 13.759 3.94739 14.955C3.94739 15.1543 4.10251 15.3159 4.29385 15.3159H13.6316" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                       <ellipse cx="7.73685" cy="16.5791" rx="1.26316" ry="1.26315" stroke="currentcolor" stroke-width="1.5" />
                                       <ellipse cx="13.6316" cy="16.5791" rx="1.26316" ry="1.26315" stroke="currentcolor" stroke-width="1.5" />
                                    </svg>
                              </i>
                              <span>
                                    <span class="text-1">{{ __('frontend.shop_now') }}</span>
                                    <span class="text-2">{{ __('frontend.shop_now') }}</span>
                              </span>
                           </a>
                        </div>

                        <div class="tp-header-bar ml-15 ">
                           <button class="tp-header-8-bar tp-offcanvas-open-btn" style="background-color: #ffffff !important; background: #ffffff !important; border: none !important; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;">
                              <span>
                                 {{ __('frontend.menu') }}
                              </span>
                              <span>
                                 <svg width="24" height="8" viewBox="0 0 24 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0H14V1.5H0V0Z" fill="currentcolor" />
                                    <path d="M0 6H24V7.5H0V6Z" fill="currentcolor" />
                                 </svg>
                              </span>
                           </button>
                        </div>
                     </div>
               </div>
            </div>
         </div>
   </div>
</div>
<!-- header area end -->
