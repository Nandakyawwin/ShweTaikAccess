<?php

// Include or require the PHP file containing the functionality
require_once 'stock_list.php';

// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow the following methods from the client side
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Allow the following headers from the client side
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");

// Allow credentials (cookies, authorization headers, etc.) to be sent cross-origin
header("Access-Control-Allow-Credentials: true");

// Get the table name from the query parameter
$tableName = isset($_GET['table']) ? $_GET['table'] : 'AR_CUSTOMER';

// Call the function directly
try {
    CheckLogin();
    GetData($tableName);
} catch (Exception $e) {
    echo 'Caught exception: ', $e->getMessage(), "\n";
}


?>
