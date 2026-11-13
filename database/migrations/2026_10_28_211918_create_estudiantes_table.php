
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->string('carne')->primary();
            $table->string('nombre');
            $table->string('apellidos');

            $table->integer('consejero_id');

            $table->foreign('consejero_id')
                ->references('codEmpleado')
                ->on('docentes');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
