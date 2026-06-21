<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JUTIF Research Analyzer — Beranda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        @layer base {
            body { font-family: 'Outfit', sans-serif; }
        }
        .card-glass { @apply bg-white/70 backdrop-blur-lg border border-white shadow-xl shadow-blue-900/5; }
        .text-gradient { @apply bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-700; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 selection:bg-blue-200 overflow-x-hidden">
    
    <!-- Background Accents -->
    <div class="fixed top-0 -z-10 h-full w-full bg-white">
        <div class="absolute top-0 left-0 right-0 h-[500px] bg-gradient-to-b from-blue-50 to-transparent"></div>
        <div class="absolute top-[10%] left-[-10%] h-[400px] w-[400px] rounded-full bg-blue-100/50 blur-[100px]"></div>
        <div class="absolute bottom-[10%] right-[-10%] h-[400px] w-[400px] rounded-full bg-indigo-100/50 blur-[100px]"></div>
    </div>

    <!-- Navigation -->
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                <i class="fas fa-chart-network text-white text-xl"></i>
            </div>
            <span class="text-2xl font-extrabold tracking-tight text-slate-900">JUTIF<span class="text-blue-600">Analyzer</span></span>
        </div>
        <div class="hidden md:flex items-center gap-4 text-sm font-semibold">
            <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition-all shadow-lg shadow-blue-600/25 flex items-center gap-2 active:scale-95">
                Buka Dashboard <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="container mx-auto px-6 pt-4 pb-24 relative z-10">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="flex-1 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-600 text-xs font-bold mb-8 uppercase tracking-wider">
                    <span class="flex h-2 w-2 rounded-full bg-blue-600 animate-ping"></span>
                    Sistem Analisis Topik Otomatis
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.1] mb-8 text-slate-900">
                    Petakan Ilmu <br><span class="text-gradient">Teknik Informatika</span> Jauh Lebih Cepat.
                </h1>
                <p class="text-lg text-slate-500 max-w-xl leading-relaxed mb-12 mx-auto lg:mx-0">
                    Platform cerdas berbasis <span class="font-bold text-slate-700">Data Mining</span> untuk mengolah ribuan publikasi ilmiah JUTIF UNSOED menjadi wawasan visual yang berharga.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('dashboard') }}" class="px-10 py-5 bg-slate-900 hover:bg-slate-800 text-white text-lg font-bold rounded-2xl transition-all shadow-2xl flex items-center justify-center gap-3 active:scale-95 group">
                        Mulai Analisis
                        <i class="fas fa-bolt group-hover:text-yellow-400 transition-colors"></i>
                    </a>
                    <a href="#fitur" class="px-10 py-5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-lg font-bold rounded-2xl transition-all flex items-center justify-center gap-3 shadow-sm hover:shadow-md">
                        Detail Fitur
                    </a>
                </div>
            </div>

            <!-- Dashboard Mockup Image -->
            <div class="flex-1 relative">
                <div class="absolute -inset-10 bg-gradient-to-r from-blue-200 to-indigo-200 blur-3xl opacity-30 -z-10 rounded-full"></div>
                <div class="card-glass p-3 rounded-[2.5rem]">
                    <div class="bg-white rounded-[2rem] overflow-hidden border border-slate-100 relative">
                        <div class="flex gap-1.5 p-4 border-b border-slate-50">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="aspect-[4/3] bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center relative overflow-hidden">
                            <i class="fas fa-microchip text-[120px] text-blue-500/10"></i>
                            <div class="absolute inset-0 flex flex-col items-center justify-center p-8 text-center">
                                <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl shadow-xl rotate-3 mb-4">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <h4 class="font-bold text-slate-800 mb-2">Visualisasi Interaktif</h4>
                                <p class="text-xs text-slate-500">Melihat persebaran topik melalui algoritma Clustering K-Means</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section id="fitur" class="py-24 bg-white border-y border-slate-100">
        <div class="container mx-auto px-6 grid md:grid-cols-3 gap-12">
            <div class="text-center md:text-left">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                    <i class="fas fa-brain text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4">Weighted TF-IDF</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Penyaringan kata kunci dengan memberikan bobot ekstra pada judul artikel untuk klasifikasi yang lebih presisi.</p>
            </div>
            <div class="text-center md:text-left">
                <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                    <i class="fas fa-network-wired text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4">K-Means Cluster</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Pengelompokan otomatis artikel ke dalam 7 klaster tema penelitian utama berdasarkan kemiripan konten.</p>
            </div>
            <div class="text-center md:text-left">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                    <i class="fas fa-link text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-4">Association Rules</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Algoritma Apriori untuk menemukan pola relasi antar kata kunci jurnal yang sering digunakan secara bersamaan.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-slate-400 text-sm font-medium italic">"Membantu mahasiswa memetakan tren riset IT dengan sekali klik"</p>
            <div class="mt-8 text-xs text-slate-400 font-semibold tracking-widest uppercase">Teknik Informatika © 2025</div>
        </div>
    </footer>

</body>
</html>
