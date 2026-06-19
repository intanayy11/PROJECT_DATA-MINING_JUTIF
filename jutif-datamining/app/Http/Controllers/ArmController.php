<?php
namespace App\Http\Controllers;

use App\Models\ArmRule;
use App\Models\FrequentItemset;
use Illuminate\Http\Request;

class ArmController extends Controller
{
    public function index(Request $request) {
        $minLift    = $request->get('min_lift', 1.0);
        $minConf    = $request->get('min_conf', 0.0);
        $search     = $request->get('search');
        $sortBy     = $request->get('sort', 'lift');

        $query = ArmRule::query();
        if ($minLift > 0) $query->where('lift', '>=', $minLift);
        if ($minConf > 0) $query->where('confidence', '>=', $minConf);
        if ($search) $query->where(function($q) use ($search) {
            $q->where('antecedents','like',"%$search%")
              ->orWhere('consequents','like',"%$search%");
        });
        $rules = $query->orderByDesc($sortBy)->paginate(20)->withQueryString();

        $stats = [
            'total'    => ArmRule::count(),
            'max_lift' => ArmRule::max('lift'),
            'avg_conf' => round(ArmRule::avg('confidence'), 4),
            'avg_lift' => round(ArmRule::avg('lift'), 4),
        ];

        return view('arm.index', compact('rules','stats','minLift','minConf','search','sortBy'));
    }

    public function itemsets(Request $request) {
        $length = $request->get('length');
        $search = $request->get('search');

        $query = FrequentItemset::query();
        if ($length) $query->where('length', $length);
        if ($search) $query->where('itemset','like',"%$search%");

        $itemsets = $query->orderByDesc('support')->paginate(30)->withQueryString();

        $counts = [
            1 => FrequentItemset::where('length',1)->count(),
            2 => FrequentItemset::where('length',2)->count(),
            3 => FrequentItemset::where('length',3)->count(),
        ];

        return view('arm.itemsets', compact('itemsets','counts','length','search'));
    }
}
