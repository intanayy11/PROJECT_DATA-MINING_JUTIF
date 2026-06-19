<?php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArmRule;
use App\Models\FrequentItemset;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() {
        $totalArticles  = Article::count();
        $totalClusters  = Article::whereNotNull('cluster')->distinct('cluster')->count('cluster');
        $totalRules     = ArmRule::count();
        $totalItemsets  = FrequentItemset::count();
        $yearRange      = Article::selectRaw('MIN(year) as min_y, MAX(year) as max_y')->first();

        // Distribusi per tahun
        $perYear = Article::whereNotNull('year')
            ->selectRaw('year, COUNT(*) as total')
            ->groupBy('year')->orderBy('year')
            ->pluck('total','year');

        // Distribusi per cluster
        $perCluster = Article::whereNotNull('cluster')
            ->selectRaw('cluster, cluster_label, COUNT(*) as total')
            ->groupBy('cluster','cluster_label')
            ->orderBy('cluster')
            ->get();

        // Tren cluster per tahun
        $clusterTrend = Article::whereNotNull('cluster')->whereNotNull('year')
            ->selectRaw('cluster, cluster_label, year, COUNT(*) as total')
            ->groupBy('cluster','cluster_label','year')
            ->orderBy('year')->orderBy('cluster')
            ->get()
            ->groupBy('cluster');

        // Top keywords dari frequent itemsets (1-itemset)
        $topKeywords = FrequentItemset::where('length', 1)
            ->orderByDesc('support')->limit(15)->get();

        // Top ARM rules
        $topRules = ArmRule::orderByDesc('lift')->limit(5)->get();

        // Artikel terbaru
        $latestArticles = Article::orderByDesc('year')->limit(5)->get();

        $colors = Article::clusterColors();
        $years  = $perYear->keys();

        return view('dashboard.index', compact(
            'totalArticles','totalClusters','totalRules','totalItemsets',
            'yearRange','perYear','perCluster','clusterTrend',
            'topKeywords','topRules','latestArticles','colors', 'years'
        ));
    }
}
