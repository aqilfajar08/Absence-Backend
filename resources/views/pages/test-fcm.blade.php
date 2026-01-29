<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test FCM Token</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .token-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            margin: 20px 0;
            min-height: 60px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #45a049;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #2196F3;
            margin: 20px 0;
        }
        .error {
            background: #ffebee;
            padding: 15px;
            border-left: 4px solid #f44336;
            margin: 20px 0;
        }
        .success {
            background: #e8f5e9;
            padding: 15px;
            border-left: 4px solid #4CAF50;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Test FCM Token Generator</h1>
        
        <div class="info">
            <strong>Instruksi:</strong><br>
            1. Klik tombol "Get FCM Token"<br>
            2. Izinkan notifikasi jika diminta browser<br>
            3. Copy token yang muncul<br>
            4. Paste di SQL query atau update manual di database
        </div>

        <button onclick="requestNotificationPermission()">Get FCM Token</button>

        <div id="status"></div>
        
        <div id="token-container" style="display: none;">
            <h3>Your FCM Token:</h3>
            <div class="token-box" id="token-display"></div>
            <button onclick="copyToken()">📋 Copy Token</button>
            <button onclick="saveToCurrentUser()">💾 Save to Current User</button>
        </div>

        <div id="sql-container" style="display: none;">
            <h3>SQL Query to Update:</h3>
            <div class="token-box" id="sql-query"></div>
            <button onclick="copySql()">📋 Copy SQL</button>
        </div>
    </div>

    <script type="module">
        import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';
        import { getMessaging, getToken } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging.js';

        // Firebase config - ganti dengan config project kamu
        const firebaseConfig = {
            apiKey: "YOUR_API_KEY",
            authDomain: "kasau-notification-flutterfire.firebaseapp.com",
            projectId: "kasau-notification-flutterfire",
            storageBucket: "kasau-notification-flutterfire.appspot.com",
            messagingSenderId: "YOUR_SENDER_ID",
            appId: "YOUR_APP_ID"
        };

        window.fcmToken = null;

        window.requestNotificationPermission = async function() {
            const statusDiv = document.getElementById('status');
            
            try {
                statusDiv.innerHTML = '<div class="info">⏳ Requesting notification permission...</div>';
                
                const permission = await Notification.requestPermission();
                
                if (permission === 'granted') {
                    statusDiv.innerHTML = '<div class="success">✅ Permission granted! Getting token...</div>';
                    
                    const app = initializeApp(firebaseConfig);
                    const messaging = getMessaging(app);
                    
                    const token = await getToken(messaging, {
                        vapidKey: 'YOUR_VAPID_KEY' // Dapatkan dari Firebase Console
                    });
                    
                    if (token) {
                        window.fcmToken = token;
                        document.getElementById('token-display').textContent = token;
                        document.getElementById('token-container').style.display = 'block';
                        
                        const sql = `UPDATE users SET fcm_token = '${token}' WHERE id = {{ auth()->id() }};`;
                        document.getElementById('sql-query').textContent = sql;
                        document.getElementById('sql-container').style.display = 'block';
                        
                        statusDiv.innerHTML = '<div class="success">✅ Token generated successfully!</div>';
                    } else {
                        statusDiv.innerHTML = '<div class="error">❌ No registration token available.</div>';
                    }
                } else {
                    statusDiv.innerHTML = '<div class="error">❌ Permission denied! Please allow notifications in browser settings.</div>';
                }
            } catch (error) {
                console.error('Error:', error);
                statusDiv.innerHTML = `<div class="error">❌ Error: ${error.message}<br><br><strong>Note:</strong> Firebase config needs to be updated with your actual project keys.</div>`;
            }
        };

        window.copyToken = function() {
            navigator.clipboard.writeText(window.fcmToken);
            alert('Token copied to clipboard!');
        };

        window.copySql = function() {
            const sql = document.getElementById('sql-query').textContent;
            navigator.clipboard.writeText(sql);
            alert('SQL query copied to clipboard!');
        };

        window.saveToCurrentUser = async function() {
            try {
                const response = await fetch('/api/user/update-fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        fcm_token: window.fcmToken
                    })
                });
                
                if (response.ok) {
                    alert('✅ Token saved successfully!');
                } else {
                    alert('❌ Failed to save token. Please copy and update manually.');
                }
            } catch (error) {
                alert('❌ Error: ' + error.message);
            }
        };
    </script>
</body>
</html>
