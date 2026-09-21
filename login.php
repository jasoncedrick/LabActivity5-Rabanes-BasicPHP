<?php
session_start();
header('Cache-Control: no-store');
if (!empty($_SESSION['email'])) {
    header('Location: index.php');
    exit;
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    $email = $_POST['email'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        $error = 'Session expired. Refresh the page and try again.';
    } elseif (!is_string($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (empty($_SESSION['registered_email'])) {
        $error = 'Your registration session expired. Please register your email again.';
    } elseif (strtolower(trim($email)) !== $_SESSION['registered_email']) {
        $error = 'Incorrect email. Use the email you registered with.';
    } else {
        session_regenerate_id(true);
        $_SESSION['email'] = $_SESSION['registered_email'];
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Rabanes</title>
<style>*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#10131b;color:#f5f6fa;font:16px/1.6 system-ui,sans-serif}main{width:100%;max-width:460px;padding:36px;background:#1b2030;border:1px solid #343d54;border-radius:20px}.eyebrow{color:#a9b9ff;font-size:12px;font-weight:700;letter-spacing:2px}h1{margin:12px 0;font-size:32px;line-height:1.2}p{color:#bdc5d8}label{display:block;margin-top:24px;font-weight:600}input,button{width:100%;padding:14px;border-radius:9px;font:inherit}input{margin:8px 0;background:#111724;color:white;border:1px solid #63708a}input:focus,button:focus-visible,a:focus-visible{outline:3px solid #a9b9ff;outline-offset:3px}button{margin-top:18px;background:#b7c3ff;color:#111724;border:0;font-weight:700;cursor:pointer}button:hover{background:#d0d8ff}a{color:#bac6ff}.error{color:#ffb3bc;min-height:26px;margin:4px 0}.note{font-size:13px}.email{overflow-wrap:anywhere;color:#d3dcff}.badge{display:inline-block;padding:4px 10px;background:#243d34;color:#b1efcc;border-radius:20px;font-size:13px}</style>
</head>
<body>
<main>
<div class="eyebrow">RABANES / LAB ACTIVITY 05</div>
<h1>Welcome back.</h1>
<p>Log in using your registered email.</p>
<?php if (isset($_GET['registered'])): ?>
<p class="badge" role="status">Registration complete. You can now log in.</p>
<?php endif; ?>
<form id="auth-form" method="post" novalidate>
<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
<label for="email">Email address</label>
<input id="email" name="email" type="email" autocomplete="email" maxlength="254" placeholder="you@example.com" required aria-describedby="error">
<p id="error" class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<button type="submit">Log in</button>
</form><p class="note">Need an account? <a href="register.php">Register</a></p>
<noscript><p>Enable JavaScript for browser email validation.</p></noscript>
</main>
<script>
const form = document.getElementById('auth-form');
const emailInput = document.getElementById('email');
const error = document.getElementById('error');
form.addEventListener('submit', (event) => {
    error.textContent = '';
    emailInput.value = emailInput.value.trim().toLowerCase();
    let savedEmail;
    try {
        savedEmail = localStorage.getItem('lab5_registered_email');
    } catch (problem) {
        event.preventDefault();
        error.textContent = 'Browser storage is unavailable. Enable it to log in.';
        return;
    }
    if (!emailInput.value) {
        error.textContent = 'Please enter your email address.';
    } else if (!emailInput.checkValidity()) {
        error.textContent = 'Please enter a valid email address.';
    } else if (!savedEmail) {
        error.textContent = 'No registered email found. Please register first.';
    } else if (emailInput.value !== savedEmail) {
        error.textContent = 'Incorrect email. Use the email you registered with.';
    }
    if (error.textContent) {
        event.preventDefault();
        emailInput.focus();
    }
    // Only a valid match proceeds to PHP to create the authenticated session.
});
</script>
</body>
</html>
