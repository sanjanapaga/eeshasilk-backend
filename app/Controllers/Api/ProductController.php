<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductModel;

class ProductController extends ResourceController
{
    protected $modelName = 'App\Models\ProductModel';
    protected $format    = 'json';
    protected $helpers   = ['url'];

    private function ensureImageUrl(&$product)
    {
        $base = rtrim(config('App')->baseURL, '/');

        if (!empty($product['image'])) {
            if (strpos($product['image'], 'http://') === 0 || strpos($product['image'], 'https://') === 0) {
                $product['image_url'] = $product['image'];
            } else {
                // build url then ensure host matches configured baseURL (localhost vs 127.0.0.1)
                $url = base_url($product['image']);
                // replace ipv4 loopback with hostname from baseURL
                $product['image_url'] = preg_replace('#https?://[^/]+#', $base, $url);
            }
        }

        if (!empty($product['images']) && is_array($product['images'])) {
            foreach ($product['images'] as &$img) {
                if (strpos($img['image_path'], 'http://') === 0 || strpos($img['image_path'], 'https://') === 0) {
                    $img['url'] = $img['image_path'];
                } else {
                    $url = base_url($img['image_path']);
                    $img['url'] = preg_replace('#https?://[^/]+#', $base, $url);
                }
            }
        }

        if (!empty($product['videos']) && is_array($product['videos'])) {
            foreach ($product['videos'] as &$vid) {
                if (strpos($vid['video_path'], 'http://') === 0 || strpos($vid['video_path'], 'https://') === 0) {
                    $vid['url'] = $vid['video_path'];
                } else {
                    $url = base_url($vid['video_path']);
                    $vid['url'] = preg_replace('#https?://[^/]+#', $base, $url);
                }
            }
        }
    }

    /**
     * GET /products
     */
    public function index()
    {
        $category = $this->request->getVar('category');
        $search   = $this->request->getVar('search'); // query parameter 'search'
        
        // Debug logging
        log_message('debug', 'Index request - Category: ' . ($category ?? 'none') . ', Search: ' . ($search ?? 'none'));

        $query = $this->model;

        if ($category && $category !== 'all') {
            $query = $query->where('LOWER(category)', strtolower($category));
        }

        if ($search) {
            $query = $query->groupStart()
                           ->like('LOWER(name)', strtolower($search))
                           ->orLike('LOWER(description)', strtolower($search))
                           ->groupEnd();
        }

        $products = $query->findAll();
        log_message('debug', 'Returned products count: ' . count($products));

        // Attach full image URL
        foreach ($products as &$product) {
            $this->ensureImageUrl($product);
        }

        return $this->respond([
            'status'   => 200,
            'products' => $products
        ]);
    }

    /**
     * GET /products/{id}
     */
    public function show($id = null)
    {
        $product = $this->model->find($id);

        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        // Fetch multiple images
        $imageModel = new \App\Models\ProductImageModel();
        $product['images'] = $imageModel->where('product_id', $id)->findAll();

        // Fetch videos
        $videoModel = new \App\Models\ProductVideoModel();
        $product['videos'] = $videoModel->where('product_id', $id)->findAll();

        $this->ensureImageUrl($product);

        return $this->respond([
            'status'  => 200,
            'product' => $product
        ]);
    }

