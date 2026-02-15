<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToOrders extends Migration
{
    public function up()
    {
        $fields = [
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'user_id',
                'null'       => true,
            ],
            'customer_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'after'      => 'customer_name',
                'null'       => true,
            ],
            'customer_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'after'      => 'customer_email',
                'null'       => true,
            ],
            'shipping_address' => [
                'type' => 'TEXT',
                'after' => 'customer_phone',
                'null' => true,
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'after'      => 'shipping_address',
                'default'    => 0,
            ],
            'discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'after'      => 'subtotal',
                'default'    => 0,
            ],
        ];
        $this->forge->addColumn('orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', ['customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'subtotal', 'discount']);
    }
}
