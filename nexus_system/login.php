<!DOCTYPE html>
<html>
<head>
    <title>Nexus OS | Login</title>
    <style>
        body { font-family: sans-serif; background: #2c3e50; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        #msg { color: red; font-size: 0.8rem; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>

<div class="login-card">
    <h2 style="text-align:center; margin-top:0;">Nexus OS</h2>
    <input type="text" id="user" placeholder="Username">
    <input type="password" id="pass" placeholder="Password">
    <button onclick="attemptLogin()">Enter System</button>
    <div id="msg"></div>
</div>

<script>
async function attemptLogin() {
    const user = document.getElementById('user').value;
    const pass = document.getElementById('pass').value;
    const msg = document.getElementById('msg');

    const response = await fetch('nexus_core/gateway.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'login', username: user, password: pass })
    });

    const result = await response.json();

    if (result.status === 'success') {
        // Professional Logic: Store the key in LocalStorage
        localStorage.setItem('nexus_api_key', result.api_key);
        localStorage.setItem('nexus_user', result.username);
        
        // Redirect to the Vitality Hub or Dashboard
        window.location.href = 'vitality_hub/index.html';
    } else {
        msg.innerText = result.message;
    }
}
</script>
</body>
</html>