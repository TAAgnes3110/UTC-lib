<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('active')->index();
            $table->integer('sort_order')->default(0);
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('address')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('biography')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->string('nationality')->nullable();
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('address')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('library_rules', function (Blueprint $table) {
            $table->id();
            $table->string('user_type')->index();
            $table->integer('max_books')->default(3);
            $table->integer('borrow_days')->default(14);
            $table->decimal('fine_per_day', 10, 2)->default(0);
            $table->json('extra_config')->nullable();
            $table->string('status')->default('active')->index();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_rules');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
    }
};
