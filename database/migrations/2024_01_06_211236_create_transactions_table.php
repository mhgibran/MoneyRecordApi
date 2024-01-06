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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_category_id');
            $table->uuid('card_source_id')->nullable();
            $table->uuid('card_target_id');
            $table->boolean('type');
            $table->date('trx_date');
            $table->string('trx_number', 10)->unique();
            $table->string('description',100)->nullable();
            $table->double('amount', 10, 2);
            $table->timestamps();
            $table->foreign('transaction_category_id')
                    ->references('id')
                    ->on('transaction_categories')
                    ->onDelete('cascade');
            $table->foreign('card_source_id')
                    ->references('id')
                    ->on('cards')
                    ->onDelete('cascade');
            $table->foreign('card_target_id')
                    ->references('id')
                    ->on('cards')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
