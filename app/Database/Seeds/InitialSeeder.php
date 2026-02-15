<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        $this->db->table('reviews')->truncate();
        $this->db->table('products')->truncate();
        $this->db->table('categories')->truncate();
        $this->db->table('offers')->truncate();
        $this->db->table('users')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');

        // Users
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@eshasilk.com',
                'password' => password_hash('admin@123', PASSWORD_DEFAULT),
                'role' => 'admin',
            ],
            [
                'username' => 'user',
                'email' => 'user@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // Categories
        $categories = [
            ['name' => 'Saree', 'slug' => 'saree', 'description' => 'Exquisite Indian sarees'],
            ['name' => 'Dress', 'slug' => 'dress', 'description' => 'Designer dresses and suits'],
            ['name' => 'Bag', 'slug' => 'bag', 'description' => 'Luxury handbags and clutches'],
        ];
        $this->db->table('categories')->insertBatch($categories);

        // Products
        $products = [
            [
                'name' => 'Kanjivaram Silk Saree',
                'price' => 12999,
                'discount' => 10,
                'category' => 'saree',
                'description' => 'Pure silk saree with traditional golden zari work',
                'image' => 'https://images.unsplash.com/photo-1610030469668-93530ec677f3?w=500',
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Banarasi Silk Saree',
                'price' => 15999,
                'discount' => 0,
                'category' => 'saree',
                'description' => 'Handwoven Banarasi silk saree with rich brocade',
                'image' => 'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=500',
                'stock_quantity' => 8,
            ],
            [
                'name' => 'Designer Anarkali',
                'price' => 8999,
                'discount' => 20,
                'category' => 'dress',
                'description' => 'Elegant Anarkali dress with embroidery',
                'image' => 'https://images.unsplash.com/photo-1585073400216-2fd5419883e1?w=500',
                'stock_quantity' => 20,
            ],
        ];
        $this->db->table('products')->insertBatch($products);

        // Offers/Coupons
        $offers = [
            ['code' => 'FIRST10', 'type' => 'percentage', 'discount' => 10.00, 'min_spend' => 0.00, 'description' => '10% off on your first order'],
            ['code' => 'WELCOME20', 'type' => 'fixed', 'discount' => 500.00, 'min_spend' => 2000.00, 'description' => '₹500 off on orders above ₹2000'],
        ];
        $this->db->table('offers')->insertBatch($offers);
    }
}
