<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOffers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['percentage', 'fixed', 'shipping'],
                'default'    => 'percentage',
            ],
            'discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'min_spend' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('offers');
    }

    public function down()
    {
        $this->forge->dropTable('offers');
    }
}
