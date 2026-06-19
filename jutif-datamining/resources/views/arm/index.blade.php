@extends('layouts.app')
@section('title','Association Rules')
@section('page-title','Association Rule Mining')
@section('page-subtitle','Hasil Algoritma Apriori — min_support=0.03, min_confidence=0.25')

@section('header-actions')
<a href="{{ route('arm.itemsets') }}" class="btn btn-outline text-xs">
    <i class="fas fa-tags"></i> Frequent Itemsets
</a>
@endsection

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $armStats = [
        ['label'=>'Total Rules','value'=>$stats['total'],'icon'=>'fa-arrow-right-arrow-left','color'=>'blue'],
        ['label'=>'Max Lift','value'=>number_format($stats['max_lift'],4),'icon'=>'fa-rocket','color'=>'orange'],
        ['label'=>'Avg Confidence','value'=>number_format($stats['avg_conf']*100,1).'%','icon'=>'fa-percent','color'=>'green'],
        ['label'=>'Avg Lift','value'=>number_format($stats['avg_lift'],4),'icon'=>'fa-chart-line','color'=>'purple'],
    ];
    @endphp
    @foreach($armStats as $s)
    <div class="card flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-{{ $s['color'] }}-100 flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $s['icon'] }} text-{{ $s['color'] }}-600"></i>
        </div>
        <div>
            <div class="text-xl font-bold text-gray-800">{{ $s['value'] }}</div>
            <div class="text-xs text-gray-500">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Cari keyword</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Keyword..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Min Lift</label>
            <input type="number" name="min_lift" value="{{ $minLift }}" step="0.1" min="0"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Min Confidence</label>
            <input type="number" name="min_conf" value="{{ $minConf }}" step="0.05" min="0" max="1"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-28 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Urutkan</label>
            <select name="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="lift"       {{ $sortBy=='lift'?'selected':'' }}>Lift ↓</option>
                <option value="confidence" {{ $sortBy=='confidence'?'selected':'' }}>Confidence ↓</option>
                <option value="support"    {{ $sortBy=='support'?'selected':'' }}>Support ↓</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        <a href="{{ route('arm.index') }}" class="btn btn-outline">Reset</a>
    </form>
</div>

{{-- Rules Table --}}
<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-gray-700">Association Rules</h3>
        <span class="text-sm text-gray-500">{{ $rules->total() }} rules</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Antecedent (IF)</th>
                    <th class="w-6"></th>
                    <th class="text-left px-3 py-2 font-semibold text-gray-600">Consequent (THEN)</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600">Support</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600">Confidence</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600">Lift</th>
                    <th class="text-center px-3 py-2 font-semibold text-gray-600">Leverage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rules as $rule)
                @php
                    $liftColor = $rule->lift >= 5 ? 'text-green-600 font-bold' : ($rule->lift >= 2 ? 'text-orange-500 font-semibold' : 'text-gray-600');
                    $rowBg = $rule->lift >= 5 ? 'bg-green-50' : ($rule->lift >= 2 ? 'bg-yellow-50' : '');
                @endphp
                <tr class="table-row border-t border-gray-100 {{ $rowBg }}">
                    <td class="px-3 py-2.5">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded font-mono text-xs">{{ $rule->antecedents }}</span>
                    </td>
                    <td class="text-center text-gray-400"><i class="fas fa-arrow-right text-xs"></i></td>
                    <td class="px-3 py-2.5">
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono text-xs">{{ $rule->consequents }}</span>
                    </td>
                    <td class="text-center px-3 py-2 text-gray-600">{{ number_format($rule->support*100,2) }}%</td>
                    <td class="text-center px-3 py-2 text-gray-600">{{ number_format($rule->confidence*100,1) }}%</td>
                    <td class="text-center px-3 py-2 {{ $liftColor }}">{{ number_format($rule->lift,4) }}</td>
                    <td class="text-center px-3 py-2 text-gray-500">{{ number_format($rule->leverage,4) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-400">
                    Tidak ada rules. <a href="{{ route('import.index') }}" class="text-blue-500">Import data ARM</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $rules->links() }}</div>

    <div class="mt-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-500">
        <strong>Keterangan:</strong>
        <span class="inline-flex items-center gap-1 mx-2"><span class="w-3 h-3 bg-green-50 border border-green-200 rounded"></span> Lift ≥ 5 (sangat kuat)</span>
        <span class="inline-flex items-center gap-1 mx-2"><span class="w-3 h-3 bg-yellow-50 border border-yellow-200 rounded"></span> Lift ≥ 2 (kuat)</span>
    </div>
</div>
@endsection
