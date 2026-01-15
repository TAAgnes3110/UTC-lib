<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('borrow_date')->index();
            $table->date('due_date')->index();
            $table->date('return_date')->nullable()->index();
            $table->string('status')->default('borrowed')->index();
            $table->text('note')->nullable();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'due_date']);
            $table->index(['user_id', 'borrow_date']);
            $table->index('created_at');
        });

        Schema::create('borrow_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_id')->constrained('borrows')->onDelete('cascade');
            $table->foreignId('book_copy_id')->constrained('book_copies')->onDelete('cascade');
            $table->date('return_date')->nullable()->index();
            $table->string('status')->default('borrowed')->index();
            $table->string('condition_before')->nullable();
            $table->string('condition_after')->nullable();
            $table->decimal('fine_amount', 10, 2)->nullable();
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index(['borrow_id', 'status']);
            $table->index(['book_copy_id', 'status']);
        });

        Schema::create('borrow_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_id')->constrained('borrows')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('old_due_date');
            $table->date('new_due_date');
            $table->integer('extension_days');
            $table->text('reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending')->index();
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index(['borrow_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrow_extensions');
        Schema::dropIfExists('borrow_items');
        Schema::dropIfExists('borrows');
    }
};
