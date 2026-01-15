<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->morphs('fileable');
            $table->timestamps();
        });

        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable()->index();
            $table->string('taxonomy', 63)->default('files')->index();
            $table->unsignedInteger('user_id')->default(0)->index();
            $table->unsignedInteger('related_id')->default(0)->index();
            $table->string('file_name', 255);
            $table->string('file_ext', 15)->nullable();
            $table->string('file_password', 63)->nullable();
            $table->unsignedInteger('file_size')->default(0);
            $table->string('file_mime', 127)->nullable();
            $table->string('file_path', 255);
            $table->string('file_url', 255)->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('ordering')->default(0);
            $table->timestamps();

            $table->index(['taxonomy', 'related_id']);
            $table->index(['user_id', 'taxonomy']);
            $table->index(['customer_id', 'user_id']);
            $table->index('created_at');
        });

        Schema::create('pdf_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('files')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('page_number')->index();
            $table->float('x_position');
            $table->float('y_position');
            $table->text('content');
            $table->string('color')->default('#FFFF00');
            $table->timestamps();

            $table->index(['file_id', 'page_number']);
        });

        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('signable');
            $table->text('signature_data');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('signed_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_signatures');
        Schema::dropIfExists('pdf_notes');
        Schema::dropIfExists('file_uploads');
        Schema::dropIfExists('files');
    }
};
