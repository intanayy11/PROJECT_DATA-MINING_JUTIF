@extends('layouts.app')
@section('title', 'Klaster '.$clusterId)
@section('page-title', 'C'.$clusterId.': '.($clusterInfo->cluster_label??''))
@section('page-subtitle', ($clusterInfo->total??0).' artikel dalam klaster ini')

@section('header-actions')
<a href="{{ route('clustering.index') }}" class="btn btn-outline text-xs">
    <i class="fas fa-arrow-left"></i> Kembali
</a>
@endsection

@section('content')
@php $color = $colors[$clusterId] ?? ['bg'=>'#3B82F6','text'=>'white']; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Info Box --}}
    <div class="card" style="border-top: 4px solid {{ $color['bg'] }}">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-lg"
                 style="background:{{ $color['bg'] }}">C{{ $clusterId }}</div>
            <div>
                <div class="font-bold text-gray-800">{{ $clusterInfo->cluster_label ?? '-' }}</div>
                <div class="text-sm text-gray-500">{{ number_format($clusterInfo->total ?? 0) }} artikel</div>
            </div>
        </div>
        <div class="text-xs text-gray-500 space-y-1">
            <div>Filter aktif:
                @if($year) <span class="badge bg-blue-100 text-blue-700">{{ $year }}</span> @endif
                @if($search) <span class="badge bg-orange-100 text-orange-700">"{{ $search }}"</span> @endif
            </div>
        </div>
    </div>

    {{-- Top Keywords --}}
    <div class="card col-span-2">
        <h3 class="font-semibold text-gray-700 mb-3 text-sm">Top Keywords dalam Klaster</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($topKw as $kw => $cnt)
            @php $size = $cnt > 20 ? 'text-base font-bold' : ($cnt > 10 ? 'text-sm font-semibold' : 'text-xs'); @endphp
            <span class="{{ $size }} px-3 py-1 rounded-full text-white"
                  style="background:{{ $color['bg'] }}; opacity:{{ min(0.5 + $cnt/max($topKw)*0.5, 1) }}">
                {{ $kw }} <span class="opacity-70">({{ $cnt }})</span>
            </span>
            @endforeach
        </div>
    </div>
</div>

{{-- Filter & Search --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('clustering.show', $clusterId) }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Cari judul/keyword</label>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Ketik kata kunci..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Filter tahun</label>
            <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Semua tahun</option>
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filter
        </button>
        <a href="{{ route('clustering.show', $clusterId) }}" class="btn btn-outline">Reset</a>
    </form>
</div>

{{-- Articles Table --}}
<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-gray-700">Daftar Artikel</h3>
        <span class="text-sm text-gray-500">{{ $articles->total() }} artikel ditemukan</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Judul</th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600 w-40">Penulis</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600 w-16">Tahun</th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Keywords</th>
                    <th class="w-10"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $art)
                <tr class="table-row border-t border-gray-100">
                    <td class="px-3 py-2 font-medium text-gray-800 max-w-xs">{{ Str::limit($art->title, 80) }}</td>
                    <td class="px-3 py-2 text-gray-500 text-xs">{{ Str::limit($art->authors, 40) }}</td>
                    <td class="px-3 py-2 text-center text-gray-600">{{ $art->year }}</td>
                    <td class="px-3 py-2 text-xs text-gray-500">{{ Str::limit($art->keywords_clean, 60) }}</td>
                    <td class="px-3 py-2">
                        <a href="{{ route('articles.show', $art->id) }}" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-gray-400">Tidak ada artikel ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $articles->links() }}</div>
</div>
@endsection
