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
    Schema::create('docente_grupo', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('grupo_id');
        $table->integer('docente_codEmpleado');

        $table->foreign('grupo_id')
            ->references('id')
            ->on('grupos')
            ->cascadeOnDelete();

        $table->foreign('docente_codEmpleado')
            ->references('codEmpleado')
            ->on('docentes')
            ->cascadeOnDelete();

        $table->unique(['grupo_id', 'docente_codEmpleado']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_grupo');
    }
};
