@extends('layouts.app')
@section('title', Str::limit($article->title, 50))
@section('page-title','Article Details')
@section('page-subtitle', $article->year.' — '.$article->cluster_label)

@section('header-actions')
<a href="{{ url()->previous() }}" class="btn btn-outline text-xs"><i class="fas fa-arrow-left"></i> Back</a>
@endsection

@section('content')
@php $c = $colors[$article->cluster] ?? ['bg'=>'#999','text'=>'white']; @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="col-span-2 space-y-4">
        <div class="card">
            <h2 class="text-lg font-bold text-gray-800 mb-3">{{ $article->title }}</h2>
            <div class="flex flex-wrap gap-3 text-sm text-gray-500 mb-4">
                <span><i class="fas fa-user mr-1"></i>{{ $article->authors ?: '-' }}</span>
                <span><i class="fas fa-calendar mr-1"></i>{{ $article->year }}</span>
                @if($article->url)
                <a href="{{ $article->url }}" target="_blank" class="text-blue-500 hover:underline">
                    <i class="fas fa-external-link mr-1"></i>View in JUTIF
                </a>
                @endif
            </div>

            @if($article->keywords_clean)
            <div class="mb-4">
                <div class="text-xs font-semibold text-gray-500 uppercase mb-2">Keywords</div>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(',', $article->keywords_clean) as $kw)
                    @if(trim($kw))
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ trim($kw) }}</span>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Similar articles --}}
        @if($similar->count() > 0)
        <div class="card">
            <h3 class="font-bold text-gray-700 mb-3"><i class="fas fa-layer-group mr-2 text-blue-500"></i>Articles in the Same Cluster</h3>
            <div class="space-y-2">
                @foreach($similar as $s)
                <a href="{{ route('articles.show', $s->id) }}" class="block p-3 rounded-lg bg-gray-50 hover:bg-blue-50 transition-colors">
                    <div class="font-medium text-sm text-gray-800">{{ Str::limit($s->title, 80) }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $s->year }}</div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-4">
        {{-- Cluster info --}}
        <div class="card" style="border-top: 4px solid {{ $c['bg'] }}">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-2">Cluster</div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-black"
                     style="background:{{ $c['bg'] }}">C{{ $article->cluster }}</div>
                <div class="font-semibold text-gray-800 text-sm">{{ $article->cluster_label }}</div>
            </div>
            <a href="{{ route('clustering.show', $article->cluster) }}" class="btn btn-outline text-xs w-full justify-center">
                <i class="fas fa-layer-group"></i> View This Cluster
            </a>
        </div>

        {{-- Keywords normalized --}}
        @if($article->kw_normalized_str)
        <div class="card">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-2">Keywords (Normalized)</div>
            <div class="flex flex-wrap gap-1.5">
                @foreach(explode(';', $article->kw_normalized_str) as $kw)
                @if(trim($kw))
                <span class="text-white px-2 py-0.5 rounded text-xs" style="background:{{ $c['bg'] }}">{{ trim($kw) }}</span>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Metadata --}}
        <div class="card text-xs text-gray-500 space-y-2">
            <div class="font-semibold text-gray-700 text-sm mb-2">Metadata</div>
            <div class="flex justify-between"><span>Publication Year</span><span class="font-medium text-gray-700">{{ $article->year }}</span></div>
            <div class="flex justify-between"><span>Number of Keywords</span><span class="font-medium text-gray-700">{{ $article->kw_tokens_count ?? '-' }}</span></div>
            <div class="flex justify-between"><span>PCA X</span><span class="font-mono">{{ $article->pca_x ? number_format($article->pca_x,4) : '-' }}</span></div>
            <div class="flex justify-between"><span>PCA Y</span><span class="font-mono">{{ $article->pca_y ? number_format($article->pca_y,4) : '-' }}</span></div>
        </div>
    </div>
</div>

@endsection
