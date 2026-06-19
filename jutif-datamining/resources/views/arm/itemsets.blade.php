@extends('layouts.app')
@section('title','Frequent Itemsets')
@section('page-title','Frequent Itemsets')
@section('page-subtitle','Pola kata kunci yang sering muncul (min_support = 0.03)')

@section('content')
{{-- Count badges --}}
<div class="flex gap-3 mb-5">
    <a href="{{ route('arm.itemsets') }}"
       class="px-4 py-2 rounded-lg text-sm font-medium border {{ !$length ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        Semua ({{ array_sum($counts) }})
    </a>
    @foreach([1,2,3] as $l)
    <a href="{{ route('arm.itemsets', ['length'=>$l]) }}"
       class="px-4 py-2 rounded-lg text-sm font-medium border {{ $length==$l ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ $l }}-itemset ({{ $counts[$l] ?? 0 }})
    </a>
    @endforeach
</div>

<div class="card mb-4">
    <form method="GET" class="flex gap-3 items-center">
        @if($length) <input type="hidden" name="length" value="{{ $length }}"> @endif
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari itemset..."
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 w-64">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
        <a href="{{ route('arm.itemsets') }}" class="btn btn-outline">Reset</a>
    </form>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-center px-3 py-2 font-semibold text-gray-600 w-12">No</th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Itemset</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600 w-24">Support</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600 w-28">Frekuensi</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600 w-24">Panjang</th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Support Bar</th>
                </tr>
            </thead>
            <tbody>
                @php $no = ($itemsets->currentPage()-1)*$itemsets->perPage()+1; @endphp
                @forelse($itemsets as $fi)
                @php
                    $lengthBadge = $fi->length==1 ? 'bg-blue-100 text-blue-700' :
                                   ($fi->length==2 ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700');
                @endphp
                <tr class="table-row border-t border-gray-100">
                    <td class="text-center px-3 py-2 text-gray-400">{{ $no++ }}</td>
                    <td class="px-3 py-2 font-medium text-gray-800 font-mono">{{ $fi->itemset }}</td>
                    <td class="text-center px-3 py-2 font-semibold text-blue-600">{{ number_format($fi->support,4) }}</td>
                    <td class="text-center px-3 py-2 text-gray-600">{{ $fi->frequency ?? round($fi->support*988) }}× artikel</td>
                    <td class="text-center px-3 py-2">
                        <span class="badge {{ $lengthBadge }}">{{ $fi->length }}-item</span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full bg-blue-400" style="width:{{ min($fi->support*100*6,100) }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-10">{{ number_format($fi->support*100,1) }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $itemsets->links() }}</div>
</div>
@endsection
