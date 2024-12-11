<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private $user;

    public function logout() {
        // Clear all session data
        session_start();
        session_unset();
        session_destroy();
        
        // Redirect to login page
        header("Location: /login");
        exit();
    }
    

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
         // Fetch all users from the database
         $users = $this->user->all();

         // Pass the data to the 'users/index' view
         $this->view('users/index', compact('users'));
    }

    public function create()
    {
        $this->view('users/create');
    }

    public function store()
    {
        $this->user->create($_POST);
        header('Location: /');
    }

    public function edit($id)
    {
        // Fetch the user data using the ID
        $user = $this->user->find($id);

        // Pass the user data to the 'users/edit' view
        $this->view('users/edit', compact('user'));
    }

    public function update($id)
    {
        $this->user->update($id, $_POST);
        header('Location: /');
    }

    public function delete($id)
    {
        $this->user->delete($id);
        header('Location: /');
    }

    public function register() {
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the form data
            $data = [
                'username' => $_POST['username'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password']
            ];

            // Initialize the User model
            $userModel = new User();

            // Check if the username already exists
            if ($userModel->usernameExists($data['username'])) {
                echo "Username is already taken!";
                return;
            }

            // Check if the email already exists
            if ($userModel->emailExists($data['email'])) {
                echo "Email is already taken!";
                return;
            }

            // Validate password confirmation
            if ($data['password'] !== $data['confirm_password']) {
                echo "Passwords do not match!";
                return;
            }


            // Register the user
            if ($userModel->register($data)) {
                header("Location: /login");
                exit;
            } else {
                echo "Something went wrong. Please try again.";
            }
        }

    // Load the registration form view
    $this->view('users/register');
    }

    public function loginForm() {
        // If user is already logged in, redirect to index
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
        $this->view('users/login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            $user = $this->user->login($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /');
                exit;
            } else {
                // Redirect back to login with error
                header('Location: /login?error=1');
                exit;
            }
        }
    }
}
