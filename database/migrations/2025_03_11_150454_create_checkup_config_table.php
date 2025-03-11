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
        if (!Schema::hasTable('checkup_config'))
        {
            Schema::create('checkup_config', function (Blueprint $table) {
                $table->bigIncrements('checkup_config_id'); 
                $table->string('lab_items_code')->nullable();                    //              
                $table->string('lab_items_name')->nullable();                    //                
                $table->string('sex')->nullable();                               //               
                $table->string('lab_items_normal_value_min')->nullable();        // 
                $table->string('lab_items_normal_value_max')->nullable();        //  
                $table->string('mark')->nullable();                              //  
                $table->string('age')->nullable();                               //  
                
                $table->string('user_id')->nullable();                          //  
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
        Schema::dropIfExists('checkup_config');
    }
};
