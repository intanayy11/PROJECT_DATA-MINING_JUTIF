<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ArmRule extends Model {
    protected $fillable = ['antecedents','consequents','support','confidence','lift','leverage','conviction'];
}
