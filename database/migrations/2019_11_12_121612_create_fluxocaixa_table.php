<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFluxocaixaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fluxo_caixa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('eventoedicao_id');
            $table->unsignedBigInteger('categoria_id');
            $table->string('nome_operacao');
            $table->decimal('valor',8,2);
            $table->date('data_movimento');
            $table->enum('tipo_operacao',['DEBITO','CREDITO']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('eventoedicao_id')->references('id')->on('evento_edicao');
            $table->foreign('categoria_id')->references('id')->on('categoria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fluxo_caixa');
    }
}
