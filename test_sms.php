<?php
/**
 * SMS Gateway Test Script
 * 
 * Use this to test connectivity to your Simple SMS Gateway.
 * Run from browser: http://localhost/e-bhm_connect/test_sms.php
 */

header('Content-Type: text/html; charset=utf-8');

// Gateway configuration - UPDATE THIS TO MATCH YOUR PHONE'S IP
$GATEWAY_URL = 'http://192.168.68.200:8080/send-sms';

// Test phone number - UPDATE THIS TO YOUR TEST NUMBER
$TEST_PHONE = '09123456789';
$TEST_MESSAGE = 'Test message from E-BHM Connect at ' . date('Y-m-d H:i:s');

?>
<!DOCTYPE html>
<html>
<head>
    <title>SMS Gateway Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #1e293b; color: #fff; }
        .card { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .success { background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; }
        .error { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; }
        .warning { background: rgba(245, 158, 11, 0.2); border: 1px solid #f59e0b; }
        pre { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 5px; overflow-x: auto; }
        button { background: #10b981; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #059669; }
        input { padding: 10px; border-radius: 5px; border: 1px solid #444; background: #2d3748; color: #fff; width: 100%; margin: 5px 0 15px 0; }
        label { display: block; margin-top: 10px; }
        h1 { color: #10b981; }
        h2 { color: #64748b; border-bottom: 1px solid #374151; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>📱 SMS Gateway Test</h1>
    
    <div class="card">
        <h2>Step 1: Check Gateway Connectivity</h2>
        <p><strong>Gateway URL:</strong> <code><?php echo $GATEWAY_URL; ?></code></p>
        
        <?php
        // Test if we can reach the gateway
        $ch = curl_init($GATEWAY_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrNo = curl_errno($ch);
        curl_close($ch);
        
        if ($curlErrNo) {
            echo '<div class="card error">';
            echo '<strong>❌ Connection Failed!</strong><br>';
            echo 'Error: ' . htmlspecialchars($curlError);
            echo '<br><br><strong>Troubleshooting:</strong><ul>';
            echo '<li>Make sure Simple SMS Gateway app is running on your phone</li>';
            echo '<li>Check that your phone and laptop are on the same WiFi network</li>';
            echo '<li>Verify the IP address in the app matches: 192.168.68.200</li>';
            echo '<li>Verify the port in the app matches: 8080</li>';
            echo '<li>Try pinging 192.168.68.200 from your laptop</li>';
            echo '</ul></div>';
        } else {
            echo '<div class="card success">';
            echo '<strong>✅ Gateway is reachable!</strong>';
            echo '<br>HTTP Status: ' . $httpCode;
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="card">
        <h2>Step 2: Send Test SMS</h2>
        <form method="POST">
            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? $TEST_PHONE); ?>" placeholder="09123456789">
            
            <label>Message:</label>
            <input type="text" name="message" value="<?php echo htmlspecialchars($_POST['message'] ?? $TEST_MESSAGE); ?>" placeholder="Test message">
            
            <button type="submit" name="send_test">Send Test SMS</button>
        </form>
        
        <?php
        if (isset($_POST['send_test'])) {
            $phone = trim($_POST['phone'] ?? '');
            $message = trim($_POST['message'] ?? '');
            
            if (empty($phone) || empty($message)) {
                echo '<div class="card error"><strong>❌ Phone and message are required!</strong></div>';
            } else {
                // Send the test SMS
                $payload = json_encode(['phone' => $phone, 'message' => $message]);
                
                echo '<div class="card warning">';
                echo '<strong>📤 Sending SMS...</strong><br>';
                echo '<strong>Payload:</strong><pre>' . htmlspecialchars($payload) . '</pre>';
                
                $ch = curl_init($GATEWAY_URL);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);
                
                echo '</div>';
                
                if ($curlError) {
                    echo '<div class="card error">';
                    echo '<strong>❌ cURL Error:</strong> ' . htmlspecialchars($curlError);
                    echo '</div>';
                } elseif ($httpCode >= 200 && $httpCode < 300) {
                    echo '<div class="card success">';
                    echo '<strong>✅ SMS Sent Successfully!</strong>';
                    echo '<br>HTTP Status: ' . $httpCode;
                    echo '<br><strong>Response:</strong><pre>' . htmlspecialchars($response ?: '(empty)') . '</pre>';
                    echo '</div>';
                } else {
                    echo '<div class="card error">';
                    echo '<strong>❌ SMS Failed!</strong>';
                    echo '<br>HTTP Status: ' . $httpCode;
                    echo '<br><strong>Response:</strong><pre>' . htmlspecialchars($response ?: '(empty)') . '</pre>';
                    echo '</div>';
                }
            }
        }
        ?>
    </div>
    
    <div class="card">
        <h2>Configuration Info</h2>
        <p><strong>Your PHP Server:</strong> <?php echo $_SERVER['SERVER_ADDR'] ?? 'localhost'; ?></p>
        <p><strong>Simple SMS Gateway should be at:</strong> http://192.168.68.200:8080/send-sms</p>
        <p><strong>Expected payload format:</strong></p>
        <pre>{"phone": "09123456789", "message": "Hello World"}</pre>
        
        <h3>Update Gateway IP</h3>
        <p>If the gateway IP has changed, update these files:</p>
        <ul>
            <li><code>process_queue.php</code> (line 19)</li>
            <li><code>actions/sms_actions.php</code> (line 15)</li>
            <li><code>test_sms.php</code> (line 11)</li>
        </ul>
    </div>
</body>
</html>
