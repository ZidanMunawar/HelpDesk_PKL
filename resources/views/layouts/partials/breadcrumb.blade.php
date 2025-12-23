@if (isset($breadcrumb))
    <div class="page-titles">
        <ol class="breadcrumb">
            @foreach ($breadcrumb as $item)
                @if ($loop->last)
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $item['title'] }}</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></li>
                @endif
            @endforeach
        </ol>
    </div>
@endif
