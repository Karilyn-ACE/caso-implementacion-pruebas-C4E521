
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes_matriculados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grupo_id')
                ->constrained('grupos');

            $table->string('estudiante_id');

            $table->foreign('estudiante_id')
                ->references('carne')
                ->on('estudiantes');

            $table->float('nota')->nullable();

            $table->unique([
                'grupo_id',
                'estudiante_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes_matriculados');
    }
};
