@extends('frontend.layouts.master')

@section('meta_title', $seo_setting['contact']['seo_title'])
@section('meta_description', $seo_setting['contact']['seo_description'])
@section('meta_keywords', $seo_setting['contact']['seo_keywords'])

@php
    extract(getSiteMenus());
@endphp

@section('header')
   @include('frontend.layouts.headers.header-inner', ['main_menu' => $main_menu])
@endsection


@section('content')

<!-- hero area start -->
<div class="tp-contact-us-ptb p-relative">
    <div class="tp-career-shape-1">
        <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="123" height="130" viewBox="0 0 123 130" fill="none">
                <path d="M58.2803 1.15449C63.3023 14.3017 71.049 54.3533 48.1082 67.0973C36.1831 73.4283 11.7107 77.3064 2.37778 43.9355C-1.14293 31.3468 9.61622 20.8908 32.0893 28.8395C45.055 33.4255 76.4207 44.0467 90.5787 70.0771C98.0511 83.8154 104.166 111.84 99.1745 129.671M99.1745 129.671C100.942 121.014 108.128 104.495 122.737 107.673M99.1745 129.671C100.181 123.978 97.0522 110.014 76.485 99.698M75.3644 33.2431C80.479 35.6688 96.6446 46.4742 101.81 64.2891" stroke="black" stroke-width="1.5" />
            </svg>
        </span>
    </div>
    <div class="container container-1230">
        <div class="ar-about-us-4-hero-ptb">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="tp-contact-us-heading tp_fade_anim" data-delay=".3">
                        <div class="ar-about-us-4-title-box d-flex align-items-center mb-20">
                            <span class="tp-section-subtitle pre tp_fade_anim">
                                {{ __('frontend.contact_us') }}
                            </span>
                            <div class="ar-about-us-4-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="81" height="9" viewBox="0 0 81 9" fill="none">
                                    <rect y="4" width="80" height="1" fill="#000" />
                                    <path d="M77 7.96366L80.5 4.48183L77 1" stroke="#000" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="tp-career-title pb-30 insert-breadcrumb-shape">
                            {!! clean(pureText(__('frontend.your_creative_journey_starts_here'))) !!}
                        </h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-8">
                    <div class="tp-faq-text tp_fade_anim">
                        <p class="m-0">{!! clean(pureText(__('frontend.contact_us_description'))) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tp-contact-us-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="tp-contact-us-text smooth">
                        <a href="#down">
                            <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="21" viewBox="0 0 15 21" fill="none">
                                    <rect x="6.25781" width="1.5" height="21" fill="#000" />
                                    <path d="M14.1641 13.6257C10.28 13.6257 7.13714 16.9239 7.13714 21" stroke="#000" stroke-width="1.5" stroke-miterlimit="10" />
                                    <path d="M7.13672 21C7.13672 16.9239 3.99384 13.6257 0.109797 13.6257" stroke="#000" stroke-width="1.5" stroke-miterlimit="10" />
                                </svg>
                                {{ __('frontend.scroll_to_explore') }}
                            </p>
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="tp-contact-us-text d-none d-md-block text-md-end">
                        <p>{{ __('frontend.see_in_map_our_office') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- hero area end -->



 <!-- contact us form area start -->
<div id="down" class="tp-contact-us-form-ptb pt-60 pb-120">
    <div class="container container-1750">
        <div class="tp-contact-us-form-wrapper">
            <div class="row">
                <div class="col-lg-6">
                    <div class="tp-contact-us-map p-relative">
                        <iframe data-src="{{ $settings->site_address_link }}" width="600" height="450" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="lazy-iframe" style="border:0;"></iframe>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="tp-contact-us-wrap">
                        <h4 class="tp-contact-us-title mb-55">{{ __('frontend.send_a_message') }}</h4>
                        <form class="ajax-contact-form" method="POST" action="{{ route('contact-message.store') }}">
                           @csrf
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="tp-contact-form-input mb-20">
                                        <label for="name">{{ __('frontend.full_name') }}</label>
                                        <input id="name" name="name" type="text" placeholder="{{ __('frontend.full_name') }}" value="{{ old('name') }}">
                                        <span class="text-danger error-text name-error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="tp-contact-form-input mb-20">
                                        <label for="email">{{ __('frontend.email') }}</label>
                                        <input id="email" name="email" type="email" placeholder="{{ __('frontend.your_email') }}" value="{{ old('email') }}">
                                        <span class="text-danger error-text email-error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="tp-contact-form-input mb-20">
                                        <label for="subject">{{ __('frontend.subject') }}</label>
                                        <input id="subject" name="subject" type="text" placeholder="{{ __('frontend.subject') }}" value="{{ old('subject') }}">
                                        <span class="text-danger error-text subject-error"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="tp-contact-form-input mb-20">
                                        <label for="message">{{  __('frontend.your_message') }}</label>
                                        <textarea id="message" name="message" placeholder="{{  __('frontend.your_message') }}">{{old('message')}}</textarea>
                                        <span class="text-danger error-text message-error"></span>
                                    </div>
                                    @if ($settings?->recaptcha_status == 1)
                                    <div class="form-group mb-3 col-12">
                                        <div class="g-recaptcha" data-sitekey="{{ $settings?->recaptcha_site_key }}"></div>
                                        <span class="text-danger error-text g-recaptcha-response-error"></span>
                                    </div>
                                    @endif
                                    <div class="tp-contact-form-btn">
                                        <button class="w-100" type="submit" class="contact-submit-btn">
                                            <span>
                                                <span class="text-1">{{ __('frontend.send_message') }}</span>
                                                <span class="text-2">{{ __('frontend.send_message') }}</span>
                                            </span>
                                        </button>
                                        <div class="ajax-message mt-5"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- contact us form area end -->

<!-- about area start -->
<div class="cn-contactform-support-area mb-140">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="cn-contactform-support-bg d-flex align-items-center justify-content-center" data-background="{{ asset('admin/img/default/common/contact-us-shape.png') }}">
                    <div class="cn-contactform-support-text text-center">
                        <span>{{ __('frontend.contact_us_message')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- about area end -->


<!-- contact area start -->
<div class="tp-contact-us-info-area pb-120">
    <div class="container container-1230">
        <div class="row">

            @if ($contacts->count())
                @php
                    $contact = $contacts->first();
                    $data = (object) $contact->value;
                @endphp
                <div class="col-12">
                    <div class="row modern-contact-card align-items-center g-3" style="background: #fff; border-radius: 1.5rem; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.10); padding: 2rem 1.5rem; margin:0;">
                        <div class="col-12 col-md-3 d-flex flex-column align-items-center justify-content-center text-center mb-3 mb-md-0">
                            <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.07); margin-bottom: 0.5rem;">
                                <img src="{{ asset($data->image) }}" alt="{{ $data->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <h4 style="font-size: 1.1rem; font-weight: 700; color: #222; margin-bottom:0; letter-spacing: -0.5px;">{{ $data->name }}</h4>
                        </div>
                        <div class="col-12 col-md-3 d-flex flex-column align-items-center justify-content-center text-center mb-3 mb-md-0">
                            <span style="font-size: 0.95rem; color: #888; font-weight: 500;">Email</span>
                            <a href="mailto:{{ $data->email }}" style="color: #4f8cff; font-weight: 500; word-break: break-all; text-decoration: none; font-size: 1.05rem;">{{ $data->email }}</a>
                        </div>
                        <div class="col-12 col-md-3 d-flex flex-column align-items-center justify-content-center text-center mb-3 mb-md-0">
                            <span style="font-size: 0.95rem; color: #888; font-weight: 500;">Phone</span>
                            <a href="tel:{{ $data->phone }}" style="color: #222; font-weight: 500; text-decoration: none; font-size: 1.05rem;">{{ $data->phone }}</a>
                        </div>
                        <div class="col-12 col-md-3 d-flex flex-column align-items-center justify-content-center text-center">
                            <span style="font-size: 0.95rem; color: #888; font-weight: 500;">Location</span>
                            @if ($data->address_link)
                            <a class="btn btn-gradient w-100 mt-2" target="_blank" href="{{ $data->address_link }}" style="background: linear-gradient(90deg, #4f8cff 0%, #38e7b0 100%); color: #fff; border: none; border-radius: 0.75rem; padding: 0.65rem 1.1rem; font-weight: 600; font-size: 1rem; box-shadow: 0 2px 8px rgba(79,140,255,0.08); transition: background 0.2s; text-align: center; text-decoration: none;">
                                <span style="display: inline-block; vertical-align: middle;">
                                    <svg width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 0.5rem; vertical-align: middle;"><path d="M9 1.5a7.5 7.5 0 1 1 0 15a7.5 7.5 0 0 1 0-15zm0 2.25a5.25 5.25 0 1 0 0 10.5a5.25 5.25 0 0 0 0-10.5zm0 2.25a3 3 0 1 1 0 6a3 3 0 0 1 0-6z" fill="#fff"/></svg>
                                    {{ __('frontend.view_location') }}
                                </span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
<!-- contact area end -->

@endsection

@section('footer')
    @include('frontend.layouts.footers.footer-1', ['footer_menu_one' => $footer_menu_one, 'footer_menu_two' => $footer_menu_two])
@endsection


@push('js')
<script>
    if(('.insert-breadcrumb-shape').length){
        const shapeURL = "{{ asset('admin/img/default/common/breadcrumb-title-shape.png') }}"
        const shapeHTML = `<img src="${shapeURL}" alt="{{ $seo_setting['portfolio']['seo_title'] }}">`;
        $('.insert-breadcrumb-shape').each(function () {
            let $wrapper = $('<div>').html($(this).html());

            $wrapper.find('span').each(function () {
                $(this).replaceWith(shapeHTML);
            });

            $(this).html($wrapper.html());
        });
    }

    // Lazy load Google Maps iframe for better performance
    document.addEventListener('DOMContentLoaded', function() {
        const lazyIframe = document.querySelector('.lazy-iframe');
        if (lazyIframe) {
            // Use Intersection Observer for efficient lazy loading
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const iframe = entry.target;
                        iframe.src = iframe.dataset.src;
                        iframe.classList.remove('lazy-iframe');
                        observer.unobserve(iframe);
                    }
                });
            }, {
                rootMargin: '50px' // Start loading 50px before iframe comes into view
            });

            observer.observe(lazyIframe);
        }
    });
</script>
@endpush
