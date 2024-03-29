@extends("app")

@section('head_title', $reaction->name .' | '.get_buzzy_config('sitename') )
@section('head_description', $reaction->name )

@section("content")
<div class="content">
    <div class="container">
        <div class="mainside ">
            <div class="colheader none">
                <span classs="color-red">{{ $reaction->name }}</span>
            </div>
            @if(count($posts) > 0)
                @foreach($posts as $k => $item)
                    @include('pages.catpostloadpage')
                @endforeach
                @else
                @include('errors.emptycontent')
            @endif
        </div>
        <div class="sidebar">
            <div class="ads">
                @include('_particles.widget.ads', ['position' => 'CatSide', 'width' => 'auto', 'height' => 'auto'])
            </div>

            @include("_widgets/facebooklike")
        </div>
    </div>
</div>
@endsection
