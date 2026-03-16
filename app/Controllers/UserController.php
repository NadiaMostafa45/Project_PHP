<?php

namespace App\Controllers;

use App\Models\User;

class UserController {

    public function store() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $passwordRaw = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $ext = !empty($_POST['ext']) ? trim($_POST['ext']) : null;

            $errors = [];

            if ($name === '') {
                $errors['name'] = 'Name is required.';
            }

            if ($email === '') {
                $errors['email'] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address.';
            }

            if ($passwordRaw === '') {
                $errors['password'] = 'Password is required.';
            } elseif (strlen($passwordRaw) < 6) {
                $errors['password'] = 'Password must be at least 6 characters.';
            }

            if (!in_array($role, ['user', 'admin'], true)) {
                $errors['role'] = 'Invalid role selected.';
            }

            if (!empty($errors)) {
                \AuthMiddleware::startSession();
                $_SESSION['add_user_errors'] = $errors;
                $_SESSION['add_user_old'] = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'ext' => $ext,
                ];

                header("Location: ../Views/add_user.php?error=1");
                exit;
            }

            $user = new User();

            if ($user->emailExists($email)) {
                \AuthMiddleware::startSession();
                $_SESSION['add_user_errors'] = [
                    'email' => 'This email is already registered.'
                ];
                $_SESSION['add_user_old'] = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'ext' => $ext,
                ];

                header("Location: ../Views/add_user.php?error=1");
                exit;
            }

            $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

            $imageName = null;

            if (!empty($_FILES['image']['name'])) {

                $imageName = time() . "_" . $_FILES['image']['name'];

                $tmp = $_FILES['image']['tmp_name'];

                $path = "../public/assets/images/users/" . $imageName;

                move_uploaded_file($tmp, $path);
            }

            $user->create($name, $email, $password, $role, $ext, $imageName);

            \AuthMiddleware::startSession();
            unset($_SESSION['add_user_errors'], $_SESSION['add_user_old']);

            header("Location: ../Views/add_user.php?success=1");
            exit;
        }
    }
    public function index(){

        $search = $_GET['search'] ?? null;

        $user = new User();

        return $user->getAll($search);
    }

    public function delete(){

        if(isset($_GET['id'])){

            $id = $_GET['id'];

            $user = new User();
            $user->delete($id);

            header("Location: ../Views/users.php?deleted=1");
            exit;
        }
    }

    public function update(){
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            header("Location: ../Views/users.php?error=Invalid user ID");
            exit;
        }

        \AuthMiddleware::startSession();
        $currentUserId = (int)($_SESSION['user_id'] ?? 0);
        $isAdmin = (($_SESSION['role'] ?? 'user') === 'admin');

        $user = new User();
        $existingUser = $user->getById($id);
        if (!$existingUser) {
            header("Location: ../Views/users.php?error=User not found");
            exit;
        }

        if (!$isAdmin && $currentUserId !== $id) {
            header("Location: ../Views/home.php");
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $isAdmin ? ($_POST['role'] ?? 'user') : ($existingUser['role'] ?? 'user');
        $ext = $isAdmin ? (!empty($_POST['ext']) ? trim($_POST['ext']) : null) : ($existingUser['ext'] ?? null);
        $passwordRaw = $_POST['password'] ?? '';

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($isAdmin && !in_array($role, ['user', 'admin'], true)) {
            $errors['role'] = 'Invalid role selected.';
        }

        if ($passwordRaw !== '' && strlen($passwordRaw) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($user->emailExistsForOtherUser($email, $id)) {
                $errors['email'] = 'This email is already registered.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['edit_user_errors'] = $errors;
            $_SESSION['edit_user_old'] = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'ext' => $ext,
            ];

            header("Location: ../Views/edit_user.php?id=" . $id . "&error=1");
            exit;
        }

        $password = null;
        if ($passwordRaw !== '') {
            $password = password_hash($passwordRaw, PASSWORD_DEFAULT);
        }

        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $imageName = time() . "_" . $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];
            $path = "../public/assets/images/users/" . $imageName;
            move_uploaded_file($tmp, $path);
        }

        $user->update($id, $name, $email, $role, $ext, $imageName, $password);

        unset($_SESSION['edit_user_errors'], $_SESSION['edit_user_old']);

        if ($isAdmin) {
            header("Location: ../Views/users.php?success=updated");
        } else {
            header("Location: ../Views/myprofile.php?success=updated");
        }
        exit;
    }
}
