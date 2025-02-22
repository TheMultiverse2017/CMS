<?php

use App\Enums\Status;
use App\Enums\WebsiteFilesFor;
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
        Schema::create('website_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reference_id'); //id belonging to respective table
            $table->string('filename');// original file name
            $table->integer('filetype'); //image or video
            $table->string('filextension');//jpg,png,jpeg,pdf,mp4
            $table->longText('filesrc');
            $table->integer('filesfor')->default(WebsiteFilesFor::MAIN->value); // define for any sub/sub-sub of main
            $table->integer('belongsTo'); // define for menus
            $table->longText('thumbnailsrc')->nullable();
            $table->integer('status')->default(Status::ACTIVE->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_files');
    }
};
