<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['investments', 'loans'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'agreement_email_sent_at')) {
                        $table->timestamp('agreement_email_sent_at')->nullable();
                    }
                    if (!Schema::hasColumn($tableName, 'agreement_email_error')) {
                        $table->string('agreement_email_error', 1000)->nullable();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['investments', 'loans'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $columns = [];
                    if (Schema::hasColumn($tableName, 'agreement_email_sent_at')) {
                        $columns[] = 'agreement_email_sent_at';
                    }
                    if (Schema::hasColumn($tableName, 'agreement_email_error')) {
                        $columns[] = 'agreement_email_error';
                    }
                    if ($columns) {
                        $table->dropColumn($columns);
                    }
                });
            }
        }
    }
};
