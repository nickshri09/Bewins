@if (isset($activeBanners) && $activeBanners->isNotEmpty())
    <section class="live-banner-carousel mb-4" aria-label="{{ _lang('Announcements') }}">
        <div class="live-banner-slides">
            @foreach ($activeBanners as $banner)
                <article class="live-banner-slide {{ $loop->first ? 'is-active' : '' }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                    @if ($banner->image)
                        @if ($banner->button_url)
                            <a href="{{ $banner->button_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $banner->title }}">
                                <img class="live-banner-artwork" src="{{ asset('uploads/live-banners/' . $banner->image) }}" alt="{{ $banner->title }}">
                            </a>
                        @else
                            <img class="live-banner-artwork" src="{{ asset('uploads/live-banners/' . $banner->image) }}" alt="{{ $banner->title }}">
                        @endif
                    @else
                        <div class="live-banner-text">
                            @if ($banner->subtitle)<div class="live-banner-subtitle">{{ $banner->subtitle }}</div>@endif
                            <h2>{{ $banner->title }}</h2>
                            @if ($banner->description)<p>{{ $banner->description }}</p>@endif
                            @if ($banner->button_text && $banner->button_url)
                                <a class="live-banner-button" href="{{ $banner->button_url }}" target="_blank" rel="noopener noreferrer">{{ $banner->button_text }} <span aria-hidden="true">→</span></a>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>

        @if ($activeBanners->count() > 1)
            <button type="button" class="live-banner-control live-banner-previous" aria-label="{{ _lang('Previous banner') }}">‹</button>
            <button type="button" class="live-banner-control live-banner-next" aria-label="{{ _lang('Next banner') }}">›</button>
            <div class="live-banner-indicators" aria-label="{{ _lang('Choose banner') }}">
                @foreach ($activeBanners as $banner)
                    <button type="button" class="live-banner-indicator {{ $loop->first ? 'is-active' : '' }}" aria-label="{{ _lang('Show banner') }} {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                @endforeach
            </div>
        @endif
    </section>

    <style>
        .live-banner-carousel{position:relative;overflow:hidden;border-radius:16px;background:#142c4d;box-shadow:0 8px 24px rgba(22,54,92,.16)}
        .live-banner-slide{display:none}.live-banner-slide.is-active{display:block}
        .live-banner-artwork{display:block;width:100%;height:auto;max-height:620px;object-fit:contain;margin:0 auto}
        .live-banner-text{min-height:280px;padding:48px 56px;color:#fff;background:linear-gradient(110deg,#142c4d,#1687d4)}
        .live-banner-subtitle{margin-bottom:8px;color:#c8eaff;font-weight:600;letter-spacing:.04em}
        .live-banner-text h2{margin:0 0 12px;color:#fff;font-size:clamp(24px,3vw,40px);font-weight:700}
        .live-banner-text p{margin:0;max-width:760px;color:#f3f8ff;font-size:16px;line-height:1.6;white-space:pre-line}
        .live-banner-button{display:inline-flex;gap:12px;align-items:center;margin-top:22px;padding:11px 22px;border-radius:24px;background:#0788f8;color:#fff;font-weight:700;text-decoration:none}
        .live-banner-button:hover{color:#fff;text-decoration:none;filter:brightness(1.08)}
        .live-banner-control{position:absolute;top:50%;z-index:2;transform:translateY(-50%);width:42px;height:42px;border:0;border-radius:50%;background:rgba(255,255,255,.92);color:#18395b;font-size:32px;line-height:1;cursor:pointer}
        .live-banner-previous{left:18px}.live-banner-next{right:18px}
        .live-banner-indicators{position:absolute;bottom:14px;left:50%;z-index:2;display:flex;gap:8px;transform:translateX(-50%)}
        .live-banner-indicator{width:10px;height:10px;padding:0;border:0;border-radius:50%;background:rgba(255,255,255,.6);cursor:pointer}
        .live-banner-indicator.is-active{width:24px;border-radius:8px;background:#fff}
        @media(max-width:767px){.live-banner-text{min-height:220px;padding:32px 24px}.live-banner-control{width:34px;height:34px;font-size:27px}.live-banner-previous{left:8px}.live-banner-next{right:8px}.live-banner-indicators{bottom:8px}}
    </style>

    @if ($activeBanners->count() > 1)
        <script>
            (function () {
                document.querySelectorAll('.live-banner-carousel').forEach(function (carousel) {
                    var slides = Array.from(carousel.querySelectorAll('.live-banner-slide'));
                    var indicators = Array.from(carousel.querySelectorAll('.live-banner-indicator'));
                    var current = 0;
                    var timer;

                    function show(index) {
                        current = (index + slides.length) % slides.length;
                        slides.forEach(function (slide, i) {
                            slide.classList.toggle('is-active', i === current);
                            slide.setAttribute('aria-hidden', i === current ? 'false' : 'true');
                        });
                        indicators.forEach(function (indicator, i) {
                            indicator.classList.toggle('is-active', i === current);
                            indicator.setAttribute('aria-current', i === current ? 'true' : 'false');
                        });
                    }

                    function start() {
                        window.clearInterval(timer);
                        timer = window.setInterval(function () { show(current + 1); }, 3000);
                    }

                    carousel.querySelector('.live-banner-previous').addEventListener('click', function () { show(current - 1); start(); });
                    carousel.querySelector('.live-banner-next').addEventListener('click', function () { show(current + 1); start(); });
                    indicators.forEach(function (indicator, index) {
                        indicator.addEventListener('click', function () { show(index); start(); });
                    });
                    carousel.addEventListener('mouseenter', function () { window.clearInterval(timer); });
                    carousel.addEventListener('mouseleave', start);
                    start();
                });
            })();
        </script>
    @endif
@endif
