@extends('pages.user.userapp')
@section('usercontent')
    <h2>{{ $patitle }}</h2>
    @include('errors.adminlook', ['relatedid' => $user->id])
    <div class="recent-activity">
        @if ($lastPost->total() > 0)
            <ul class="items_lists res-lists clearfix">
                @foreach ($lastPost as $item)
                    @include('._particles._lists.items_list', [
                        'item' => $item,
                        'listtype' => 'b',
                        'descof' => 'on',
                        'setbadgeof' => 'off',
                        'linkcolor' => 'default',
                    ])
                @endforeach
            </ul>
            <div class="center-elements clearfix">
                {!! $lastPost->render() !!}
            </div>
        @else
            @include('errors.emptycontent')
        @endif
    </div>
@endsection
