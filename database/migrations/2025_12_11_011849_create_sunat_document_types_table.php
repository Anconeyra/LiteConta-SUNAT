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
        Schema::create('sunat_document_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 2)->unique('code');
            $table->string('description', 100);
            $table->string('short_name', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunat_document_types');
    }
};
