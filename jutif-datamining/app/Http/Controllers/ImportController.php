<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArmRule;
use App\Models\FrequentItemset;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function index() {
        $logs = DB::table('import_logs')->orderByDesc('created_at')->get();
        $counts = [
            'articles'  => Article::count(),
            'arm_rules' => ArmRule::count(),
            'itemsets'  => FrequentItemset::count(),
        ];
        return view('import.index', compact('logs','counts'));
    }

    public function importArticles(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());

        // Coba sheet "Dataset Clustering" dulu, fallback ke sheet pertama
        $sheetNames = $spreadsheet->getSheetNames();
        $targetSheet = in_array('Dataset Clustering', $sheetNames) ? 'Dataset Clustering' : $sheetNames[0];
        $sheet = $spreadsheet->getSheetByName($targetSheet);
        $rows  = $sheet->toArray(null, true, true, true);

        // Deteksi header row
        $headers = array_map('strtolower', array_map('trim', $rows[1]));
        $map = [];
        foreach ($headers as $col => $h) {
            if (str_contains($h,'title'))           $map['title']             = $col;
            if (str_contains($h,'author'))          $map['authors']           = $col;
            if ($h === 'year' || $h === 'tahun')    $map['year']              = $col;
            if (str_contains($h,'keywords_clean') || str_contains($h,'keyword')) $map['keywords_clean'] = $col;
            if (str_contains($h,'kw_normalized'))   $map['kw_normalized_str'] = $col;
            if (str_contains($h,'tokens_count'))    $map['kw_tokens_count']   = $col;
            if ($h === 'cluster id' || $h === 'cluster') $map['cluster']      = $col;
            if (str_contains($h,'label'))           $map['cluster_label']     = $col;
            if (str_contains($h,'pca x') || $h==='pca_x') $map['pca_x']      = $col;
            if (str_contains($h,'pca y') || $h==='pca_y') $map['pca_y']      = $col;
            if (str_contains($h,'url'))             $map['url']               = $col;
        }

        DB::table('articles')->truncate();
        $count = 0;
        $batch = [];

        foreach ($rows as $i => $row) {
            if ($i === 1) continue; // skip header
            if (empty($row[$map['title'] ?? 'A'])) continue;

            $batch[] = [
                'title'             => $this->val($row, $map, 'title'),
                'authors'           => $this->val($row, $map, 'authors'),
                'year'              => (int)$this->val($row, $map, 'year') ?: null,
                'keywords_clean'    => $this->val($row, $map, 'keywords_clean'),
                'kw_normalized_str' => $this->val($row, $map, 'kw_normalized_str'),
                'kw_tokens_count'   => (int)$this->val($row, $map, 'kw_tokens_count') ?: null,
                'cluster'           => is_numeric($this->val($row, $map, 'cluster')) ? (int)$this->val($row, $map, 'cluster') : null,
                'cluster_label'     => $this->val($row, $map, 'cluster_label'),
                'pca_x'             => (float)$this->val($row, $map, 'pca_x') ?: null,
                'pca_y'             => (float)$this->val($row, $map, 'pca_y') ?: null,
                'url'               => $this->val($row, $map, 'url'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
            $count++;
            if (count($batch) >= 200) { DB::table('articles')->insert($batch); $batch = []; }
        }
        if ($batch) DB::table('articles')->insert($batch);

        DB::table('import_logs')->insert(['filename'=>$file->getClientOriginalName(),'type'=>'articles','rows_imported'=>$count,'created_at'=>now(),'updated_at'=>now()]);
        return back()->with('success', "✅ Berhasil import $count artikel!");
    }

    public function importArm(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());

        // Import Association Rules
        $sheetNames = $spreadsheet->getSheetNames();
        DB::table('arm_rules')->truncate();
        DB::table('frequent_itemsets')->truncate();

        $rulesCount = 0;
        $fiCount = 0;

        // Sheet: Association Rules
        $ruleSheets = ['Association Rules', 'Top 20 by Lift', 'Semua Rules'];
        foreach ($ruleSheets as $sn) {
            if (!in_array($sn, $sheetNames)) continue;
            $sheet = $spreadsheet->getSheetByName($sn);
            $rows  = $sheet->toArray(null, true, true, true);
            $headers = array_map('strtolower', array_map('trim', $rows[1]));
            $map = [];
            foreach ($headers as $col => $h) {
                if ($h === 'antecedents') $map['antecedents'] = $col;
                if ($h === 'consequents') $map['consequents'] = $col;
                // Pastikan yang diambil adalah 'support' murni, bukan 'antecedent support'
                if ($h === 'support')     $map['support']     = $col;
                if ($h === 'confidence')  $map['confidence']  = $col;
                if ($h === 'lift')        $map['lift']        = $col;
                if ($h === 'leverage')    $map['leverage']    = $col;
                if ($h === 'conviction')  $map['conviction']  = $col;
            }

            $batch = [];
            foreach ($rows as $i => $row) {
                if ($i === 1) continue;
                $ant = $this->val($row, $map, 'antecedents');
                if (empty($ant)) continue;
                
                $batch[] = [
                    'antecedents' => $ant,
                    'consequents' => $this->val($row, $map, 'consequents'),
                    'support'     => (float)$this->val($row, $map, 'support'),
                    'confidence'  => (float)$this->val($row, $map, 'confidence'),
                    'lift'        => (float)$this->val($row, $map, 'lift'),
                    'leverage'    => (float)$this->val($row, $map, 'leverage'),
                    'conviction'  => (float)$this->val($row, $map, 'conviction'),
                    'created_at'  => now(), 'updated_at' => now(),
                ];
                $rulesCount++;
            }
            if ($batch) DB::table('arm_rules')->insert($batch);
            break;
        }

        // Sheet: Frequent Itemsets
        $fiSheets = ['Frequent Itemsets'];
        foreach ($fiSheets as $sn) {
            if (!in_array($sn, $sheetNames)) continue;
            $sheet = $spreadsheet->getSheetByName($sn);
            $rows  = $sheet->toArray(null, true, true, true);
            $batch = [];
            foreach ($rows as $i => $row) {
                if ($i === 1) continue;
                $itemset = trim($row['A'] ?? ''); // Nama itemset harus di kolom A
                if (empty($itemset) || strtolower($itemset) === 'itemsets' || strtolower($itemset) === 'itemset') continue;
                
                $support = (float)($row['B'] ?? 0); // Support di kolom B
                $length  = (int)($row['C'] ?? 1);  // Length di kolom C
                $freq    = (int)round($support * 988);
                $batch[] = [
                    'itemset'    => $itemset,
                    'support'    => $support,
                    'length'     => $length ?: 1,
                    'frequency'  => $freq,
                    'created_at' => now(), 'updated_at' => now(),
                ];
                $fiCount++;
            }
            if ($batch) DB::table('frequent_itemsets')->insert($batch);
            break;
        }

        DB::table('import_logs')->insert(['filename'=>$file->getClientOriginalName(),'type'=>'arm','rows_imported'=>$rulesCount+$fiCount,'created_at'=>now(),'updated_at'=>now()]);
        return back()->with('success', "✅ Berhasil import $rulesCount rules + $fiCount frequent itemsets!");
    }

    private function val($row, $map, $key) {
        if (!isset($map[$key])) return null;
        return $row[$map[$key]] ?? null;
    }
}
