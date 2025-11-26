<?php
/**
 * Storage Setup Script
 * 
 * Script ini digunakan untuk membuat symlink atau copy file dari storage/app/public ke public/storage
 * Akses script ini via browser: https://yourdomain.com/setup-storage.php
 * 
 * PENTING: Hapus file ini setelah setup selesai untuk keamanan!
 */

// Security: Only allow if not in production or with secret key
$secretKey = 'CHANGE_THIS_SECRET_KEY_BEFORE_USE';
$providedKey = $_GET['key'] ?? '';

if ($providedKey !== $secretKey) {
    die('Access denied. Please provide correct key: ?key=YOUR_SECRET_KEY');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Storage Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Storage Setup Script</h1>
    
    <?php
    $basePath = dirname(__DIR__);
    $storagePath = $basePath . '/storage/app/public';
    $publicStoragePath = $basePath . '/public/storage';
    $errors = [];
    $success = [];
    
    // Check if storage directory exists
    if (!is_dir($storagePath)) {
        $errors[] = "Storage directory tidak ditemukan: $storagePath";
    }
    
    // Check if public/storage already exists
    $storageExists = file_exists($publicStoragePath);
    $isSymlink = is_link($publicStoragePath);
    
    if ($storageExists && !$isSymlink) {
        $info[] = "Directory public/storage sudah ada (bukan symlink).";
    } elseif ($isSymlink) {
        $success[] = "Symlink sudah ada di: $publicStoragePath";
        $success[] = "Target: " . readlink($publicStoragePath);
    }
    
    // Handle actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create_symlink') {
            // Try to create symlink
            if ($storageExists && !$isSymlink) {
                // Remove existing directory first
                if (is_dir($publicStoragePath)) {
                    if (!rmdir($publicStoragePath)) {
                        $errors[] = "Tidak bisa menghapus directory yang sudah ada. Silakan hapus manual: $publicStoragePath";
                    }
                }
            }
            
            if (!$storageExists || !$isSymlink) {
                if (symlink($storagePath, $publicStoragePath)) {
                    $success[] = "Symlink berhasil dibuat!";
                    $success[] = "Dari: $publicStoragePath";
                    $success[] = "Ke: $storagePath";
                } else {
                    $errors[] = "Gagal membuat symlink. Kemungkinan hosting tidak mendukung symlink.";
                    $errors[] = "Error: " . error_get_last()['message'] ?? 'Unknown error';
                }
            }
        } elseif ($action === 'copy_files') {
            // Copy files instead of symlink
            if (!is_dir($publicStoragePath)) {
                if (!mkdir($publicStoragePath, 0755, true)) {
                    $errors[] = "Gagal membuat directory: $publicStoragePath";
                }
            }
            
            if (is_dir($publicStoragePath)) {
                $copied = 0;
                $failed = 0;
                
                function copyRecursive($src, $dst) {
                    global $copied, $failed;
                    $dir = opendir($src);
                    @mkdir($dst, 0755, true);
                    while (($file = readdir($dir)) !== false) {
                        if ($file != '.' && $file != '..') {
                            $srcFile = $src . '/' . $file;
                            $dstFile = $dst . '/' . $file;
                            if (is_dir($srcFile)) {
                                copyRecursive($srcFile, $dstFile);
                            } else {
                                if (copy($srcFile, $dstFile)) {
                                    $copied++;
                                } else {
                                    $failed++;
                                }
                            }
                        }
                    }
                    closedir($dir);
                }
                
                copyRecursive($storagePath, $publicStoragePath);
                $success[] = "File berhasil di-copy!";
                $success[] = "Berhasil: $copied file";
                if ($failed > 0) {
                    $errors[] = "Gagal: $failed file";
                }
            }
        }
    }
    
    // Display messages
    foreach ($errors as $error) {
        echo "<div class='error'>$error</div>";
    }
    
    foreach ($success as $msg) {
        echo "<div class='success'>$msg</div>";
    }
    
    if (isset($info)) {
        foreach ($info as $msg) {
            echo "<div class='info'>$msg</div>";
        }
    }
    ?>
    
    <div class="info">
        <h3>Informasi:</h3>
        <ul>
            <li><strong>Storage Path:</strong> <?php echo $storagePath; ?></li>
            <li><strong>Public Storage Path:</strong> <?php echo $publicStoragePath; ?></li>
            <li><strong>Storage Exists:</strong> <?php echo $storageExists ? 'Ya' : 'Tidak'; ?></li>
            <li><strong>Is Symlink:</strong> <?php echo $isSymlink ? 'Ya' : 'Tidak'; ?></li>
            <li><strong>Storage Writable:</strong> <?php echo is_writable($storagePath) ? 'Ya' : 'Tidak'; ?></li>
            <li><strong>Public Writable:</strong> <?php echo is_writable($basePath . '/public') ? 'Ya' : 'Tidak'; ?></li>
        </ul>
    </div>
    
    <?php if (!$isSymlink): ?>
    <div class="warning">
        <h3>Pilih Metode Setup:</h3>
        <p><strong>Opsi 1: Symlink (Recommended)</strong> - Membuat link simbolis. Lebih efisien tapi mungkin tidak didukung semua hosting.</p>
        <p><strong>Opsi 2: Copy Files</strong> - Menyalin semua file. Lebih lambat tapi bekerja di semua hosting.</p>
        
        <form method="POST">
            <button type="submit" name="action" value="create_symlink">Buat Symlink</button>
            <button type="submit" name="action" value="copy_files">Copy Files</button>
        </form>
    </div>
    <?php else: ?>
    <div class="success">
        <h3>Setup Selesai!</h3>
        <p>Symlink sudah ada dan berfungsi. Anda bisa menghapus file setup-storage.php ini sekarang.</p>
    </div>
    <?php endif; ?>
    
    <div class="warning" style="margin-top: 30px;">
        <strong>PENTING:</strong> Hapus file ini (setup-storage.php) setelah setup selesai untuk keamanan!
    </div>
</body>
</html>

