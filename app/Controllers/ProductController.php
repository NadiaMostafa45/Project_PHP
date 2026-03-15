<?php

namespace App\Controllers;

use App\Models\Product;
use Exception;

class ProductController
{

    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function store()
    {
        $name  = trim($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $error = '';

        if (empty($name) || $price <= 0) {
            $error = 'Please fill in all fields with valid values.';
        } else if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== 0) {
            $error = 'Please upload a product image.';
        } else {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = 'Invalid image type. Allowed: jpg, jpeg, png, gif, webp';
            } else {
                $imgName = time() . '_' . uniqid() . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imgName);
            }

            if (empty($error)) {
                $this->productModel->create($name, $price, $imgName);
                header("Location: ../Views/products.php?success=added");
                exit;
            }
        }

        if ($error) {
            header("Location: ../Views/add_product.php?error=" . urlencode($error));
            exit;
        }
    }

    public function update()
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: ../Views/products.php?error=Invalid product ID");
            exit;
        }

        $name  = trim($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $error = '';

        if (empty($name) || $price <= 0) {
            $error = 'Please fill in all fields with valid values.';
        } else {
            $imgName = null;

            if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $imgName = time() . '_' . uniqid() . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../public/assets/images/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imgName);
                } else {
                    $error = 'Invalid image type. Allowed: jpg, jpeg, png, gif, webp';
                }
            }

            if (empty($error)) {
                $available = intval($_POST['available'] ?? 1);
                $this->productModel->update($id, $name, $price, $available, $imgName);
                header("Location: ../Views/products.php?success=updated");
                exit;
            }
        }

        if ($error) {
            header("Location: ../Views/edit_product.php?id=" . $id . "&error=" . urlencode($error));
            exit;
        }
    }

    public function destroy()
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: ../Views/products.php?error=Invalid product ID");
            exit;
        }

        try {
            $this->productModel->delete($id);
            header("Location: ../Views/products.php?success=deleted");
        } catch (Exception $e) {
            header("Location: ../Views/products.php?error=" . urlencode("Cannot delete this product because it is linked to existing orders."));
        }
        exit;
    }

    public function toggleAvailability()
    {
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: ../Views/products.php?error=Invalid product ID");
            exit;
        }

        $this->productModel->toggleAvailability($id);
        header("Location: ../Views/products.php?success=toggled");
        exit;
    }
}
