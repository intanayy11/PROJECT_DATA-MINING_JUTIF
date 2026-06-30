@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-subtitle','Summary of JUTIF UNSOED research topic trends')

@section('content')
{{-- STAT CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $stats = [
        ['icon'=>'fa-newspaper','color'=>'blue','label'=>'Total Articles','value'=>number_format($totalArticles),'sub'=>($yearRange->min_y??'-').' – '.($yearRange->max_y??'-')],
        ['icon'=>'fa-layer-group','color'=>'green','label'=>'Clusters','value'=>$totalClusters,'sub'=>'K-Means k=7'],
        ['icon'=>'fa-arrow-right-arrow-left','color'=>'orange','label'=>'Association Rules','value'=>$totalRules,'sub'=>'Apriori min_sup=0.03'],
        ['icon'=>'fa-tags','color'=>'purple','label'=>'Frequent Itemsets','value'=>$totalItemsets,'sub'=>'min_support=0.03'],
    ];
    @endphp
    @foreach($stats as $s)
    <div class="card flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-{{ $s['color'] }}-100 flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $s['icon'] }} text-{{ $s['color'] }}-600 text-xl"></i>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-800">{{ $s['value'] }}</div>
            <div class="text-sm font-medium text-gray-600">{{ $s['label'] }}</div>
            <div class="text-xs text-gray-400">{{ $s['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- CHART: Artikel per Tahun --}}
    <div class="card col-span-2">
        <h3 class="font-bold text-gray-700 mb-4"><i class="fas fa-chart-bar mr-2 text-blue-500"></i>Articles per Year</h3>
        <canvas id="yearChart" height="90"></canvas>
    </div>

    {{-- Distribusi Klaster --}}
    <div class="card">
        <h3 class="font-bold text-gray-700 mb-4"><i class="fas fa-pie-chart mr-2 text-green-500"></i>Cluster Distribution</h3>
        <canvas id="clusterPie" height="200"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- CHART: Tren Klaster per Tahun --}}
    <div class="card col-span-2">
        <h3 class="font-bold text-gray-700 mb-4"><i class="fas fa-chart-line mr-2 text-purple-500"></i>Cluster Trend per Year</h3>
        <canvas id="trendChart" height="80"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Top Keywords --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-700"><i class="fas fa-tags mr-2 text-orange-500"></i>Top Keywords</h3>
            <a href="{{ route('arm.itemsets') }}" class="text-xs text-blue-600 hover:underline">View all →</a>
        </div>
        <div class="space-y-2">
            @foreach($topKeywords as $kw)
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700 capitalize">{{ $kw->itemset }}</span>
                        <span class="text-gray-500 text-xs">{{ number_format($kw->support*100,1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width:{{ min($kw->support*100*5,100) }}%"></div>
                    </div>
                </div>
                <span class="text-xs text-gray-400 w-12 text-right">{{ $kw->frequency ?? round($kw->support*988) }}×</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top ARM Rules --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-700"><i class="fas fa-arrow-right-arrow-left mr-2 text-red-500"></i>Top Association Rules</h3>
            <a href="{{ route('arm.index') }}" class="text-xs text-blue-600 hover:underline">View all →</a>
        </div>
        @forelse($topRules as $rule)
        <div class="border border-gray-100 rounded-lg p-3 mb-2 hover:border-blue-200 transition-colors">
            <div class="flex items-center gap-2 text-sm mb-2">
                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-mono text-xs">{{ $rule->antecedents }}</span>
                <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-mono text-xs">{{ $rule->consequents }}</span>
            </div>
            <div class="flex gap-3 text-xs text-gray-500">
                <span>Lift: <strong class="text-orange-600">{{ number_format($rule->lift,3) }}</strong></span>
                <span>Conf: <strong>{{ number_format($rule->confidence*100,1) }}%</strong></span>
                <span>Sup: <strong>{{ number_format($rule->support*100,2) }}%</strong></span>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-sm text-center py-4">No ARM data available. <a href="{{ route('import.index') }}" class="text-blue-500">Import first</a></p>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
const clusterColors = @json(array_column($colors, 'bg'));
const clusterLabels = @json(array_column($colors, 'label'));

// Chart 1: Artikel per Tahun
new Chart(document.getElementById('yearChart'), {
    type: 'bar',
    data: {
        labels: @json($perYear->keys()),
        datasets: [{
            label: 'Total Articles',
            data: @json($perYear->values()),
            backgroundColor: '#3B82F6',
            borderRadius: 6,
        }]
    },
    options: { responsive:true, plugins:{ legend:{display:false} }, scales:{ y:{beginAtZero:true} } }
});

// Chart 2: Distribusi Klaster (Doughnut)
const clusterData = @json($perCluster);
new Chart(document.getElementById('clusterPie'), {
    type: 'doughnut',
    data: {
        labels: clusterData.map(c => 'C'+c.cluster+': '+c.cluster_label),
        datasets: [{ data: clusterData.map(c => c.total), backgroundColor: clusterData.map(c => clusterColors[c.cluster]||'#999') }]
    },
    options: { responsive:true, plugins:{ legend:{position:'bottom', labels:{font:{size:10}, boxWidth:12}} } }
});

// Chart 3: Tren Klaster per Tahun (Line)
const trendRaw = @json($clusterTrend);
const years = @json($years);
const datasets = [];
for (const [clusterId, rows] of Object.entries(trendRaw)) {
    const label = rows[0]?.cluster_label || ('C'+clusterId);
    const data = years.map(y => {
        const found = rows.find(r => r.year == y);
        return found ? found.total : 0;
    });
    datasets.push({
        label: 'C'+clusterId+': '+label,
        data, borderColor: clusterColors[clusterId]||'#999',
        backgroundColor: (clusterColors[clusterId]||'#999')+'33',
        tension: 0.4, fill: false, pointRadius: 4
    });
}
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: { labels: years, datasets },
    options: { responsive:true, interaction:{mode:'index',intersect:false},
        plugins:{ legend:{position:'bottom', labels:{font:{size:10},boxWidth:12}} },
        scales:{ y:{beginAtZero:true} } }
});
</script>
@endsection
