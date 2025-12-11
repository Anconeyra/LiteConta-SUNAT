<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('classification_rules', function (Blueprint $table) {
            $table->foreign(['company_id'], 'classification_rules_ibfk_1')->references(['id'])->on('companies')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['partner_id'], 'classification_rules_ibfk_2')->references(['id'])->on('partners')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['suggested_category_id'], 'classification_rules_ibfk_3')->references(['id'])->on('categories')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classification_rules', function (Blueprint $table) {
            $table->dropForeign('classification_rules_ibfk_1');
            $table->dropForeign('classification_rules_ibfk_2');
            $table->dropForeign('classification_rules_ibfk_3');
        });
    }
};
