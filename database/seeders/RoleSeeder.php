<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use App\Models\StudentProfile;
use App\Models\StaffProfile;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'SUPER_ADMIN', 'description' => 'Quản trị viên hệ thống - Toàn quyền', 'status' => 'active'],
            ['id' => 2, 'name' => 'ADMIN', 'description' => 'Quản trị viên - Quản lý hệ thống', 'status' => 'active'],
            ['id' => 3, 'name' => 'LIBRARIAN', 'description' => 'Thủ thư - Quản lý sách và mượn trả', 'status' => 'active'],
            ['id' => 4, 'name' => 'LECTURER', 'description' => 'Giảng viên - Mượn sách với quyền cao hơn', 'status' => 'active'],
            ['id' => 5, 'name' => 'STUDENT', 'description' => 'Sinh viên - Mượn sách', 'status' => 'active'],
        ];
        DB::table('roles')->insertOrIgnore($roles);

        $permissions = [
            ['id' => 1, 'name' => 'manage_users', 'description' => 'Quản lý người dùng', 'group' => 'users'],
            ['id' => 2, 'name' => 'manage_books', 'description' => 'Quản lý sách', 'group' => 'books'],
            ['id' => 3, 'name' => 'manage_book_copies', 'description' => 'Quản lý bản sao sách', 'group' => 'books'],
            ['id' => 4, 'name' => 'borrow_books', 'description' => 'Mượn sách', 'group' => 'borrows'],
            ['id' => 5, 'name' => 'return_books', 'description' => 'Trả sách', 'group' => 'borrows'],
            ['id' => 6, 'name' => 'manage_borrows', 'description' => 'Quản lý phiếu mượn', 'group' => 'borrows'],
            ['id' => 7, 'name' => 'manage_fines', 'description' => 'Quản lý phạt', 'group' => 'fines'],
            ['id' => 8, 'name' => 'manage_payments', 'description' => 'Quản lý thanh toán', 'group' => 'fines'],
            ['id' => 9, 'name' => 'view_reports', 'description' => 'Xem báo cáo', 'group' => 'reports'],
            ['id' => 10, 'name' => 'manage_rules', 'description' => 'Quản lý quy tắc thư viện', 'group' => 'settings'],
            ['id' => 11, 'name' => 'manage_categories', 'description' => 'Quản lý danh mục', 'group' => 'books'],
            ['id' => 12, 'name' => 'import_export', 'description' => 'Import/Export Excel', 'group' => 'tools'],
        ];
        DB::table('permissions')->insertOrIgnore($permissions);

        $adminPermissions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        foreach ($adminPermissions as $permissionId) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id' => 1,
                'permission_id' => $permissionId,
            ]);
            DB::table('role_permissions')->insertOrIgnore([
                'role_id' => 2,
                'permission_id' => $permissionId,
            ]);
        }

        $librarianPermissions = [2, 3, 4, 5, 6, 7, 8, 9, 11, 12];
        foreach ($librarianPermissions as $permissionId) {
            DB::table('role_permissions')->insertOrIgnore([
                'role_id' => 3,
                'permission_id' => $permissionId,
            ]);
        }

        DB::table('role_permissions')->insertOrIgnore([
            ['role_id' => 4, 'permission_id' => 4],
            ['role_id' => 4, 'permission_id' => 9],
        ]);

        DB::table('role_permissions')->insertOrIgnore([
            ['role_id' => 5, 'permission_id' => 4],
        ]);

        $users = [
            [
                'id' => 1,
                'name' => 'Admin UTC',
                'email' => 'admin@utc.edu.vn',
                'password' => Hash::make('123456'),
                'user_code' => 'ADMIN001',
                'status' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Thủ Thư Nguyễn Thị Lan',
                'email' => 'librarian@utc.edu.vn',
                'password' => Hash::make('123456'),
                'user_code' => 'LIB001',
                'status' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Nguyễn Văn Sinh Viên',
                'email' => 'student@utc.edu.vn',
                'password' => Hash::make('123456'),
                'user_code' => 'SV20250001',
                'status' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'PGS.TS Trần Văn Giảng Viên',
                'email' => 'lecturer@utc.edu.vn',
                'password' => Hash::make('123456'),
                'user_code' => 'GV20250001',
                'status' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(['email' => $user['email']], $user);
        }

        DB::table('user_roles')->insertOrIgnore([
            ['user_id' => 1, 'role_id' => 1],
            ['user_id' => 2, 'role_id' => 3],
            ['user_id' => 3, 'role_id' => 5],
            ['user_id' => 4, 'role_id' => 4],
        ]);

        $studentCustomer = [
            'user_id' => 3,
            'user_type' => 'student',
            'identity_card' => '001234567890',
            'first_name' => 'Văn Sinh Viên',
            'last_name' => 'Nguyễn',
            'full_name' => 'Nguyễn Văn Sinh Viên',
            'phone' => '0987654321',
            'email' => 'student@utc.edu.vn',
            'address' => 'Hà Nội',
            'birthday' => Carbon::parse('2005-05-15'),
            'gender' => 'male',
            'card_number' => 'LIB-2025-001',
            'department' => 'Khoa Công nghệ thông tin',
            'card_issue_date' => Carbon::now()->subMonths(1),
            'card_expiry_date' => Carbon::now()->addYears(4),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('customers')->updateOrInsert(['user_id' => 3], $studentCustomer);

        $studentProfile = [
            'customer_id' => DB::table('customers')->where('user_id', 3)->value('id'),
            'student_code' => 'SV20250001',
            'class_name' => 'IT-K64',
            'major' => 'Kỹ thuật phần mềm',
            'student_year' => 'K2024',
            'enrollment_date' => Carbon::parse('2024-09-01'),
            'graduation_date' => Carbon::parse('2028-06-30'),
            'gpa' => 3.5,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('student_profiles')->updateOrInsert(['customer_id' => $studentProfile['customer_id']], $studentProfile);

        $lecturerCustomer = [
            'user_id' => 4,
            'user_type' => 'lecturer',
            'identity_card' => '001234567891',
            'first_name' => 'Văn Giảng Viên',
            'last_name' => 'Trần',
            'full_name' => 'Trần Văn Giảng Viên',
            'phone' => '0912345678',
            'email' => 'lecturer@utc.edu.vn',
            'address' => 'Hà Nội',
            'birthday' => Carbon::parse('1980-03-20'),
            'gender' => 'male',
            'card_number' => 'LIB-2025-002',
            'department' => 'Khoa Công nghệ thông tin',
            'card_issue_date' => Carbon::now()->subMonths(6),
            'card_expiry_date' => Carbon::now()->addYears(5),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('customers')->updateOrInsert(['user_id' => 4], $lecturerCustomer);

        $staffProfile = [
            'customer_id' => DB::table('customers')->where('user_id', 4)->value('id'),
            'staff_code' => 'GV20250001',
            'employee_id' => 'EMP20250001',
            'position' => 'Giảng viên',
            'academic_rank' => 'Phó Giáo sư, Tiến sĩ',
            'specialization' => 'Khoa học máy tính',
            'hire_date' => Carbon::parse('2010-09-01'),
            'contract_start_date' => Carbon::parse('2024-01-01'),
            'contract_end_date' => Carbon::parse('2029-12-31'),
            'contract_type' => 'fulltime',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('staff_profiles')->updateOrInsert(['customer_id' => $staffProfile['customer_id']], $staffProfile);

        $librarianCustomer = [
            'user_id' => 2,
            'user_type' => 'librarian',
            'identity_card' => '001234567892',
            'first_name' => 'Thị Lan',
            'last_name' => 'Nguyễn',
            'full_name' => 'Nguyễn Thị Lan',
            'phone' => '0901234567',
            'email' => 'librarian@utc.edu.vn',
            'address' => 'Hà Nội',
            'birthday' => Carbon::parse('1990-08-15'),
            'gender' => 'female',
            'card_number' => 'LIB-2025-003',
            'department' => 'Thư viện',
            'card_issue_date' => Carbon::now()->subMonths(12),
            'card_expiry_date' => Carbon::now()->addYears(10),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('customers')->updateOrInsert(['user_id' => 2], $librarianCustomer);

        $librarianStaffProfile = [
            'customer_id' => DB::table('customers')->where('user_id', 2)->value('id'),
            'staff_code' => 'LIB001',
            'employee_id' => 'EMP20250002',
            'position' => 'Thủ thư',
            'academic_rank' => 'Thạc sĩ',
            'specialization' => 'Quản lý thư viện',
            'hire_date' => Carbon::parse('2015-01-01'),
            'contract_start_date' => Carbon::parse('2024-01-01'),
            'contract_end_date' => Carbon::parse('2029-12-31'),
            'contract_type' => 'fulltime',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('staff_profiles')->updateOrInsert(['customer_id' => $librarianStaffProfile['customer_id']], $librarianStaffProfile);

        $adminCustomer = [
            'user_id' => 1,
            'user_type' => 'superadmin',
            'identity_card' => '001234567893',
            'first_name' => 'UTC',
            'last_name' => 'Admin',
            'full_name' => 'Admin UTC',
            'phone' => '0241234567',
            'email' => 'admin@utc.edu.vn',
            'address' => 'Hà Nội',
            'birthday' => Carbon::parse('1985-01-01'),
            'gender' => 'male',
            'card_number' => 'LIB-2025-000',
            'department' => 'Phòng Công nghệ thông tin',
            'card_issue_date' => Carbon::now()->subYears(2),
            'card_expiry_date' => Carbon::now()->addYears(10),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('customers')->updateOrInsert(['user_id' => 1], $adminCustomer);

        $adminStaffProfile = [
            'customer_id' => DB::table('customers')->where('user_id', 1)->value('id'),
            'staff_code' => 'ADMIN001',
            'employee_id' => 'EMP20250000',
            'position' => 'Quản trị viên hệ thống',
            'academic_rank' => 'Cử nhân',
            'specialization' => 'Quản trị hệ thống',
            'hire_date' => Carbon::parse('2020-01-01'),
            'contract_start_date' => Carbon::parse('2024-01-01'),
            'contract_end_date' => Carbon::parse('2030-12-31'),
            'contract_type' => 'fulltime',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        DB::table('staff_profiles')->updateOrInsert(['customer_id' => $adminStaffProfile['customer_id']], $adminStaffProfile);
    }
}
