@if (isset($activeTickers) && $activeTickers->isNotEmpty())
    <div class="dashboard-ticker mb-4" role="region" aria-label="{{ _lang('Announcements and updates') }}">
        <div class="dashboard-ticker__label"><i class="fas fa-bullhorn" aria-hidden="true"></i> {{ _lang('Updates') }}</div>
        <div class="dashboard-ticker__viewport">
            <div class="dashboard-ticker__track">
                @for ($copy = 0; $copy < 2; $copy++)
                    <div class="dashboard-ticker__group" @if ($copy) aria-hidden="true" @endif>
                        @foreach ($activeTickers as $ticker)
                            <span class="dashboard-ticker__message">{{ $ticker->message }}</span>
                            <span class="dashboard-ticker__separator" aria-hidden="true">✦</span>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
    </div>
    <style>
        .dashboard-ticker{display:flex;align-items:stretch;min-height:50px;overflow:hidden;border-radius:8px;background:#f2efff;border:1px solid #d2c8ff;color:#24236f}
        .dashboard-ticker__label{z-index:1;display:flex;align-items:center;gap:9px;flex-shrink:0;padding:0 18px;background:linear-gradient(110deg,#7138f4,#5430e8);color:#fff;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.04em}
        .dashboard-ticker__viewport{display:flex;align-items:center;min-width:0;overflow:hidden}
        .dashboard-ticker__track{display:flex;width:max-content;will-change:transform;animation:dashboard-ticker-scroll var(--ticker-duration,30s) linear infinite}
        .dashboard-ticker__group{display:flex;flex:0 0 auto;align-items:center;gap:22px;padding:0 24px;white-space:nowrap}
        .dashboard-ticker__message{font-size:14px;font-weight:600}
        .dashboard-ticker__separator{color:#7044f5;font-size:14px}
        @keyframes dashboard-ticker-scroll{from{transform:translateX(0)}to{transform:translateX(var(--ticker-distance, -100vw))}}
        @media(max-width:575px){.dashboard-ticker__label{padding:0 12px;font-size:11px}.dashboard-ticker__group{gap:14px;padding:0 16px}.dashboard-ticker__message{font-size:12px}}
    </style>
    <script>
        (function () {
            document.querySelectorAll('.dashboard-ticker').forEach(function (ticker) {
                var track = ticker.querySelector('.dashboard-ticker__track');
                var groups = track.querySelectorAll('.dashboard-ticker__group');
                var firstGroup = groups[0];
                var viewport = ticker.querySelector('.dashboard-ticker__viewport');
                var originalItems = Array.from(firstGroup.children);
                while (firstGroup.scrollWidth < viewport.clientWidth + 40) {
                    originalItems.forEach(function (item) {
                        firstGroup.appendChild(item.cloneNode(true));
                    });
                }
                groups[1].innerHTML = firstGroup.innerHTML;
                var distance = firstGroup.scrollWidth;
                track.style.setProperty('--ticker-distance', '-' + distance + 'px');
                track.style.setProperty('--ticker-duration', Math.max(12, distance / 70) + 's');
            });
        })();
    </script>
@endif
