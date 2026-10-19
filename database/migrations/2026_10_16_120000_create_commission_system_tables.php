<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('members', 'referred_by_member_id')) {
            Schema::table('members', function (Blueprint $table) {
                $table->foreignId('referred_by_member_id')->nullable()->after('referral_code')
                    ->constrained('members')->nullOnDelete();
            });
        }

        Schema::create('commission_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->foreignId('source_member_id')->constrained('members')->cascadeOnDelete();
            $table->string('recipient_type', 16);
            $table->unsignedBigInteger('recipient_id');
            $table->unsignedTinyInteger('level');
            $table->decimal('base_amount', 15, 2);
            $table->decimal('rate_percent', 7, 4);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->unique(['investment_id', 'level', 'recipient_type', 'recipient_id'], 'commission_recipient_once');
            $table->index(['recipient_type', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_records');
        if (Schema::hasColumn('members', 'referred_by_member_id')) {
            Schema::table('members', function (Blueprint $table) {
                $table->dropForeign(['referred_by_member_id']);
                $table->dropColumn('referred_by_member_id');
            });
        }
    }
};
