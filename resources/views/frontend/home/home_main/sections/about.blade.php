<div class="dgm-about-area pt-120 pb-120 position-relative overflow-hidden modern-about-bg">
    <div class="container container-1230">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <div class="dgm-about-thumb-wrap p-relative modern-glass-card">
                    <div class="about-image-main-wrapper modern-img-shadow">
                        <img class="tp_fade_anim" data-delay=".3" data-fade-from="left" src="{{ asset($default_content?->image) }}" alt="{{ $content?->title }}">
                    </div>
                    <img class="dgm-about-thumb-1 tp_fade_anim" data-speed="1.1" data-delay=".5" src="{{ asset($default_content?->image_2) }}" alt="{{ $content?->title }}">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dgm-about-right modern-about-content">
                    <div class="dgm-about-title-box z-index-1 mb-35">
                        <span class="tp-section-subtitle subtitle-black mb-15 tp_fade_anim" data-delay=".3">{!! clean(pureText($content?->subtitle)) !!}</span>
                        <h2 class="tp-section-title-grotesk tp_fade_anim the-title modern-about-title" data-delay=".5">
                           {!! clean(pureText($content?->title)) !!}
                        </h2>
                    </div>
                    <div class="dgm-about-content">
                        <div class="tp_fade_anim" data-delay=".3">
                            <p class="modern-about-desc">
                                {!! clean(pureText($content?->description)) !!}
                            </p>
                        </div>
                        @if (!empty($content?->btn_text))
                        <div class="tp_fade_anim" data-delay=".5">
                            <a class="tp-btn-yellow-green green-solid btn-60 mb-50 modern-about-btn" href="{{ url($default_content?->btn_url ?? '#') }}">
                                <span>
                                    <span class="text-1">{{ $content?->btn_text }}</span>
                                    <span class="text-2">{{ $content?->btn_text }}</span>
                                </span>
                                <i>
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="9" cy="9" r="8.5" stroke="currentcolor" stroke-width="1.5"/>
                                        <path d="M6 9H12M12 9L10 7M12 9L10 11" stroke="currentcolor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </i>
                            </a>
                        </div>
                        @endif
                        <div class="dgm-about-review-wrap tp_fade_anim modern-review-card" data-delay=".6">
                            <div class="dgm-about-review-box d-inline-flex align-items-center">
                                <div class="dgm-about-review">
                                    <h4>{{ __('frontend.review_number', ['number' => 4.9]) }}</h4>
                                    <span>{{ __('frontend.review_count', ['count' => 24]) }}</span>
                                </div>
                                <div class="dgm-about-ratting">
                                    <h4>{{ __('frontend.average_rating') }}</h4>
                                    <div class="dgm-about-ratting-icon">
                                        @for ($i = 0; $i < 5; $i++)
                                        <span>
                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8 0L10.24 4.27L15.32 4.84L11.55 8.04L12.63 13L8 10.37L3.37 13L4.45 8.04L0.68 4.84L5.76 4.27L8 0Z" fill="currentcolor" />
                                            </svg>
                                        </span>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
