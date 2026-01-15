<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('period_reports', function (Blueprint $table) {
            $table->id();
            $table->string('period')->index();
            $table->string('report_type')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('data')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->index();
            $table->string('status')->default('draft')->index();
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index(['period', 'report_type']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('period_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('period')->index();
            $table->date('stat_date')->index();
            $table->string('stat_type')->index();
            $table->integer('total_borrows')->default(0);
            $table->integer('total_returns')->default(0);
            $table->integer('total_new_books')->default(0);
            $table->integer('total_new_users')->default(0);
            $table->decimal('total_fines', 12, 2)->default(0);
            $table->decimal('total_payments', 12, 2)->default(0);
            $table->json('details')->nullable();
            $table->json('params')->nullable();
            $table->timestamps();

            $table->unique(['period', 'stat_date', 'stat_type']);
            $table->index(['period', 'stat_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('period_statistics');
        Schema::dropIfExists('period_reports');
    }
};
