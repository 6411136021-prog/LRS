<?php
// c:\xampp\htdocs\LRS\api.php
// Enterprise Security & Robust REST/RPC API Router for LRS System

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
}

if (!ob_get_level() && !headers_sent()) {
    if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
        @ob_start('ob_gzhandler');
    } else {
        @ob_start();
    }
}

require_once __DIR__ . '/db.php';

// Automatic Session Garbage Collection (Clear expired sessions periodically)
if (mt_rand(1, 20) === 1) {
    try {
        $pdo->exec("DELETE FROM sessions WHERE datetime(expires_at) < datetime('now')");
    } catch (Exception $e) {}
}

// Read raw JSON Payload or POST
$rawInput = file_get_contents('php://input');
$req = json_decode($rawInput, true) ?? $_POST;

$action = trim((string)($req['action'] ?? ''));
$token = trim((string)($req['token'] ?? ''));
$payload = is_array($req['payload'] ?? null) ? $req['payload'] : [];

if (!function_exists('respond_json')) {
    function respond_json($data, $ok = true, $error = null) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
        }
        $res = $ok ? ['ok' => true, 'data' => $data] : ['ok' => false, 'error' => $error];
        $json = json_encode($res, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json === false) {
            $json = json_encode(['ok' => false, 'error' => 'JSON Encoding Error'], JSON_UNESCAPED_UNICODE);
        }
        echo $json;
        exit;
    }
}

// -------------------------------------------------------------
// Auth & RBAC Checks
// -------------------------------------------------------------

$CAPS = [
    'teacher' => ['request.create','request.view_own','request.edit_own','request.submit','request.cancel_own','profile.edit','file.upload'],
    'clerk' => ['request.view_all','request.review','user.view_all','report.view_all','setting.read','profile.edit','file.upload'],
    'director' => ['request.view_all','request.approve','report.view_all','profile.edit'],
    'admin' => ['request.manage','user.manage','setting.manage','report.manage','audit.view_all','notify.manage','profile.edit','file.upload']
];

function lrs_has_cap($role, $cap) {
    global $CAPS;
    if (!$cap || $cap === '*') return true;
    $userCaps = $CAPS[$role] ?? [];
    $requestedCaps = explode('|', $cap);
    foreach ($requestedCaps as $c) {
        $c = trim($c);
        if (!$c) continue;
        if ($c === '*') return true;
        if (in_array($c, $userCaps)) return true;
        if (preg_match('/\.(view_own|edit_own|cancel_own|view_self)$/', $c)) return false;
        $dotPos = strpos($c, '.');
        if ($dotPos !== false && in_array(substr($c, 0, $dotPos) . '.manage', $userCaps)) return true;
    }
    return false;
}

function lrs_verify_token($token) {
    global $pdo;
    if (!$token) throw new Exception('ต้องเข้าสู่ระบบก่อน');
    
    $stmt = $pdo->prepare("SELECT s.*, u.* FROM sessions s JOIN users u ON s.user_id = u.id WHERE s.token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) throw new Exception('เซสชันหมดอายุ — กรุณาเข้าสู่ระบบใหม่');
    
    $expTime = strtotime($user['expires_at']);
    if ($expTime && $expTime < time()) {
        $pdo->prepare("DELETE FROM sessions WHERE token = ?")->execute([$token]);
        throw new Exception('เซสชันหมดอายุ — กรุณาเข้าสู่ระบบใหม่');
    }
    if ($user['is_active'] !== 'yes') {
        throw new Exception('บัญชีถูกระงับ — ติดต่อผู้ดูแลระบบ');
    }
    return $user;
}

function lrs_public_user($u) {
    return [
        'id' => $u['id'] ?? '',
        'username' => $u['username'] ?? '',
        'prefix' => $u['prefix'] ?? '',
        'full_name' => $u['full_name'] ?? '',
        'position' => $u['position'] ?? '',
        'academic_rank' => $u['academic_rank'] ?? '',
        'department' => $u['department'] ?? '',
        'email' => $u['email'] ?? '',
        'phone' => $u['phone'] ?? '',
        'role' => $u['role'] ?? 'teacher',
        'avatar_url' => $u['avatar_url'] ?? '',
        'signature_url' => $u['signature_url'] ?? '',
        'line_user_id' => $u['line_user_id'] ?? ''
    ];
}

function lrs_public_bundle() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT key, value FROM settings");
    $stmt->execute();
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }

    $enableDemo = ($settings['enable_demo_login'] ?? 'yes') === 'yes';
    $demoUsers = [];
    if ($enableDemo) {
        $stmtDemo = $pdo->prepare("SELECT username, full_name, role FROM users WHERE is_active = 'yes' ORDER BY username ASC");
        $stmtDemo->execute();
        $demoUsers = $stmtDemo->fetchAll();
    }

    $stmtUserCount = $pdo->query("SELECT COUNT(*) FROM users");
    $hasUsers = intval($stmtUserCount->fetchColumn()) > 0;

    return [
        'app' => [
            'name' => 'ระบบเอกสารใบลาราชการ',
            'short' => 'LRS',
            'title' => 'ระบบเอกสารใบลาราชการ · สพฐ.',
            'version' => '1.0.0',
            'logo_icon' => 'file-earmark-text-fill',
            'description' => 'ระบบยื่น–อนุมัติ–พิมพ์เอกสารใบลาสำหรับข้าราชการครูและบุคลากรทางการศึกษา'
        ],
        'dev' => [
            'NAME' => 'ครูวิรัตน์ หาดคำ',
            'URL' => 'https://www.kruwirat.com',
            'LOGO' => 'https://mts-ssk3.com/uploads/team/team_1771053860_6990232440dc8.png'
        ],
        'settings' => $settings,
        'roles' => [
            'teacher' => 'ครู/บุคลากร',
            'clerk' => 'เจ้าหน้าที่ธุรการ',
            'director' => 'ผู้อำนวยการ',
            'admin' => 'ผู้ดูแลระบบ'
        ],
        'statuses' => [
            'draft' => 'ฉบับร่าง',
            'submitted' => 'รอตรวจสอบ',
            'reviewed' => 'รอผู้อนุมัติ',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ไม่อนุมัติ',
            'cancelled' => 'ยกเลิก'
        ],
        'req_types' => [
            'leave' => 'ใบลาป่วย ลาคลอดบุตร ลากิจส่วนตัว',
            'vacation' => 'ใบลาพักผ่อน',
            'cancel' => 'ใบขอยกเลิกวันลา',
            'resign' => 'หนังสือขอลาออกจากราชการ',
            'idcard' => 'คำขอมีบัตรประจำตัว'
        ],
        'has_users' => $hasUsers,
        'demo' => $demoUsers
    ];
}

function lrs_audit_log($user, $action, $targetType = '', $targetId = '', $meta = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO audit (id, ts, user_id, username, action, target_type, target_id, meta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            lrs_uuid(),
            lrs_now(),
            $user['id'] ?? '',
            $user['username'] ?? '',
            $action,
            $targetType,
            $targetId,
            json_encode($meta, JSON_UNESCAPED_UNICODE)
        ]);
    } catch (Exception $e) {}
}

function lrs_notify_push_user($userId, $ntype, $title, $message, $link = '') {
    global $pdo;
    if (!$userId) return;
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (id, target, ntype, title, message, link, is_read, created_at) VALUES (?, ?, ?, ?, ?, ?, 'no', ?)");
        $stmt->execute([lrs_uuid(), (string)$userId, $ntype ?: 'info', mb_substr($title, 0, 120), mb_substr($message, 0, 300), mb_substr($link, 0, 60), lrs_now()]);
    } catch (Exception $e) {}
}

function lrs_notify_push_roles($roles, $ntype, $title, $message, $link = '') {
    global $pdo;
    if (empty($roles)) return;
    $placeholders = implode(',', array_fill(0, count($roles), '?'));
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE is_active = 'yes' AND role IN ($placeholders)");
        $stmt->execute($roles);
        $users = $stmt->fetchAll();
        foreach ($users as $u) {
            lrs_notify_push_user($u['id'], $ntype, $title, $message, $link);
        }
    } catch (Exception $e) {}
}

function lrs_user_autologin_token($user) {
    if (!$user || empty($user['id'])) return '';
    $secret = "LRS_SECURE_AUTOLOGIN_KEY_2026";
    $sig = hash_hmac('sha256', $user['id'] . $user['password_hash'] . ($user['email'] ?? ''), $secret);
    return base64_encode($user['id'] . ':' . $sig);
}

function lrs_send_email($toEmail, $subject, $contentHtml, $actionDocId = '') {
    global $pdo;
    if (!$toEmail) return false;
    try {
        $stmt = $pdo->query("SELECT key, value FROM settings WHERE key IN ('email_enabled', 'email_mode', 'smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'smtp_pass', 'sender_name', 'sender_email', 'org_name', 'app_url')");
        $s = [];
        while ($r = $stmt->fetch()) { $s[$r['key']] = $r['value']; }

        if (($s['email_enabled'] ?? 'yes') !== 'yes') return false;

        $senderName = $s['sender_name'] ?? 'ระบบใบลาราชการ ' . ($s['org_name'] ?? 'โรงเรียนปากพูน');
        $senderEmail = $s['sender_email'] ?? 'noreply@pakpoon.ac.th';
        $appUrl = rtrim($s['app_url'] ?? (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : 'http://localhost/LRS'), '/');

        // Auto-login Link Generation
        $autologinToken = '';
        try {
            $stmtUser = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?) AND is_active = 'yes' LIMIT 1");
            $stmtUser->execute([$toEmail]);
            $targetUser = $stmtUser->fetch();
            if ($targetUser) {
                $autologinToken = lrs_user_autologin_token($targetUser);
            }
        } catch (Exception $e) {}

        $buttonUrl = $appUrl;
        if ($autologinToken) {
            $hashRoute = $actionDocId ? '#/my' : '#/dashboard';
            $buttonUrl = $appUrl . '/?autologin=' . urlencode($autologinToken) . $hashRoute;
        }

        // Header theme color based on status/subject
        $headerBg = '#0a84ff';
        if (mb_strpos($subject, 'อนุมัติ') !== false && mb_strpos($subject, 'ไม่') === false) $headerBg = '#34c759';
        else if (mb_strpos($subject, 'ไม่อนุมัติ') !== false || mb_strpos($subject, 'ยกเลิก') !== false) $headerBg = '#ff3b30';
        else if (mb_strpos($subject, 'ตรวจสอบ') !== false) $headerBg = '#af52de';

        $fullHtml = '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background-color:#f5f5f7;font-family:Sarabun,Kanit,sans-serif;color:#1d1d1f;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f5f5f7;padding:30px 10px;">
          <tr>
            <td align="center">
              <table width="100%" max-width="600" border="0" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.08);">
                <tr>
                  <td style="background:' . $headerBg . ';padding:24px 28px;color:#ffffff;">
                    <div style="font-size:12px;opacity:0.9;font-weight:bold;letter-spacing:0.5px;">' . htmlspecialchars($s['org_name'] ?? 'โรงเรียนปากพูน') . ' · สพฐ.</div>
                    <div style="font-size:20px;font-weight:bold;margin-top:6px;">' . htmlspecialchars($subject) . '</div>
                  </td>
                </tr>
                <tr>
                  <td style="padding:28px;">
                    ' . $contentHtml . '
                    <div style="margin-top:28px;padding-top:20px;border-top:1px solid #ececf1;text-align:center;">
                      <a href="' . htmlspecialchars($buttonUrl) . '" style="display:inline-block;background:' . $headerBg . ';color:#ffffff;text-decoration:none;padding:12px 24px;border-radius:10px;font-weight:bold;font-size:14px;">เปิดเข้าระบบอัตโนมัติ (Direct Auto-Login)</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td style="background:#fafafd;padding:16px 28px;text-align:center;font-size:12px;color:#86868b;border-top:1px solid #ececf1;">
                    ข้อความอัตโนมัติจากระบบเอกสารใบลาราชการ (LRS) · สำนักงานคณะกรรมการการศึกษาขั้นพื้นฐาน
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </body>
        </html>';

        $mode = $s['email_mode'] ?? 'smtp';
        $smtpHost = trim($s['smtp_host'] ?? '');
        $smtpPort = intval($s['smtp_port'] ?? 587);
        $smtpUser = trim($s['smtp_user'] ?? '');
        $smtpPass = trim($s['smtp_pass'] ?? '');
        $smtpSecure = strtolower(trim($s['smtp_secure'] ?? 'tls'));

        // 1. Direct SMTP Socket Transmission
        if ($mode === 'smtp' && $smtpHost && $smtpUser && $smtpPass) {
            $prefix = ($smtpSecure === 'ssl') ? 'ssl://' : '';
            $socketHost = $prefix . $smtpHost;
            $fp = @fsockopen($socketHost, $smtpPort, $errno, $errstr, 8);
            if ($fp) {
                stream_set_timeout($fp, 8);
                fgets($fp, 512);

                fputs($fp, "EHLO " . gethostname() . "\r\n");
                while ($line = fgets($fp, 512)) { if (substr($line, 3, 1) === ' ') break; }

                if ($smtpSecure === 'tls') {
                    fputs($fp, "STARTTLS\r\n");
                    $res = fgets($fp, 512);
                    if (strpos($res, '220') === 0) {
                        stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                        fputs($fp, "EHLO " . gethostname() . "\r\n");
                        while ($line = fgets($fp, 512)) { if (substr($line, 3, 1) === ' ') break; }
                    }
                }

                fputs($fp, "AUTH LOGIN\r\n");
                fgets($fp, 512);
                fputs($fp, base64_encode($smtpUser) . "\r\n");
                fgets($fp, 512);
                fputs($fp, base64_encode($smtpPass) . "\r\n");
                $authRes = fgets($fp, 512);

                if (strpos($authRes, '235') === 0) {
                    fputs($fp, "MAIL FROM: <$senderEmail>\r\n");
                    fgets($fp, 512);
                    fputs($fp, "RCPT TO: <$toEmail>\r\n");
                    fgets($fp, 512);
                    fputs($fp, "DATA\r\n");
                    fgets($fp, 512);

                    $headers  = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: =?UTF-8?B?" . base64_encode($senderName) . "?= <$senderEmail>\r\n";
                    $headers .= "To: <$toEmail>\r\n";
                    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";

                    fputs($fp, $headers . "\r\n" . $fullHtml . "\r\n.\r\n");
                    fgets($fp, 512);
                    fputs($fp, "QUIT\r\n");
                    fclose($fp);
                    return true;
                }
                fclose($fp);
            }
        }

        // 2. Native PHP mail() Fallback
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: =?UTF-8?B?" . base64_encode($senderName) . "?= <$senderEmail>\r\n";
        @mail($toEmail, "=?UTF-8?B?" . base64_encode($subject) . "?=", $fullHtml, $headers);
        return true;

    } catch (Exception $e) {
        return false;
    }
}

