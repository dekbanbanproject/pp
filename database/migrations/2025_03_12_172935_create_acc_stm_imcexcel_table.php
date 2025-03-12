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
        if (!Schema::hasTable('acc_stm_imcexcel'))
        {
            Schema::create('acc_stm_imcexcel', function (Blueprint $table) {
                $table->bigIncrements('acc_stm_imcexcel_id');
                $table->enum('sendto', ['Y', 'N'])->default('N'); //
                $table->string('rep_no')->nullable();// รหัส
                $table->string('tran_id')->nullable();//
                $table->string('hn')->nullable();//
                $table->string('an')->nullable();//
                $table->string('cid')->nullable();//
                $table->string('ptname')->nullable();//
                $table->string('pttype')->nullable();//
                $table->string('hmain_op')->nullable();//
                $table->date('date_send')->nullable();//
                $table->date('vstdate')->nullable();//
                $table->string('berg_no')->nullable();//
                $table->string('berg_name')->nullable();//
                $table->string('berg_qty')->nullable();//
                $table->string('berg_price')->nullable();//
                $table->string('berg_price_pedan')->nullable();//
                $table->string('berg_price_totalberg')->nullable();//
                $table->string('pscode')->nullable();//
                $table->string('percent')->nullable();//
                $table->string('chod_chery')->nullable();//
                $table->string('nochod_chery')->nullable();//
                $table->string('pay_plus')->nullable();//
                $table->string('price_back')->nullable();//
                $table->string('status')->nullable();//
                $table->string('comment')->nullable();//
                $table->string('comment_orther')->nullable();//
                $table->string('nhso_adp_code')->nullable();//
                $table->string('nhso_adp_name')->nullable();//
                $table->string('hmain')->nullable();//                
                // $table->enum('status', ['Y', 'N'])->default('N'); 
                $table->string('STMDoc')->nullable();//

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_stm_imcexcel');
    }
};
