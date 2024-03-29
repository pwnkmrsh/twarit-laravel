<div class="content-timeline__item is-active large">
    <div class="content-timeline--right">
        <div class="content-timeline__link clearfix">
            <div class="content-timeline__media">
                <figure class="content-timeline__media__image">
                    <a href="{{ $item->post_link }}" title="{!! $item->title !!}">
                        <img data-src="{{ makepreview($item->thumb, 's', 'posts') }}" class="lazyload"
                            alt="{!! $item->title !!}" width="262" height="147"
                            src="{{ url('assets/images/preloader.gif') }}">
                    </a>
                </figure>
            </div>

            <div class="content-timeline__detail">
                <div class="content-timeline__detail__container">

                    <a href="{{ $item->post_link }}" title="{!! $item->title !!}">
                        <h3 class="content-timeline__detail__title">{!! $item->title !!}</h3>
                    </a>

                    <div class="content-timeline__detail--top">
                        <p class="content-timeline__detail__desc">{{ str_limit($item->body, 255) }}</p>
                    </div>

                    <div class="content-timeline__detail--bottom">
                        <div class="content-timeline__detail__date share_counts">
                            <span class="facebook">
                                <div class="buzz-icon buzz-facebook"></div>
                                {{ isset($item->shared->facebook) ? $item->shared->facebook : '0' }}
                            </span>
                            <span class="twitter">
                                <div class="buzz-icon buzz-twitter"></div>
                                {{ isset($item->shared->twitter) ? $item->shared->twitter : '0' }}
                            </span>
                            <span class="whatsapp">
                                <div class="buzz-icon buzz-whatsapp"></div>
                                {{ isset($item->shared->whatsapp) ? $item->shared->whatsapp : '0' }}
                            </span>
                        </div>

                        <div class="content-timeline__detail__date">
                            {{ get_buzzy_locale() == 'en' ? $item->created_at->format('j M, h:i A') : $item->created_at->diffForHumans() }}
                        </div>

                        @if ($item->user)
                            <a class="content-timeline__detail__author hide-phone"
                                href="{{ $item->user->profile_link }}" title="{{ $item->user->username }}">
                                {{ $item->user->username }}</a>
                        @endif
                        <div class="content-timeline__detail__social-media">
                            <span class="has-dropdown" data-target="share-dropdown--{{ $item->id }}"
                                data-align="left-bottom"><i class="material-icons"></i></span>
                            <div class="share-dropdown share-dropdown--{{ $item->id }}  dropdown-container">
                                <ul>
                                    <li class="dropdown-container__item ripple buzz-share-button has-ripple"
                                        data-share-type="facebook" data-type="news" data-id="{{ $item->id }}"
                                        data-post-url="{{ route('post.share') }}" data-title="{!! $item->title !!}"
                                        data-sef="{{ $item->post_link }}">
                                        <span class="share-dropdown__icon share-dropdown__icon--facebook"></span>
                                        <span class="share-dropdown__title">Facebook</span>
                                    </li>
                                    <li class="dropdown-container__item ripple buzz-share-button has-ripple"
                                        data-share-type="twitter" data-type="news" data-id="{{ $item->id }}"
                                        data-post-url="{{ route('post.share') }}" data-title="{!! $item->title !!}"
                                        data-sef="{{ $item->post_link }}">
                                        <span class="share-dropdown__icon share-dropdown__icon--twitter"></span>
                                        <span class="share-dropdown__title">Twitter</span>
                                    </li>
                                    <li class="dropdown-container__item ripple buzz-share-button has-ripple"
                                        data-share-type="whatsapp" data-type="news" data-id="{{ $item->id }}"
                                        data-post-url="{{ route('post.share') }}" data-title="{!! $item->title !!}"
                                        data-sef="{{ $item->post_link }}">
                                        <span class="share-dropdown__icon share-dropdown__icon--whatsapp"></span>
                                        <span class="share-dropdown__title">Whatsapp</span>
                                    </li>
                                    <li class="dropdown-container__item ripple buzz-share-button has-ripple"
                                        data-share-type="mail" data-type="news" data-id="{{ $item->id }}"
                                        data-post-url="{{ route('post.share') }}" data-title="{!! $item->title !!}"
                                        data-sef="{{ $item->post_link }}">
                                        <span class="share-dropdown__icon share-dropdown__icon--mail"></span>
                                        <span class="share-dropdown__title">Email</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
