<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClusteringController;
use App\Http\Controllers\ArmController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ImportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('clustering')->name('clustering.')->group(function () {
    Route::get('/',        [ClusteringController::class, 'index'])->name('index');
    Route::get('/pca',     [ClusteringController::class, 'pca'])->name('pca');
    Route::get('/{id}',    [ClusteringController::class, 'show'])->name('show');
});

Route::prefix('arm')->name('arm.')->group(function () {
    Route::get('/',         [ArmController::class, 'index'])->name('index');
    Route::get('/itemsets', [ArmController::class, 'itemsets'])->name('itemsets');
});

Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/',    [ArticleController::class, 'index'])->name('index');
    Route::get('/{id}',[ArticleController::class, 'show'])->name('show');
});

Route::prefix('import')->name('import.')->group(function () {
    Route::get('/',               [ImportController::class, 'index'])->name('index');
    Route::post('/articles',      [ImportController::class, 'importArticles'])->name('articles');
    Route::post('/arm',           [ImportController::class, 'importArm'])->name('arm');
});
