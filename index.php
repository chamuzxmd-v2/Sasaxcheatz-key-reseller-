<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>X4X RESELLER v2</title>
    <style>
        :root { --neon: #00ff41; --bg: #050505; }
        body { background: var(--bg); color: var(--neon); font-family: 'Courier New', monospace; margin: 0; overflow: hidden; }
        canvas { position: fixed; top: 0; left: 0; z-index: -1; opacity: 0.5; }
        .panel { background: rgba(0,0,0,0.9); border: 1px solid var(--neon); padding: 30px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 350px; text-align: center; }
        .dashboard { max-width: 900px; margin: 20px auto; background: rgba(0,0,0,0.85); border: 1px solid var(--neon); padding: 20px; position: relative; top: 20px; }
        input { background: #000; border: 1px solid var(--neon); color: var(--neon); padding: 10px; width: 90%; margin-bottom: 10px; }
        .btn { background: transparent; color: var(--neon); border: 1px solid var(--neon); padding: 10px 20px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: var(--neon); color: #000; box-shadow: 0 0 15px var(--neon); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid var(--neon); padding: 10px; text-align: left; }
    </style>
</head>
<body>
<canvas id="matrix"></canvas>

<?php if (!isset($_SESSION['pass_verified'])): ?>
    <div class="panel">
        <h2 style="letter-spacing: 5px;">SECURITY CHECK</h2>
        <form method="POST">
            <input type="text" name="u" placeholder="Admin ID" required>
            <input type="password" name="p" placeholder="Security Key" required>
            <button type="submit" name="main_login" class="btn">UNLOCK</button>
        </form>
    </div>

<?php elseif (!isset($_SESSION['gh_logged_in'])): ?>
    <div class="panel">
        <h2>GITHUB AUTH REQUIRED</h2>
        <p>Authorize your developer identity.</p>
        <a href="https://github.com/login/oauth/authorize?client_id=<?php echo GH_CLIENT_ID; ?>&scope=user" class="btn">LOGIN WITH GITHUB</a>
    </div>

<?php else: ?>
    <div class="dashboard">
        <header style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--neon); padding-bottom: 10px;">
            <span>[ USER: AUTHENTICATED ]</span>
            <a href="?logout=1" style="color:red; text-decoration:none;">DISCONNECT</a>
        </header>
        
        <h3 style="margin-top:20px;">KEY MANAGEMENT</h3>
        <form method="POST">
            <input type="text" name="key_val" placeholder="Generate License Key..." style="width: 70%;">
            <button type="submit" name="add_key" class="btn">ADD</button>
        </form>

        <table>
            <tr><th>ID</th><th>LICENSE</th><th>STATUS</th><th>COMMAND</th></tr>
            <?php foreach($all_keys as $k): ?>
            <tr>
                <td>#<?php echo $k['id']; ?></td>
                <td><?php echo htmlspecialchars($k['license_key']); ?></td>
                <td><?php echo $k['status']; ?></td>
                <td><a href="?reset=<?php echo $k['id']; ?>" class="btn" style="padding: 2px 10px; font-size: 12px;">RESET</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<script>
    const canvas = document.getElementById('matrix');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth; canvas.height = window.innerHeight;
    const chars = "01X4XSAZ";
    const fontSize = 16;
    const columns = canvas.width / fontSize;
    const drops = Array(Math.floor(columns)).fill(1);
    function draw() {
        ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "#0f4";
        ctx.font = fontSize + "px monospace";
        for (let i = 0; i < drops.length; i++) {
            const text = chars[Math.floor(Math.random() * chars.length)];
            ctx.fillText(text, i * fontSize, drops[i] * fontSize);
            if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        }
    }
    setInterval(draw, 35);
</script>
</body>
</html>

