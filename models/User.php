<?php
/**
 * AssetFlow - User Model
 * Handles user data access and authentication.
 */

class User
{
    /**
     * Find an active user by email address.
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare(
            'SELECT id, email, password, full_name, role, status
             FROM users
             WHERE email = ? AND status = ?'
        );
        $stmt->execute([$email, 'active']);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Verify user credentials.
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }
}
