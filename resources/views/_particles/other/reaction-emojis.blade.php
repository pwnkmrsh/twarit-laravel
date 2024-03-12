@if (get_buzzy_config('p_reactionform') == 'on')
    <div class="reaction-emojis with-names">
        @foreach (\App\ReactionIcon::byActive()->byLanguage()->orderBy('ord', 'asc')->get() as $react)
            <a {{ $reaction->reaction_type == $react->reaction_type ? 'class=selected' : '' }}
                href="{{ route('reaction.show', ['reactionIcon' => $react->reaction_type]) }}"
                title="{{ $react->name }}">
                <img alt="{{ $react->name }}" src="{{ url($react->icon) }}" width="55">
                <div class="reaction_name">{{ $react->name }}</div>
            </a>
        @endforeach
    </div>
@endif
