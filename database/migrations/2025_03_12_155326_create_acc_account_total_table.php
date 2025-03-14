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
        if (!Schema::hasTable('acc_account_total'))
        {
            Schema::create('acc_account_total', function (Blueprint $table) {
                $table->bigIncrements('acc_account_total_id');
                $table->enum('sendto', ['Y', 'N'])->default('N'); //
                $table->enum('active', ['Y', 'N'])->default('N'); //
                $table->string('bg_yearnow')->nullable();// 
                $table->string('vn')->nullable();// รหัส
                $table->string('an')->nullable();//
                $table->string('hn')->nullable();//
                $table->string('cid')->nullable();//
                $table->string('ptname')->nullable();//
                $table->date('vstdate')->nullable();//
                $table->Time('vsttime')->nullable();//
                $table->string('hm')->nullable();//
                $table->date('regdate')->nullable();//
                $table->date('dchdate')->nullable();//
                $table->string('hospmain')->nullable();//
                $table->string('hsub')->nullable();//
                $table->string('pttype')->nullable();//
                $table->string('pttype_nhso')->nullable();//
                $table->date('pttype_nhso_startdate')->nullable();//
                $table->string('income_group')->nullable();//
                $table->string('acc_code')->nullable();//
                $table->string('account_code')->nullable();//

                $table->string('rw')->nullable();//
                $table->string('adjrw')->nullable();//
                $table->string('total_adjrw_income')->nullable();//

                $table->string('income')->nullable();//
                $table->string('uc_money')->nullable();//
                $table->string('discount_money')->nullable();//
                $table->string('rcpt_money')->nullable();//  paid_money
                $table->string('rcpno')->nullable();//
                $table->string('debit')->nullable();//

                $table->string('debit_drug')->nullable();//เฉพาะรายการยา
                $table->string('debit_instument')->nullable();// เฉพาะรอวัยวะเทียม
                $table->string('debit_refer')->nullable();// เฉพาะ Refer
                $table->string('debit_toa')->nullable();//
                $table->string('debit_total')->nullable();//

                $table->string('debit_walkin')->nullable();//
                $table->string('debit_imc')->nullable();// เฉพาะ Refer
                $table->string('debit_imc_adpcode')->nullable();//
                $table->string('debit_thai')->nullable();//

                $table->string('max_debt_amount')->nullable();//
                $table->string('acc_debtor_filename')->nullable();//
                $table->string('stm_rep')->nullable();//
                $table->string('stm_money')->nullable();//
                $table->string('stm_uc_money')->nullable();//
                $table->string('stm_rcpt_money')->nullable();//
                $table->string('stm_rcpno')->nullable();//
                $table->string('stm_rw')->nullable();//
                $table->string('acc_debtor_userid')->nullable();//
                $table->enum('status', ['Y', 'N'])->default('N');
                $table->string('comment')->nullable();//
                $table->date('date_req')->nullable();//
               
                $table->string('stm_trainid')->nullable();//
                $table->string('stm_total')->nullable();//
                $table->string('va')->nullable();//
                $table->string('STMDoc')->nullable();//
                $table->string('pdx')->nullable();//
                $table->string('acc_debtor_log_id')->nullable();//
                

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_account_total');
    }
};
