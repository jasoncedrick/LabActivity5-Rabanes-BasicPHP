<?php
session_start();
header('Cache-Control: no-store');
if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (is_string($token) && hash_equals($_SESSION['csrf'], $token)) {
        // Preserve the registered email for logging in again after logout.
        unset($_SESSION['email']);
        session_regenerate_id(true);
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: login.php');
        exit;
    }
    http_response_code(403);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Rabanes</title>
<style>*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#10131b;color:#f5f6fa;font:16px/1.6 system-ui,sans-serif}main{width:100%;max-width:460px;padding:36px;background:#1b2030;border:1px solid #343d54;border-radius:20px}.eyebrow{color:#a9b9ff;font-size:12px;font-weight:700;letter-spacing:2px}h1{margin:12px 0;font-size:32px;line-height:1.2}p{color:#bdc5d8}label{display:block;margin-top:24px;font-weight:600}input,button{width:100%;padding:14px;border-radius:9px;font:inherit}input{margin:8px 0;background:#111724;color:white;border:1px solid #63708a}input:focus,button:focus-visible,a:focus-visible{outline:3px solid #a9b9ff;outline-offset:3px}button{margin-top:18px;background:#b7c3ff;color:#111724;border:0;font-weight:700;cursor:pointer}button:hover{background:#d0d8ff}a{color:#bac6ff}.error{color:#ffb3bc;min-height:26px;margin:4px 0}.note{font-size:13px}.email{overflow-wrap:anywhere;color:#d3dcff}.badge{display:inline-block;padding:4px 10px;background:#243d34;color:#b1efcc;border-radius:20px;font-size:13px}</style>
</head>
<body>
<main>
<div class="eyebrow">RABANES / LAB ACTIVITY 05</div>
<span class="badge">Authenticated</span>
<h1>You're logged in.</h1>
<p>Welcome to your protected dashboard.</p>
<p class="email"><?= htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8') ?></p>
<p class="note">This page requires an active PHP login session.</p>
<form method="post" action="index.php">
<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
<button type="submit">Log out</button>
</form>
</main>

</body>
</html>
