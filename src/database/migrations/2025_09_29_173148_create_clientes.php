<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       
            Schema::create('clientes', function (Blueprint $table) {
                // siga seu padrão: PK nomeada
                $table->bigIncrements('id_cliente');

                // identificação
                $table->enum('tipo_pessoa', ['PF','PJ'])->default('PJ');
                $table->string('documento', 20)->nullable()->index(); // CPF/CNPJ
                $table->string('inscricao_estadual', 30)->nullable();
                $table->string('razao_social', 120)->nullable();   // PJ
                $table->string('nome_fantasia', 120)->nullable();  // PJ
                $table->string('nome', 120)->nullable();           // PF

                // contato
                $table->string('email', 150)->nullable();
                $table->string('whatsapp', 30)->nullable()->index();
                $table->string('telefone', 30)->nullable();
                $table->string('site', 150)->nullable();

                // endereço principal (pode depois extrair para tabela de endereços)
                $table->string('cep', 12)->nullable();
                $table->string('logradouro', 150)->nullable();
                $table->string('numero', 20)->nullable();
                $table->string('complemento', 80)->nullable();
                $table->string('bairro', 80)->nullable();
                $table->string('cidade', 80)->nullable();
                $table->string('uf', 2)->nullable();
                $table->string('pais', 80)->nullable()->default('Brasil');

                // comercial
                $table->unsignedBigInteger('segment_id')->nullable();
                $table->decimal('limite_credito', 12, 2)->nullable();
                $table->boolean('bloqueado')->default(false);
                $table->string('tabela_preco', 60)->nullable(); // simples; pode virar FK no futuro

                // metadados
                $table->unsignedBigInteger('id_users_fk')->nullable(); // dono/cadastrante, se fizer sentido no seu fluxo
                $table->tinyInteger('status')->default(1); // 1=ativo, 0=inativo
                $table->text('observacoes')->nullable();

                $table->timestamps();

                // índices úteis
                $table->index(['status','segment_id']);
                $table->index(['cidade','uf']);
            });
        }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
