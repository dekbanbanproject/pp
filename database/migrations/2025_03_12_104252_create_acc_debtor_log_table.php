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
        if (!Schema::hasTable('acc_debtor_log'))
        {
            Schema::create('acc_debtor_log', function (Blueprint $table) {
                $table->bigIncrements('acc_debtor_log_id'); 
                $table->string('account_code')->nullable();                //  
                $table->string('make_gruop')->nullable();                //  
                $table->date('date_save')->nullable();                     //      
                $table->time('date_time')->nullable();                     //                
                $table->string('user_id')->nullable();                    //  
                $table->enum('active', ['Y','N'])->default('Y'); 

               
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_debtor_log');
    }
};
