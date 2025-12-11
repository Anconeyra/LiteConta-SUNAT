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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign(['company_id'], 'documents_ibfk_1')->references(['id'])->on('companies')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['partner_id'], 'documents_ibfk_2')->references(['id'])->on('partners')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['category_id'], 'documents_ibfk_3')->references(['id'])->on('categories')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['sunat_type_id'], 'documents_ibfk_4')->references(['id'])->on('sunat_document_types')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign('documents_ibfk_1');
            $table->dropForeign('documents_ibfk_2');
            $table->dropForeign('documents_ibfk_3');
            $table->dropForeign('documents_ibfk_4');
        });
    }
};
