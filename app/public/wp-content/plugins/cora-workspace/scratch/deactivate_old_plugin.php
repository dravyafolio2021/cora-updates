<?php
$socket = '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock';
$user = 'root';
$pass = 'root';
$db   = 'local';

try {
    $pdo = new PDO("mysql:unix_socket={$socket};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $stmt = $pdo->prepare("SELECT option_value FROM wp_options WHERE option_name = 'active_plugins'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $active_plugins = unserialize($row['option_value']);
        echo "Currently active plugins:\n";
        print_r($active_plugins);
        
        $key = array_search('cora-real-estate/cora-real-estate.php', $active_plugins);
        if ($key !== false) {
            unset($active_plugins[$key]);
            $active_plugins = array_values($active_plugins);
            $new_value = serialize($active_plugins);
            
            $update = $pdo->prepare("UPDATE wp_options SET option_value = ? WHERE option_name = 'active_plugins'");
            $update->execute([$new_value]);
            echo "\nSuccessfully deactivated cora-real-estate/cora-real-estate.php!\nNew list:\n";
            print_r($active_plugins);
        } else {
            echo "\ncora-real-estate/cora-real-estate.php is not active.\n";
        }
    } else {
        echo "Could not find active_plugins option.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
