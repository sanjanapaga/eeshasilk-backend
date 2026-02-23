<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSizesToTables extends Migration
{
    public function up()
    {
        // Add sizes to products
        $this->forge->addColumn('products', [
            'sizes' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'category',
                'comment'    => 'Comma-separated sizes for Kurtas'
            ],
        ]);

        // Add size to cart
        $this->forge->addColumn('cart', [
            'size' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'product_id',
            ],
        ]);

        // Add size to order_items
        $this->forge->addColumn('order_items', [
            'size' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'product_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'sizes');
        $this->forge->dropColumn('cart', 'size');
        $this->forge->dropColumn('order_items', 'size');
    }
}
