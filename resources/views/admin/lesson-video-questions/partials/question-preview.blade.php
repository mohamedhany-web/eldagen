<div class="p-4 text-right">
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ $typeLabel }}</p>
    <div class="text-gray-900 dark:text-white font-medium mb-3">{{ $question->question }}</div>
    @if($question->image_url)
        <div class="mb-3">
            <img src="{{ $question->getImageUrl() }}" alt="" class="max-w-full h-auto rounded-lg max-h-40 object-contain">
        </div>
    @endif
    @if($question->options && is_array($question->options))
        <ul class="space-y-2">
            @foreach($question->options as $idx => $opt)
                <li class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded flex items-center justify-center text-xs bg-gray-200 dark:bg-gray-600">{{ chr(65 + $idx) }}</span>
                    <span>{{ $opt }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
