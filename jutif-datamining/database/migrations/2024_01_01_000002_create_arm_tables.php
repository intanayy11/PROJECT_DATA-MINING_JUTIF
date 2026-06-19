<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('arm_rules', function (Blueprint $table) {
            $table->id();
            $table->string('antecedents', 255);
            $table->string('consequents', 255);
            $table->float('support');
            $table->float('confidence');
            $table->float('lift');
            $table->float('leverage')->nullable();
            $table->float('conviction')->nullable();
            $table->timestamps();
        });

        Schema::create('frequent_itemsets', function (Blueprint $table) {
            $table->id();
            $table->string('itemset', 255);
            $table->float('support');
            $table->integer('length');
            $table->integer('frequency')->nullable();
            $table->timestamps();
        });

        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('type'); // articles, arm, clustering
            $table->integer('rows_imported');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('arm_rules');
        Schema::dropIfExists('frequent_itemsets');
        Schema::dropIfExists('import_logs');
    }
};
