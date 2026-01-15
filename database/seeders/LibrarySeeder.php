<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Công nghệ thông tin',
                'code' => '004',
                'description' => 'Sách chuyên ngành Công nghệ thông tin, Lập trình, Khoa học máy tính',
                'status' => 'active',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Kinh tế vận tải',
                'code' => '330',
                'description' => 'Sách về Kinh tế, Logistics, Quản lý chuỗi cung ứng',
                'status' => 'active',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Toán học',
                'code' => '510',
                'description' => 'Giáo trình Toán cao cấp, Đại số, Giải tích',
                'status' => 'active',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Xây dựng',
                'code' => '624',
                'description' => 'Sách về Xây dựng, Kiến trúc, Cầu đường',
                'status' => 'active',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Cơ khí',
                'code' => '621',
                'description' => 'Sách về Cơ khí, Tự động hóa, Cơ điện tử',
                'status' => 'active',
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('categories')->insertOrIgnore($categories);

        $publishers = [
            [
                'id' => 1,
                'name' => 'NXB Giao thông Vận tải',
                'address' => 'Hà Nội',
                'phone' => '0243123456',
                'email' => 'contact@nxbgtvt.vn',
                'website' => 'https://nxbgtvt.vn',
                'description' => 'Nhà xuất bản chuyên về giao thông vận tải',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'NXB Giáo dục Việt Nam',
                'address' => 'Hà Nội',
                'phone' => '02438220801',
                'email' => 'contact@nxbgd.vn',
                'website' => 'https://nxbgd.vn',
                'description' => 'Nhà xuất bản giáo dục hàng đầu Việt Nam',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'NXB Tài Chính',
                'address' => 'Hà Nội',
                'phone' => '02438220802',
                'email' => 'contact@nxbtc.vn',
                'website' => null,
                'description' => 'Nhà xuất bản về tài chính và kinh tế',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('publishers')->insertOrIgnore($publishers);

        $authors = [
            [
                'id' => 1,
                'name' => 'Phạm Văn Ất',
                'biography' => 'Giảng viên lâu năm về lập trình',
                'birth_date' => Carbon::parse('1960-01-01'),
                'death_date' => null,
                'nationality' => 'Việt Nam',
                'description' => 'Tác giả nhiều giáo trình lập trình',
                'photo' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Đoàn Thị Hồng Vân',
                'biography' => 'Chuyên gia về logistics',
                'birth_date' => Carbon::parse('1975-05-15'),
                'death_date' => null,
                'nationality' => 'Việt Nam',
                'description' => 'Tác giả chuyên về logistics và quản lý chuỗi cung ứng',
                'photo' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Nguyễn Đình Trí',
                'biography' => 'Giáo sư Toán học',
                'birth_date' => Carbon::parse('1950-03-20'),
                'death_date' => null,
                'nationality' => 'Việt Nam',
                'description' => 'Tác giả nhiều giáo trình Toán cao cấp',
                'photo' => null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('authors')->insertOrIgnore($authors);

        $suppliers = [
            [
                'id' => 1,
                'name' => 'NXB Giao thông Vận tải',
                'contact_person' => 'Nguyễn Văn A',
                'phone' => '0243123456',
                'email' => 'contact@nxbgtvt.vn',
                'address' => 'Hà Nội',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Fahasa',
                'contact_person' => 'Trần Thị B',
                'phone' => '1900636467',
                'email' => 'contact@fahasa.com.vn',
                'address' => 'TP. Hồ Chí Minh',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'NXB Giáo dục Việt Nam',
                'contact_person' => 'Lê Văn C',
                'phone' => '02438220801',
                'email' => 'contact@nxbgd.vn',
                'address' => 'Hà Nội',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('suppliers')->insertOrIgnore($suppliers);

        DB::table('library_rules')->insertOrIgnore([
            [
                'user_type' => 'student',
                'max_books' => 5,
                'borrow_days' => 14,
                'fine_per_day' => 2000.00,
                'extra_config' => json_encode([
                    'max_renewal' => 3,
                    'renewal_days' => 7,
                    'block_if_fine_unpaid' => true,
                ]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_type' => 'lecturer',
                'max_books' => 10,
                'borrow_days' => 30,
                'fine_per_day' => 1000.00,
                'extra_config' => json_encode([
                    'max_renewal' => 5,
                    'renewal_days' => 14,
                    'block_if_fine_unpaid' => true,
                ]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_type' => 'librarian',
                'max_books' => 20,
                'borrow_days' => 60,
                'fine_per_day' => 0.00,
                'extra_config' => json_encode([
                    'max_renewal' => 10,
                    'renewal_days' => 30,
                ]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $books = [
            [
                'id' => 1,
                'category_id' => 1,
                'publisher_id' => 1,
                'title' => 'Nhập môn Lập trình C++',
                'author' => 'Phạm Văn Ất',
                'isbn' => '978-604-0-12345-6',
                'publication_year' => 2023,
                'publication_place' => 'Hà Nội',
                'language' => 'Vietnamese',
                'number_of_pages' => 350,
                'call_number' => '005.133',
                'description' => 'Giáo trình cơ bản về lập trình C++ dành cho sinh viên năm nhất',
                'metadata' => json_encode([
                    'level' => 'basic',
                    'format' => 'Bìa mềm',
                    'size' => '16x24 cm',
                ]),
                'cover_image' => null,
                'is_ebook' => false,
                'ebook_file_path' => null,
                'price' => 150000,
                'total_copies' => 10,
                'available_copies' => 9,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'category_id' => 2,
                'publisher_id' => 3,
                'title' => 'Logistics và Quản lý chuỗi cung ứng',
                'author' => 'Đoàn Thị Hồng Vân',
                'isbn' => '978-604-1-67890-1',
                'publication_year' => 2022,
                'publication_place' => 'Hà Nội',
                'language' => 'Vietnamese',
                'number_of_pages' => 420,
                'call_number' => '658.7',
                'description' => 'Tài liệu chuyên sâu về logistics và quản lý chuỗi cung ứng trong vận tải',
                'metadata' => json_encode([
                    'level' => 'advanced',
                    'format' => 'Bìa cứng',
                ]),
                'cover_image' => null,
                'is_ebook' => false,
                'ebook_file_path' => null,
                'price' => 200000,
                'total_copies' => 5,
                'available_copies' => 5,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'publisher_id' => 2,
                'title' => 'Lập trình Laravel Framework',
                'author' => 'Nguyễn Văn B',
                'isbn' => '978-604-0-23456-7',
                'publication_year' => 2024,
                'publication_place' => 'Hà Nội',
                'language' => 'Vietnamese',
                'number_of_pages' => 500,
                'call_number' => '005.276',
                'description' => 'Hướng dẫn chi tiết về Laravel Framework từ cơ bản đến nâng cao',
                'metadata' => json_encode([
                    'level' => 'intermediate',
                    'format' => 'Bìa mềm',
                ]),
                'cover_image' => null,
                'is_ebook' => true,
                'ebook_file_path' => '/ebooks/laravel.pdf',
                'price' => 180000,
                'total_copies' => 15,
                'available_copies' => 12,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'category_id' => 3,
                'publisher_id' => 2,
                'title' => 'Toán cao cấp A1',
                'author' => 'Nguyễn Đình Trí',
                'isbn' => '978-604-0-34567-8',
                'publication_year' => 2023,
                'publication_place' => 'Hà Nội',
                'language' => 'Vietnamese',
                'number_of_pages' => 280,
                'call_number' => '515',
                'description' => 'Giáo trình Toán cao cấp A1 dành cho sinh viên kỹ thuật',
                'metadata' => null,
                'cover_image' => null,
                'is_ebook' => false,
                'ebook_file_path' => null,
                'price' => 120000,
                'total_copies' => 20,
                'available_copies' => 18,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($books as $book) {
            DB::table('books')->updateOrInsert(['id' => $book['id']], $book);
        }

        DB::table('book_author')->insertOrIgnore([
            ['book_id' => 1, 'author_id' => 1, 'order' => 1],
            ['book_id' => 2, 'author_id' => 2, 'order' => 1],
            ['book_id' => 4, 'author_id' => 3, 'order' => 1],
        ]);

        $copies = [];

        for ($i = 1; $i <= 10; $i++) {
            $copies[] = [
                'book_id' => 1,
                'supplier_id' => 1,
                'barcode' => 'IT-001-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => $i === 1 ? 'borrowed' : 'available',
                'condition' => $i <= 8 ? 'good' : ($i === 9 ? 'fair' : 'new'),
                'price' => 150000,
                'import_date' => Carbon::parse('2024-01-01'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        for ($i = 1; $i <= 5; $i++) {
            $copies[] = [
                'book_id' => 2,
                'supplier_id' => 2,
                'barcode' => 'LOG-002-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'available',
                'condition' => 'good',
                'price' => 200000,
                'import_date' => Carbon::parse('2024-05-15'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        for ($i = 1; $i <= 15; $i++) {
            $copies[] = [
                'book_id' => 3,
                'supplier_id' => 3,
                'barcode' => 'LAR-003-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => $i <= 3 ? 'borrowed' : 'available',
                'condition' => 'new',
                'price' => 180000,
                'import_date' => Carbon::parse('2024-08-01'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        for ($i = 1; $i <= 20; $i++) {
            $copies[] = [
                'book_id' => 4,
                'supplier_id' => 3,
                'barcode' => 'MATH-004-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => $i <= 2 ? 'borrowed' : 'available',
                'condition' => $i <= 15 ? 'good' : 'new',
                'price' => 120000,
                'import_date' => Carbon::parse('2024-09-01'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('book_copies')->insertOrIgnore($copies);
    }
}
