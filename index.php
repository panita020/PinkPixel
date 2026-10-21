<?php
require_once 'config.php';

// ===================================================================
// ส่วนที่ 1: ประกาศตัวแปรและเก็บข้อมูลภาพรวม (ทีม PinkPixel)
// ===================================================================
$clubName    = 'ชมรมเครือข่ายคอมพิวเตอร์'; // string (ชื่อชมรม)
$teamName    = 'PinkPixel';               // string (ชื่อทีมผู้พัฒนา)
$studentId   = '66309010001';             // string (รหัสนักศึกษา)
$studentName = 'สมชาย ใจดี';               // string (ชื่อสมาชิกชมรม)
$userRole    = 'นักเรียน (สมาชิกชมรม)';    // string (สิทธิ์การใช้งาน)

// สถิติการเช็คชื่อเข้าร่วมกิจกรรมชมรม (จำนวนครั้ง)
$attended    = 12;                         // int (มา)
$absent      = 2;                          // int (ขาด)
$leave       = 1;                          // int (ลา)

// ===================================================================
// ส่วนที่ 2: การคำนวณตัวเลขภาพรวมสถิติ
// ===================================================================
// จำนวนกิจกรรมชมรมทั้งหมดที่จัดขึ้น
$total = $attended + $absent + $leave; 

// คำนวณเปอร์เซ็นต์การเข้าร่วมชมรม (ป้องกันการหารด้วยศูนย์ Division by Zero)
$attendPercent = $total > 0 ? round(($attended / $total) * 100, 1) : 0; // float (%)

// รายการบันทึกเช็คชื่อล่าสุด 1 รายการ
$lastActivity = 'กิจกรรมเช็คชื่อประจำสัปดาห์ และอบรมระบบเครือข่ายเบื้องต้น';
$lastDate     = '2026-10-21';
$lastStatus   = 'มา';

