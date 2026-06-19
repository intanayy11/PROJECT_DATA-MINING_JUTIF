@extends('layouts.app')
@section('title','K-Means Clustering')
@section('page-title','K-Means Clustering')
@section('page-subtitle','Hasil pengelompokan topik penelitian JUTIF (k=7, TF-IDF Keyword Boosting)')

@section('header-actions')
<a href="{{ route('clustering.pca') }}" class="btn btn-outline text-xs">
    <i class="fas fa-braille"></i> Visualisasi PCA
</a>
@endsection

@section('content')
{{-- Cluster Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach($summary as $c)
    @php $color = $colors[$c->cluster] ?? ['bg'=>'#999','text'=>'white']; @endphp
    <a href="{{ route('clustering.show', $c->cluster) }}"
       class="card hover:shadow-md transition-shadow cursor-pointer border-l-4"
       style="border-left-color: {{ $color['bg'] }}">
        <div class="flex items-center justify-between mb-3">
            <span class="text-2xl font-black" style="color:{{ $color['bg'] }}">C{{ $c->cluster }}</span>
            <span class="text-2xl font-black text-gray-700">{{ $c->pct }}%</span>
        </div>
        <div class="font-semibold text-gray-800 text-sm leading-tight mb-2">{{ $c->cluster_label }}</div>
        <div class="text-xs text-gray-500">{{ number_format($c->total) }} artikel</div>
        <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5">
            <div class="h-1.5 rounded-full" style="width:{{ $c->pct }}%; background:{{ $color['bg'] }}"></div>
        </div>
    </a>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Bar Chart Distribusi --}}
    <div class="card">
        <h3 class="font-bold text-gray-700 mb-4"><i class="fas fa-chart-bar mr-2 text-blue-500"></i>Distribusi Artikel per Klaster</h3>
        <canvas id="clusterBar" height="180"></canvas>
    </div>

    {{-- Tren Line Chart --}}
    <div class="card">
        <h3 class="font-bold text-gray-700 mb-4"><i class="fas fa-chart-line mr-2 text-purple-500"></i>Tren per Tahun</h3>
        <canvas id="trendLine" height="180"></canvas>
    </div>

    {{-- Tabel Tren --}}
    <div class="card col-span-2">
        <h3 class="font-bold text-gray-700 mb-4"><i class="fas fa-table mr-2 text-green-500"></i>Distribusi Klaster per Tahun</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-3 py-2 font-semibold text-gray-600">Klaster</th>
                        @foreach($years as $y)
                        <th class="text-center px-3 py-2 font-semibold text-gray-600">{{ $y }}</th>
                        @endforeach
                        <th class="text-center px-3 py-2 font-semibold text-gray-600">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summary as $c)
                    @php $color = $colors[$c->cluster] ?? ['bg'=>'#999']; @endphp
                    <tr class="table-row border-t border-gray-100">
                        <td class="px-3 py-2">
                            <span class="inline-block w-3 h-3 rounded-full mr-2" style="background:{{ $color['bg'] }}"></span>
                            <span class="font-medium">C{{ $c->cluster }}: {{ $c->cluster_label }}</span>
                        </td>
                        @foreach($years as $y)
                        <td class="text-center px-3 py-2 text-gray-600">
                            {{ $trend[$c->cluster][$y] ?? 0 }}
                        </td>
                        @endforeach
                        <td class="text-center px-3 py-2 font-bold text-gray-800">{{ $c->total }}</td>
                    </tr>
                    @endforeach
                    <tr class="border-t-2 border-gray-300 bg-gray-50 font-bold">
                        <td class="px-3 py-2">TOTAL</td>
                        @foreach($years as $y)
                        <td class="text-center px-3 py-2">{{ $summary->sum(fn($c) => $trend[$c->cluster][$y] ?? 0) }}</td>
                        @endforeach
                        <td class="text-center px-3 py-2">{{ $summary->sum('total') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const colors = @json(array_column($colors,'bg'));
const summary = @json($summary);
const trend   = @json($trend);
const years   = @json($years);

new Chart(document.getElementById('clusterBar'), {
    type: 'bar',
    data: {
        labels: summary.map(c => 'C'+c.cluster),
        datasets: [{ data: summary.map(c=>c.total),
            backgroundColor: summary.map(c=>colors[c.cluster]||'#999'), borderRadius:6 }]
    },
    options:{ responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});

const datasets = Object.entries(trend).map(([cid, byYear]) => ({
    label: 'C'+cid,
    data: years.map(y => byYear[y]||0),
    borderColor: colors[cid]||'#999',
    tension:0.4, fill:false, pointRadius:3
}));
new Chart(document.getElementById('trendLine'), {
    type:'line',
    data:{ labels:years, datasets },
    options:{ responsive:true, interaction:{mode:'index',intersect:false},
        plugins:{legend:{position:'bottom',labels:{font:{size:10},boxWidth:10}}},
        scales:{y:{beginAtZero:true}} }
});
</script>
@endsection
