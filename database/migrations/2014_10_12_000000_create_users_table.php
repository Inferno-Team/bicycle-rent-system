<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('national_id')->unique()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->enum('type', ['manager', 'customer','esp32']);
            $table->string('ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        
        DB::table('users')->insert([
            'first_name' => 'root',
            'last_name' => 'root',
            'email' => 'root@bicycle.com',
            'password' => Hash::make('root1234'),
            "national_id" => "024000000",
            "phone_number" => "0962020202",
            "type" => "manager",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
