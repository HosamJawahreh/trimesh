<!-- step area start -->
<div class="dgm-step-area pt-50 pb-80">
    <div class="container container-1230">
        <div class="row">
            <div class="col-12">
                <div class="mb-60">
                    <h4 class="dgm-step-title mb-25"> {!! clean(pureText($content?->title)) !!}</h4>

                </div>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="process-steps-wrapper">
            <div class="row position-relative">
                <!-- Progress Line Background -->
                <div class="progress-line-bg"></div>
                <div class="progress-line-fill"></div>

                @for ($index = 1; $index <= 3; $index++)
                    <div class="col-lg-4 col-md-6">
                        <div class="process-step-item" data-step="{{ $index }}">
                            <div class="step-number-wrapper">
                                <span class="step-number">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h4 class="step-title">{{ $content?->{'process_title_' . $index} }}</h4>
                            <p class="step-description">
                                {!! clean(pureText($content?->{'process_description_' . $index})) !!}
                            </p>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Get Quote Button -->
            <div class="row mt-50">
                <div class="col-12 text-center">
                    <a href="{{ route('quote') }}" class="tp-btn-yellow-green green-solid get-quote-btn">
                        <i>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 17H15M9 13H15M9 9H10M13 3H8.2C7.0799 3 6.51984 3 6.09202 3.21799C5.71569 3.40973 5.40973 3.71569 5.21799 4.09202C5 4.51984 5 5.0799 5 6.2V17.8C5 18.9201 5 19.4802 5.21799 19.908C5.40973 20.2843 5.71569 20.5903 6.09202 20.782C6.51984 21 7.0799 21 8.2 21H15.8C16.9201 21 17.4802 21 17.908 20.782C18.2843 20.5903 18.5903 20.2843 18.782 19.908C19 19.4802 19 18.9201 19 17.8V9M13 3L19 9M13 3V7.4C13 7.96005 13 8.24008 13.109 8.45399C13.2049 8.64215 13.3578 8.79513 13.546 8.89101C13.7599 9 14.0399 9 14.6 9H19" stroke="currentcolor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </i>
                        <span>
                            <span class="text-1">Get Quote</span>
                            <span class="text-2">Get Quote</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.process-steps-wrapper {
    position: relative;
}

.process-steps-wrapper .row {
    position: relative;
}

/* Progress Line Background */
.progress-line-bg {
    position: absolute;
    top: 30px;
    left: 10%;
    right: 10%;
    height: 4px;
    background: rgba(59, 130, 246, 0.15);
    z-index: 0;
    border-radius: 10px;
}

/* Progress Line Fill */
.progress-line-fill {
    position: absolute;
    top: 30px;
    left: 10%;
    height: 4px;
    width: 0;
    background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
    z-index: 1;
    border-radius: 10px;
    transition: width 2s ease-out;
}

.process-steps-wrapper.animate .progress-line-fill {
    width: 80%;
}

/* Step Item */
.process-step-item {
    text-align: center;
    padding: 0 15px;
    position: relative;
    z-index: 2;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.process-step-item.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Step Number */
.step-number-wrapper {
    width: 60px;
    height: 60px;
    margin: 0 auto 20px;
    background: white;
    border: 3px solid #3b82f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
    transition: all 0.4s ease;
}

.process-step-item:hover .step-number-wrapper {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(59, 130, 246, 0.35);
}

.step-number {
    font-size: 24px;
    font-weight: 700;
    color: #3b82f6;
    line-height: 1;
}

/* Step Title */
.step-title {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 15px;
    transition: color 0.3s ease;
}

.process-step-item:hover .step-title {
    color: #3b82f6;
}

/* Step Description */
.step-description {
    font-size: 14px;
    line-height: 1.6;
    color: #64748b;
    margin: 0;
}

/* Animation Delays */
.process-step-item[data-step="1"] {
    transition-delay: 0.2s;
}

.process-step-item[data-step="2"] {
    transition-delay: 0.4s;
}

.process-step-item[data-step="3"] {
    transition-delay: 0.6s;
}

/* Responsive */
@media (max-width: 991px) {
    .progress-line-bg,
    .progress-line-fill {
        display: none;
    }

    .step-number-wrapper {
        width: 50px;
        height: 50px;
    }

    .step-number {
        font-size: 20px;
    }

    .process-step-item {
        margin-bottom: 40px;
    }
}

/* Get Quote Button */
.get-quote-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    font-size: 16px;
    font-weight: 600;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease-out 0.8s;
}

.process-steps-wrapper.animate .get-quote-btn {
    opacity: 1;
    transform: translateY(0);
}

.get-quote-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stepsWrapper = document.querySelector('.process-steps-wrapper');
    const stepItems = document.querySelectorAll('.process-step-item');

    if (!stepsWrapper) return;

    // Intersection Observer for scroll animation
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Animate wrapper (progress line)
                entry.target.classList.add('animate');

                // Animate step items
                stepItems.forEach(item => {
                    item.classList.add('animate');
                });

                // Stop observing after animation
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    observer.observe(stepsWrapper);
});
</script>
<!-- step area end -->
