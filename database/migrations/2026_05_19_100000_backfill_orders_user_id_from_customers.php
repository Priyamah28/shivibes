<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->tablesExist()) {
            return;
        }

        DB::statement('
            UPDATE orders o
            INNER JOIN customers c ON o.customer_id = c.id
            INNER JOIN users u ON LOWER(u.email) = LOWER(c.email)
            SET o.user_id = u.id
            WHERE o.user_id IS NULL
        ');
    }

    public function down(): void
    {
        // Non-reversible data backfill.
    }

    private function tablesExist(): bool
    {
        return DB::getSchemaBuilder()->hasTable('orders')
            && DB::getSchemaBuilder()->hasTable('customers')
            && DB::getSchemaBuilder()->hasTable('users');
    }
};
