<?php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClusteringController extends Controller
{
    public function index() {
        $summary = Article::whereNotNull('cluster')
            ->selectRaw('cluster, cluster_label, COUNT(*) as total,
                ROUND(COUNT(*)*100.0/(SELECT COUNT(*) FROM articles),1) as pct')
            ->groupBy('cluster','cluster_label')
            ->orderBy('cluster')->get();

        $trendRaw = Article::whereNotNull('cluster')->whereNotNull('year')
            ->selectRaw('cluster, cluster_label, year, COUNT(*) as total')
            ->groupBy('cluster','cluster_label','year')
            ->orderBy('cluster')->orderBy('year')->get();

        $years = Article::whereNotNull('year')->distinct()->orderBy('year')->pluck('year');

        $trend = [];
        foreach ($trendRaw as $r) {
            $trend[$r->cluster][$r->year] = $r->total;
        }

        $colors = Article::clusterColors();

        return view('clustering.index', compact('summary','trend','years','colors'));
    }

    public function show(Request $request, $clusterId) {
        $search = $request->get('search');
        $year   = $request->get('year');

        $query = Article::where('cluster', $clusterId);
        if ($search) $query->where(function($q) use ($search) {
            $q->where('title','like',"%$search%")
              ->orWhere('keywords_clean','like',"%$search%");
        });
        if ($year) $query->where('year', $year);

        $articles = $query->orderByDesc('year')->paginate(20)->withQueryString();
        $clusterInfo = Article::where('cluster', $clusterId)
            ->selectRaw('cluster, cluster_label, COUNT(*) as total')->groupBy('cluster','cluster_label')->first();

        $years = Article::where('cluster', $clusterId)->whereNotNull('year')
            ->distinct()->orderBy('year')->pluck('year');

        // Top keywords dalam cluster ini
        $kwRaw = Article::where('cluster', $clusterId)->whereNotNull('kw_normalized_str')
            ->pluck('kw_normalized_str');
        $kwCount = [];
        foreach ($kwRaw as $kws) {
            foreach (explode(';', $kws) as $kw) {
                $kw = trim(strtolower($kw));
                if ($kw) $kwCount[$kw] = ($kwCount[$kw] ?? 0) + 1;
            }
        }
        arsort($kwCount);
        $topKw = array_slice($kwCount, 0, 20, true);

        $colors = Article::clusterColors();

        return view('clustering.show', compact(
            'articles','clusterInfo','clusterId','years','topKw','colors','search','year'
        ));
    }

    public function pca() {
        $points = Article::whereNotNull('pca_x')->whereNotNull('pca_y')
            ->select('id','title','cluster','cluster_label','pca_x','pca_y','year')
            ->get();
        $colors = Article::clusterColors();
        return view('clustering.pca', compact('points','colors'));
    }
}
