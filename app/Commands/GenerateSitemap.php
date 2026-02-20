<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProductModel;

class GenerateSitemap extends BaseCommand
{
    protected $group       = 'SEO';
    protected $name        = 'sitemap:generate';
    protected $description = 'Generates a static sitemap.xml file.';

    public function run(array $params)
    {
        $productModel = new ProductModel();
        $products = $productModel->findAll();

        $baseUrl = "https://eeshasilk.com";

        $urls = [
            $baseUrl . '/',
            $baseUrl . '/shop',
            $baseUrl . '/about',
            $baseUrl . '/contact',
            $baseUrl . '/login',
            $baseUrl . '/register',
        ];

        foreach ($products as $product) {
            $urls[] = $baseUrl . '/product/' . $product['id'];
        }

        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";

        foreach ($urls as $url) {
            $xml .= "\t<url>\n";
            $xml .= "\t\t<loc>" . htmlspecialchars($url) . "</loc>\n";
            $xml .= "\t\t<changefreq>weekly</changefreq>\n";
            $xml .= "\t\t<priority>0.8</priority>\n";
            $xml .= "\t</url>\n";
        }

        $xml .= "</urlset>";

        // Define path to save sitemap.xml
        // Saving to backend/public for easy access, and we can move it
        $path = FCPATH . 'sitemap.xml';

        if (file_put_contents($path, $xml)) {
            CLI::write('Sitemap generated successfully at: ' . $path, 'green');
        } else {
            CLI::error('Failed to write sitemap to: ' . $path);
        }
    }
}
