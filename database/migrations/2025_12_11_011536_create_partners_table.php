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
        Schema::create('partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index('company_id');
            $table->enum('document_type', ['RUC', 'DNI', 'CE'])->nullable()->default('RUC');
            $table->string('document_number', 15);
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('status_sunat', 50)->nullable();
            $table->string('condition_sunat', 50)->nullable();
            $table->boolean('is_supplier')->nullable()->default(false);
            $table->boolean('is_customer')->nullable()->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
