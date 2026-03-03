@if ($cars->hasPages())
<div class="flex items-center justify-between text-sm text-gray-300">
    <p>Pagina {{ $cars->currentPage() }} van {{ $cars->lastPage() }}</p>
    <div class="flex items-center gap-2">
        {{-- Simple pagination links captured for fetch updates --}}
        {!! $cars->onEachSide(1)->links() !!}
    </div>
</div>
@endif