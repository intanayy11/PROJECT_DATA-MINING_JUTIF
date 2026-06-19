<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Article extends Model {
    protected $fillable = [
        'title','authors','year','keywords_clean','kw_normalized_str',
        'kw_tokens_count','cluster','cluster_label','pca_x','pca_y','url'
    ];

    public static function clusterColors(): array {
        return [
            0 => ['bg'=>'#4472C4','text'=>'white','label'=>'Information Systems & Security'],
            1 => ['bg'=>'#ED7D31','text'=>'white','label'=>'Sentiment Analysis & Text Mining'],
            2 => ['bg'=>'#70AD47','text'=>'white','label'=>'Classical Machine Learning'],
            3 => ['bg'=>'#FFC000','text'=>'black','label'=>'CNN & Neural Network'],
            4 => ['bg'=>'#5B9BD5','text'=>'white','label'=>'Clustering & Data Mining'],
            5 => ['bg'=>'#E74C3C','text'=>'white','label'=>'Systems Dev. & Applications'],
            6 => ['bg'=>'#7030A0','text'=>'white','label'=>'Deep Learning & LSTM'],
        ];
    }
}
