<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountToProducts extends Migration
{
    public function up()
    {
        $fields = [
            'discount' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
                'after'      => 'price'
            ],
        ];
        $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'discount');
    }
}
