<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTargetCategoryToOffers extends Migration
{
    public function up()
    {
        $fields = [
            'target_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'all',
                'after'      => 'min_spend'
            ],
        ];
        $this->forge->addColumn('offers', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('offers', 'target_category');
    }
}
