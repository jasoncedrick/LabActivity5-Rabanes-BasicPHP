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
// Keep a server-side copy so a direct login POST cannot skip registration.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $token = $_POST['csrf'] ?? '';
    $email = $_POST['email'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Session expired. Refresh the page and try again.']);
    } elseif (!is_string($email) || !filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
        http_response_code(422);
        echo json_encode(['error' => 'Please enter a valid email address.']);
    } else {
        $_SESSION['registered_email'] = strtolower(trim($email));
        echo json_encode(['success' => true]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | Rabanes</title>
<style>*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#10131b;color:#f5f6fa;font:16px/1.6 system-ui,sans-serif}main{width:100%;max-width:460px;padding:36px;background:#1b2030;border:1px solid #343d54;border-radius:20px}.eyebrow{color:#a9b9ff;font-size:12px;font-weight:700;letter-spacing:2px}h1{margin:12px 0;font-size:32px;line-height:1.2}p{color:#bdc5d8}label{display:block;margin-top:24px;font-weight:600}input,button{width:100%;padding:14px;border-radius:9px;font:inherit}input{margin:8px 0;background:#111724;color:white;border:1px solid #63708a}input:focus,button:focus-visible,a:focus-visible{outline:3px solid #a9b9ff;outline-offset:3px}button{margin-top:18px;background:#b7c3ff;color:#111724;border:0;font-weight:700;cursor:pointer}button:hover{background:#d0d8ff}a{color:#bac6ff}.error{color:#ffb3bc;min-height:26px;margin:4px 0}.note{font-size:13px}.email{overflow-wrap:anywhere;color:#d3dcff}.badge{display:inline-block;padding:4px 10px;background:#243d34;color:#b1efcc;border-radius:20px;font-size:13px}</style>
</head>
<body>
<main>
<div class="eyebrow">RABANES / LAB ACTIVITY 05</div>
<h1>Create your account.</h1>
<p>Register your email to access the dashboard.</p>
<form id="auth-form" method="post" novalidate>
<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
<label for="email">Email address</label>
<input id="email" name="email" type="email" autocomplete="email" maxlength="254" placeholder="you@example.com" required aria-describedby="error">
<p id="error" class="error" role="alert"></p>
<button type="submit">Create account</button>
</form><p class="note">Already registered? <a href="login.php">Log in</a></p>
<p class="note">Email-only classroom demo. Use the same browser to register and log in.</p>
<noscript><p>Enable JavaScript to register and save your email.</p></noscript>
</main>
<script>
const form = document.getElementById('auth-form');
const emailInput = document.getElementById('email');
const error = document.getElementById('error');
form.addEventListener('submit', async (event) => {
    event.preventDefault();
    error.textContent = '';
    emailInput.value = emailInput.value.trim().toLowerCase();
    if (!emailInput.value || !emailInput.checkValidity()) {
        error.textContent = 'Please enter a valid email address.';
        emailInput.focus();
        return;
    }
    const button = form.querySelector('button');
    button.disabled = true;
    try {
        // Check browser storage before creating the registration.
        localStorage.setItem('lab5-storage-test', 'ok');
        localStorage.removeItem('lab5-storage-test');
        const response = await fetch('register.php', {
            method: 'POST', body: new FormData(form)
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
            error.textContent = result.error || 'Registration failed. Please try again.';
            return;
        }
        localStorage.setItem('lab5_registered_email', emailInput.value);
        window.location.href = 'login.php?registered=1';
    } catch (problem) {
        error.textContent = 'Could not register. Enable browser storage and check that the PHP server is running.';
    } finally {
        button.disabled = false;
    }
});
</script>
</body>
</html>
