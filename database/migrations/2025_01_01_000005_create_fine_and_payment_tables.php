<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('borrow_id')->nullable()->constrained('borrows')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('reason');
            $table->string('status')->default('unpaid')->index();
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('created_at');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fine_id')->constrained('fines')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamp('payment_date')->index();
            $table->string('payment_method')->nullable()->index();
            $table->string('transaction_id')->nullable()->index();
            $table->json('gateway_log')->nullable();
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('fines');
    }
};
