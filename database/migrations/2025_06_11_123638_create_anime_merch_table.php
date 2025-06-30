<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Run the migrations. */
    public function up(): void
    {
        Schema::create('anime_merch', function (Blueprint $table) {
            $table->id(); // INT auto_increment primary key
            $table->text('nama_item');
            $table->string('Producer', 100)->nullable();
            $table->string('tahun_rilis', 4)->nullable();
            $table->text('gambar')->nullable();
            $table->text('description')->nullable();
            $table->double('harga')->nullable();

            $table->timestamp('created_at')->useCurrent(); // default now()
            $table->integer('created_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }
    /** Reverse the migrations. */
    public function down(): void
    {
        Schema::dropIfExists('anime_merch');
    }

};