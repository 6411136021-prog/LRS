<?php
// c:\xampp\htdocs\LRS\db.php
// ระบบฐานข้อมูล LRS (ระบบเอกสารใบลาราชการ สพฐ.) - Enterprise Class SQLite PDO Engine

date_default_timezone_set('Asia/Bangkok');

$db_file = __DIR__ . '/database.db';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Performance & Stability PRAGMAs (Enterprise Class Tuning)
    $pdo->exec("PRAGMA foreign_keys = ON;");
    $pdo->exec("PRAGMA journal_mode = WAL;");        // Write-Ahead Logging for maximum concurrency
    $pdo->exec("PRAGMA busy_timeout = 10000;");      // Wait up to 10s if DB is locked
    $pdo->exec("PRAGMA synchronous = NORMAL;");      // Optimal performance & durability balance
    $pdo->exec("PRAGMA cache_size = -64000;");       // 64MB high-speed memory cache
    $pdo->exec("PRAGMA temp_store = MEMORY;");       // Store temp tables and sorters in RAM
    $pdo->exec("PRAGMA mmap_size = 268435456;");     // 256MB Memory-mapped file I/O for instant reads

    // 1. ตาราง users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id TEXT PRIMARY KEY,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        salt TEXT NOT NULL,
        pbkdf2_iter INTEGER DEFAULT 1200,
        prefix TEXT DEFAULT '',
        full_name TEXT NOT NULL,
        position TEXT DEFAULT '',
        academic_rank TEXT DEFAULT '',
        department TEXT DEFAULT '',
        email TEXT DEFAULT '',
        phone TEXT DEFAULT '',
        role TEXT NOT NULL CHECK(role IN ('admin', 'director', 'clerk', 'teacher')),
        avatar_url TEXT DEFAULT '',
        signature_url TEXT DEFAULT '',
        id_card TEXT DEFAULT '',
        birth_date TEXT DEFAULT '',
        blood_group TEXT DEFAULT '',
        address TEXT DEFAULT '',
        start_gov_date TEXT DEFAULT '',
        line_user_id TEXT DEFAULT '',
        is_active TEXT DEFAULT 'yes',
        created_at TEXT NOT NULL,
        updated_at TEXT DEFAULT ''
    )");

    // Safe migration: Add line_user_id column if missing
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN line_user_id TEXT DEFAULT '';");
    } catch (Exception $e) {}

    // 2. ตาราง requests
    $pdo->exec("CREATE TABLE IF NOT EXISTS requests (
        id TEXT PRIMARY KEY,
        doc_no TEXT UNIQUE NOT NULL,
        req_type TEXT NOT NULL CHECK(req_type IN ('leave', 'vacation', 'cancel', 'resign', 'idcard')),
        user_id TEXT NOT NULL,
        status TEXT NOT NULL CHECK(status IN ('draft', 'submitted', 'reviewed', 'approved', 'rejected', 'cancelled')),
        place TEXT DEFAULT '',
        doc_date TEXT DEFAULT '',
        subject TEXT DEFAULT '',
        to_person TEXT DEFAULT '',
        reason TEXT DEFAULT '',
        start_date TEXT DEFAULT '',
        end_date TEXT DEFAULT '',
        days REAL DEFAULT 0,
        contact_addr TEXT DEFAULT '',
        contact_phone TEXT DEFAULT '',
        leave_kind TEXT DEFAULT '',
        last_kind TEXT DEFAULT '',
        last_start TEXT DEFAULT '',
        last_end TEXT DEFAULT '',
        last_days REAL DEFAULT 0,
        accum_days REAL DEFAULT 0,
        right_days REAL DEFAULT 0,
        total_right REAL DEFAULT 0,
        orig_leave TEXT DEFAULT '',
        orig_start TEXT DEFAULT '',
        orig_end TEXT DEFAULT '',
        orig_days REAL DEFAULT 0,
        cancel_days REAL DEFAULT 0,
        cancel_start TEXT DEFAULT '',
        cancel_end TEXT DEFAULT '',
        start_gov_date TEXT DEFAULT '',
        position_now TEXT DEFAULT '',
        dept_section TEXT DEFAULT '',
        division TEXT DEFAULT '',
        dept_gov TEXT DEFAULT '',
        salary_level TEXT DEFAULT '',
        salary_step TEXT DEFAULT '',
        salary_amount TEXT DEFAULT '',
        resign_reason TEXT DEFAULT '',
        resign_date TEXT DEFAULT '',
        idc_case TEXT DEFAULT '',
        idc_reason TEXT DEFAULT '',
        idc_old_no TEXT DEFAULT '',
        photo_url TEXT DEFAULT '',
        stat_prev TEXT DEFAULT '{}',
        stat_this TEXT DEFAULT '{}',
        stat_total TEXT DEFAULT '{}',
        reviewer_id TEXT DEFAULT '',
        reviewer_note TEXT DEFAULT '',
        reviewed_at TEXT DEFAULT '',
        approver_id TEXT DEFAULT '',
        approver_note TEXT DEFAULT '',
        approver_decision TEXT DEFAULT '',
        approved_at TEXT DEFAULT '',
        created_at TEXT NOT NULL,
        created_by TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        updated_by TEXT NOT NULL,
        rev INTEGER DEFAULT 1,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 3. ตาราง settings
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT DEFAULT '',
        updated_at TEXT NOT NULL
    )");

    // 4. ตาราง sessions
    $pdo->exec("CREATE TABLE IF NOT EXISTS sessions (
        token TEXT PRIMARY KEY,
        user_id TEXT NOT NULL,
        created_at TEXT NOT NULL,
        expires_at TEXT NOT NULL,
        user_agent TEXT DEFAULT '',
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 5. ตาราง audit
    $pdo->exec("CREATE TABLE IF NOT EXISTS audit (
        id TEXT PRIMARY KEY,
        ts TEXT NOT NULL,
        user_id TEXT DEFAULT '',
        username TEXT DEFAULT '',
        action TEXT NOT NULL,
        target_type TEXT DEFAULT '',
        target_id TEXT DEFAULT '',
        meta TEXT DEFAULT '{}'
    )");

    // 6. ตาราง notifications
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id TEXT PRIMARY KEY,
        target TEXT NOT NULL,
        ntype TEXT DEFAULT 'info',
        title TEXT DEFAULT '',
        message TEXT DEFAULT '',
        link TEXT DEFAULT '',
        is_read TEXT DEFAULT 'no',
        created_at TEXT NOT NULL
    )");

    // 7. ตาราง password_resets
    $pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id TEXT PRIMARY KEY,
        user_id TEXT NOT NULL,
        token_hash TEXT NOT NULL,
        expires_at TEXT NOT NULL,
        used TEXT DEFAULT 'no',
        created_at TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 9. ตาราง holidays
    $pdo->exec("CREATE TABLE IF NOT EXISTS holidays (
        id TEXT PRIMARY KEY,
        event_date TEXT UNIQUE NOT NULL,
        title TEXT NOT NULL,
        year INTEGER NOT NULL
    )");

    // High-performance SQLite Compound Indexes for Maximum Speed
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_requests_user_status ON requests(user_id, status);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_requests_status_dates ON requests(status, start_date, end_date);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_requests_created ON requests(created_at DESC);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_sessions_user_exp ON sessions(user_id, expires_at);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_notifications_target_read ON notifications(target, is_read, created_at DESC);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_role_active ON users(role, is_active);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_attachments_request ON attachments(request_id);");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_holidays_date ON holidays(event_date);");

} catch (PDOException $e) {
    error_log("LRS Database Connection Failure: " . $e->getMessage());
    die("Database Connection Error. Please verify server environment.");
}

