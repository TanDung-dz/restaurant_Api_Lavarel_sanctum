<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ThanhToan', function (Blueprint $table) {
            $table->string('MoMo_RequestId')->nullable();
            $table->string('MoMo_OrderId')->nullable();
            $table->string('MoMo_PaymentUrl')->nullable();
            $table->string('MoMo_TransId')->nullable();
            $table->string('MoMo_ResultCode')->nullable();
            $table->string('MoMo_Message')->nullable();
            $table->json('MoMo_ExtraData')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ThanhToan', function (Blueprint $table) {
            $table->dropColumn([
                'MoMo_RequestId',
                'MoMo_OrderId',
                'MoMo_PaymentUrl',
                'MoMo_TransId',
                'MoMo_ResultCode',
                'MoMo_Message',
                'MoMo_ExtraData',
            ]);
        });
    }
};