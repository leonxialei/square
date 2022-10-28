@if ($paginator->hasPages())
<div class="layui-table-page">
    <div id="layui-table-page1">
        <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-1">

            @if ($paginator->onFirstPage())
            <a href="javascript:;" class="layui-laypage-prev layui-disabled" data-page="0"><i class="layui-icon"></i></a>
            @else
            <a href="{{ $paginator->previousPageUrl() }}" class="layui-laypage-prev" data-page="0"><i class="layui-icon"></i></a>
            @endif

            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                        <span class=""><em class="layui-laypage-em"></em><em>{{ $element }}</em></span>

                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                                <span class="layui-laypage-curr"><em class="layui-laypage-em"></em><em>{{ $page }}</em></span>
                        @else
                                <a  href="{{ $url }}" data-page="2">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach





            @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="layui-laypage-next" data-page="2"><i class="layui-icon"></i></a>
            @else
            <a href="javascript:;" class="layui-laypage-next  layui-disabled" data-page="2"><i class="layui-icon"></i></a>
            @endif
            <span class="layui-laypage-count">共 {{$paginator->total()}} 条</span>
        </div>
    </div>
</div>
@endif
