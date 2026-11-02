<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apriori_rules', function (Blueprint $table) {
            $table->id();
            $table->json('antecedent');
            $table->json('consequent');
            $table->decimal('support', 5, 2);
            $table->decimal('confidence', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apriori_rules');
    }
};
