<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugsTable extends Migration
{
    public function up()
    {
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('effective_material');
            $table->text('description');
            $table->text('side_effects');
            $table->text('dosage');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drugs');
    }
}
