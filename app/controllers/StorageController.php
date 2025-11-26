<?php

class StorageController extends Controller {
    
    /**
     * Serve files from storage/uploads securely
     */
    public function serve($filename) {
        // Build file path
        $filePath = ROOT_PATH . '/storage/uploads/' . $filename;
        
        // Security: prevent directory traversal
        $realPath = realpath($filePath);
        $basePath = realpath(ROOT_PATH . '/storage/uploads/');
        
        // Check if file exists and is within allowed directory
        if (!$realPath || strpos($realPath, $basePath) !== 0 || !is_file($realPath)) {
            header('HTTP/1.0 404 Not Found');
            echo 'File not found';
            exit;
        }
        
        // Get mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $realPath);
        finfo_close($finfo);
        
        // Set headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($realPath));
        header('Cache-Control: public, max-age=86400');
        
        // Output file
        readfile($realPath);
        exit;
    }
}
