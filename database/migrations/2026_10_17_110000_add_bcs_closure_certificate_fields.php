<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addCertificatePath = !Schema::hasColumn('investments', 'closure_certificate_path');
        $addEmailSentAt = !Schema::hasColumn('investments', 'closure_certificate_sent_at');
        $addClosedAt = !Schema::hasColumn('investments', 'closed_at');

        Schema::table('investments', function (Blueprint $table) use ($addCertificatePath, $addEmailSentAt, $addClosedAt) {
            if ($addCertificatePath) {
                $table->string('closure_certificate_path')->nullable();
            }
            if ($addEmailSentAt) {
                $table->timestamp('closure_certificate_sent_at')->nullable();
            }
            if ($addClosedAt) {
                $table->timestamp('closed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columns = array_filter([
            Schema::hasColumn('investments', 'closure_certificate_path') ? 'closure_certificate_path' : null,
            Schema::hasColumn('investments', 'closure_certificate_sent_at') ? 'closure_certificate_sent_at' : null,
            Schema::hasColumn('investments', 'closed_at') ? 'closed_at' : null,
        ]);
        if ($columns) {
            Schema::table('investments', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
