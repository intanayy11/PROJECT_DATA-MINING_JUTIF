@extends('layouts.app')
@section('title','PCA Visualization')
@section('page-title','PCA 2D Visualization')
@section('page-subtitle','Article distribution in PCA feature space (2 principal components)')

@section('content')
<div class="card mb-4">
    <div class="flex flex-wrap gap-3 items-center mb-3">
        @foreach($colors as $cid => $c)
        <div class="flex items-center gap-1.5 text-xs">
            <div class="w-3 h-3 rounded-full" style="background:{{ $c['bg'] }}"></div>
            <span class="text-gray-600">C{{ $cid }}: {{ $c['label'] }}</span>
        </div>
        @endforeach
    </div>
    <p class="text-xs text-gray-400">Hover over points to view article titles. Each point represents 1 article.</p>
</div>

<div class="card">
    <canvas id="pcaChart" height="400"></canvas>
</div>
@endsection

@section('scripts')
<script>
const colorMap = @json(array_column($colors,'bg'));
const points   = @json($points);

// Group by cluster
const datasets = {};
for (const p of points) {
    const c = p.cluster ?? 99;
    if (!datasets[c]) datasets[c] = { label: 'C'+c, data: [], backgroundColor: colorMap[c]||'#999', pointRadius: 3, pointHoverRadius: 6 };
    datasets[c].data.push({ x: p.pca_x, y: p.pca_y, title: p.title, year: p.year });
}

new Chart(document.getElementById('pcaChart'), {
    type: 'scatter',
    data: { datasets: Object.values(datasets) },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 10 } },
            tooltip: {
                callbacks: {
                    label: ctx => {
                        const d = ctx.raw;
                        return [d.title?.substring(0,60)+'...', 'Year: '+d.year];
                    }
                }
            }
        },
        scales: {
            x: { title: { display: true, text: 'PCA Component 1' } },
            y: { title: { display: true, text: 'PCA Component 2' } }
        }
    }
});
</script>
@endsection
