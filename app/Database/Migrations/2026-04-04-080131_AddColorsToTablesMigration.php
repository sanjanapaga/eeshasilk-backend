<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColorsToTablesMigration extends Migration
{
    public function up()
    {
        // Add colors to products
        $this->forge->addColumn('products', [
            'colors' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'sizes',
                'comment'    => 'Comma-separated colors for products'
            ],
        ]);

        // Add color to cart
        $this->forge->addColumn('cart', [
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'size',
            ],
        ]);

        // Add color to order_items
        $this->forge->addColumn('order_items', [
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'size',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'colors');
        $this->forge->dropColumn('cart', 'color');
        $this->forge->dropColumn('order_items', 'color');
    }
}
