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
        if (!Schema::hasTable('acc_1102050101_3013'))
        {
            Schema::create('acc_1102050101_3013', function (Blueprint $table) {
                $table->bigIncrements('acc_1102050101_3013_id'); 
                $table->string('vn')->nullable();// รหัส
                $table->string('an')->nullable();// 
                $table->string('hn')->nullable();// 
                $table->string('cid')->nullable();//  
                $table->string('ptname')->nullable();// 
                $table->date('vstdate')->nullable();//
                $table->Time('vsttime')->nullable();// 
                $table->date('regdate')->nullable();//
                $table->date('dchdate')->nullable();//            
                $table->string('pttype')->nullable();//  
                $table->string('pttype_nhso')->nullable();// 
                $table->date('pttype_nhso_startdate')->nullable();// 
                $table->string('income_group')->nullable();// 
                $table->string('acc_code')->nullable();// 
                $table->string('account_code')->nullable();// 
                $table->string('hospcode')->nullable();// 
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
                $table->char('debit_ins_sss', length: 100)->nullable();//
                $table->char('debit_ct_sss', length: 100)->nullable();//
                $table->string('sauntang')->nullable();// 
                $table->longtext('cc')->nullable();// 
                $table->string('referin_no')->nullable();//  
                $table->string('ct_price')->nullable();//
                $table->string('ct_sumprice')->nullable();// 
                $table->enum('ct_refer', ['Y', 'N'])->default('N');
                $table->string('pdx')->nullable();// 
                $table->string('dx0')->nullable();//  
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
                $table->string('nhso_docno')->nullable();// เลขที่หนังสือ
                $table->string('nhso_ownright_pid')->nullable();//ลงรับใน hos 
                $table->string('recieve_true')->nullable();//รับจริง
                $table->string('difference')->nullable();//ส่วนต่าง
                $table->string('recieve_no')->nullable();//เลขที่ใบเสร็จ
                $table->string('recieve_date')->nullable();//ลงวันที่
                $table->string('recieve_user')->nullable();//ผู้ลง
                $table->string('comment')->nullable();// 
                $table->date('date_req')->nullable();// 
                $table->string('STMdoc')->nullable();// 
                $table->string('debit_drug_ct')->nullable();// 
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_1102050101_3013');
    }
};
