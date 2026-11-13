
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_docente', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('grupo_id');
            $table->integer('docente_id');

            $table->foreign('grupo_id')
                ->references('id')
                ->on('grupos')
                ->cascadeOnDelete();

            $table->foreign('docente_id')
                ->references('codEmpleado')
                ->on('docentes')
                ->cascadeOnDelete();

            $table->unique(['grupo_id', 'docente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_docente');
    }
};
