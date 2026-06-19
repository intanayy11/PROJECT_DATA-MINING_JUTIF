@extends('layouts.app')
@section('title','Data Artikel')
@section('page-title','Data Artikel JUTIF')
@section('page-subtitle','Seluruh artikel yang telah dikumpulkan dan dianalisis')

@section('content')
<div class="card mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Cari judul / keyword / penulis</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik untuk mencari..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tahun</label>
            <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Semua tahun</option>
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Klaster</label>
            <select name="cluster" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Semua klaster</option>
                @foreach($clusters as $c)
                <option value="{{ $c->cluster }}" {{ $cluster==$c->cluster?'selected':'' }}>
                    C{{ $c->cluster }}: {{ $c->cluster_label }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
        <a href="{{ route('articles.index') }}" class="btn btn-outline">Reset</a>
    </form>
</div>

<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-gray-700">Daftar Artikel</h3>
        <span class="text-sm text-gray-500">{{ number_format($articles->total()) }} artikel ditemukan</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Judul</th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600 w-40">Penulis</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600 w-16">Tahun</th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Klaster</th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Keywords</th>
                    <th class="w-10"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $art)
                @php $c = $colors[$art->cluster] ?? ['bg'=>'#999','text'=>'white']; @endphp
                <tr class="table-row border-t border-gray-100">
                    <td class="px-3 py-2 font-medium text-gray-800 max-w-xs">
                        <a href="{{ route('articles.show', $art->id) }}" class="hover:text-blue-600">
                            {{ Str::limit($art->title, 70) }}
                        </a>
                    </td>
                    <td class="px-3 py-2 text-gray-500 text-xs">{{ Str::limit($art->authors, 35) }}</td>
                    <td class="px-3 py-2 text-center text-gray-600">{{ $art->year }}</td>
                    <td class="px-3 py-2">
                        @if($art->cluster !== null)
                        <span class="badge text-white text-xs" style="background:{{ $c['bg'] }}">
                            C{{ $art->cluster }}
                        </span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-xs text-gray-500">{{ Str::limit($art->keywords_clean, 55) }}</td>
                    <td class="px-3 py-2">
                        <a href="{{ route('articles.show', $art->id) }}" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Tidak ada artikel ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $articles->links() }}</div>
</div>
@endsection