// ===================================================================
// ส่วนที่ 3: เงื่อนไขการตัดสินใจและการแจ้งเตือนสถานะการเป็นสมาชิก
// ===================================================================
// ตรวจสอบเงื่อนไขการขาดเกินกำหนด หรือ เปอร์เซ็นต์ต่ำกว่าเกณฑ์ขั้นต่ำ
$isLateOrAbsent = ($absent >= MAX_ABSENT) || ($attendPercent < MIN_ATTEND_PERCENT);
$statusText     = $isLateOrAbsent 
    ? '⚠️ เสี่ยงตกกิจกรรมชมรม (ขาด/ลา เกินกำหนด หรือ % การเข้าร่วมต่ำกว่าเกณฑ์ 80%)' 
    : '✅ สภาพการเป็นสมาชิกปกติ (ผ่านเกณฑ์การเข้าร่วมกิจกรรมชมรม)';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    
    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Sarabun', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            padding: 24px;
            min-height: 100vh;
        }

        .container {
            max-width: 1050px;
            margin: 0 auto;
        }

        /* Header Card */
        .header-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            padding: 28px 32px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.3);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .header-title p {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }

        .user-badge {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 14px;
        }

        /* Stat Grid Cards (4 Boxes) */
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .metric-card {
            background: var(--card-bg);
            padding: 20px 24px;
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .metric-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .metric-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .metric-value .unit {
            font-size: 14px;
            font-weight: 400;
            color: var(--text-muted);
        }

        /* Color Borders */
        .metric-card.total { border-left: 5px solid var(--primary); }
        .metric-card.attended { border-left: 5px solid var(--success); }
        .metric-card.attended .metric-value { color: var(--success); }
        .metric-card.absent { border-left: 5px solid var(--danger); }
        .metric-card.absent .metric-value { color: var(--danger); }
        .metric-card.percent { border-left: 5px solid var(--warning); }
        .metric-card.percent.danger-bg { border-left: 5px solid var(--danger); background-color: #fef2f2; }
        .metric-card.percent.danger-bg .metric-value { color: var(--danger); }

        /* Content Layout Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .main-grid { grid-template-columns: 1fr; }
        }

        .section-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        .section-header {
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
        }

        .recent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid #f3f4f6;
        }

        .activity-name {
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .activity-meta {
            font-size: 13px;
            color: var(--text-muted);
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Alert Status Card */
        .alert-card {
            padding: 20px;
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .alert-card.normal {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-card.danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-icon {
            font-size: 24px;
            line-height: 1;
        }

        .alert-text h4 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .alert-text p {
            font-size: 13px;
            line-height: 1.5;
            opacity: 0.9;
        }

        /* Footer Link */
        .footer-nav {
            margin-top: 24px;
            text-align: center;
        }

        .btn-about {
            display: inline-block;
            padding: 10px 22px;
            background: #ffffff;
            color: var(--primary);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .btn-about:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Header Section -->
    <header class="header-card">
        <div class="header-title">
            <h1><?php echo APP_NAME; ?></h1>
            <p>ชมรม: <b><?php echo $clubName; ?></b> | ทีมพัฒนา: <b><?php echo $teamName; ?></b> | วันที่: <?php echo date('d/m/Y'); ?></p>
        </div>
        <div class="user-badge">
            👤 <b><?php echo $studentName; ?></b> (รหัส: <?php echo $studentId; ?>)<br>
            <span style="font-size: 12px; opacity: 0.85;"><?php echo $userRole; ?></span>
        </div>
    </header>

    <!-- 4 กล่องสถิติภาพรวม (Metric Cards) -->
    <div class="grid-cards">
        <div class="metric-card total">
            <div class="metric-label">กิจกรรมทั้งหมด</div>
            <div class="metric-value"><?php echo $total; ?> <span class="unit">ครั้ง</span></div>
        </div>

        <div class="metric-card attended">
            <div class="metric-label">เข้าร่วม (มา)</div>
            <div class="metric-value"><?php echo $attended; ?> <span class="unit">ครั้ง</span></div>
        </div>

        <div class="metric-card absent">
            <div class="metric-label">ขาดกิจกรรม</div>
            <div class="metric-value"><?php echo $absent; ?> <span class="unit">ครั้ง</span></div>
        </div>

        <div class="metric-card percent <?php echo $isLateOrAbsent ? 'danger-bg' : ''; ?>">
            <div class="metric-label">อัตราการเข้าร่วม</div>
            <div class="metric-value"><?php echo $attendPercent; ?><span class="unit">%</span></div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="main-grid">
        
        <!-- รายการบันทึกเช็คชื่อล่าสุด (1 รายการ) -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">📌 บันทึกการเข้าร่วมกิจกรรมล่าสุด</div>
            </div>

            <div class="recent-item">
                <div>
                    <div class="activity-name"><?php echo $lastActivity; ?></div>
                    <div class="activity-meta">วันที่บันทึก: <?php echo $lastDate; ?> | ลาสะสม: <?php echo $leave; ?> ครั้ง</div>
                </div>
                <div>
                    <span class="status-badge">✓ <?php echo $lastStatus; ?></span>
                </div>
            </div>
        </div>

        <!-- จุดแจ้งเตือนสถานะการเข้าร่วม (1 จุด) -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">🔔 สรุปสถานะการเข้าร่วมชมรม</div>
            </div>

            <div class="alert-card <?php echo $isLateOrAbsent ? 'danger' : 'normal'; ?>">
                <div class="alert-icon"><?php echo $isLateOrAbsent ? '⚠️' : '✅'; ?></div>
                <div class="alert-text">
                    <h4><?php echo $isLateOrAbsent ? 'ต้องติดตามใกล้ชิด' : 'ผ่านเกณฑ์ปกติ'; ?></h4>
                    <p><?php echo $statusText; ?></p>
                </div>
            </div>
        </div>

    </div>

    <!-- ลิงก์ไปหน้าเกี่ยวกับโครงงาน -->
    <div class="footer-nav">
        <a href="about.php" class="btn-about">ℹ️ เกี่ยวกับโครงงาน (About Project)</a>
    </div>

</div>

</body>
</html>