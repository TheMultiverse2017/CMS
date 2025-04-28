<?php

use App\Enums\Status;
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
        Schema::create('page_items', function (Blueprint $table) {
            $table->id();
            $table->string('menuId')->nullable();
            $table->string('contentTitle')->nullable(); //used to recogonise items only
            $table->integer('contentSortOrder')->nullable(); //sort order of view of content
            $table->longText('contentOthers')->nullable(); //used for additional optional content or options
            $table->string('elementType')->nullable();
            $table->longText('content')->nullable();
            $table->integer('status')->default(Status::ACTIVE->value); // after all the satus will set to deactive
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_items');
    }
};
