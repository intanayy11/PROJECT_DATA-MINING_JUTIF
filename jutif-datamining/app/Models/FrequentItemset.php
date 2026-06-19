<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FrequentItemset extends Model {
    protected $fillable = ['itemset','support','length','frequency'];
}
