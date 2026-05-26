@if ($cars->hasPages())
<div class="flex flex-col gap-3 rounded-2xl border border-stone-200 bg-white p-4 md:flex-row md:items-center md:justify-between">
    <p class="text-sm text-stone-600">Pagina {{ $cars->currentPage() }} van {{ $cars->lastPage() }}</p>
    <div class="flex items-center gap-2">
        {{ $cars->onEachSide(1)->links() }}
    </div>
</div>
@endif