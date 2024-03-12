@extends('pages.user.userapp')
@section('usercontent')
    <h2> {{ trans('updates.following') }} ({{ $user->following()->count() }})</h2>
    <div class="setting-form">
        <br><br>
        @if ($follows->count() > 0)
            <div class="profile-section follow-images ">
                @foreach ($follows as $follow)
                    @if ($follow->followed)
                        <a class="follow-image big" href="{{ $follow->followed->profile_link }}"
                            title="{{ $follow->followed->username }}">
                            <img src="{{ makepreview($follow->followed->icon, 's', 'members/avatar') }}" width="90"
                                height="90" alt="{{ $follow->followed->username }}">
                            <span>{{ $follow->followed->username }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
            <div class="clear"></div>
            <div class="center-elements clearfix">
                {!! $follows->render() !!}
            </div>
        @else
            @include('errors.emptycontent')
        @endif
    </div>
@endsection
