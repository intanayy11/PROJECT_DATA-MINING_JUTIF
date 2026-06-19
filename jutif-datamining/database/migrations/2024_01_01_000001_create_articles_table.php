<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('authors')->nullable();
            $table->integer('year')->nullable();
            $table->text('keywords_clean')->nullable();
            $table->text('kw_normalized_str')->nullable();
            $table->integer('kw_tokens_count')->nullable();
            $table->integer('cluster')->nullable();
            $table->string('cluster_label', 100)->nullable();
            $table->float('pca_x')->nullable();
            $table->float('pca_y')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('articles'); }
};