function lrs_email_notify_user($userId, $subject, $contentHtml) {
    global $pdo;
    if (!$userId) return;
    try {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ? AND is_active = 'yes' AND email != ''");
        $stmt->execute([$userId]);
        $email = $stmt->fetchColumn();
        if ($email) {
            lrs_send_email($email, $subject, $contentHtml);
        }
    } catch (Exception $e) {}
}

function lrs_email_notify_roles($roles, $subject, $contentHtml) {
    global $pdo;
    if (empty($roles)) return;
    $placeholders = implode(',', array_fill(0, count($roles), '?'));
    try {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE is_active = 'yes' AND role IN ($placeholders) AND email != ''");
        $stmt->execute($roles);
        $users = $stmt->fetchAll();
        foreach ($users as $u) {
            if (!empty($u['email'])) {
                lrs_send_email($u['email'], $subject, $contentHtml);
            }
        }
    } catch (Exception $e) {}
}

// -------------------------------------------------------------
// Fiscal Year & Leave Quota Calculator
// -------------------------------------------------------------

function lrs_fy_of($dateStr) {
    $s = lrs_date_only($dateStr);
    if (!preg_match('/^\d{4}-\d{2}/', $s)) return 0;
    $y = intval(substr($s, 0, 4));
    $m = intval(substr($s, 5, 2));
    $be = $y + 543;
    return $m >= 10 ? $be + 1 : $be;
}

function lrs_cur_fy() {
    return lrs_fy_of(date('Y-m-d'));
}

function lrs_leave_quotas() {
    global $pdo;
    $stmt = $pdo->query("SELECT key, value FROM settings");
    $s = [];
    while ($row = $stmt->fetch()) {
        $s[$row['key']] = $row['value'];
    }
    return [
        'sick' => floatval($s['quota_sick'] ?? 60),
        'personal' => floatval($s['quota_personal'] ?? 45),
        'vacation' => floatval($s['quota_vacation'] ?? 10),
        'maternity' => floatval($s['quota_maternity'] ?? 90),
        '_warn' => floatval($s['quota_warn_pct'] ?? 80)
    ];
}

function lrs_leave_user_summary($userId, $fy = null) {
    global $pdo;
    $fy = $fy ?: lrs_cur_fy();
    $acc = [
        'sick' => ['count' => 0, 'days' => 0.0],
        'personal' => ['count' => 0, 'days' => 0.0],
        'vacation' => ['count' => 0, 'days' => 0.0],
        'maternity' => ['count' => 0, 'days' => 0.0]
    ];

    $stmt = $pdo->prepare("SELECT * FROM requests WHERE user_id = ? AND status = 'approved'");
    $stmt->execute([(string)$userId]);
    $rows = $stmt->fetchAll();

    foreach ($rows as $r) {
        $key = null;
        if ($r['req_type'] === 'leave') $key = $r['leave_kind'];
        else if ($r['req_type'] === 'vacation') $key = 'vacation';

        if (!$key || !isset($acc[$key])) continue;
        $reqFy = lrs_fy_of($r['start_date'] ?: $r['doc_date']);
        if ($reqFy !== $fy) continue;

        $acc[$key]['count']++;
        $acc[$key]['days'] += floatval($r['days']);
    }
    return $acc;
}

function lrs_leave_status($userId, $fy = null) {
    $fy = $fy ?: lrs_cur_fy();
    $quotas = lrs_leave_quotas();
    $summary = lrs_leave_user_summary($userId, $fy);

    $kinds = [
        ['key' => 'sick', 'label' => 'ลาป่วย', 'icon' => 'thermometer-half'],
        ['key' => 'personal', 'label' => 'ลากิจส่วนตัว', 'icon' => 'person'],
        ['key' => 'vacation', 'label' => 'ลาพักผ่อน', 'icon' => 'sun'],
        ['key' => 'maternity', 'label' => 'ลาคลอดบุตร', 'icon' => 'heart-pulse']
    ];

    $result = [];
    foreach ($kinds as $k) {
        $used = $summary[$k['key']] ?? ['count' => 0, 'days' => 0.0];
        $quota = $quotas[$k['key']] ?? 0;
        $pct = $quota > 0 ? round(($used['days'] / $quota) * 100) : 0;
        $result[] = [
            'key' => $k['key'],
            'label' => $k['label'],
            'icon' => $k['icon'],
            'count' => $used['count'],
            'days' => $used['days'],
            'quota' => $quota,
            'remaining' => max(0, $quota - $used['days']),
            'pct' => $pct,
            'near' => ($quota > 0 && $pct >= $quotas['_warn'] && $pct < 100),
            'over' => ($quota > 0 && $used['days'] > $quota)
        ];
    }
    return $result;
}

function lrs_next_doc_no() {
    global $pdo;
    $be = intval(date('Y')) + 543;
    $prefix = "ล.";
    
    $stmt = $pdo->prepare("SELECT doc_no FROM requests WHERE doc_no LIKE ?");
    $stmt->execute(["$prefix%/$be"]);
    $rows = $stmt->fetchAll();

    $max = 0;
    foreach ($rows as $r) {
        if (preg_match('/^ล\.(\d+)\/(\d+)$/', $r['doc_no'], $m)) {
            if (intval($m[2]) === $be) {
                $max = max($max, intval($m[1]));
            }
        }
    }
    $next = $max + 1;
    return "{$prefix}{$next}/{$be}";
}

// -------------------------------------------------------------
// Router Actions
// -------------------------------------------------------------

