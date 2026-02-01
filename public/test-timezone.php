<?php
/**
 * TIMEZONE TESTER
 * Akses file ini di browser: http://localhost:8000/test-timezone.php
 * Untuk cek apakah timezone server sudah benar
 */

echo "<h1>🕐 Timezone Test - Sistem Absensi</h1>";
echo "<hr>";

// 1. Server PHP Timezone
echo "<h2>1. PHP Timezone Setting</h2>";
echo "<strong>Current Timezone:</strong> " . date_default_timezone_get() . "<br>";
echo "<strong>Expected:</strong> Asia/Makassar<br>";

if (date_default_timezone_get() === 'Asia/Makassar') {
    echo "<span style='color: green; font-weight: bold;'>✅ CORRECT!</span><br>";
} else {
    echo "<span style='color: red; font-weight: bold;'>❌ WRONG! Please restart server!</span><br>";
}

echo "<hr>";

// 2. Current Time
echo "<h2>2. Current Server Time</h2>";
echo "<strong>Date:</strong> " . date('Y-m-d') . "<br>";
echo "<strong>Time:</strong> " . date('H:i:s') . "<br>";
echo "<strong>Full DateTime:</strong> " . date('Y-m-d H:i:s') . "<br>";
echo "<strong>Day:</strong> " . date('l, d F Y') . "<br>";

echo "<hr>";

// 3. QR Code Format Test
echo "<h2>3. QR Code Format</h2>";
$qrCode = 'KASAU-ABSENSI-' . date('Y-m-d');
echo "<strong>Generated QR Code:</strong> <code style='background: #f0f0f0; padding: 5px;'>" . $qrCode . "</code><br>";
echo "<strong>Should be:</strong> KASAU-ABSENSI-[TODAY'S DATE IN WITA]<br>";

echo "<hr>";

// 4. Comparison with UTC
echo "<h2>4. Timezone Comparison</h2>";
$utcTime = gmdate('Y-m-d H:i:s');
$localTime = date('Y-m-d H:i:s');
echo "<strong>UTC Time:</strong> " . $utcTime . "<br>";
echo "<strong>Local Time (Asia/Makassar):</strong> " . $localTime . "<br>";
echo "<strong>Difference:</strong> Should be +8 hours from UTC<br>";

// Calculate difference
$utcTimestamp = strtotime($utcTime);
$localTimestamp = strtotime($localTime);
$diffHours = ($localTimestamp - $utcTimestamp) / 3600;
echo "<strong>Actual Difference:</strong> " . $diffHours . " hours<br>";

if ($diffHours == 8) {
    echo "<span style='color: green; font-weight: bold;'>✅ CORRECT! (+8 hours from UTC)</span><br>";
} else {
    echo "<span style='color: red; font-weight: bold;'>❌ WRONG! Should be +8 hours</span><br>";
}

echo "<hr>";

// 5. Laravel Carbon Test (if available)
echo "<h2>5. Laravel Carbon Test</h2>";
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    
    $carbon = \Carbon\Carbon::now('Asia/Makassar');
    echo "<strong>Carbon::now('Asia/Makassar'):</strong> " . $carbon->toDateTimeString() . "<br>";
    echo "<strong>Carbon Timezone:</strong> " . $carbon->timezone->getName() . "<br>";
    
    if ($carbon->timezone->getName() === 'Asia/Makassar') {
        echo "<span style='color: green; font-weight: bold;'>✅ CARBON OK!</span><br>";
    } else {
        echo "<span style='color: red; font-weight: bold;'>❌ CARBON TIMEZONE WRONG!</span><br>";
    }
} else {
    echo "<em>Laravel not loaded, skipping Carbon test</em><br>";
}

echo "<hr>";

// 6. Final Verdict
echo "<h2>📊 Final Verdict</h2>";
$isCorrect = (date_default_timezone_get() === 'Asia/Makassar' && $diffHours == 8);

if ($isCorrect) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #155724; margin: 0;'>✅ TIMEZONE CONFIGURED CORRECTLY!</h3>";
    echo "<p style='margin: 10px 0 0 0;'>Server is using Asia/Makassar (WITA) timezone. QR Code generation and attendance system will work correctly.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24; margin: 0;'>❌ TIMEZONE NOT CONFIGURED!</h3>";
    echo "<p style='margin: 10px 0 0 0;'><strong>Action Required:</strong></p>";
    echo "<ol style='margin: 5px 0 0 20px;'>";
    echo "<li>Stop server: Ctrl+C</li>";
    echo "<li>Run: <code>php artisan config:clear</code></li>";
    echo "<li>Restart server: <code>php artisan serve --host=0.0.0.0 --port=8000</code></li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>Test page created: " . date('Y-m-d H:i:s') . "</em></p>";
echo "<p><a href='/'>← Back to Dashboard</a></p>";
?>
