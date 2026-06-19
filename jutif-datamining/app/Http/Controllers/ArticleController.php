<?php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request) {
        $search  = $request->get('search');
        $year    = $request->get('year');
        $cluster = $request->get('cluster');

        $query = Article::query();
        if ($search) $query->where(function($q) use ($search) {
            $q->where('title','like',"%$search%")
              ->orWhere('keywords_clean','like',"%$search%")
              ->orWhere('authors','like',"%$search%");
        });
        if ($year)    $query->where('year', $year);
        if ($cluster !== null && $cluster !== '') $query->where('cluster', $cluster);

        $articles = $query->orderByDesc('year')->paginate(25)->withQueryString();
        $years    = Article::whereNotNull('year')->distinct()->orderBy('year')->pluck('year');
        $clusters = Article::whereNotNull('cluster')
            ->selectRaw('cluster, cluster_label')->distinct()
            ->orderBy('cluster')->get();
        $colors   = Article::clusterColors();

        return view('articles.index', compact('articles','years','clusters','colors','search','year','cluster'));
    }

    public function show($id) {
        $article = Article::findOrFail($id);
        $colors  = Article::clusterColors();
        $similar = Article::where('cluster', $article->cluster)
            ->where('id','!=',$id)->inRandomOrder()->limit(5)->get();
        return view('articles.show', compact('article','colors','similar'));
    }
}
