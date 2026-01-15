<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('group')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->unique();
            $table->string('user_type')->index()->comment('student, lecturer, librarian, admin, superadmin');
            $table->string('identity_card', 20)->unique()->index()->comment('Mã định danh (CCCD/CMND) - Bắt buộc');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->index()->comment('Họ và tên đầy đủ');
            $table->string('phone', 20)->index();
            $table->string('email')->nullable()->index();
            $table->string('address');
            $table->date('birthday');
            $table->string('gender', 10)->comment('male, female, other');
            $table->string('card_number')->unique()->nullable()->comment('Mã thẻ thư viện');
            $table->string('department')->nullable()->index()->comment('Khoa/Viện');
            $table->date('card_issue_date')->nullable()->comment('Ngày cấp thẻ');
            $table->date('card_expiry_date')->nullable()->index()->comment('Ngày hết hạn thẻ');
            $table->string('status')->default('active')->index()->comment('active, inactive, graduated, suspended, expired, retired');
            $table->json('params')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_type', 'status']);
            $table->index(['card_expiry_date', 'status']);
            $table->index(['department', 'user_type']);
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->unique();
            $table->string('student_code')->unique()->index()->comment('Mã sinh viên');
            $table->string('class_name')->nullable()->index()->comment('Lớp');
            $table->string('major')->nullable()->index()->comment('Chuyên ngành');
            $table->string('student_year')->nullable()->index()->comment('Khóa học (VD: K2024)');
            $table->date('enrollment_date')->nullable()->comment('Ngày nhập học');
            $table->date('graduation_date')->nullable()->comment('Ngày tốt nghiệp dự kiến');
            $table->decimal('gpa', 3, 2)->nullable()->comment('Điểm trung bình');
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index(['student_code', 'student_year']);
            $table->index(['major', 'class_name']);
        });

        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->unique();
            $table->string('staff_code')->unique()->index()->comment('Mã nhân viên/Giáo viên');
            $table->string('employee_id')->unique()->nullable()->index()->comment('Mã nhân viên (nếu có)');
            $table->string('position')->nullable()->index()->comment('Chức vụ: Giảng viên, Thủ thư, Trưởng khoa, etc.');
            $table->string('academic_rank')->nullable()->comment('Học hàm: Thạc sĩ, Tiến sĩ, Phó Giáo sư, Giáo sư');
            $table->string('specialization')->nullable()->comment('Chuyên ngành giảng dạy');
            $table->date('hire_date')->nullable()->comment('Ngày vào làm');
            $table->date('contract_start_date')->nullable()->comment('Ngày bắt đầu hợp đồng');
            $table->date('contract_end_date')->nullable()->comment('Ngày kết thúc hợp đồng');
            $table->string('contract_type')->nullable()->comment('Loại hợp đồng: fulltime, parttime, contract');
            $table->json('params')->nullable();
            $table->timestamps();

            $table->index(['staff_code', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
