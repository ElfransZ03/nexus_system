<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nexus OS | Command Deck</title>
    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --success: #2ecc71; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; display: flex; flex-direction: column; height: 100vh; }
        
        /* Top Navigation Bar */
        .top-bar { background: var(--primary); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .status-pill { background: rgba(46, 204, 113, 0.2); border: 1px solid var(--success); color: var(--success); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }

        /* Main Content Layout */
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; padding: 30px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box; }
        
        /* App Launcher Cards */
        .launcher-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .app-card { background: white; padding: 25px; border-radius: 15px; text-align: center; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-bottom: 4px solid #ddd; }
        .app-card:hover { transform: translateY(-5px); border-bottom-color: var(--accent); }
        .app-card h3 { margin: 10px 0 5px 0; color: var(--primary); }

        /* Sidebar Widgets */
        .widget { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .affinity-mini-bar { height: 10px; background: #eee; border-radius: 5px; overflow: hidden; display: flex; margin-top: 10px; }
        
        .log-item { font-size: 13px; padding: 10px 0; border-bottom: 1px solid #eee; color: #555; }
        .log-item small { color: #999; display: block; }
    </style>
</head>
<body>

<div class="top-bar">
    <div style="font-size: 20px; font-weight: bold; letter-spacing: 1px;">NEXUS OS <span style="font-weight: 300; opacity: 0.7;">v3.0</span></div>
    <div class="status-pill">● CORE SYSTEMS ONLINE</div>
</div>

<div class="dashboard-grid">
    <div class="launcher-grid">
        <div class="app-card" onclick="location.href='nexus_vault/index.php'">
            <div style="font-size: 40px;">🔒</div>
            <h3>Vault</h3>
            <small>Security & Secrets</small>
        </div>
        <div class="app-card" onclick="location.href='nexus_calendar/index.php'">
            <div style="font-size: 40px;">📅</div>
            <h3>Calendar</h3>
            <small>Events & Planning</small>
        </div>
        <div class="app-card" onclick="location.href='hobby_tracker/index.php'">
            <div style="font-size: 40px;">📊</div>
            <h3>Affinity</h3>
            <small>Skill & Hobby Logs</small>
        </div>
        <div class="app-card" onclick="location.href='wrestler.php'">
            <div style="font-size: 40px;">🤼</div>
            <h3>Wrestler World</h3>
            <small>RPG Engine & Lore</small>
        </div>
    </div>

    <div class="sidebar">
        <div class="widget">
            <h4 style="margin:0;">System Affinity</h4>
            <div class="affinity-mini-bar" id="miniAffinity"></div>
            <p id="topHobby" style="font-size: 12px; margin-top: 8px; color: #666;"></p>
        </div>

        <div class="widget">
            <h4 style="margin:0 0 10px 0;">Recent Activity</h4>
            <div id="recentLogs">
                <p style="font-size: 12px; color: #999;">Fetching logs...</p>
            </div>
        </div>
    </div>
</div>

<script>
    const API_KEY = localStorage.getItem('nexus_api_key');
    const GATEWAY = '../nexus_core/gateway.php';

    async function loadDashboardData() {
        try {
            const response = await fetch(GATEWAY, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Nexus-Key': API_KEY },
                body: JSON.stringify({ action: 'get_affinity' }) // Reusing the hobby tracker logic
            });
            const result = await response.json();
            
            if (result.data) {
                renderMiniAffinity(result.data);
                renderRecentLogs(result.data.slice(-5).reverse()); // Last 5 logs
            }
        } catch (err) {
            console.error("Dashboard Sync Error");
        }
    }

    function renderMiniAffinity(data) {
        const bar = document.getElementById('miniAffinity');
        const totals = { 'Coding': 0, 'Writing': 0, 'World-Building': 0, 'Fitness': 0 };
        let grandTotal = 0;

        data.forEach(item => {
            const d = JSON.parse(item.item_data);
            totals[item.item_name] += d.minutes;
            grandTotal += d.minutes;
        });

        const colors = { 'Coding': '#3498db', 'Writing': '#f1c40f', 'World-Building': '#9b59b6', 'Fitness': '#2ecc71' };
        bar.innerHTML = "";
        
        for (const [name, val] of Object.entries(totals)) {
            if (val > 0) {
                const pc = (val / grandTotal) * 100;
                bar.innerHTML += `<div style="width:${pc}%; background:${colors[name]}"></div>`;
            }
        }
        
        // Find top hobby
        const top = Object.keys(totals).reduce((a, b) => totals[a] > totals[b] ? a : b);
        document.getElementById('topHobby').innerText = `Current Focus: ${top}`;
    }

    function renderRecentLogs(logs) {
        const container = document.getElementById('recentLogs');
        container.innerHTML = "";
        logs.forEach(log => {
            const d = JSON.parse(log.item_data);
            container.innerHTML += `
                <div class="log-item">
                    <strong>${log.item_name}</strong>: ${d.note || 'No description'}
                    <small>${d.timestamp || ''}</small>
                </div>
            `;
        });
    }

    loadDashboardData();
</script>
</body>
</html>