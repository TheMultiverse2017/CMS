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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('menuId')->nullable();
            $table->string('menuName')->nullable();
            $table->text('metaTags')->nullable();
            $table->text('metaDesc')->nullable();

            $table->string('contentTitle')->nullable();
            $table->text('contentSortOrder')->nullable();
            $table->text('contentOthers')->nullable();

            $table->longText('content')->nullable();
            $table->integer('status')->default(Status::ACTIVE->value);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
