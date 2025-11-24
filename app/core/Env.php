<?php

class Env
{
    private static $variables = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = dirname(dirname(dirname(__FILE__))) . '/.env';
        }

        if (!file_exists($path)) {
            throw new Exception('.env file not found at: ' . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                // Store in array and set as environment variable
                self::$variables[$key] = $value;
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Get environment variable
     * 
     * @param string $key Variable name
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Try from our array first
        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        // Try from $_ENV
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        // Try from $_SERVER
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        // Try getenv
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Check if environment variable exists
     * 
     * @param string $key Variable name
     * @return bool
     */
    public static function has($key)
    {
        return self::get($key) !== null;
    }

    /**
     * Set environment variable
     * 
     * @param string $key Variable name
     * @param mixed $value Variable value
     */
    public static function set($key, $value)
    {
        self::$variables[$key] = $value;
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    /**
     * Get all environment variables
     * 
     * @return array
     */
    public static function all()
    {
        return self::$variables;
    }
}
