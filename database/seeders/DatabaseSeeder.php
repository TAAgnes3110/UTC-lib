<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Seed order:
     * 1. Roles & Permissions (must be first for user roles)
     * 2. Library data (categories, suppliers, rules, books)
     * 3. Borrows & Fines (depends on users and books)
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // Roles, permissions, users, customers
            LibrarySeeder::class,    // Categories, suppliers, rules, books, book_copies
            BorrowSeeder::class,     // Borrows, borrow_items, fines, payments
        ]);
    }
}
