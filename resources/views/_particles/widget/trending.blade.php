@if ($lastTrending)
    <div class="sidebar-block clearfix">
        <div class="colheader rosy">
            <h3 class="header-title">{{ trans('index.today') }} {!! trans('index.top', ['type' => '<span>' . $name . '</span>']) !!}</h3>
        </div>
        <br>
        <ol class="sidebar-mosts sidebar-mosts--readed">
            @foreach ($lastTrending as $item)
                <li class="sidebar-mosts__item ">
                    <a class="sidebar-mosts__item__link" href="{{ $item->post_link }}" title="{{ $item->title }}">
                        <figure class="sidebar-mosts__item__body">
                            <div class="sidebar-mosts__item__image">
                                <img class="sidebar-mosts__item__image__item lazyload"
                                    src="{{ url('assets/images/preloader.gif') }}"
                                    data-src="{{ makepreview($item->thumb, 's', 'posts') }}" alt="{{ $item->title }}"
                                    width="300" height="169">
                            </div>
                            <figcaption class="sidebar-mosts__item__caption">
                                <div class="sidebar-mosts__item__view">
                                    <span
                                        class="sidebar-mosts__item__view__count">{{ $item->one_day_stats ? number_format($item->one_day_stats) : '0' }}</span>
                                    <span class="sidebar-mosts__item__view__icon"><i
                                            class="material-icons">&#xE8E5;</i></span>
                                </div>
                                <h3 class="sidebar-mosts__item__title">{{ $item->title }}</h3>
                            </figcaption>

                        </figure>
                    </a>
                </li>
            @endforeach
        </ol>
    </div>
@endif
