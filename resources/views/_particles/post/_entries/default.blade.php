
<section class="entry fr-view" id="section_{{ $entry->order }}" entry="{{ $entry->id }}">
    @include('_particles.post._entries._entry_title')

    @include('_particles.post._entries._entry_image')

    @include('_particles.post._entries._entry_video')

    {!! $entry->body !!}

    @if($entry->type=='text' && !empty($entry->source))
    <div class="text-center">
        <a href="{{ $entry->source }}" class="button button-big button-blue" target="_target">{{ trans('v4.read_more') }}</a>
    </div>
    @endif

    <div class="clear"></div>
</section>
