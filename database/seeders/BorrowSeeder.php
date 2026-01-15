<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BorrowSeeder extends Seeder
{
    public function run(): void
    {
        $borrow1Date = Carbon::now()->subDays(5);
        $borrow1DueDate = $borrow1Date->copy()->addDays(14);

        $borrow1Id = DB::table('borrows')->insertGetId([
            'user_id' => 3,
            'staff_id' => 2,
            'borrow_date' => $borrow1Date,
            'due_date' => $borrow1DueDate,
            'status' => 'borrowed',
            'note' => 'Sinh viên mượn học kỳ 1',
            'created_at' => $borrow1Date,
            'updated_at' => $borrow1Date,
        ]);

        DB::table('borrow_items')->insert([
            'borrow_id' => $borrow1Id,
            'book_copy_id' => 1,
            'status' => 'borrowed',
            'condition_before' => 'good',
            'created_at' => $borrow1Date,
            'updated_at' => $borrow1Date,
        ]);

        $borrow2Date = Carbon::now()->subDays(3);
        $borrow2DueDate = $borrow2Date->copy()->addDays(14);

        $borrow2Id = DB::table('borrows')->insertGetId([
            'user_id' => 3,
            'staff_id' => 2,
            'borrow_date' => $borrow2Date,
            'due_date' => $borrow2DueDate,
            'status' => 'borrowed',
            'note' => null,
            'created_at' => $borrow2Date,
            'updated_at' => $borrow2Date,
        ]);

        DB::table('borrow_items')->insert([
            'borrow_id' => $borrow2Id,
            'book_copy_id' => 11,
            'status' => 'borrowed',
            'condition_before' => 'new',
            'created_at' => $borrow2Date,
            'updated_at' => $borrow2Date,
        ]);

        $borrow3Date = Carbon::now()->subDays(10);
        $borrow3DueDate = $borrow3Date->copy()->addDays(30);

        $borrow3Id = DB::table('borrows')->insertGetId([
            'user_id' => 4,
            'staff_id' => 2,
            'borrow_date' => $borrow3Date,
            'due_date' => $borrow3DueDate,
            'status' => 'borrowed',
            'note' => 'Giảng viên mượn nghiên cứu',
            'created_at' => $borrow3Date,
            'updated_at' => $borrow3Date,
        ]);

        DB::table('borrow_items')->insert([
            'borrow_id' => $borrow3Id,
            'book_copy_id' => 12,
            'status' => 'borrowed',
            'condition_before' => 'new',
            'created_at' => $borrow3Date,
            'updated_at' => $borrow3Date,
        ]);

        $borrow4Date = Carbon::now()->subDays(20);
        $borrow4DueDate = $borrow4Date->copy()->addDays(14);
        $borrow4ReturnDate = Carbon::now()->subDays(2);

        $borrow4Id = DB::table('borrows')->insertGetId([
            'user_id' => 3,
            'staff_id' => 2,
            'borrow_date' => $borrow4Date,
            'due_date' => $borrow4DueDate,
            'return_date' => $borrow4ReturnDate,
            'status' => 'returned',
            'note' => null,
            'created_at' => $borrow4Date,
            'updated_at' => $borrow4ReturnDate,
        ]);

        DB::table('borrow_items')->insert([
            'borrow_id' => $borrow4Id,
            'book_copy_id' => 13,
            'return_date' => $borrow4ReturnDate,
            'status' => 'returned',
            'condition_before' => 'good',
            'condition_after' => 'good',
            'created_at' => $borrow4Date,
            'updated_at' => $borrow4ReturnDate,
        ]);

        $overdueDays = $borrow4DueDate->diffInDays($borrow4ReturnDate);
        $fineAmount = $overdueDays * 2000;

        $fine1Id = DB::table('fines')->insertGetId([
            'user_id' => 3,
            'borrow_id' => $borrow4Id,
            'amount' => $fineAmount,
            'reason' => 'Quá hạn trả sách',
            'status' => 'paid',
            'created_at' => $borrow4ReturnDate,
            'updated_at' => $borrow4ReturnDate,
        ]);

        DB::table('payments')->insert([
            'fine_id' => $fine1Id,
            'amount' => $fineAmount,
            'payment_date' => $borrow4ReturnDate,
            'payment_method' => 'cash',
            'transaction_id' => null,
            'gateway_log' => null,
            'created_at' => $borrow4ReturnDate,
            'updated_at' => $borrow4ReturnDate,
        ]);

        DB::table('fines')->insert([
            'user_id' => 3,
            'borrow_id' => null,
            'amount' => 50000,
            'reason' => 'Làm mất thẻ thư viện',
            'status' => 'unpaid',
            'created_at' => Carbon::now()->subMonths(1),
            'updated_at' => Carbon::now()->subMonths(1),
        ]);

        $overdueBorrowDate = Carbon::now()->subDays(20);
        $overdueDueDate = $overdueBorrowDate->copy()->addDays(14);

        $overdueBorrowId = DB::table('borrows')->insertGetId([
            'user_id' => 3,
            'staff_id' => 2,
            'borrow_date' => $overdueBorrowDate,
            'due_date' => $overdueDueDate,
            'status' => 'borrowed',
            'note' => 'Đã quá hạn',
            'created_at' => $overdueBorrowDate,
            'updated_at' => $overdueBorrowDate,
        ]);

        DB::table('borrow_items')->insert([
            'borrow_id' => $overdueBorrowId,
            'book_copy_id' => 14,
            'status' => 'borrowed',
            'condition_before' => 'good',
            'created_at' => $overdueBorrowDate,
            'updated_at' => $overdueBorrowDate,
        ]);

        $overdueDays2 = Carbon::now()->diffInDays($overdueDueDate);
        $fineAmount2 = $overdueDays2 * 2000;

        DB::table('fines')->insert([
            'user_id' => 3,
            'borrow_id' => $overdueBorrowId,
            'amount' => $fineAmount2,
            'reason' => 'Quá hạn trả sách',
            'status' => 'unpaid',
            'created_at' => $overdueDueDate->copy()->addDay(),
            'updated_at' => $overdueDueDate->copy()->addDay(),
        ]);
    }
}
