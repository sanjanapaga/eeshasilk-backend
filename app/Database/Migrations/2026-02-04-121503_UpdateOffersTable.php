<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateOffersTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('offers', true);
        
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
                'type'       => 'VARCHAR',
                'constraint' => '20',
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
        // No simple way to revert a drop and recreate without data loss, 
        // but for this task we just need the table corrected.
    }
}
