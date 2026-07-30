<?php
echo "Step 1: Before wp-load.php\n";
try {
    require_once '/Users/shrutian/Desktop/cora/app/public/wp-load.php';
    echo "Step 2: wp-load.php loaded successfully\n";
} catch (Throwable $e) {
    echo "Caught: " . $e->getMessage() . "\n";
}
