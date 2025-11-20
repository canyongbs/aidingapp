<div class="prose max-w-none dark:prose-invert">
    <{{ $tag }}>
        @foreach ($items as $item)
            <li>{!! str($item)->sanitizeHtml() !!}</li>
        @endforeach
        </{{ $tag }}>
</div>
