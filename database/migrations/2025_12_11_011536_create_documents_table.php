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
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->index('company_id');
            $table->unsignedBigInteger('partner_id')->nullable()->index('partner_id');
            $table->unsignedBigInteger('category_id')->nullable()->index('category_id');
            $table->unsignedBigInteger('sunat_type_id')->index('sunat_type_id');
            $table->enum('operation_type', ['purchase', 'sale']);
            $table->string('serie', 10);
            $table->integer('numero');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->char('currency', 3)->nullable()->default('PEN');
            $table->decimal('exchange_rate', 10, 4)->nullable()->default(1);
            $table->decimal('subtotal', 12)->nullable()->default(0);
            $table->decimal('igv', 12)->nullable()->default(0);
            $table->decimal('total', 12)->nullable()->default(0);
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('cdr_path')->nullable();
            $table->enum('status', ['registrado', 'anulado', 'procesando'])->nullable()->default('registrado');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
