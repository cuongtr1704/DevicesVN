<?php
/**
 * User Model
 */

require_once ROOT_PATH . '/app/models/Model.php';

class User extends Model {
    protected $table = 'users';

    public function register($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['verification_token'] = bin2hex(random_bytes(32));
        
        try {
            return $this->insert($data);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function findByEmail($email) {
        return $this->findBy('email', $email);
    }

    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    public function createResetToken($email) {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        ]);
        
        return $token;
    }

    public function resetPassword($token, $newPassword) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE reset_token = :token 
                AND reset_token_expiry > NOW() 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        return $this->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expiry' => null
        ]);
    }
}
