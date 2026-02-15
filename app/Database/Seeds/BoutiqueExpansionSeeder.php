<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BoutiqueExpansionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'           => 'Pure Mysore Silk Saree - Royal Blue',
                'description'    => 'A magnificent Pure Mysore Silk saree in Royal Blue with a rich gold zari border. Known for its soft texture and natural luster.',
                'price'          => 18500.00,
                'category'       => 'mysore-silk',
                'image'          => 'mysore_silk.png',
                'stock_quantity' => 5,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Silver Plated Zari Saree - 150g Silver',
                'description'    => 'Exquisite silk saree featuring 150 grams of silver-plated zari work. A true heirloom piece with traditional motifs.',
                'price'          => 45000.00,
                'category'       => 'silver-plated',
                'image'          => 'https://images.unsplash.com/photo-1610030469668-935142b36de3?q=80&w=2574&auto=format&fit=crop',
                'stock_quantity' => 2,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Heritage Zardosi Lehenga',
                'description'    => 'A handcrafted designer lehenga with intricate zardosi and sequence work. Perfect for the modern heritage bride.',
                'price'          => 85000.00,
                'category'       => 'lehenga',
                'image'          => 'https://images.unsplash.com/photo-1595914146118-2e1f4870d05c?q=80&w=2574&auto=format&fit=crop',
                'stock_quantity' => 3,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Premium Silk Designer Kurta',
                'description'    => 'Premium Tussar silk kurta for men with hand-embroidered detailing on the collar and cuffs.',
                'price'          => 12500.00,
                'category'       => 'kurta',
                'image'          => 'https://images.unsplash.com/photo-1597983073493-88cd35cf93b0?q=80&w=2570&auto=format&fit=crop',
                'stock_quantity' => 10,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'name'           => 'Zari Embroidered Silk Potli',
                'description'    => 'A luxury silk potli bag adorned with heavy zari work and pearl tassels. An essential boutique accessory.',
                'price'          => 4500.00,
                'category'       => 'bags',
                'image'          => 'https://images.unsplash.com/photo-1621252179027-94459d278660?q=80&w=2574&auto=format&fit=crop',
                'stock_quantity' => 15,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder
        foreach ($data as $item) {
            $this->db->table('products')->insert($item);
        }
    }
}