    /**
     * POST /products
     */
    public function create()
    {
        // Diagnostics
        log_message('debug', 'Product Create Request');

        $data = [
            'name'           => $this->request->getPost('name'),
            'description'    => $this->request->getPost('description'),
            'price'          => $this->request->getPost('price'),
            'discount'       => $this->request->getPost('discount'),
            'category'       => $this->request->getPost('category'),
            'stock_quantity' => $this->request->getPost('stock_quantity'),
        ];

        // Handle main image
        $mainImg = $this->request->getFile('image');
        if ($mainImg && $mainImg->isValid()) {
            $uploadPath = FCPATH . 'uploads/products';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
            $newName = $mainImg->getRandomName();
            if ($mainImg->move($uploadPath, $newName)) {
                $data['image'] = 'uploads/products/' . $newName;
            }
        }

        $id = $this->model->insert($data);

        if (!$id) {
            return $this->fail($this->model->errors());
        }

        // Handle multiple images
        $images = $this->request->getFileMultiple('images');
        if ($images) {
            $imageModel = new \App\Models\ProductImageModel();
            $uploadPath = FCPATH . 'uploads/products';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);

            foreach ($images as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName();
                    if ($img->move($uploadPath, $newName)) {
                        $imageModel->insert([
                            'product_id' => $id,
                            'image_path' => 'uploads/products/' . $newName
                        ]);
                    }
                }
            }
        }

        // Handle video upload
        $videoFile = $this->request->getFile('video');
        if ($videoFile && $videoFile->isValid() && !$videoFile->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/products/videos';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
            $newName = $videoFile->getRandomName();
            if ($videoFile->move($uploadPath, $newName)) {
                $videoModel = new \App\Models\ProductVideoModel();
                $videoModel->insert([
                    'product_id' => $id,
                    'video_path' => 'uploads/products/videos/' . $newName
                ]);
            }
        }

        $product = $this->model->find($id);
        $imageModel = new \App\Models\ProductImageModel();
        $product['images'] = $imageModel->where('product_id', $id)->findAll();
        $videoModel = new \App\Models\ProductVideoModel();
        $product['videos'] = $videoModel->where('product_id', $id)->findAll();
        $this->ensureImageUrl($product);

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Product created successfully',
            'product' => $product
        ]);
    }

    /**
     * POST /products/{id}  (can work as PUT if _method=PUT is sent)
     */
    public function update($id = null)
    {
        $product = $this->model->find($id);
        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        $data = $this->request->getPost();
        if (empty($data)) {
            $data = $this->request->getRawInput();
        }

        // Handle main image update
        $mainImg = $this->request->getFile('image');
        if ($mainImg && $mainImg->isValid()) {
            if (!empty($product['image']) && file_exists(FCPATH . $product['image'])) {
                unlink(FCPATH . $product['image']);
            }
            $uploadPath = FCPATH . 'uploads/products';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
            $newName = $mainImg->getRandomName();
            if ($mainImg->move($uploadPath, $newName)) {
                $data['image'] = 'uploads/products/' . $newName;
            }
        }

        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }

        // Handle additional images (append mode)
        $images = $this->request->getFileMultiple('images');
        if ($images) {
            $imageModel = new \App\Models\ProductImageModel();
            $uploadPath = FCPATH . 'uploads/products';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);

            foreach ($images as $img) {
                if ($img->isValid() && !$img->hasMoved()) {
                    $newName = $img->getRandomName();
                    if ($img->move($uploadPath, $newName)) {
                        $imageModel->insert([
                            'product_id' => $id,
                            'image_path' => 'uploads/products/' . $newName
                        ]);
                    }
                }
            }
        }

        // Handle image deletion (if IDs provided as comma-separated list or array)
        $deleteImageIds = $this->request->getPost('delete_image_ids');
        if ($deleteImageIds) {
            if (!is_array($deleteImageIds)) $deleteImageIds = explode(',', $deleteImageIds);
            $imageModel = new \App\Models\ProductImageModel();
            foreach ($deleteImageIds as $imgId) {
                $img = $imageModel->find($imgId);
                if ($img && (int)$img['product_id'] === (int)$id) {
                    if (file_exists(FCPATH . $img['image_path'])) {
                        unlink(FCPATH . $img['image_path']);
                    }
                    $imageModel->delete($imgId);
                }
            }
        }

        // Handle video update (replace existing)
        $videoFile = $this->request->getFile('video');
        if ($videoFile && $videoFile->isValid() && !$videoFile->hasMoved()) {
            $videoModel = new \App\Models\ProductVideoModel();
            $existingVideos = $videoModel->where('product_id', $id)->findAll();
            foreach ($existingVideos as $evid) {
                if (file_exists(FCPATH . $evid['video_path'])) {
                    unlink(FCPATH . $evid['video_path']);
                }
                $videoModel->delete($evid['id']);
            }

            $uploadPath = FCPATH . 'uploads/products/videos';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0775, true);
            $newName = $videoFile->getRandomName();
            if ($videoFile->move($uploadPath, $newName)) {
                $videoModel->insert([
                    'product_id' => $id,
                    'video_path' => 'uploads/products/videos/' . $newName
                ]);
            }
        }

        // Handle video deletion
        if ($this->request->getPost('delete_video') === 'true') {
            $videoModel = new \App\Models\ProductVideoModel();
            $existingVideos = $videoModel->where('product_id', $id)->findAll();
            foreach ($existingVideos as $evid) {
                if (file_exists(FCPATH . $evid['video_path'])) {
                    unlink(FCPATH . $evid['video_path']);
                }
                $videoModel->delete($evid['id']);
            }
        }

        $updatedProduct = $this->model->find($id);
        $imageModel = new \App\Models\ProductImageModel();
        $updatedProduct['images'] = $imageModel->where('product_id', $id)->findAll();
        $videoModel = new \App\Models\ProductVideoModel();
        $updatedProduct['videos'] = $videoModel->where('product_id', $id)->findAll();
        $this->ensureImageUrl($updatedProduct);

        return $this->respond([
            'status'  => 200,
            'message' => 'Product updated successfully',
            'product' => $updatedProduct
        ]);
    }

    /**
     * DELETE /products/{id}
     */
    public function delete($id = null)
    {
        $product = $this->model->find($id);

        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        // delete main image file
        if (!empty($product['image']) && file_exists(FCPATH . $product['image'])) {
            unlink(FCPATH . $product['image']);
        }

        // delete additional images
        $imageModel = new \App\Models\ProductImageModel();
        $images = $imageModel->where('product_id', $id)->findAll();
        foreach ($images as $img) {
            if (file_exists(FCPATH . $img['image_path'])) {
                unlink(FCPATH . $img['image_path']);
            }
        }
        $imageModel->where('product_id', $id)->delete();

        // delete videos
        $videoModel = new \App\Models\ProductVideoModel();
        $videos = $videoModel->where('product_id', $id)->findAll();
        foreach ($videos as $vid) {
            if (file_exists(FCPATH . $vid['video_path'])) {
                unlink(FCPATH . $vid['video_path']);
            }
        }
        $videoModel->where('product_id', $id)->delete();

        $this->model->delete($id);

        return $this->respondDeleted([
            'status'  => 200,
            'message' => 'Product deleted successfully'
        ]);
    }
}
