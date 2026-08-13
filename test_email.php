<?php
// Patch: prevent api.php router from running by stubbing $action before include
// and stub respond_json if not yet defined
if (!function_exists('respond_json')) {
    function respond_json($data, $ok = true, $error = null) { /* CLI no-op */ }
}

define('LRS_NO_ROUTER', true);
require_once 'db.php';
require_once 'api.php';

global $pdo;

// Get SMTP settings
$stmt = $pdo->query("SELECT key, value FROM settings WHERE key IN ('smtp_host','smtp_port','smtp_secure','smtp_user','smtp_pass','sender_name','sender_email','app_url','email_enabled','org_name')");
$settings = [];
while ($r = $stmt->fetch()) { $settings[$r['key']] = $r['value']; }

echo "=== SMTP Settings ===\n";
echo "Email Enabled : " . ($settings['email_enabled'] ?? 'yes') . "\n";
echo "SMTP Host     : " . ($settings['smtp_host'] ?? '(ยังไม่ได้ตั้งค่า)') . "\n";
echo "SMTP Port     : " . ($settings['smtp_port'] ?? '587') . "\n";
echo "SMTP Secure   : " . ($settings['smtp_secure'] ?? 'tls') . "\n";
echo "SMTP User     : " . ($settings['smtp_user'] ?? '(ว่าง)') . "\n";
echo "Sender Name   : " . ($settings['sender_name'] ?? '(ว่าง)') . "\n";
echo "Sender Email  : " . ($settings['sender_email'] ?? '(ว่าง)') . "\n";
echo "App URL       : " . ($settings['app_url'] ?? '(ว่าง)') . "\n\n";

// List all users with emails
$users = $pdo->query("SELECT username, full_name, role, email FROM users WHERE email != '' AND is_active = 'yes' ORDER BY role, full_name")->fetchAll();
echo "=== ผู้ใช้ที่มีอีเมล (" . count($users) . " คน) ===\n";
foreach ($users as $u) {
    printf("  %-12s | %-10s | %s\n", $u['username'], $u['role'], $u['email']);
}
echo "\n";

if (empty($settings['smtp_host']) || empty($settings['smtp_user'])) {
    echo "❌ ยังไม่ได้ตั้งค่า SMTP — กรุณาไปตั้งค่าที่หน้า Settings ของระบบ\n";
    exit(1);
}

if (($settings['email_enabled'] ?? 'yes') !== 'yes') {
    echo "⚠️  Email ถูกปิดอยู่ในการตั้งค่าระบบ (email_enabled != yes)\n";
    exit(1);
}

echo "=== ส่งอีเมลทดสอบพร้อม Auto-Login Link ===\n";
$passed = 0; $failed = 0;
foreach ($users as $u) {
    $fullUser = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $fullUser->execute([$u['username']]);
    $fullRow = $fullUser->fetch();

    $alToken = lrs_user_autologin_token($fullRow);
    $appUrl = rtrim($settings['app_url'] ?? 'http://127.0.0.1:8000', '/');
    $loginLink = $appUrl . '/?autologin=' . urlencode($alToken) . '#/dashboard';

    $subject = "🔔 ทดสอบระบบอีเมล LRS — " . $u['full_name'];
    $html = '<p>เรียน <strong>' . htmlspecialchars($u['full_name']) . '</strong>,</p>'
        . '<p>นี่คืออีเมลทดสอบจากระบบเอกสารใบลาราชการ (LRS) เพื่อยืนยันว่าระบบส่งอีเมลแจ้งเตือนทำงานได้ถูกต้อง</p>'
        . '<table style="border-collapse:collapse;width:100%;margin:16px 0;font-size:14px">'
        . '<tr><td style="padding:8px 12px;background:#f5f5f7;font-weight:bold;width:140px;border-radius:6px 0 0 0">รหัสผู้ใช้</td><td style="padding:8px 12px;border-bottom:1px solid #eee">' . htmlspecialchars($u['username']) . '</td></tr>'
        . '<tr><td style="padding:8px 12px;background:#f5f5f7;font-weight:bold">บทบาท</td><td style="padding:8px 12px;border-bottom:1px solid #eee">' . htmlspecialchars($u['role']) . '</td></tr>'
        . '<tr><td style="padding:8px 12px;background:#f5f5f7;font-weight:bold;border-radius:0 0 0 6px">อีเมล</td><td style="padding:8px 12px">' . htmlspecialchars($u['email']) . '</td></tr>'
        . '</table>'
        . '<p style="padding:12px;background:#f0fff4;border-left:4px solid #34c759;border-radius:4px;color:#1a5c30">'
        . '✅ ระบบอีเมลทำงานปกติ — กดปุ่มด้านล่างเพื่อเข้าสู่ระบบโดยอัตโนมัติ ไม่ต้องกรอกรหัสผ่าน</p>';

    $result = lrs_send_email($u['email'], $subject, $html);
    if ($result) { $passed++; $icon = '✅'; } else { $failed++; $icon = '❌'; }
    printf("  $icon %-28s → %s\n", $u['email'], $result ? 'สำเร็จ' : 'ล้มเหลว');
}

echo "\n=== สรุป ===\n";
echo "  ✅ สำเร็จ : $passed อีเมล\n";
echo "  ❌ ล้มเหลว: $failed อีเมล\n";
echo "\nเสร็จสิ้น\n";