if (!defined('LRS_NO_ROUTER') && $action !== '') {
try {
    switch ($action) {
        case 'app.bootstrap':
            $user = null;
            $caps = [];
            if ($token) {
                try {
                    $u = lrs_verify_token($token);
                    $user = lrs_public_user($u);
                    $caps = $CAPS[$u['role']] ?? [];
                } catch (Exception $e) {}
            }
            $bundle = lrs_public_bundle();
            $bundle['me'] = $user;
            $bundle['caps'] = $caps;
            respond_json($bundle);
            break;

        case 'auth.login':
            $username = trim(strtolower((string)($payload['username'] ?? '')));
            $password = (string)($payload['password'] ?? '');
            if (!$username || !$password) throw new Exception('กรุณากรอกชื่อผู้ใช้และรหัสผ่าน');

            // Support login by username or email
            if (strpos($username, '@') !== false) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = ?");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(username) = ?");
            }
            $stmt->execute([$username]);
            $u = $stmt->fetch();

            if (!$u || $u['is_active'] !== 'yes' || !lrs_sec_verify($password, $u['salt'], $u['password_hash'])) {
                lrs_audit_log(['id' => '', 'username' => $username], 'auth.fail', 'user', $u['id'] ?? '', []);
                throw new Exception('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
            }

            $newToken = lrs_uuid() . '-' . lrs_uuid();
            $expiresAt = date('c', time() + (86400 * 365)); // 1 Year extended session
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // Clean old session and insert new
            $pdo->prepare("DELETE FROM sessions WHERE user_id = ?")->execute([$u['id']]);

            $stmtSess = $pdo->prepare("INSERT INTO sessions (token, user_id, created_at, expires_at, user_agent) VALUES (?, ?, ?, ?, ?)");
            $stmtSess->execute([$newToken, $u['id'], lrs_now(), $expiresAt, substr($ua, 0, 200)]);

            lrs_audit_log($u, 'auth.login', 'user', $u['id'], []);

            respond_json([
                'token' => $newToken,
                'user' => lrs_public_user($u),
                'caps' => $CAPS[$u['role']] ?? []
            ]);
            break;

        case 'auth.autologin':
            $autoToken = trim((string)($payload['autologin'] ?? ''));
            if (!$autoToken) throw new Exception('ไม่พบรหัสความปลอดภัยเข้าสู่ระบบ');

            $autoToken = str_replace(' ', '+', $autoToken);
            $decoded = base64_decode($autoToken);
            $parts = explode(':', $decoded, 2);
            if (count($parts) !== 2) throw new Exception('รหัสเข้าสู่ระบบไม่ถูกต้อง');

            list($uid, $sig) = $parts;
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_active = 'yes'");
            $stmt->execute([$uid]);
            $u = $stmt->fetch();

            if (!$u) throw new Exception('ไม่พบข้อมูลบัญชีผู้ใช้');

            $secret = "LRS_SECURE_AUTOLOGIN_KEY_2026";
            $expectedSig = hash_hmac('sha256', $u['id'] . $u['password_hash'] . ($u['email'] ?? ''), $secret);

            if (!hash_equals($expectedSig, $sig)) {
                throw new Exception('รหัสเข้าสู่ระบบหมดอายุหรือไม่ถูกต้อง');
            }

            $newToken = lrs_uuid() . '-' . lrs_uuid();
            $expiresAt = date('c', time() + (86400 * 365));
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

            $pdo->prepare("DELETE FROM sessions WHERE user_id = ?")->execute([$u['id']]);

            $stmtSess = $pdo->prepare("INSERT INTO sessions (token, user_id, created_at, expires_at, user_agent) VALUES (?, ?, ?, ?, ?)");
            $stmtSess->execute([$newToken, $u['id'], lrs_now(), $expiresAt, substr($ua, 0, 200)]);

            lrs_audit_log($u, 'auth.autologin', 'user', $u['id'], []);

            respond_json([
                'token' => $newToken,
                'user' => lrs_public_user($u),
                'caps' => $CAPS[$u['role']] ?? []
            ]);
            break;

        case 'auth.logout':
            if ($token) {
                $pdo->prepare("DELETE FROM sessions WHERE token = ?")->execute([$token]);
            }
            respond_json(['ok' => true]);
            break;

        case 'auth.request_reset':
            $email = trim(strtolower((string)($payload['email'] ?? '')));
            if (!$email || strpos($email, '@') === false) throw new Exception('กรุณากรอกอีเมลให้ถูกต้อง');

            $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = ? AND is_active = 'yes'");
            $stmt->execute([$email]);
            $u = $stmt->fetch();

            if ($u) {
                // Clean old reset tokens
                $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?")->execute([$u['id']]);

                $rawToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $rawToken);
                $expiresAt = date('c', time() + 3600); // 1 hour

                $pdo->prepare("INSERT INTO password_resets (id, user_id, token_hash, expires_at, used, created_at) VALUES (?, ?, ?, ?, 'no', ?)")
                    ->execute([lrs_uuid(), $u['id'], $tokenHash, $expiresAt, lrs_now()]);

                $s = [];
                $stmt2 = $pdo->query("SELECT key, value FROM settings WHERE key IN ('app_url','org_name')");
                while ($r = $stmt2->fetch()) { $s[$r['key']] = $r['value']; }
                $appUrl = rtrim($s['app_url'] ?? 'http://localhost/LRS', '/');
                $resetLink = $appUrl . '/?reset_token=' . urlencode($rawToken) . '#/reset';

                $html = '<p>เรียน <strong>' . htmlspecialchars($u['full_name']) . '</strong>,</p>'
                    . '<p>มีคำขอรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณในระบบเอกสารใบลาราชการ (LRS)</p>'
                    . '<p>กดปุ่มด้านล่างเพื่อตั้งรหัสผ่านใหม่ ลิ้งก์นี้มีอายุ <strong>1 ชั่วโมง</strong></p>'
                    . '<div style="margin:24px 0;text-align:center"><a href="' . htmlspecialchars($resetLink) . '" style="display:inline-block;background:#ff9f0a;color:#fff;text-decoration:none;padding:14px 28px;border-radius:12px;font-weight:bold;font-size:15px">🔑 ตั้งรหัสผ่านใหม่</a></div>'
                    . '<p style="color:#86868b;font-size:12px">หากคุณไม่ได้ร้องขอ กรุณาเพิกเฉยต่ออีเมลนี้</p>';

                lrs_send_email($u['email'], '🔑 รีเซ็ตรหัสผ่าน — ระบบเอกสารใบลาราชการ LRS', $html);
            }
            // Always respond OK (security: don't reveal whether email exists)
            respond_json(['ok' => true, 'message' => 'หากอีเมลนี้มีในระบบ จะได้รับลิ้งก์รีเซ็ตรหัสผ่านภายในไม่กี่นาที']);
            break;

        case 'auth.do_reset':
            $rawToken = trim((string)($payload['token'] ?? ''));
            $newPw = (string)($payload['password'] ?? '');
            if (!$rawToken) throw new Exception('ไม่พบ token');
            if (strlen($newPw) < 6) throw new Exception('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');

            $tokenHash = hash('sha256', $rawToken);
            $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token_hash = ? AND used = 'no' AND datetime(expires_at) > datetime('now')");
            $stmt->execute([$tokenHash]);
            $reset = $stmt->fetch();

            if (!$reset) throw new Exception('ลิ้งก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุแล้ว');

            $salt = lrs_salt();
            $hash = lrs_sec_hash($newPw, $salt, 1200);

            $pdo->beginTransaction();
            try {
                $pdo->prepare("UPDATE users SET password_hash = ?, salt = ?, updated_at = ? WHERE id = ?")
                    ->execute([$hash, $salt, lrs_now(), $reset['user_id']]);
                $pdo->prepare("UPDATE password_resets SET used = 'yes' WHERE id = ?")
                    ->execute([$reset['id']]);
                // Invalidate all sessions
                $pdo->prepare("DELETE FROM sessions WHERE user_id = ?")->execute([$reset['user_id']]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            respond_json(['ok' => true, 'message' => 'เปลี่ยนรหัสผ่านสำเร็จ กรุณาเข้าสู่ระบบใหม่']);
            break;

        case 'stats.dashboard':
            $user = lrs_verify_token($token);
            $canViewAll = lrs_has_cap($user['role'], 'request.view_all');
            $fy = intval($payload['fy'] ?? lrs_cur_fy());
            $be = $fy;
            $ceStart = $be - 543 . '-10-01';
            $ceEnd = ($be - 543 + 1) . '-09-30';

            $uidFilter = $canViewAll ? '' : ' AND r.user_id = ' . $pdo->quote($user['id']);

            // Monthly breakdown (approved)
            $monthRows = $pdo->query("SELECT strftime('%m',r.start_date) as mon, SUM(r.days) as total_days, COUNT(*) as total_count
                FROM requests r WHERE r.status = 'approved' AND r.start_date >= '$ceStart' AND r.start_date <= '$ceEnd'$uidFilter
                GROUP BY mon ORDER BY mon")->fetchAll();
            $monthly = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthly[$m] = ['days' => 0, 'count' => 0];
            }
            foreach ($monthRows as $row) {
                $m = intval($row['mon']);
                $monthly[$m] = ['days' => round(floatval($row['total_days']), 1), 'count' => intval($row['total_count'])];
            }

            // By kind
            $kindRows = $pdo->query("SELECT r.leave_kind, SUM(r.days) as days, COUNT(*) as cnt
                FROM requests r WHERE r.status = 'approved' AND r.req_type = 'leave' AND r.start_date >= '$ceStart' AND r.start_date <= '$ceEnd'$uidFilter
                GROUP BY r.leave_kind")->fetchAll();

            // Status summary
            $statRows = $pdo->query("SELECT r.status, COUNT(*) as cnt
                FROM requests r WHERE r.created_at >= '$ceStart'$uidFilter
                GROUP BY r.status")->fetchAll();
            $byStatus = [];
            foreach ($statRows as $s) { $byStatus[$s['status']] = intval($s['cnt']); }

            // Pending count for current user's department
            $pendingCount = intval($pdo->query("SELECT COUNT(*) FROM requests WHERE status IN ('submitted','reviewed')")->fetchColumn());

            respond_json([
                'fy' => $fy,
                'monthly' => $monthly,
                'by_kind' => $kindRows,
                'by_status' => $byStatus,
                'pending' => $pendingCount,
            ]);
            break;

        case 'calendar.leaves':
            $user = lrs_verify_token($token);
            $year = intval($payload['year'] ?? date('Y'));
            $month = intval($payload['month'] ?? date('n'));
            $yStr = sprintf('%04d', $year);
            $mStr = sprintf('%02d', $month);
            $dateFrom = "$yStr-$mStr-01";
            $dateTo = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));

            $rows = $pdo->query("SELECT r.id, r.start_date, r.end_date, r.days, r.leave_kind, r.subject,
                    u.prefix, u.full_name, u.department
                FROM requests r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.status = 'approved'
                AND ((r.start_date <= '$dateTo' AND r.end_date >= '$dateFrom')
                     OR (r.start_date BETWEEN '$dateFrom' AND '$dateTo'))
                ORDER BY r.start_date")->fetchAll();

            respond_json(['items' => $rows, 'year' => $year, 'month' => $month]);
            break;

        case 'export.csv':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'request.view_all')) throw new Exception('ไม่มีสิทธิ์ Export');

            $status = (string)($payload['status'] ?? 'all');
            $dateFrom = (string)($payload['date_from'] ?? '');
            $dateTo = (string)($payload['date_to'] ?? '');

            $sql = "SELECT r.doc_no, u.prefix||u.full_name as name, u.department, r.req_type, r.leave_kind,
                    r.start_date, r.end_date, r.days, r.status, r.reason, r.created_at
                    FROM requests r LEFT JOIN users u ON r.user_id = u.id WHERE 1=1";
            $params = [];
            if ($status !== 'all') { $sql .= " AND r.status = ?"; $params[] = $status; }
            if ($dateFrom) { $sql .= " AND r.start_date >= ?"; $params[] = $dateFrom; }
            if ($dateTo) { $sql .= " AND r.start_date <= ?"; $params[] = $dateTo; }
            $sql .= " ORDER BY r.created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            $headers = ['เลขที่เอกสาร','ชื่อ-สกุล','กลุ่มสาระ/ฝ่าย','ประเภทเอกสาร','ประเภทการลา','วันที่เริ่ม','วันที่สิ้นสุด','จำนวนวัน','สถานะ','เหตุผล','วันที่ยื่น'];
            $csv = implode(',', array_map(fn($h) => '"' . $h . '"', $headers)) . "\n";
            $typeMap = ['leave'=>'ใบลา','vacation'=>'ลาพักผ่อน','cancel'=>'ยกเลิก','resign'=>'ลาออก','idcard'=>'บัตรประจำตัว'];
            $statusMap = ['draft'=>'ร่าง','submitted'=>'รอตรวจสอบ','reviewed'=>'รอผู้อนุมัติ','approved'=>'อนุมัติ','rejected'=>'ไม่อนุมัติ','cancelled'=>'ยกเลิก'];
            foreach ($rows as $r) {
                $line = [
                    $r['doc_no'], $r['name'], $r['department'],
                    $typeMap[$r['req_type']] ?? $r['req_type'],
                    $r['leave_kind'] ?: '-',
                    $r['start_date'], $r['end_date'], $r['days'],
                    $statusMap[$r['status']] ?? $r['status'],
                    $r['reason'],
                    substr($r['created_at'], 0, 10)
                ];
                $csv .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $line)) . "\n";
            }

            respond_json(['csv_b64' => base64_encode("\xEF\xBB\xBF" . $csv), 'filename' => 'leave_export_' . date('Ymd') . '.csv', 'count' => count($rows)]);
            break;

        case 'attachment.save':
            $user = lrs_verify_token($token);
            $rid = (string)($payload['request_id'] ?? '');
            $filename = mb_substr((string)($payload['filename'] ?? ''), 0, 200);
            $mime = mb_substr((string)($payload['mime'] ?? 'application/octet-stream'), 0, 100);
            $dataB64 = (string)($payload['data_b64'] ?? '');
            $size = intval($payload['size'] ?? 0);

            if (!$rid || !$filename || !$dataB64) throw new Exception('ข้อมูลไม่ครบ');
            if ($size > 5 * 1024 * 1024) throw new Exception('ไฟล์ขนาดเกิน 5MB');

            $stmtChk = $pdo->prepare("SELECT id, user_id FROM requests WHERE id = ?");
            $stmtChk->execute([$rid]);
            $req = $stmtChk->fetch();
            if (!$req) throw new Exception('ไม่พบเอกสาร');
            if ($req['user_id'] !== $user['id'] && !lrs_has_cap($user['role'], 'request.view_all')) {
                throw new Exception('ไม่มีสิทธิ์แนบไฟล์');
            }

            $aid = lrs_uuid();
            $pdo->prepare("INSERT INTO attachments (id, request_id, filename, mime, data_b64, size, created_by, created_at) VALUES (?,?,?,?,?,?,?,?)")
                ->execute([$aid, $rid, $filename, $mime, $dataB64, $size, $user['id'], lrs_now()]);

            respond_json(['id' => $aid, 'filename' => $filename]);
            break;

        case 'attachment.list':
            $user = lrs_verify_token($token);
            $rid = (string)($payload['request_id'] ?? '');
            if (!$rid) throw new Exception('ไม่พบเอกสาร');

            $rows = $pdo->prepare("SELECT id, filename, mime, size, created_by, created_at FROM attachments WHERE request_id = ? ORDER BY created_at");
            $rows->execute([$rid]);
            respond_json(['items' => $rows->fetchAll()]);
            break;

        case 'attachment.get':
            $user = lrs_verify_token($token);
            $aid = (string)($payload['id'] ?? '');
            $stmt = $pdo->prepare("SELECT * FROM attachments WHERE id = ?");
            $stmt->execute([$aid]);
            $att = $stmt->fetch();
            if (!$att) throw new Exception('ไม่พบไฟล์');
            respond_json(['filename' => $att['filename'], 'mime' => $att['mime'], 'data_b64' => $att['data_b64']]);
            break;

        case 'attachment.delete':
            $user = lrs_verify_token($token);
            $aid = (string)($payload['id'] ?? '');
            $stmt = $pdo->prepare("SELECT a.*, r.user_id as owner FROM attachments a LEFT JOIN requests r ON a.request_id = r.id WHERE a.id = ?");
            $stmt->execute([$aid]);
            $att = $stmt->fetch();
            if (!$att) throw new Exception('ไม่พบไฟล์');
            if ($att['owner'] !== $user['id'] && !lrs_has_cap($user['role'], 'request.view_all')) {
                throw new Exception('ไม่มีสิทธิ์ลบไฟล์นี้');
            }
            $pdo->prepare("DELETE FROM attachments WHERE id = ?")->execute([$aid]);
            respond_json(['ok' => true]);
            break;

        case 'leave.history':
            $user = lrs_verify_token($token);
            $uid = lrs_has_cap($user['role'], 'request.view_all') ? ((string)($payload['user_id'] ?? $user['id'])) : $user['id'];

            $rows = $pdo->prepare("SELECT strftime('%Y', start_date) as yr,
                    SUM(CASE WHEN leave_kind='sick' THEN days ELSE 0 END) as sick,
                    SUM(CASE WHEN leave_kind='personal' THEN days ELSE 0 END) as personal,
                    SUM(CASE WHEN leave_kind='maternity' THEN days ELSE 0 END) as maternity,
                    SUM(CASE WHEN req_type='vacation' THEN days ELSE 0 END) as vacation,
                    COUNT(*) as cnt
                FROM requests WHERE user_id = ? AND status = 'approved' AND start_date != ''
                GROUP BY yr ORDER BY yr DESC LIMIT 5");
            $rows->execute([$uid]);

            respond_json(['items' => $rows->fetchAll(), 'user_id' => $uid]);
            break;

        case 'request.list':
            $user = lrs_verify_token($token);
            $status = (string)($payload['status'] ?? 'all');
            $type = (string)($payload['type'] ?? 'all');
            $q = trim(mb_strtolower((string)($payload['q'] ?? '')));
            $page = max(1, intval($payload['page'] ?? 1));
            $per = 20;

            $canViewAll = lrs_has_cap($user['role'], 'request.view_all');

            $sql = "SELECT r.*, u.prefix as owner_prefix, u.full_name as owner_fullname, u.department as owner_dept 
                    FROM requests r 
                    LEFT JOIN users u ON r.user_id = u.id 
                    WHERE 1=1";
            $params = [];

            if (!$canViewAll) {
                $sql .= " AND r.user_id = ?";
                $params[] = $user['id'];
            }

            if ($status !== 'all') {
                $sql .= " AND r.status = ?";
                $params[] = $status;
            }

            if ($type !== 'all') {
                $sql .= " AND r.req_type = ?";
                $params[] = $type;
            }

            if ($q !== '') {
                $sql .= " AND (LOWER(r.doc_no) LIKE ? OR LOWER(r.subject) LIKE ? OR LOWER(u.full_name) LIKE ?)";
                $qParam = "%$q%";
                $params[] = $qParam;
                $params[] = $qParam;
                $params[] = $qParam;
            }

            $sql .= " ORDER BY r.created_at DESC";

            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM ($sql)");
            $stmtCount->execute($params);
            $total = intval($stmtCount->fetchColumn());

            $offset = ($page - 1) * $per;
            $sql .= " LIMIT $per OFFSET $offset";
            $stmtData = $pdo->prepare($sql);
            $stmtData->execute($params);
            $rows = $stmtData->fetchAll();

            $items = array_map(function($r) {
                return [
                    'id' => $r['id'],
                    'doc_no' => $r['doc_no'],
                    'req_type' => $r['req_type'],
                    'user_id' => $r['user_id'],
                    'status' => $r['status'],
                    'subject' => $r['subject'],
                    'leave_kind' => $r['leave_kind'],
                    'start_date' => lrs_date_only($r['start_date']),
                    'end_date' => lrs_date_only($r['end_date']),
                    'days' => floatval($r['days']),
                    'doc_date' => lrs_date_only($r['doc_date']),
                    'created_at' => $r['created_at'],
                    'updated_at' => $r['updated_at'],
                    'owner_name' => trim(($r['owner_prefix'] ?? '') . ' ' . ($r['owner_fullname'] ?? '')) ?: '-',
                    'owner_dept' => $r['owner_dept'] ?? '',
                    'reviewer_id' => $r['reviewer_id'],
                    'approver_id' => $r['approver_id']
                ];
            }, $rows);

            respond_json([
                'items' => $items,
                'total' => $total,
                'page' => $page,
                'per' => $per,
                'pages' => max(1, ceil($total / $per))
            ]);
            break;

        case 'request.get':
            $user = lrs_verify_token($token);
            $id = (string)($payload['id'] ?? '');
            if (!$id) throw new Exception('ไม่พบเอกสาร');

            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $r = $stmt->fetch();
            if (!$r) throw new Exception('ไม่พบเอกสาร');

            $canViewAll = lrs_has_cap($user['role'], 'request.view_all');
            if (!$canViewAll && $r['user_id'] !== $user['id']) {
                throw new Exception('ไม่มีสิทธิ์เข้าถึงเอกสารนี้');
            }

            $r['stat_prev'] = json_decode($r['stat_prev'] ?? '{}', true) ?: [];
            $r['stat_this'] = json_decode($r['stat_this'] ?? '{}', true) ?: [];
            $r['stat_total'] = json_decode($r['stat_total'] ?? '{}', true) ?: [];

            $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmtUser->execute([$r['user_id']]);
            $owner = $stmtUser->fetch();
            $r['_owner'] = $owner ? lrs_public_user($owner) : null;

            $r['_reviewer_name'] = '';
            if ($r['reviewer_id']) {
                $stmtUser->execute([$r['reviewer_id']]);
                $rv = $stmtUser->fetch();
                if ($rv) $r['_reviewer_name'] = trim(($rv['prefix'] ?? '') . ' ' . ($rv['full_name'] ?? ''));
            }

            $r['_approver_name'] = '';
            if ($r['approver_id']) {
                $stmtUser->execute([$r['approver_id']]);
                $ap = $stmtUser->fetch();
                if ($ap) $r['_approver_name'] = trim(($ap['prefix'] ?? '') . ' ' . ($ap['full_name'] ?? ''));
            }

            respond_json($r);
            break;

        case 'request.save':
            $user = lrs_verify_token($token);
            $id = (string)($payload['id'] ?? '');
            $now = lrs_now();

            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
                $stmt->execute([$id]);
                $ex = $stmt->fetch();
                if (!$ex) throw new Exception('ไม่พบเอกสาร');
                if ($ex['user_id'] !== $user['id'] && !lrs_has_cap($user['role'], 'request.manage')) {
                    throw new Exception('ไม่มีสิทธิ์แก้ไขเอกสารนี้');
                }
                if ($ex['status'] !== 'draft' && $ex['status'] !== 'rejected') {
                    throw new Exception('เอกสารส่งแล้ว ไม่สามารถแก้ไขได้');
                }

                $docNo = $ex['doc_no'];
                $rev = intval($ex['rev']) + 1;
            } else {
                $id = lrs_uuid();
                $docNo = lrs_next_doc_no();
                $rev = 1;
            }

            $reqType = (string)($payload['req_type'] ?? 'leave');
            $place = mb_substr((string)($payload['place'] ?? ''), 0, 500);
            $docDate = lrs_date_only($payload['doc_date'] ?? null);
            $subject = mb_substr((string)($payload['subject'] ?? ''), 0, 500);
            $toPerson = mb_substr((string)($payload['to_person'] ?? ''), 0, 500);
            $reason = mb_substr((string)($payload['reason'] ?? ''), 0, 500);
            $startDate = lrs_date_only($payload['start_date'] ?? null);
            $endDate = lrs_date_only($payload['end_date'] ?? null);
            $days = floatval($payload['days'] ?? 0);

            // Smart Leave Balance: Auto calculate working days excluding weekends & public holidays if days <= 0
            if ($startDate && $endDate) {
                $calcDays = lrs_calc_working_days($startDate, $endDate);
                if ($days <= 0 || !empty($payload['auto_calc_days'])) {
                    $days = $calcDays;
                }

                // Duplicate Date Guard: Prevent overlapping leave dates for active requests
                $stmtDup = $pdo->prepare("SELECT doc_no FROM requests WHERE user_id = ? AND id != ? AND status IN ('submitted', 'reviewed', 'approved', 'cancel_requested') AND (start_date <= ? AND end_date >= ?)");
                $stmtDup->execute([$user['id'], $id, $endDate, $startDate]);
                $dup = $stmtDup->fetch();
                if ($dup) {
                    throw new Exception('ช่วงวันที่ลา ' . $startDate . ' ถึง ' . $endDate . ' คาบเกี่ยวกับใบลาเลขที่ ' . $dup['doc_no'] . ' ที่ได้ยื่นไว้แล้ว');
                }
            }

            $contactAddr = mb_substr((string)($payload['contact_addr'] ?? ''), 0, 500);
            $contactPhone = mb_substr((string)($payload['contact_phone'] ?? ''), 0, 500);

            $leaveKind = (string)($payload['leave_kind'] ?? 'sick');
            $lastKind = mb_substr((string)($payload['last_kind'] ?? ''), 0, 20);
            $lastStart = lrs_date_only($payload['last_start'] ?? null);
            $lastEnd = lrs_date_only($payload['last_end'] ?? null);
            $lastDays = floatval($payload['last_days'] ?? 0);
            $accumDays = floatval($payload['accum_days'] ?? 0);
            $rightDays = floatval($payload['right_days'] ?? 0);
            $totalRight = floatval($payload['total_right'] ?? 0);
            $origLeave = mb_substr((string)($payload['orig_leave'] ?? ''), 0, 100);
            $origStart = lrs_date_only($payload['orig_start'] ?? null);
            $origEnd = lrs_date_only($payload['orig_end'] ?? null);
            $origDays = floatval($payload['orig_days'] ?? 0);
            $cancelDays = floatval($payload['cancel_days'] ?? 0);
            $cancelStart = lrs_date_only($payload['cancel_start'] ?? null);
            $cancelEnd = lrs_date_only($payload['cancel_end'] ?? null);
            $startGovDate = lrs_date_only($payload['start_gov_date'] ?? null);
            $positionNow = mb_substr((string)($payload['position_now'] ?? ''), 0, 100);
            $deptSection = mb_substr((string)($payload['dept_section'] ?? ''), 0, 100);
            $division = mb_substr((string)($payload['division'] ?? ''), 0, 100);
            $deptGov = mb_substr((string)($payload['dept_gov'] ?? ''), 0, 100);
            $salaryLevel = mb_substr((string)($payload['salary_level'] ?? ''), 0, 30);
            $salaryStep = mb_substr((string)($payload['salary_step'] ?? ''), 0, 30);
            $salaryAmount = mb_substr((string)($payload['salary_amount'] ?? ''), 0, 30);
            $resignReason = mb_substr((string)($payload['resign_reason'] ?? ''), 0, 1000);
            $resignDate = lrs_date_only($payload['resign_date'] ?? null);
            $idcCase = (string)($payload['idc_case'] ?? 'first');
            $idcReason = mb_substr((string)($payload['idc_reason'] ?? ''), 0, 300);
            $idcOldNo = mb_substr((string)($payload['idc_old_no'] ?? ''), 0, 40);
            $photoUrl = mb_substr((string)($payload['photo_url'] ?? ''), 0, 500);

            $pdo->beginTransaction();
            try {
                $stmtUpsert = $pdo->prepare("REPLACE INTO requests (
                    id, doc_no, req_type, user_id, status, place, doc_date, subject, to_person, reason,
                    start_date, end_date, days, contact_addr, contact_phone, leave_kind, last_kind, last_start, last_end, last_days,
                    accum_days, right_days, total_right, orig_leave, orig_start, orig_end, orig_days, cancel_days, cancel_start, cancel_end,
                    start_gov_date, position_now, dept_section, division, dept_gov, salary_level, salary_step, salary_amount, resign_reason, resign_date,
                    idc_case, idc_reason, idc_old_no, photo_url, created_at, created_by, updated_at, updated_by, rev
                ) VALUES (
                    ?, ?, ?, ?, 'draft', ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                )");

                $stmtUpsert->execute([
                    $id, $docNo, $reqType, $user['id'], $place, $docDate, $subject, $toPerson, $reason,
                    $startDate, $endDate, $days, $contactAddr, $contactPhone, $leaveKind, $lastKind, $lastStart, $lastEnd, $lastDays,
                    $accumDays, $rightDays, $totalRight, $origLeave, $origStart, $origEnd, $origDays, $cancelDays, $cancelStart, $cancelEnd,
                    $startGovDate, $positionNow, $deptSection, $division, $deptGov, $salaryLevel, $salaryStep, $salaryAmount, $resignReason, $resignDate,
                    $idcCase, $idcReason, $idcOldNo, $photoUrl, $now, $user['id'], $now, $user['id'], $rev
                ]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_audit_log($user, 'request.save', 'request', $id, ['req_type' => $reqType]);
            respond_json(['ok' => true, 'id' => $id, 'doc_no' => $docNo, 'days' => $days]);
            break;

        case 'request.submit':
            $user = lrs_verify_token($token);
            $id = (string)($payload['id'] ?? '');
            if (!$id) throw new Exception('ไม่พบเอกสาร');

            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $ex = $stmt->fetch();

            if (!$ex) throw new Exception('ไม่พบเอกสาร');
            if ($ex['user_id'] !== $user['id']) throw new Exception('ยื่นได้เฉพาะเอกสารของตนเอง');
            if ($ex['status'] !== 'draft' && $ex['status'] !== 'rejected') throw new Exception('เอกสารนี้ยื่นแล้ว');
            if (!$ex['subject'] || !$ex['start_date']) throw new Exception('กรอกข้อมูลไม่ครบ (เรื่อง/วันที่ลา)');

            // Medical Certificate Constraint for Sick Leave >= 3 Days
            if (($ex['leave_kind'] ?? '') === 'sick' && floatval($ex['days']) >= 3) {
                $stmtAtt = $pdo->prepare("SELECT COUNT(*) FROM attachments WHERE request_id = ?");
                $stmtAtt->execute([$id]);
                $attCount = intval($stmtAtt->fetchColumn());
                if ($attCount === 0) {
                    throw new Exception('การลาป่วยตั้งแต่ 3 วันขึ้นไป ต้องแนบไฟล์ใบรับรองแพทย์ด้วย');
                }
            }

            $now = lrs_now();
            $rev = intval($ex['rev']) + 1;

            $pdo->beginTransaction();
            try {
                $stmtUpd = $pdo->prepare("UPDATE requests SET status = 'submitted', updated_at = ?, updated_by = ?, rev = ?, reviewer_note = '', approver_note = '' WHERE id = ?");
                $stmtUpd->execute([$now, $user['id'], $rev, $id]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_notify_push_roles(['clerk', 'admin'], 'submit', 'เอกสารรอตรวจสอบ', 'มีเอกสารใหม่รอตรวจสอบจาก ' . $user['full_name'], '#/inbox');
            
            $emailHtml = '<p>เรียน เจ้าหน้าที่ธุรการและฝ่ายบริหาร</p>'
              . '<p>มีเอกสารใบลาราชการยื่นใหม่เข้าสู่ระบบ โปรดเข้าตรวจสอบตามรายละเอียดดังนี้:</p>'
              . '<table style="width:100%;border-collapse:collapse;margin:16px 0;">'
              . '<tr><td style="padding:6px;font-weight:bold;width:130px;">เลขที่เอกสาร:</td><td style="padding:6px;">' . htmlspecialchars($ex['doc_no']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">ผู้ยื่นใบลา:</td><td style="padding:6px;">' . htmlspecialchars($user['full_name']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">เรื่อง:</td><td style="padding:6px;">' . htmlspecialchars($ex['subject']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">วันที่ลา:</td><td style="padding:6px;">' . htmlspecialchars($ex['start_date']) . ' ถึง ' . htmlspecialchars($ex['end_date']) . ' (' . $ex['days'] . ' วัน)</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">เหตุผล:</td><td style="padding:6px;">' . htmlspecialchars($ex['reason'] ?: '-') . '</td></tr>'
              . '</table>';
            
            lrs_email_notify_roles(['clerk', 'admin'], '📌 มีเอกสารใบลาราชการใหม่ยื่นเข้าสู่ระบบ (' . $ex['doc_no'] . ')', $emailHtml);
            
            lrs_audit_log($user, 'request.submit', 'request', $id, []);
            respond_json(['ok' => true]);
            break;

        case 'request.review':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'request.review')) throw new Exception('ไม่มีสิทธิ์ดำเนินการนี้');

            $id = (string)($payload['id'] ?? '');
            $note = mb_substr((string)($payload['note'] ?? ''), 0, 500);
            $statPrev = json_encode($payload['stat_prev'] ?? [], JSON_UNESCAPED_UNICODE);
            $statThis = json_encode($payload['stat_this'] ?? [], JSON_UNESCAPED_UNICODE);
            $statTotal = json_encode($payload['stat_total'] ?? [], JSON_UNESCAPED_UNICODE);

            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $ex = $stmt->fetch();
            if (!$ex) throw new Exception('ไม่พบเอกสาร');
            if ($ex['status'] !== 'submitted') throw new Exception('เอกสารไม่อยู่ในสถานะรอตรวจสอบ');

            $now = lrs_now();
            $rev = intval($ex['rev']) + 1;

            $pdo->beginTransaction();
            try {
                $stmtUpd = $pdo->prepare("UPDATE requests SET status = 'reviewed', reviewer_id = ?, reviewer_note = ?, reviewed_at = ?, stat_prev = ?, stat_this = ?, stat_total = ?, updated_at = ?, updated_by = ?, rev = ? WHERE id = ?");
                $stmtUpd->execute([$user['id'], $note, $now, $statPrev, $statThis, $statTotal, $now, $user['id'], $rev, $id]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_notify_push_roles(['director', 'admin'], 'review', 'เอกสารรออนุมัติ', 'มีเอกสารผ่านการตรวจสอบ รอการอนุมัติ', '#/inbox');
            lrs_notify_push_user($ex['user_id'], 'review', 'เอกสารผ่านการตรวจสอบ', 'เอกสาร ' . $ex['doc_no'] . ' ผ่านการตรวจสอบแล้ว', '#/my');
            
            $emailHtml = '<p>เรียน ผู้อำนวยการโรงเรียน</p>'
              . '<p>เอกสารใบลาราชการผ่านการตรวจสอบจากเจ้าหน้าที่ธุรการแล้ว รอการพิจารณาลงนามอนุมัติ:</p>'
              . '<table style="width:100%;border-collapse:collapse;margin:16px 0;">'
              . '<tr><td style="padding:6px;font-weight:bold;width:130px;">เลขที่เอกสาร:</td><td style="padding:6px;">' . htmlspecialchars($ex['doc_no']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">เรื่อง:</td><td style="padding:6px;">' . htmlspecialchars($ex['subject']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">ผู้ตรวจสอบ:</td><td style="padding:6px;">' . htmlspecialchars($user['full_name']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">ความเห็น:</td><td style="padding:6px;">' . htmlspecialchars($note ?: 'ตรวจสอบเรียบร้อย') . '</td></tr>'
              . '</table>';

            lrs_email_notify_roles(['director', 'admin'], '📋 ใบลาผ่านการตรวจสอบแล้ว - รอลงนามอนุมัติ (' . $ex['doc_no'] . ')', $emailHtml);

            lrs_audit_log($user, 'request.review', 'request', $id, []);
            respond_json(['ok' => true]);
            break;

        case 'request.approve':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'request.approve')) throw new Exception('ไม่มีสิทธิ์ดำเนินการนี้');

            $id = (string)($payload['id'] ?? '');
            $decision = ($payload['decision'] ?? 'approve') === 'approve' ? 'approve' : 'reject';
            $note = mb_substr((string)($payload['note'] ?? ''), 0, 500);

            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $ex = $stmt->fetch();
            if (!$ex) throw new Exception('ไม่พบเอกสาร');
            if ($ex['status'] !== 'reviewed' && $ex['status'] !== 'submitted') throw new Exception('เอกสารไม่พร้อมอนุมัติ');

            $newStatus = $decision === 'approve' ? 'approved' : 'rejected';
            $now = lrs_now();
            $rev = intval($ex['rev']) + 1;

            $pdo->beginTransaction();
            try {
                $stmtUpd = $pdo->prepare("UPDATE requests SET status = ?, approver_id = ?, approver_note = ?, approver_decision = ?, approved_at = ?, updated_at = ?, updated_by = ?, rev = ? WHERE id = ?");
                $stmtUpd->execute([$newStatus, $user['id'], $note, $decision, $now, $now, $user['id'], $rev, $id]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            $msgTitle = $decision === 'approve' ? 'เอกสารได้รับอนุมัติ' : 'เอกสารไม่ได้รับอนุมัติ';
            $msgBody = 'เอกสาร ' . $ex['doc_no'] . ' ' . ($decision === 'approve' ? 'ได้รับอนุมัติแล้ว' : 'ไม่ได้รับอนุมัติ');
            lrs_notify_push_user($ex['user_id'], 'approve', $msgTitle, $msgBody, '#/my');

            $stmtOwner = $pdo->prepare("SELECT prefix, full_name, email FROM users WHERE id = ?");
            $stmtOwner->execute([$ex['user_id']]);
            $ownerObj = $stmtOwner->fetch();
            $ownerName = $ownerObj ? trim(($ownerObj['prefix'] ?? '') . ' ' . ($ownerObj['full_name'] ?? '')) : 'บุคลากร';

            $statusText = $decision === 'approve' ? '<span style="color:#248a3d;font-weight:bold;">✅ อนุมัติการลาแล้ว</span>' : '<span style="color:#c9271f;font-weight:bold;">❌ ไม่อนุมัติการลา</span>';
            $emailSubject = ($decision === 'approve' ? '✅ แจ้งผลการอนุมัติใบลา ' : '❌ แจ้งผลไม่อนุมัติใบลา ') . $ex['doc_no'];
            
            $emailHtml = '<p>เรียนคุณ ' . htmlspecialchars($ownerName) . '</p>'
              . '<p>ผลการพิจารณาใบลาราชการของคุณมีรายละเอียดดังนี้:</p>'
              . '<table style="width:100%;border-collapse:collapse;margin:16px 0;">'
              . '<tr><td style="padding:6px;font-weight:bold;width:130px;">เลขที่เอกสาร:</td><td style="padding:6px;">' . htmlspecialchars($ex['doc_no']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">เรื่อง:</td><td style="padding:6px;">' . htmlspecialchars($ex['subject']) . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">คำสั่งผู้อนุมัติ:</td><td style="padding:6px;">' . $statusText . '</td></tr>'
              . '<tr><td style="padding:6px;font-weight:bold;">หมายเหตุ:</td><td style="padding:6px;">' . htmlspecialchars($note ?: '-') . '</td></tr>'
              . '</table>';

            lrs_email_notify_user($ex['user_id'], $emailSubject, $emailHtml);

            lrs_audit_log($user, 'request.approve', 'request', $id, ['decision' => $decision]);
            respond_json(['ok' => true]);
            break;

        case 'request.request_cancel':
            $user = lrs_verify_token($token);
            $id = (string)($payload['id'] ?? '');
            $reason = mb_substr((string)($payload['reason'] ?? ''), 0, 500);

            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $ex = $stmt->fetch();
            if (!$ex) throw new Exception('ไม่พบเอกสาร');
            if ($ex['user_id'] !== $user['id'] && !lrs_has_cap($user['role'], 'request.manage')) {
                throw new Exception('ขอยกเลิกได้เฉพาะเอกสารของตนเอง');
            }
            if ($ex['status'] !== 'approved') {
                throw new Exception('ยื่นขอยกเลิกได้เฉพาะใบลาที่ได้รับการอนุมัติแล้วเท่านั้น');
            }

            $now = lrs_now();
            $rev = intval($ex['rev']) + 1;
            $pdo->prepare("UPDATE requests SET status = 'cancel_requested', reviewer_note = ?, updated_at = ?, updated_by = ?, rev = ? WHERE id = ?")
                ->execute(['เหตุผลขอยกเลิก: ' . $reason, $now, $user['id'], $rev, $id]);

            lrs_notify_push_roles(['clerk', 'admin', 'director'], 'cancel_request', 'มีคำขอยกเลิกใบลา', 'มีคำขอยกเลิกใบลาเลขที่ ' . $ex['doc_no'], '#/inbox');
            lrs_audit_log($user, 'request.request_cancel', 'request', $id, ['reason' => $reason]);
            respond_json(['ok' => true]);
            break;

        case 'request.approve_cancel':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'request.approve') && !lrs_has_cap($user['role'], 'request.review')) {
                throw new Exception('ไม่มีสิทธิ์ดำเนินการนี้');
            }
            $id = (string)($payload['id'] ?? '');
            $decision = ($payload['decision'] ?? 'approve') === 'approve' ? 'approve' : 'reject';
            $note = mb_substr((string)($payload['note'] ?? ''), 0, 500);

            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $ex = $stmt->fetch();
            if (!$ex) throw new Exception('ไม่พบเอกสาร');
            if ($ex['status'] !== 'cancel_requested') throw new Exception('เอกสารนี้ไม่อยู่ในสถานะยื่นขอยกเลิก');

            $newStatus = $decision === 'approve' ? 'cancelled' : 'approved';
            $now = lrs_now();
            $rev = intval($ex['rev']) + 1;

            $pdo->prepare("UPDATE requests SET status = ?, approver_note = ?, updated_at = ?, updated_by = ?, rev = ? WHERE id = ?")
                ->execute([$newStatus, $note, $now, $user['id'], $rev, $id]);

            $msg = $decision === 'approve' ? 'คำขอยกเลิกใบลาได้รับการอนุมัติแล้ว' : 'คำขอยกเลิกใบลาไม่ได้รับการอนุมัติ';
            lrs_notify_push_user($ex['user_id'], 'approve_cancel', $msg, 'เอกสาร ' . $ex['doc_no'] . ' ' . $msg, '#/my');
            lrs_audit_log($user, 'request.approve_cancel', 'request', $id, ['decision' => $decision]);
            respond_json(['ok' => true]);
            break;

        case 'request.bulk_approve':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'request.approve') && !lrs_has_cap($user['role'], 'request.review')) {
                throw new Exception('ไม่มีสิทธิ์ดำเนินการนี้');
            }
            $ids = is_array($payload['ids'] ?? null) ? $payload['ids'] : [];
            $decision = ($payload['decision'] ?? 'approve') === 'approve' ? 'approve' : 'reject';
            $note = mb_substr((string)($payload['note'] ?? 'อนุมัติแบบกลุ่ม'), 0, 500);

            if (empty($ids)) throw new Exception('กรุณาเลือกอย่างน้อย 1 รายการ');

            $count = 0;
            $now = lrs_now();

            foreach ($ids as $reqId) {
                $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
                $stmt->execute([$reqId]);
                $ex = $stmt->fetch();
                if (!$ex) continue;

                if ($user['role'] === 'clerk') {
                    if ($ex['status'] === 'submitted') {
                        $rev = intval($ex['rev']) + 1;
                        $pdo->prepare("UPDATE requests SET status = 'reviewed', reviewer_id = ?, reviewer_note = ?, reviewed_at = ?, updated_at = ?, updated_by = ?, rev = ? WHERE id = ?")
                            ->execute([$user['id'], $note, $now, $now, $user['id'], $rev, $reqId]);
                        $count++;
                    }
                } else if ($user['role'] === 'director' || $user['role'] === 'admin') {
                    if ($ex['status'] === 'reviewed' || $ex['status'] === 'submitted' || $ex['status'] === 'cancel_requested') {
                        $newStatus = $decision === 'approve' ? ($ex['status'] === 'cancel_requested' ? 'cancelled' : 'approved') : ($ex['status'] === 'cancel_requested' ? 'approved' : 'rejected');
                        $rev = intval($ex['rev']) + 1;
                        $pdo->prepare("UPDATE requests SET status = ?, approver_id = ?, approver_note = ?, approver_decision = ?, approved_at = ?, updated_at = ?, updated_by = ?, rev = ? WHERE id = ?")
                            ->execute([$newStatus, $user['id'], $note, $decision, $now, $now, $user['id'], $rev, $reqId]);
                        $count++;
                        lrs_notify_push_user($ex['user_id'], 'approve', 'เอกสารได้รับการอนุมัติ', 'เอกสาร ' . $ex['doc_no'] . ' ได้รับการอนุมัติแล้ว', '#/my');
                    }
                }
            }

            lrs_audit_log($user, 'request.bulk_approve', 'request', '', ['count' => $count, 'decision' => $decision]);
            respond_json(['ok' => true, 'updated_count' => $count]);
            break;

        case 'audit.list':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'audit.view_all')) throw new Exception('ไม่มีสิทธิ์เข้าถึงประวัติการทำรายการ');

            $page = max(1, intval($payload['page'] ?? 1));
            $limit = min(100, max(10, intval($payload['limit'] ?? 25)));
            $offset = ($page - 1) * $limit;
            $q = trim((string)($payload['query'] ?? ''));

            $where = [];
            $params = [];
            if ($q !== '') {
                $where[] = "(username LIKE ? OR action LIKE ? OR target_type LIKE ?)";
                $like = '%' . $q . '%';
                $params = [$like, $like, $like];
            }
            $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM audit " . $whereClause);
            $stmtCount->execute($params);
            $total = intval($stmtCount->fetchColumn());

            $stmt = $pdo->prepare("SELECT * FROM audit " . $whereClause . " ORDER BY ts DESC LIMIT " . $limit . " OFFSET " . $offset);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            respond_json(['items' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
            break;

        case 'request.verify':
            $docNo = trim((string)($payload['doc_no'] ?? $_GET['doc_no'] ?? ''));
            if (!$docNo) throw new Exception('กรุณาระบุเลขที่เอกสาร');

            $stmt = $pdo->prepare("SELECT r.id, r.doc_no, r.req_type, r.subject, r.start_date, r.end_date, r.days, r.status, r.approved_at, u.full_name as owner_name, u.position as owner_position FROM requests r JOIN users u ON r.user_id = u.id WHERE r.doc_no = ?");
            $stmt->execute([$docNo]);
            $req = $stmt->fetch();

            if (!$req) {
                respond_json(['verified' => false, 'message' => 'ไม่พบเอกสารนี้ในระบบ LRS']);
            } else {
                $req['verified'] = true;
                $req['org_name'] = lrs_get_setting('org_name', 'โรงเรียนปากพูน');
                respond_json($req);
            }
            break;

        case 'holidays.list':
            $stmt = $pdo->query("SELECT * FROM holidays ORDER BY event_date ASC");
            $list = $stmt->fetchAll();
            respond_json(['items' => $list]);
            break;

        case 'setting.vacuum_db':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'setting.manage')) throw new Exception('ไม่มีสิทธิ์ดำเนินการนี้');

            $pdo->exec("PRAGMA vacuum;");
            lrs_audit_log($user, 'setting.vacuum_db', 'setting', '', []);
            respond_json(['ok' => true, 'message' => 'เพิ่มประสิทธิภาพและกระชับฐานข้อมูลสำเร็จ']);
            break;

        case 'setting.restore_db':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'setting.manage')) throw new Exception('ไม่มีสิทธิ์ดำเนินการนี้');

            $dataB64 = (string)($payload['data_b64'] ?? '');
            if (!$dataB64) throw new Exception('ไม่พบข้อมูลไฟล์สำรอง');

            $binary = base64_decode(preg_replace('#^data:.*?;base64,#', '', $dataB64));
            if (!$binary || substr($binary, 0, 15) !== 'SQLite format 3') {
                throw new Exception('ไฟล์สำรองไม่ถูกต้อง (ต้องเป็นไฟล์ SQLite database)');
            }

            $dbPath = __DIR__ . '/database.db';
            $backupCur = __DIR__ . '/database_backup_before_restore.db';
            copy($dbPath, $backupCur);

            file_put_contents($dbPath, $binary);
            lrs_audit_log($user, 'setting.restore_db', 'setting', '', []);
            respond_json(['ok' => true, 'message' => 'กู้คืนฐานข้อมูลสำเร็จ']);
            break;

        case 'request.cancel':
            $user = lrs_verify_token($token);
            $id = (string)($payload['id'] ?? '');
            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $ex = $stmt->fetch();
            if (!$ex) throw new Exception('ไม่พบเอกสาร');

            $isOwn = $ex['user_id'] === $user['id'];
            if (!$isOwn && !lrs_has_cap($user['role'], 'request.manage')) throw new Exception('ยกเลิกได้เฉพาะเอกสารของตนเอง');
            if ($ex['status'] === 'approved' || $ex['status'] === 'cancelled') throw new Exception('เอกสารนี้ยกเลิกไม่ได้');

            $now = lrs_now();
            $rev = intval($ex['rev']) + 1;
            $pdo->prepare("UPDATE requests SET status = 'cancelled', updated_at = ?, updated_by = ?, rev = ? WHERE id = ?")->execute([$now, $user['id'], $rev, $id]);

            lrs_audit_log($user, 'request.cancel', 'request', $id, []);
            respond_json(['ok' => true]);
            break;

        case 'request.delete':
            $user = lrs_verify_token($token);
            $id = (string)($payload['id'] ?? '');
            $stmt = $pdo->prepare("SELECT * FROM requests WHERE id = ?");
            $stmt->execute([$id]);
            $ex = $stmt->fetch();
            if (!$ex) throw new Exception('ไม่พบเอกสาร');

            $isOwn = $ex['user_id'] === $user['id'];
            if (!$isOwn && !lrs_has_cap($user['role'], 'request.manage')) throw new Exception('ลบได้เฉพาะเอกสารของตนเอง');
            if ($isOwn && $ex['status'] !== 'draft' && !lrs_has_cap($user['role'], 'request.manage')) throw new Exception('ลบได้เฉพาะฉบับร่าง');

            $pdo->prepare("DELETE FROM requests WHERE id = ?")->execute([$id]);
            lrs_audit_log($user, 'request.delete', 'request', $id, []);
            respond_json(['ok' => true]);
            break;

        case 'request.stats':
            $user = lrs_verify_token($token);
            $uid = (string)($payload['user_id'] ?? $user['id']);
            if ($uid !== $user['id'] && !lrs_has_cap($user['role'], 'request.view_all')) {
                throw new Exception('ไม่มีสิทธิ์เข้าถึงสถิตินี้');
            }

            $fy = lrs_cur_fy();
            $summary = lrs_leave_user_summary($uid, $fy);
            $types = lrs_leave_status($uid, $fy);

            respond_json([
                'sick' => $summary['sick'],
                'personal' => $summary['personal'],
                'maternity' => $summary['maternity'],
                'vacation' => $summary['vacation'],
                'fiscal_year' => $fy,
                'warn_pct' => lrs_leave_quotas()['_warn'],
                'types' => $types
            ]);
            break;

        case 'user.list':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'user.view_all|user.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');

            $q = trim(mb_strtolower((string)($payload['q'] ?? '')));
            $role = (string)($payload['role'] ?? 'all');

            $sql = "SELECT * FROM users WHERE 1=1";
            $params = [];
            if ($role !== 'all') {
                $sql .= " AND role = ?";
                $params[] = $role;
            }
            if ($q !== '') {
                $sql .= " AND (LOWER(full_name) LIKE ? OR LOWER(username) LIKE ? OR LOWER(department) LIKE ?)";
                $qParam = "%$q%";
                $params[] = $qParam;
                $params[] = $qParam;
                $params[] = $qParam;
            }
            $sql .= " ORDER BY full_name ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll();

            $items = array_map('lrs_public_user', $rows);
            respond_json(['items' => $items, 'total' => count($items)]);
            break;

        case 'user.get':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'user.view_all|user.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');
            $id = (string)($payload['id'] ?? '');
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $u = $stmt->fetch();
            if (!$u) throw new Exception('ไม่พบผู้ใช้');
            respond_json(lrs_public_user($u));
            break;

        case 'user.save':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'user.manage|setting.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');

            $id = (string)($payload['id'] ?? '');
            $role = in_array($payload['role'] ?? '', ['teacher','clerk','director','admin']) ? $payload['role'] : 'teacher';
            $fullName = trim((string)($payload['full_name'] ?? ''));
            if (!$fullName) throw new Exception('กรุณากรอกชื่อ-สกุล');

            $now = lrs_now();
            $lineUserId = trim((string)($payload['line_user_id'] ?? ''));

            $pdo->beginTransaction();
            try {
                if ($id) {
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->execute([$id]);
                    $ex = $stmt->fetch();
                    if (!$ex) throw new Exception('ไม่พบผู้ใช้');

                    $sql = "UPDATE users SET 
                        prefix = ?, full_name = ?, position = ?, academic_rank = ?, department = ?, email = ?, phone = ?, role = ?, line_user_id = ?, id_card = ?, birth_date = ?, blood_group = ?, address = ?, start_gov_date = ?, is_active = ?, updated_at = ?";
                    $params = [
                        mb_substr((string)($payload['prefix'] ?? ''), 0, 30),
                        mb_substr($fullName, 0, 120),
                        mb_substr((string)($payload['position'] ?? ''), 0, 100),
                        mb_substr((string)($payload['academic_rank'] ?? ''), 0, 60),
                        mb_substr((string)($payload['department'] ?? ''), 0, 120),
                        mb_substr((string)($payload['email'] ?? ''), 0, 120),
                        mb_substr((string)($payload['phone'] ?? ''), 0, 30),
                        $role,
                        $lineUserId,
                        mb_substr((string)($payload['id_card'] ?? ''), 0, 30),
                        lrs_date_only($payload['birth_date'] ?? null),
                        mb_substr((string)($payload['blood_group'] ?? ''), 0, 5),
                        mb_substr((string)($payload['address'] ?? ''), 0, 300),
                        lrs_date_only($payload['start_gov_date'] ?? null),
                        ($payload['is_active'] ?? 'yes') === 'yes' ? 'yes' : 'no',
                        $now
                    ];

                    if (!empty($payload['password'])) {
                        $pw = (string)$payload['password'];
                        if (strlen($pw) < 6) throw new Exception('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
                        $salt = lrs_salt();
                        $hash = lrs_sec_hash($pw, $salt, 1200);
                        $sql .= ", password_hash = ?, salt = ?";
                        $params[] = $hash;
                        $params[] = $salt;
                    }

                    $sql .= " WHERE id = ?";
                    $params[] = $id;

                    $stmtUpd = $pdo->prepare($sql);
                    $stmtUpd->execute($params);
                } else {
                    $un = trim(strtolower((string)($payload['username'] ?? '')));
                    if (!preg_match('/^[a-z0-9_.]{3,30}$/', $un)) throw new Exception('ชื่อผู้ใช้ต้องเป็น a-z 0-9 _ . ยาว 3-30 ตัว');

                    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE LOWER(username) = ?");
                    $stmtCheck->execute([$un]);
                    if ($stmtCheck->fetchColumn() > 0) throw new Exception('ชื่อผู้ใช้นี้มีอยู่แล้ว');

                    $pw = (string)($payload['password'] ?? '123456');
                    if (strlen($pw) < 6) throw new Exception('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');

                    $id = lrs_uuid();
                    $salt = lrs_salt();
                    $hash = lrs_sec_hash($pw, $salt, 1200);

                    $stmtIns = $pdo->prepare("INSERT INTO users 
                        (id, username, password_hash, salt, pbkdf2_iter, prefix, full_name, position, academic_rank, department, email, phone, role, line_user_id, id_card, birth_date, blood_group, address, start_gov_date, is_active, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, 1200, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                    $stmtIns->execute([
                        $id, $un, $hash, $salt,
                        mb_substr((string)($payload['prefix'] ?? ''), 0, 30),
                        mb_substr($fullName, 0, 120),
                        mb_substr((string)($payload['position'] ?? ''), 0, 100),
                        mb_substr((string)($payload['academic_rank'] ?? ''), 0, 60),
                        mb_substr((string)($payload['department'] ?? ''), 0, 120),
                        mb_substr((string)($payload['email'] ?? ''), 0, 120),
                        mb_substr((string)($payload['phone'] ?? ''), 0, 30),
                        $role,
                        $lineUserId,
                        mb_substr((string)($payload['id_card'] ?? ''), 0, 30),
                        lrs_date_only($payload['birth_date'] ?? null),
                        mb_substr((string)($payload['blood_group'] ?? ''), 0, 5),
                        mb_substr((string)($payload['address'] ?? ''), 0, 300),
                        lrs_date_only($payload['start_gov_date'] ?? null),
                        ($payload['is_active'] ?? 'yes') === 'yes' ? 'yes' : 'no',
                        $now, $now
                    ]);
                }
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_audit_log($user, 'user.save', 'user', $id, ['role' => $role]);
            respond_json(['ok' => true, 'id' => $id]);
            break;

        case 'user.delete':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'user.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');
            $id = (string)($payload['id'] ?? '');
            if ($id === $user['id']) throw new Exception('ลบบัญชีตนเองไม่ได้');

            $pdo->beginTransaction();
            try {
                $pdo->prepare("DELETE FROM sessions WHERE user_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }
            lrs_audit_log($user, 'user.delete', 'user', $id, []);
            respond_json(['ok' => true]);
            break;

        case 'user.reset_password':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'user.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');
            $id = (string)($payload['id'] ?? '');
            
            $tempPw = 'L' . substr(bin2hex(random_bytes(4)), 0, 6) . mt_rand(10, 99);
            $salt = lrs_salt();
            $hash = lrs_sec_hash($tempPw, $salt, 1200);

            $pdo->beginTransaction();
            try {
                $pdo->prepare("UPDATE users SET salt = ?, password_hash = ?, pbkdf2_iter = 1200, updated_at = ? WHERE id = ?")->execute([$salt, $hash, lrs_now(), $id]);
                $pdo->prepare("DELETE FROM sessions WHERE user_id = ?")->execute([$id]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_audit_log($user, 'user.reset_password', 'user', $id, []);
            respond_json(['ok' => true, 'temp_password' => $tempPw]);
            break;

        case 'user.set_active':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'user.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');
            $id = (string)($payload['id'] ?? '');
            $active = ($payload['active'] ?? true) ? 'yes' : 'no';

            if ($id === $user['id'] && $active === 'no') throw new Exception('ปิดบัญชีตนเองไม่ได้');

            $pdo->beginTransaction();
            try {
                $pdo->prepare("UPDATE users SET is_active = ?, updated_at = ? WHERE id = ?")->execute([$active, lrs_now(), $id]);
                if ($active === 'no') {
                    $pdo->prepare("DELETE FROM sessions WHERE user_id = ?")->execute([$id]);
                }
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_audit_log($user, 'user.set_active', 'user', $id, ['active' => $active]);
            respond_json(['ok' => true]);
            break;

        case 'report.dashboard':
            $user = lrs_verify_token($token);
            $canViewAll = lrs_has_cap($user['role'], 'request.view_all');

            $byStatus = ['draft' => 0, 'submitted' => 0, 'reviewed' => 0, 'approved' => 0, 'rejected' => 0, 'cancelled' => 0];
            $byType = ['leave' => 0, 'vacation' => 0, 'cancel' => 0, 'resign' => 0, 'idcard' => 0];
            $byKind = ['sick' => 0, 'personal' => 0, 'maternity' => 0];
            $trend = [];
            $recent = [];
            $pending = 0; $total = 0; $daysApproved = 0;

            $sql = "SELECT r.*, u.prefix as owner_prefix, u.full_name as owner_fullname FROM requests r LEFT JOIN users u ON r.user_id = u.id";
            if (!$canViewAll) {
                $sql .= " WHERE r.user_id = " . $pdo->quote($user['id']);
            }
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();

            foreach ($rows as $r) {
                $total++;
                if (isset($byStatus[$r['status']])) $byStatus[$r['status']]++;
                if (isset($byType[$r['req_type']])) $byType[$r['req_type']]++;

                if ($r['req_type'] === 'leave' && isset($byKind[$r['leave_kind']])) {
                    $byKind[$r['leave_kind']] += floatval($r['days']);
                }

                if ($r['status'] === 'submitted' || $r['status'] === 'reviewed') $pending++;
                if ($r['status'] === 'approved') $daysApproved += floatval($r['days']);

                $mo = substr(lrs_date_only($r['created_at']), 0, 7);
                if ($mo) $trend[$mo] = ($trend[$mo] ?? 0) + 1;

                $recent[] = [
                    'id' => $r['id'],
                    'doc_no' => $r['doc_no'],
                    'req_type' => $r['req_type'],
                    'user_id' => $r['user_id'],
                    'status' => $r['status'],
                    'subject' => $r['subject'],
                    'leave_kind' => $r['leave_kind'],
                    'start_date' => lrs_date_only($r['start_date']),
                    'end_date' => lrs_date_only($r['end_date']),
                    'days' => floatval($r['days']),
                    'doc_date' => lrs_date_only($r['doc_date']),
                    'created_at' => $r['created_at'],
                    'updated_at' => $r['updated_at'],
                    'owner_name' => trim(($r['owner_prefix'] ?? '') . ' ' . ($r['owner_fullname'] ?? '')) ?: '-'
                ];
            }

            usort($recent, function($a, $b) {
                return strcmp($b['created_at'], $a['created_at']);
            });
            $recent = array_slice($recent, 0, 8);

            $trendArr = [];
            $thMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            for ($i = 5; $i >= 0; $i--) {
                $time = strtotime("-$i month");
                $kk = date('Y-m', $time);
                $mIdx = intval(date('n', $time)) - 1;
                $beShort = substr(strval(intval(date('Y', $time)) + 543), -2);
                $label = $thMonths[$mIdx] . ' ' . $beShort;
                $trendArr[] = ['month' => $kk, 'label' => $label, 'count' => $trend[$kk] ?? 0];
            }

            respond_json([
                'total' => $total,
                'pending' => $pending,
                'days_approved' => $daysApproved,
                'by_status' => $byStatus,
                'by_type' => $byType,
                'by_kind' => $byKind,
                'trend' => $trendArr,
                'recent' => $recent
            ]);
            break;

        case 'report.summary':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'report.view_all|report.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');

            $year = (string)($payload['year'] ?? 'all');
            $byUser = [];
            $byDept = [];
            $byType = ['leave' => 0, 'vacation' => 0, 'cancel' => 0, 'resign' => 0, 'idcard' => 0];
            $byMonth = [];
            $totalDays = 0; $totalDocs = 0; $approvedDocs = 0;

            $stmtUsers = $pdo->query("SELECT id, prefix, full_name, department FROM users");
            $usersMap = [];
            while ($u = $stmtUsers->fetch()) {
                $usersMap[$u['id']] = $u;
            }

            $stmtReqs = $pdo->query("SELECT * FROM requests");
            while ($r = $stmtReqs->fetch()) {
                if ($year !== 'all') {
                    $be = strval(intval(substr(lrs_date_only($r['created_at']), 0, 4)) + 543);
                    if ($be !== $year) continue;
                }

                $totalDocs++;
                if (isset($byType[$r['req_type']])) $byType[$r['req_type']]++;

                $mo = substr(lrs_date_only($r['created_at']), 0, 7);
                if ($mo) $byMonth[$mo] = ($byMonth[$mo] ?? 0) + 1;

                if ($r['status'] === 'approved') {
                    $approvedDocs++;
                    $totalDays += floatval($r['days']);
                }

                $u = $usersMap[$r['user_id']] ?? null;
                $uid = $r['user_id'];
                if (!isset($byUser[$uid])) {
                    $byUser[$uid] = [
                        'name' => $u ? trim(($u['prefix'] ?? '') . ' ' . ($u['full_name'] ?? '')) : '-',
                        'dept' => $u['department'] ?? '',
                        'docs' => 0,
                        'days' => 0.0,
                        'approved' => 0
                    ];
                }
                $byUser[$uid]['docs']++;
                if ($r['status'] === 'approved') {
                    $byUser[$uid]['days'] += floatval($r['days']);
                    $byUser[$uid]['approved']++;
                }

                $dept = ($u && $u['department']) ? $u['department'] : 'อื่น ๆ';
                $byDept[$dept] = ($byDept[$dept] ?? 0) + 1;
            }

            $usersList = array_values($byUser);
            usort($usersList, function($a, $b) { return $b['docs'] - $a['docs']; });

            $deptsList = [];
            foreach ($byDept as $k => $v) {
                $deptsList[] = ['name' => $k, 'count' => $v];
            }
            usort($deptsList, function($a, $b) { return $b['count'] - $a['count']; });

            $monthsList = [];
            $thMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            for ($i = 11; $i >= 0; $i--) {
                $time = strtotime("-$i month");
                $kk = date('Y-m', $time);
                $mIdx = intval(date('n', $time)) - 1;
                $monthsList[] = ['month' => $kk, 'label' => $thMonths[$mIdx], 'count' => $byMonth[$kk] ?? 0];
            }

            respond_json([
                'total_docs' => $totalDocs,
                'approved_docs' => $approvedDocs,
                'total_days' => $totalDays,
                'by_type' => $byType,
                'users' => $usersList,
                'depts' => $deptsList,
                'months' => $monthsList,
                'generated_at' => lrs_now()
            ]);
            break;

        case 'report.leave_summary':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'report.view_all|report.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');

            $fy = lrs_cur_fy();
            $quotas = lrs_leave_quotas();

            $stmtTeachers = $pdo->query("SELECT id, prefix, full_name, department FROM users WHERE is_active = 'yes' AND role = 'teacher'");
            $teachers = $stmtTeachers->fetchAll();

            $acc = [];
            foreach ($teachers as $u) {
                $acc[$u['id']] = [
                    'id' => $u['id'],
                    'name' => trim(($u['prefix'] ?? '') . ' ' . ($u['full_name'] ?? '')),
                    'dept' => $u['department'] ?? '',
                    'kinds' => [
                        'sick' => ['count' => 0, 'days' => 0.0],
                        'personal' => ['count' => 0, 'days' => 0.0],
                        'vacation' => ['count' => 0, 'days' => 0.0],
                        'maternity' => ['count' => 0, 'days' => 0.0]
                    ]
                ];
            }

            $stmtReqs = $pdo->query("SELECT * FROM requests WHERE status = 'approved'");
            while ($r = $stmtReqs->fetch()) {
                $uid = $r['user_id'];
                if (!isset($acc[$uid])) continue;

                $key = null;
                if ($r['req_type'] === 'leave') $key = $r['leave_kind'];
                else if ($r['req_type'] === 'vacation') $key = 'vacation';

                if (!$key || !isset($acc[$uid]['kinds'][$key])) continue;
                if (lrs_fy_of($r['start_date'] ?: $r['doc_date']) !== $fy) continue;

                $acc[$uid]['kinds'][$key]['count']++;
                $acc[$uid]['kinds'][$key]['days'] += floatval($r['days']);
            }

            $kindsList = [
                ['key' => 'sick', 'label' => 'ลาป่วย'],
                ['key' => 'personal', 'label' => 'ลากิจส่วนตัว'],
                ['key' => 'vacation', 'label' => 'ลาพักผ่อน'],
                ['key' => 'maternity', 'label' => 'ลาคลอดบุตร']
            ];

            $rows = [];
            foreach ($acc as $uid => $data) {
                $totalDays = 0; $totalCount = 0;
                $hasAlert = false;
                $userKinds = [];

                foreach ($kindsList as $k) {
                    $used = $data['kinds'][$k['key']];
                    $quota = $quotas[$k['key']] ?? 0;
                    $pct = $quota > 0 ? round(($used['days'] / $quota) * 100) : 0;
                    $near = ($quota > 0 && $pct >= $quotas['_warn'] && $pct < 100);
                    $over = ($quota > 0 && $used['days'] > $quota);
                    if ($near || $over) $hasAlert = true;

                    $totalDays += $used['days'];
                    $totalCount += $used['count'];

                    $userKinds[] = [
                        'key' => $k['key'],
                        'label' => $k['label'],
                        'count' => $used['count'],
                        'days' => $used['days'],
                        'quota' => $quota,
                        'pct' => $pct,
                        'near' => $near,
                        'over' => $over
                    ];
                }

                $rows[] = [
                    'id' => $uid,
                    'name' => $data['name'],
                    'dept' => $data['dept'],
                    'total_days' => $totalDays,
                    'total_count' => $totalCount,
                    'kinds' => $userKinds,
                    'alert' => $hasAlert
                ];
            }

            usort($rows, function($a, $b) { return $b['total_days'] - $a['total_days']; });

            respond_json([
                'fiscal_year' => $fy,
                'warn_pct' => $quotas['_warn'],
                'quotas' => $quotas,
                'kinds' => $kindsList,
                'rows' => $rows,
                'generated_at' => lrs_now()
            ]);
            break;

        case 'setting.get':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'setting.read|setting.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');

            $stmt = $pdo->query("SELECT key, value FROM settings");
            $out = [];
            while ($row = $stmt->fetch()) {
                $out[$row['key']] = $row['value'];
            }
            respond_json($out);
            break;

        case 'setting.update':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'setting.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');

            $now = lrs_now();
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("REPLACE INTO settings (key, value, updated_at) VALUES (?, ?, ?)");
                foreach ($payload as $k => $v) {
                    $stmt->execute([$k, mb_substr((string)$v, 0, 2000), $now]);
                }
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_audit_log($user, 'setting.update', 'setting', '', ['keys' => array_keys($payload)]);
            respond_json(['ok' => true]);
            break;

        case 'setting.backup_db':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'setting.manage')) throw new Exception('ไม่มีสิทธิ์ดาวน์โหลดไฟล์สำรอง');
            $dbFile = __DIR__ . '/database.db';
            if (!file_exists($dbFile)) throw new Exception('ไม่พบไฟล์ฐานข้อมูล');
            $data = file_get_contents($dbFile);
            $filename = 'LRS_Backup_' . date('Ymd_His') . '.db';
            lrs_audit_log($user, 'setting.backup_db', 'database', $filename, []);
            respond_json([
                'filename' => $filename,
                'data_b64' => base64_encode($data),
                'size' => filesize($dbFile)
            ]);
            break;

        case 'setting.test_email':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'setting.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');
            $toEmail = trim((string)($payload['test_email'] ?? $user['email']));
            if (!$toEmail) throw new Exception('กรุณาระบุอีเมลรับการทดสอบ');

            $html = '<p>สวัสดีครับคุณ ' . htmlspecialchars($user['full_name']) . '</p>'
              . '<p>นี่คืออีเมลทดสอบการแจ้งเตือนจาก <b>ระบบเอกสารใบลาราชการ (LRS)</b></p>'
              . '<p>หากคุณได้รับอีเมลฉบับนี้ แสดงว่าการตั้งค่าระบบส่งอีเมล (Email & SMTP Notification Engine) ทำงานถูกต้องสมบูรณ์ 100%!</p>';

            $ok = lrs_send_email($toEmail, '📧 ทดสอบส่งอีเมลแจ้งเตือนจากระบบใบลาราชการ LRS', $html);
            if ($ok) {
                respond_json(['ok' => true, 'message' => "ส่งอีเมลทดสอบไปยัง {$toEmail} เรียบร้อยแล้ว"]);
            } else {
                throw new Exception("ไม่สามารถส่งอีเมลไปยัง {$toEmail} ได้ กรุณาตรวจสอบการตั้งค่า SMTP");
            }
            break;

        case 'profile.update':
            $user = lrs_verify_token($token);
            $fullName = trim((string)($payload['full_name'] ?? ''));
            if (!$fullName) throw new Exception('กรุณากรอกชื่อ-สกุล');

            $now = lrs_now();
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE users SET 
                    prefix = ?, full_name = ?, position = ?, academic_rank = ?, department = ?, email = ?, phone = ?, id_card = ?, birth_date = ?, blood_group = ?, address = ?, start_gov_date = ?, avatar_url = ?, signature_url = ?, line_user_id = ?, updated_at = ? 
                    WHERE id = ?");

                $stmt->execute([
                    mb_substr((string)($payload['prefix'] ?? ''), 0, 30),
                    mb_substr($fullName, 0, 120),
                    mb_substr((string)($payload['position'] ?? ''), 0, 100),
                    mb_substr((string)($payload['academic_rank'] ?? ''), 0, 60),
                    mb_substr((string)($payload['department'] ?? ''), 0, 120),
                    mb_substr((string)($payload['email'] ?? ''), 0, 120),
                    mb_substr((string)($payload['phone'] ?? ''), 0, 30),
                    mb_substr((string)($payload['id_card'] ?? ''), 0, 30),
                    lrs_date_only($payload['birth_date'] ?? null),
                    mb_substr((string)($payload['blood_group'] ?? ''), 0, 5),
                    mb_substr((string)($payload['address'] ?? ''), 0, 300),
                    lrs_date_only($payload['start_gov_date'] ?? null),
                    (string)($payload['avatar_url'] ?? ''),
                    (string)($payload['signature_url'] ?? ''),
                    mb_substr((string)($payload['line_user_id'] ?? ''), 0, 100),
                    $now,
                    $user['id']
                ]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            $stmtFetch = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmtFetch->execute([$user['id']]);
            $updatedUser = $stmtFetch->fetch();

            lrs_audit_log($user, 'profile.update', 'user', $user['id'], []);
            respond_json(['ok' => true, 'user' => lrs_public_user($updatedUser)]);
            break;

        case 'profile.change_password':
            $user = lrs_verify_token($token);
            $cur = (string)($payload['current'] ?? '');
            $nw = (string)($payload['newpass'] ?? '');

            if (!lrs_sec_verify($cur, $user['salt'], $user['password_hash'])) {
                throw new Exception('รหัสผ่านเดิมไม่ถูกต้อง');
            }
            if (strlen($nw) < 6) throw new Exception('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');

            $newSalt = lrs_salt();
            $newHash = lrs_sec_hash($nw, $newSalt, 1200);

            $pdo->beginTransaction();
            try {
                $pdo->prepare("UPDATE users SET salt = ?, password_hash = ?, pbkdf2_iter = 1200, updated_at = ? WHERE id = ?")->execute([$newSalt, $newHash, lrs_now(), $user['id']]);
                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $e;
            }

            lrs_audit_log($user, 'profile.change_password', 'user', $user['id'], []);
            respond_json(['ok' => true]);
            break;

        case 'file.upload':
            $user = lrs_verify_token($token);
            $b64 = (string)($payload['data'] ?? '');
            $folder = (string)($payload['folder'] ?? 'branding');

            if (!$b64) throw new Exception('ไม่มีข้อมูลไฟล์');
            if (preg_match('/^data:image\/(png|jpeg|jpg|gif|webp);base64,/', $b64, $m)) {
                $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $cleanB64 = preg_replace('/^data:[^;]+;base64,/', '', $b64);
                $binary = base64_decode($cleanB64);

                if (strlen($binary) > 5 * 1024 * 1024) throw new Exception('ไฟล์ใหญ่เกิน 5MB');

                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $filename = $folder . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                $filePath = $uploadDir . $filename;
                file_put_contents($filePath, $binary);

                $url = 'uploads/' . $filename;
                lrs_audit_log($user, 'file.upload', 'file', $filename, ['folder' => $folder]);

                respond_json(['ok' => true, 'url' => $url, 'id' => $filename]);
            } else {
                throw new Exception('รองรับเฉพาะไฟล์ภาพ JPG/PNG/GIF/WebP');
            }
            break;

        case 'notify.list':
            $user = lrs_verify_token($token);
            $stmt = $pdo->prepare("SELECT * FROM notifications WHERE target = ? ORDER BY created_at DESC LIMIT 30");
            $stmt->execute([$user['id']]);
            $rows = $stmt->fetchAll();

            $unread = 0;
            $items = array_map(function($r) use (&$unread) {
                if ($r['is_read'] !== 'yes') $unread++;
                return [
                    'id' => $r['id'],
                    'ntype' => $r['ntype'],
                    'title' => $r['title'],
                    'message' => $r['message'],
                    'link' => $r['link'],
                    'is_read' => $r['is_read'],
                    'created_at' => $r['created_at']
                ];
            }, $rows);

            respond_json(['items' => $items, 'unread' => $unread]);
            break;

        case 'notify.mark_read':
            $user = lrs_verify_token($token);
            $id = (string)($payload['id'] ?? '');
            if ($id) {
                $pdo->prepare("UPDATE notifications SET is_read = 'yes' WHERE id = ? AND target = ?")->execute([$id, $user['id']]);
            }
            respond_json(['ok' => true]);
            break;

        case 'notify.mark_all_read':
            $user = lrs_verify_token($token);
            $pdo->prepare("UPDATE notifications SET is_read = 'yes' WHERE target = ?")->execute([$user['id']]);
            respond_json(['ok' => true]);
            break;



        case 'user.test_line':
            $user = lrs_verify_token($token);
            if (!lrs_has_cap($user['role'], 'user.manage|setting.manage')) throw new Exception('ไม่มีสิทธิ์ใช้งาน');
            $targetUserId = trim((string)($payload['line_user_id'] ?? ''));
            $targetName = trim((string)($payload['full_name'] ?? 'ผู้ใช้'));

            if (!$targetUserId) throw new Exception('ผู้ใช้คนนี้ยังไม่ได้ระบุ LINE User ID');

            $ok = lrs_line_notify(
                "🔔 ทดสอบการแจ้งเตือนส่วนบุคคลถึงคุณ {$targetName}\nเชื่อมต่อกับระบบเอกสารใบลาราชการ (LRS) สำเร็จ 100%!",
                '🔔 ทดสอบส่ง LINE ส่วนบุคคล',
                '🟢 เปิดเข้าระบบ LRS',
                'index.php#/my',
                $targetUserId
            );

            if ($ok) {
                respond_json(['ok' => true, 'message' => "ส่งข้อความทดสอบตรงถึง {$targetName} ผ่าน LINE เรียบร้อยแล้ว"]);
            } else {
                throw new Exception("ไม่สามารถส่ง LINE หา {$targetName} ได้ กรุณาเช็ค LINE User ID");
            }
            break;

        default:
            throw new Exception("ไม่พบ action: $action");
    }
} catch (Exception $e) {
    respond_json(null, false, $e->getMessage());
}
}
