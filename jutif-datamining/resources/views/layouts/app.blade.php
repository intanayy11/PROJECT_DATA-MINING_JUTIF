<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JUTIF Research Analyzer') — JUTIF Analyzer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style type="text/tailwindcss">
        @layer utilities {
            .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 w-full; }
            .sidebar-link:hover { @apply bg-blue-700 text-white; }
            .sidebar-link.active { @apply bg-white text-blue-700 font-bold shadow; }
            .card { @apply bg-white rounded-xl shadow-sm border border-gray-100 p-5; }
            .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold; }
            .btn { @apply inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150; }
            .btn-primary { @apply bg-blue-600 text-white hover:bg-blue-700; }
            .btn-outline { @apply border border-gray-300 text-gray-700 hover:bg-gray-50; }
            .table-row:hover { @apply bg-blue-50; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- SIDEBAR -->
<div class="flex h-screen overflow-hidden">
    <aside class="w-64 bg-blue-800 text-white flex flex-col flex-shrink-0 overflow-y-auto">
        <!-- Logo -->
        <div class="px-6 py-5 border-b border-blue-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-network text-blue-700 text-lg"></i>
                </div>
                <div>
                    <div class="font-bold text-base leading-tight">JUTIF Analyzer</div>
                    <div class="text-blue-300 text-xs">Research Topic Mapper</div>
                </div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <div class="text-blue-400 text-xs font-semibold uppercase px-4 mb-2">Overview</div>
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : 'text-blue-100' }}">
                <i class="fas fa-gauge-high w-4"></i> Dashboard
            </a>

            <div class="text-blue-400 text-xs font-semibold uppercase px-4 mt-4 mb-2">Analisis</div>
            <a href="{{ route('clustering.index') }}"
               class="sidebar-link {{ request()->routeIs('clustering.*') ? 'active' : 'text-blue-100' }}">
                <i class="fas fa-layer-group w-4"></i> K-Means Clustering
            </a>
            <a href="{{ route('arm.index') }}"
               class="sidebar-link {{ request()->routeIs('arm.*') ? 'active' : 'text-blue-100' }}">
                <i class="fas fa-arrow-right-arrow-left w-4"></i> Association Rules
            </a>
            <a href="{{ route('arm.itemsets') }}"
               class="sidebar-link {{ request()->routeIs('arm.itemsets') ? 'active' : 'text-blue-100' }}">
                <i class="fas fa-tags w-4"></i> Frequent Itemsets
            </a>
            <a href="{{ route('clustering.pca') }}"
               class="sidebar-link {{ request()->routeIs('clustering.pca') ? 'active' : 'text-blue-100' }}">
                <i class="fas fa-braille w-4"></i> Visualisasi PCA
            </a>

            <div class="text-blue-400 text-xs font-semibold uppercase px-4 mt-4 mb-2">Data</div>
            <a href="{{ route('articles.index') }}"
               class="sidebar-link {{ request()->routeIs('articles.*') ? 'active' : 'text-blue-100' }}">
                <i class="fas fa-newspaper w-4"></i> Data Artikel
            </a>
            <a href="{{ route('import.index') }}"
               class="sidebar-link {{ request()->routeIs('import.*') ? 'active' : 'text-blue-100' }}">
                <i class="fas fa-file-import w-4"></i> Import Data
            </a>
        </nav>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-blue-700 text-xs text-blue-400">
            <div class="font-medium text-blue-200">JUTIF UNSOED Analyzer</div>
            <div>Teknik Informatika © 2025</div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto">
        <!-- Top bar -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 class="text-lg font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-gray-500">@yield('page-subtitle', 'JUTIF Research Topic Trend Analyzer')</p>
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
                <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1.5 rounded-full">
                    <i class="fas fa-database mr-1"></i>
                    {{ \App\Models\Article::count() }} artikel
                </span>
            </div>
        </header>

        <!-- Alerts -->
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4 flex items-center gap-2 text-sm">
                    <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4 text-sm">
                    <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif
        </div>

        <div class="px-8 py-4">
            @yield('content')
        </div>
    </main>
</div>

@yield('scripts')
</body>
</html>
