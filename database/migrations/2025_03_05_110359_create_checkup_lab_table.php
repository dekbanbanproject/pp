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
        if (!Schema::hasTable('checkup_lab'))
        {
            Schema::create('checkup_lab', function (Blueprint $table) {
                $table->bigIncrements('checkup_lab_id'); 
                $table->string('vn')->nullable();                         //              
                $table->string('hn')->nullable();                           //
                $table->date('order_date')->nullable();                   //  
                $table->string('lab_items_code')->nullable();              //               
                $table->string('lab_items_name')->nullable();              // 
                $table->string('lab_order_result')->nullable();           //  
                $table->string('lab_items_name_ref')->nullable();         //  
                $table->string('lab_items_normal_value_ref')->nullable();   //  
                $table->string('lab_items_sub_group_code')->nullable();   //     
                $table->string('sub_group_list')->nullable();             //  

                $table->enum('labtype', ['IN','OUT'])->default('IN'); 
                
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
        Schema::dropIfExists('checkup_lab');
    }
};
