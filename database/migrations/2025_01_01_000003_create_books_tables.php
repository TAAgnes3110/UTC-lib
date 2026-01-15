<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('publisher_id')->nullable()->constrained('publishers')->onDelete('set null');
            $table->string('title')->index();
            $table->string('author')->index();
            $table->year('publication_year')->nullable()->index();
            $table->string('publication_place')->nullable();
            $table->string('language')->default('Vietnamese');
            $table->integer('number_of_pages')->nullable();
            $table->string('isbn')->unique()->nullable()->index();
            $table->string('call_number')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_ebook')->default(false)->index();
            $table->string('ebook_file_path')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->integer('total_copies')->default(0);
            $table->integer('available_copies')->default(0)->index();
            $table->string('status')->default('active')->index();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index(['status', 'available_copies']);
            $table->index(['is_ebook', 'status']);
        });

        Schema::create('book_author', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['book_id', 'author_id']);
        });

        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('barcode')->unique();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('status')->default('available')->index();
            $table->string('condition')->default('new')->index();
            $table->date('import_date')->nullable()->index();
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['book_id', 'status']);
            $table->index(['status', 'condition']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('books');
    }
};
