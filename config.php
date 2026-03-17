<?php
session_start();

// --- CONFIGURATION ---
$main_user = "Chamu2010@";
$main_pass = "Sasax2010@";

define('GH_CLIENT_ID', 'YOUR_GITHUB_CLIENT_ID');
define('GH_CLIENT_SECRET', 'YOUR_GITHUB_CLIENT_SECRET');
define('REDIRECT_URL', 'https://oyage-app-link.com/'); // GitHub eke dapu URL eka danna

// Database Setup
$db = new PDO('sqlite:reseller_v2.db');
$db->exec("CREATE TABLE IF NOT EXISTS keys_table (id INTEGER PRIMARY KEY, license_key TEXT, status TEXT DEFAULT 'Active')");

// 1. Password Auth Logic
if (isset($_POST['main_login'])) {
    if ($_POST['u'] === $main_user && $_POST['p'] === $main_pass) {
        $_SESSION['pass_verified'] = true;
    }
}

// 2. GitHub OAuth Logic
if (isset($_GET['code']) && isset($_SESSION['pass_verified'])) {
    $ch = curl_init('https://github.com/login/oauth/access_token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'client_id' => GH_CLIENT_ID,
        'client_secret' => GH_CLIENT_SECRET,
        'code' => $_GET['code']
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $response = json_decode(curl_exec($ch), true);
    
    if (isset($response['access_token'])) {
        $_SESSION['gh_logged_in'] = true;
        header("Location: index.php");
        exit;
    }
}

// 3. Key Management
if (isset($_POST['add_key']) && isset($_SESSION['gh_logged_in'])) {
    $stmt = $db->prepare("INSERT INTO keys_table (license_key) VALUES (?)");
    $stmt->execute([$_POST['key_val']]);
}

if (isset($_GET['reset']) && isset($_SESSION['gh_logged_in'])) {
    $stmt = $db->prepare("UPDATE keys_table SET status = 'Active' WHERE id = ?");
    $stmt->execute([$_GET['reset']]);
    header("Location: index.php");
}

$all_keys = $db->query("SELECT * FROM keys_table ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