// -------------------------------------------------------------
// Helper Functions: Security, Formatting & Working Days Calculation
// -------------------------------------------------------------

function lrs_calc_working_days($startDate, $endDate) {
    global $pdo;
    if (!$startDate || !$endDate) return 0;
    
    $startTs = strtotime($startDate);
    $endTs = strtotime($endDate);
    if (!$startTs || !$endTs || $startTs > $endTs) return 0;

    $holidays = [];
    try {
        $stmt = $pdo->prepare("SELECT event_date FROM holidays WHERE event_date >= ? AND event_date <= ?");
        $stmt->execute([$startDate, $endDate]);
        $holidays = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {}

    $workingDays = 0;
    $currTs = $startTs;
    while ($currTs <= $endTs) {
        $dayOfWeek = intval(date('w', $currTs)); // 0 = Sun, 6 = Sat
        $currDate = date('Y-m-d', $currTs);
        if ($dayOfWeek !== 0 && $dayOfWeek !== 6 && !in_array($currDate, $holidays)) {
            $workingDays++;
        }
        $currTs = strtotime('+1 day', $currTs);
    }
    return $workingDays;
}

function lrs_seed_holidays() {
    global $pdo;
    try {
        $count = intval($pdo->query("SELECT COUNT(*) FROM holidays")->fetchColumn());
        if ($count > 0) return;

        $list = [
            ['2026-01-01', 'วันขึ้นปีใหม่', 2026],
            ['2026-03-03', 'วันมาฆบูชา', 2026],
            ['2026-04-06', 'วันพระบาทสมเด็จพระพุทธยอดฟ้าจุฬาโลกมหาราช และวันที่ระลึกมหาจักรีบรมราชวงศ์', 2026],
            ['2026-04-13', 'วันสงกรานต์', 2026],
            ['2026-04-14', 'วันสงกรานต์', 2026],
            ['2026-04-15', 'วันสงกรานต์', 2026],
            ['2026-05-01', 'วันแรงงานแห่งชาติ', 2026],
            ['2026-05-04', 'วันฉัตรมงคล', 2026],
            ['2026-05-31', 'วันวิสาขบูชา', 2026],
            ['2026-06-03', 'วันเฉลิมพระชนมพรรษา สมเด็จพระนางเจ้าสุทิดา พัชรสุธาพิมลลักษณ พระบรมราชินี', 2026],
            ['2026-07-28', 'วันเฉลิมพระชนมพรรษา พระบาทสมเด็จพระปรเมนทรรามาธิบดีศรีสินทรมหาวชิราลงกรณ พระวชิรเกล้าเจ้าอยู่หัว', 2026],
            ['2026-07-29', 'วันอาสาฬหบูชา', 2026],
            ['2026-07-30', 'วันเข้าพรรษา', 2026],
            ['2026-08-12', 'วันเฉลิมพระชนมพรรษา สมเด็จพระบรมราชชนนีพันปีหลวง และวันแม่แห่งชาติ', 2026],
            ['2026-10-13', 'วันคล้ายวันสวรรคต พระบาทสมเด็จพระบรมชนกาธิเบศร มหาภูมิพลอดุลยเดชมหาราช บรมนาถบพิตร (วันนวมินทรมหาราช)', 2026],
            ['2026-10-23', 'วันปิยมหาราช', 2026],
            ['2026-12-05', 'วันคล้ายวันพระบรมราชสมภพ พระบาทสมเด็จพระบรมชนกาธิเบศร มหาภูมิพลอดุลยเดชมหาราช บรมนาถบพิตร (วันพ่อแห่งชาติ)', 2026],
            ['2026-12-10', 'วันรัฐธรรมนูญ', 2026],
            ['2026-12-31', 'วันสิ้นปี', 2026]
        ];

        $stmt = $pdo->prepare("INSERT OR IGNORE INTO holidays (id, event_date, title, year) VALUES (?, ?, ?, ?)");
        foreach ($list as $item) {
            $stmt->execute([lrs_uuid(), $item[0], $item[1], $item[2]]);
        }
    } catch (Exception $e) {}
}

lrs_seed_holidays();

function lrs_uuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function lrs_now() {
    return date('c');
}

function lrs_date_only($dateStr = null) {
    if (!$dateStr) return date('Y-m-d');
    $ts = strtotime($dateStr);
    return $ts ? date('Y-m-d', $ts) : date('Y-m-d');
}

function lrs_salt() {
    return substr(bin2hex(random_bytes(16)), 0, 22);
}

function lrs_pbkdf2($pw, $salt, $iter = 1200) {
    return hash_pbkdf2('sha256', $pw, 'LRS:' . $salt, $iter, 44);
}

function lrs_sec_hash($pw, $salt, $iter = 1200) {
    return 'p2$' . $iter . '$' . lrs_pbkdf2($pw, $salt, $iter);
}

function lrs_sec_verify($pw, $salt, $storedHash) {
    if (strpos($storedHash, 'p2$') === 0) {
        $parts = explode('$', $storedHash);
        $iter = intval($parts[1] ?? 1200);
        $calc = lrs_sec_hash($pw, $salt, $iter);
        return hash_equals($calc, $storedHash);
    }
    $legacy = hash('sha256', $salt . ':' . $pw);
    return hash_equals($legacy, $storedHash);
}

// -------------------------------------------------------------
// Seeding Default Settings & Users (Only Seed If Empty)
// -------------------------------------------------------------

function lrs_seed_defaults() {
    global $pdo;

    try {
        $userCount = intval($pdo->query("SELECT COUNT(*) FROM users")->fetchColumn());
        if ($userCount > 0) {
            return;
        }

        $pdo->beginTransaction();

        $defaults = [
            'org_name' => 'โรงเรียนปากพูน',
            'org_address' => 'ต.ปากพูน อ.เมือง จ.นครศรีธรรมราช',
            'org_phone' => '075-380-000',
            'sae_zone' => 'สำนักงานเขตพื้นที่การศึกษามัธยมศึกษานครศรีธรรมราช',
            'director_name' => 'นายวิริยะ วุฒิมานพ',
            'director_position' => 'ผู้อำนวยการโรงเรียนปากพูน',
            'sub_director_name' => 'นางสาวพรพรรณ ผลไชย',
            'sub_director_position' => 'รองผู้อำนวยการโรงเรียนปากพูน',
            'clerk_name' => 'นายธงชัย ศักดามาศ',
            'clerk_position' => 'หัวหน้ากลุ่มบริหารงานบุคคล',
            'app_url' => 'http://localhost/LRS',
            'theme' => 'macblue',
            'quota_sick' => '60',
            'quota_personal' => '45',
            'quota_vacation' => '10',
            'quota_maternity' => '90',
            'quota_warn_pct' => '80',
            'email_enabled' => 'yes',
            'email_mode' => 'smtp',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => '587',
            'smtp_secure' => 'tls',
            'smtp_user' => 'noreply@pakpoon.ac.th',
            'smtp_pass' => '',
            'sender_name' => 'ระบบใบลาราชการ โรงเรียนปากพูน',
            'sender_email' => 'noreply@pakpoon.ac.th',
            'enable_demo_login' => 'yes'
        ];

        $stmtIns = $pdo->prepare("REPLACE INTO settings (key, value, updated_at) VALUES (?, ?, ?)");
        $now = lrs_now();
        foreach ($defaults as $k => $v) {
            $stmtIns->execute([$k, $v, $now]);
        }

        $demoUsers = [
            ['username' => 'admin', 'role' => 'admin', 'prefix' => 'นาย', 'full_name' => 'ผู้ดูแล ระบบ', 'position' => 'ผู้ดูแลระบบ', 'academic_rank' => '', 'department' => 'ฝ่ายบริหาร'],
            ['username' => 'director', 'role' => 'director', 'prefix' => 'นาย', 'full_name' => 'วิริยะ วุฒิมานพ', 'position' => 'ผู้อำนวยการโรงเรียนปากพูน', 'academic_rank' => 'ชำนาญการพิเศษ', 'department' => 'ฝ่ายบริหาร'],
            ['username' => 'clerk', 'role' => 'clerk', 'prefix' => 'นาย', 'full_name' => 'ธงชัย ศักดามาศ', 'position' => 'หัวหน้ากลุ่มบริหารงานบุคคล', 'academic_rank' => 'ชำนาญการพิเศษ', 'department' => 'กลุ่มบริหารงานบุคคล'],
            ['username' => 'teacher', 'role' => 'teacher', 'prefix' => 'นาง', 'full_name' => 'ครูใจดี สอนเก่ง', 'position' => 'ครู', 'academic_rank' => 'ชำนาญการ', 'department' => 'กลุ่มสาระการเรียนรู้ภาษาไทย']
        ];

        $stmtUserIns = $pdo->prepare("INSERT INTO users 
            (id, username, password_hash, salt, pbkdf2_iter, prefix, full_name, position, academic_rank, department, email, phone, role, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 1200, ?, ?, ?, ?, ?, ?, '0800000000', ?, 'yes', ?, ?)");

        foreach ($demoUsers as $u) {
            $salt = lrs_salt();
            $hash = lrs_sec_hash('123456', $salt, 1200);
            $stmtUserIns->execute([
                lrs_uuid(),
                strtolower($u['username']),
                $hash,
                $salt,
                $u['prefix'],
                $u['full_name'],
                $u['position'],
                $u['academic_rank'],
                $u['department'],
                $u['username'] . '@pakpoon.ac.th',
                $u['role'],
                $now,
                $now
            ]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("LRS Seed Defaults Error: " . $e->getMessage());
    }
}

lrs_seed_defaults();
