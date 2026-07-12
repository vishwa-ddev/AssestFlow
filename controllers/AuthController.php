<?php
/**
 * AssetFlow - Authentication Controller
 * Handles login, logout, and related auth actions.
 */

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Display the login page and process login form submission.
     */
    public function login(): void
    {
        if (isLoggedIn()) {
            redirect('dashboard');
        }

        $error = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Server-side validation
            if ($email === '' || $password === '') {
                $error = 'Please enter both email and password.';
            } elseif (!isValidEmail($email)) {
                $error = 'Please enter a valid email address.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } else {
                $user = $this->userModel->authenticate($email, $password);

                if ($user) {
                    loginUser($user);
                    setFlash('success', 'Welcome back, ' . ($user['full_name'] ?? 'User') . '!');
                    redirect('dashboard');
                }

                $error = 'Invalid email or password.';
            }
        }

        $flash = getFlash();

        require APP_ROOT . '/views/auth/login.php';
    }

    /**
     * Log out the current user and return to login.
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        setFlash('success', 'You have been signed out.');
        redirect('login');
    }
}
