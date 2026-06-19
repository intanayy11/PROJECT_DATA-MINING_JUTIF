@extends('layouts.app')
@section('title','Visualisasi PCA')
@section('page-title','Visualisasi PCA 2D')
@section('page-subtitle','Distribusi artikel dalam ruang fitur PCA (2 komponen utama)')

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
    <p class="text-xs text-gray-400">Hover titik untuk melihat judul artikel. Setiap titik merepresentasikan 1 artikel.</p>
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
                        return [d.title?.substring(0,60)+'...', 'Tahun: '+d.year];
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
