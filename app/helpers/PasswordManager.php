<?php
/**
 * PasswordManager - Secure password hashing and verification
 * Uses HMAC-SHA256 with pepper + BCrypt for maximum security
 */

class PasswordManager {
    
    /**
     * Hash a password using HMAC-SHA256 + BCrypt
     * 
     * @param string $password Plain text password
     * @return string Hashed password (60 chars bcrypt)
     */
    public static function hashPassword($password) {
        $pepper = Env::get('PASSWORD_PEPPER', '');
        
        if (empty($pepper)) {
            throw new Exception('PASSWORD_PEPPER not configured in .env file');
        }
        
        // Step 1: HMAC-SHA256 with pepper (protects against rainbow tables)
        $peppered = hash_hmac('sha256', $password, $pepper);
        
        // Step 2: BCrypt with cost from config (protects against brute-force)
        $cost = (int) Env::get('BCRYPT_COST', 12);
        $hashed = password_hash($peppered, PASSWORD_BCRYPT, ['cost' => $cost]);
        
        return $hashed;
    }
    
    /**
     * Verify a password against a hash
     * 
     * @param string $password Plain text password
     * @param string $hash Stored hash from database
     * @return bool True if password matches
     */
    public static function verifyPassword($password, $hash) {
        $pepper = Env::get('PASSWORD_PEPPER', '');
        
        if (empty($pepper)) {
            throw new Exception('PASSWORD_PEPPER not configured in .env file');
        }
        
        // Step 1: Apply same HMAC-SHA256 with pepper
        $peppered = hash_hmac('sha256', $password, $pepper);
        
        // Step 2: Verify against BCrypt hash
        return password_verify($peppered, $hash);
    }
    
    /**
     * Check if password needs rehashing (if cost changed)
     * 
     * @param string $hash Stored hash from database
     * @return bool True if needs rehashing
     */
    public static function needsRehash($hash) {
        $cost = (int) Env::get('BCRYPT_COST', 12);
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $cost]);
    }
}
