@extends('layouts.app')
@section('title','Import Data')
@section('page-title','Import Data Excel')
@section('page-subtitle','Upload file hasil scraping dan analisis ke database')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    @foreach([['label'=>'Artikel','count'=>$counts['articles'],'color'=>'blue','icon'=>'fa-newspaper'],
              ['label'=>'ARM Rules','count'=>$counts['arm_rules'],'color'=>'orange','icon'=>'fa-arrow-right-arrow-left'],
              ['label'=>'Frequent Itemsets','count'=>$counts['itemsets'],'color'=>'green','icon'=>'fa-tags']] as $s)
    <div class="card flex items-center gap-4">
        <div class="w-10 h-10 rounded-lg bg-{{ $s['color'] }}-100 flex items-center justify-center">
            <i class="fas {{ $s['icon'] }} text-{{ $s['color'] }}-600"></i>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ number_format($s['count']) }}</div>
            <div class="text-xs text-gray-500">{{ $s['label'] }} tersimpan</div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Import Artikel --}}
    <div class="card">
        <h3 class="font-bold text-gray-700 mb-1"><i class="fas fa-newspaper mr-2 text-blue-500"></i>Import Dataset Artikel</h3>
        <p class="text-xs text-gray-400 mb-4">Upload file <code class="bg-gray-100 px-1 rounded">hasil_clustering_jutif.xlsx</code> — sheet "Dataset Clustering"</p>
        <form method="POST" action="{{ route('import.articles') }}" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-blue-400 transition-colors mb-4 cursor-pointer"
                 onclick="document.getElementById('fileArticles').click()">
                <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                <div class="text-sm text-gray-500">Klik atau drag file .xlsx di sini</div>
                <div id="fileArticlesName" class="text-xs text-blue-500 mt-1"></div>
            </div>
            <input type="file" id="fileArticles" name="file" accept=".xlsx,.xls" class="hidden"
                   onchange="document.getElementById('fileArticlesName').textContent = this.files[0]?.name || ''">
            <button type="submit" class="btn btn-primary w-full justify-center">
                <i class="fas fa-file-import"></i> Import Artikel
            </button>
        </form>
        <div class="mt-3 p-3 bg-blue-50 rounded-lg text-xs text-blue-700">
            <strong>⚠️ Perhatian:</strong> Import akan menghapus semua data artikel sebelumnya dan menggantinya dengan data baru.
        </div>
    </div>

    {{-- Import ARM --}}
    <div class="card">
        <h3 class="font-bold text-gray-700 mb-1"><i class="fas fa-arrow-right-arrow-left mr-2 text-orange-500"></i>Import Hasil ARM</h3>
        <p class="text-xs text-gray-400 mb-4">Upload file <code class="bg-gray-100 px-1 rounded">hasil_apriori_jutif.xlsx</code> — sheet "Association Rules" + "Frequent Itemsets"</p>
        <form method="POST" action="{{ route('import.arm') }}" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-orange-400 transition-colors mb-4 cursor-pointer"
                 onclick="document.getElementById('fileArm').click()">
                <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                <div class="text-sm text-gray-500">Klik atau drag file .xlsx di sini</div>
                <div id="fileArmName" class="text-xs text-orange-500 mt-1"></div>
            </div>
            <input type="file" id="fileArm" name="file" accept=".xlsx,.xls" class="hidden"
                   onchange="document.getElementById('fileArmName').textContent = this.files[0]?.name || ''">
            <button type="submit" class="btn w-full justify-center" style="background:#ED7D31; color:white">
                <i class="fas fa-file-import"></i> Import ARM
            </button>
        </form>
        <div class="mt-3 p-3 bg-orange-50 rounded-lg text-xs text-orange-700">
            <strong>⚠️ Perhatian:</strong> Import akan menghapus semua data ARM sebelumnya.
        </div>
    </div>
</div>

{{-- Import Log --}}
<div class="card">
    <h3 class="font-bold text-gray-700 mb-4"><i class="fas fa-history mr-2 text-gray-500"></i>Riwayat Import</h3>
    @forelse($logs as $log)
    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 text-sm">
        <div class="flex items-center gap-3">
            <span class="badge {{ $log->type=='articles' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                {{ $log->type }}
            </span>
            <span class="text-gray-700">{{ $log->filename }}</span>
        </div>
        <div class="flex items-center gap-4 text-xs text-gray-500">
            <span>{{ number_format($log->rows_imported) }} rows</span>
            <span>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</span>
        </div>
    </div>
    @empty
    <p class="text-gray-400 text-sm text-center py-4">Belum ada riwayat import.</p>
    @endforelse
</div>
@endsection
