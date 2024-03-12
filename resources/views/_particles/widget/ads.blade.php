@php($widgets = \App\Widgets::where('type', $position)->where('display', 'on')->getCached('ad_'. $position))
@if(count($widgets))
    <div class="clearfix"> </div>
    <div class="ads clearfix" >
        @foreach($widgets as $widget)
           <div class="{!! $widget->showweb == 'off' ? 'hide-web' : 'show-web' !!} {!! $widget->showmobile == 'off' ? 'hide-phone' : 'visible-mobile' !!}">
                {!! $widget->text !!}
           </div>
        @endforeach
    </div>
    <div class="clearfix"> </div>
@endif
