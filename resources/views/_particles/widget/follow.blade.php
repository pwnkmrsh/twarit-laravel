@php
    $social_links = collect(config('buzzy.social_links'))
        ->filter(function ($item, $provider) {
            return get_buzzy_config($provider . 'page') > '';
        })
        ->map(function ($item, $provider) {
            return array_merge($item, [
                'url' => get_buzzy_config($provider . 'page', $item['url'] ?? ''),
                'follow_text' => get_buzzy_config($provider . 'page_btn_text', $item['follow_text'] ?? ''),
            ]);
        });
@endphp
@if (count($social_links) > 0)
    <div class="sidebar-block clearfix">
        <div class="colheader sea">
            <h3 class="header-title">{{ trans('index.ccommunity') }}</h3>
        </div>
        <div class="social_links">
            @foreach ($social_links as $provider => $item)
                <a href="{!! $item['url'] !!}" class="social-{{ $provider }}" target="_blank" rel="nofollow">
                    @php($follow_text = !empty($item['follow_text']) ? $item['follow_text'] : get_social_links_trans($item))
                    <img width="24px" height="24px" src="{{ url($item['icon']) }}" alt="{{ $follow_text }}" />
                    <span>{{ $follow_text }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
