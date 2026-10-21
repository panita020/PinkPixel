<?php
require_once 'config.php';

// ข้อมูลตัวอย่างสมาชิกและการเช็คชื่อกิจกรรมชมรม
$totalActivities = 20; // กิจกรรมทั้งหมดในเทอมนี้

$members = [
  ['code' => 'STD-001', 'name' => 'กรกนก', 'activity' => 'อบรมการเข้าหัวสาย LAN Cat6', 'date' => '2026-10-20', 'status' => 'attended', 'total_absent' => 0],
  ['code' => 'STD-002', 'name' => 'ปณิตา', 'activity' => 'สัมมนาความปลอดภัยบนเครือข่าย', 'date' => '2026-10-18', 'status' => 'absent', 'total_absent' => 3], // เสี่ยงพ้นสภาพ
  ['code' => 'STD-003', 'name' => 'ธนกร', 'activity' => 'จัดตั้งเซิร์ฟเวอร์ปฏิบัติการ', 'date' => '2026-10-15', 'status' => 'leave', 'total_absent' => 1],
  ['code' => 'STD-004', 'name' => 'สุชาดา', 'activity' => 'อบรมการเข้าหัวสาย LAN Cat6', 'date' => '2026-10-20', 'status' => 'attended', 'total_absent' => 0],
  ['code' => 'STD-005', 'name' => 'วรเมธ', 'activity' => 'สัมมนาความปลอดภัยบนเครือข่าย', 'date' => '2026-10-18', 'status' => 'attended', 'total_absent' => 2],
  ['code' => 'STD-006', 'name' => 'ภัทรา', 'activity' => 'จัดตั้งเซิร์ฟเวอร์ปฏิบัติการ', 'date' => '2026-10-15', 'status' => 'absent', 'total_absent' => 4], // สภาพเสี่ยงสูง
];

// คำนวณสถิติภาพรวมและระบบคะแนนพฤติกรรม (Activity Points)
$attendedCount = 0;
$absentCount = 0;
$leaveCount = 0;
$atRiskCount = 0; // ฟังก์ชันเสริม: สมาชิกกลุ่มเสี่ยงขาดเกินเกณฑ์

foreach ($members as $item) {
  if ($item['status'] === 'attended') {
    $attendedCount++;
  } elseif ($item['status'] === 'absent') {
    $absentCount++;
  } elseif ($item['status'] === 'leave') {
    $leaveCount++;
  }

  // คำนวณสมาชิกกลุ่มเสี่ยง
  if ($item['total_absent'] >= MAX_ABSENT) {
    $atRiskCount++;
  }
}

$totalCheckCount = count($members);
$attendPercent = $totalCheckCount > 0 ? round(($attendedCount / $totalCheckCount) * 100) : 0;

// คำนวณคะแนนพฤติกรรมเฉลี่ย (มา +10, ขาด -5, ลา 0)
$totalPoints = ($attendedCount * 10) - ($absentCount * 5);

$stats = [
  ['label' => 'กิจกรรมทั้งหมด', 'value' => $totalActivities, 'unit' => 'ครั้ง/เทอม', 'icon' => 'bi-calendar-event', 'tone' => 'blue'],
  ['label' => 'เข้าร่วม (มา)', 'value' => $attendedCount, 'unit' => "คน · {$attendPercent}%", 'icon' => 'bi-check-circle', 'tone' => 'mint'],
  ['label' => 'ขาดกิจกรรม', 'value' => $absentCount, 'unit' => "เสี่ยงติด มส. {$atRiskCount} คน", 'icon' => 'bi-x-circle', 'tone' => 'coral'],
  ['label' => 'คะแนนพฤติกรรมรวม', 'value' => number_format($totalPoints), 'unit' => 'แต้มกิจกรรม', 'icon' => 'bi-trophy', 'tone' => 'sky'],
];

$statusBadge = [
  'attended' => ['มาเข้าร่วม (+10)', 'badge-soft-mint'],
  'absent'   => ['ขาดกิจกรรม (-5)', 'badge-soft-coral'],
  'leave'    => ['ลากิจ/ลาป่วย (0)', 'badge-soft-blue'],
];

$menu = [
  ['icon' => 'bi-grid-1x2-fill', 'label' => 'แดชบอร์ด', 'href' => 'index.php', 'active' => true],
  ['icon' => 'bi-card-checklist', 'label' => 'เช็คชื่อกิจกรรม', 'href' => '#'],
  ['icon' => 'bi-people', 'label' => 'รายชื่อสมาชิก', 'href' => '#'],
  ['icon' => 'bi-award', 'label' => 'คะแนนพฤติกรรม', 'href' => '#'],
  ['icon' => 'bi-info-circle', 'label' => 'เกี่ยวกับ', 'href' => 'about.php'],
];

function e($s)
{
  return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>แดชบอร์ดเช็คชื่อชมรม · <?php echo e(APP_NAME); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --pp-bg: #f8fafc;
      --pp-surface: #ffffff;
      --pp-sidebar: #0f172a;
      --pp-sidebar-text: #94a3b8;
      --pp-sidebar-muted: #475569;
      --pp-border: #e2e8f0;
      --pp-text: #1e293b;
      --pp-muted: #64748b;
      --pp-primary: #3b82f6;
      --pp-coral: #ef4444;
      --pp-mint: #10b981;
      --sidebar-w: 260px;
    }

    body {
      font-family: 'Prompt', system-ui, -apple-system, sans-serif;
      background: var(--pp-bg);
      color: var(--pp-text);
      -webkit-font-smoothing: antialiased;
    }

    /* ---------- Sidebar ---------- */
    .sidebar {
      width: var(--sidebar-w);
      background-color: var(--pp-sidebar) !important;
      border-right: 1px solid rgba(255, 255, 255, 0.05);
      box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04);
    }

    @media (min-width: 992px) {
      .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
      }
      .main {
        margin-left: var(--sidebar-w);
      }
    }

    .brand {
      display: flex;
      align-items: center;
      gap: .85rem;
      padding: 1.5rem 1.25rem 1.25rem;
      color: #fff;
      text-decoration: none;
    }

    .brand-logo {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
      color: #fff;
      font-size: 1.25rem;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .brand-name {
      font-weight: 700;
      font-size: .95rem;
      line-height: 1.2;
    }

    .menu-title {
      font-size: .7rem;
      font-weight: 600;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--pp-sidebar-muted);
      padding: 1.25rem 1.5rem .5rem;
    }

    .side-link {
      display: flex;
      align-items: center;
      gap: .85rem;
      margin: .2rem .85rem;
      padding: .7rem 1rem;
      border-radius: 12px;
      color: var(--pp-sidebar-text);
      text-decoration: none;
      font-size: .92rem;
      transition: all .2s ease;
    }

    .side-link:hover {
      background: rgba(255, 255, 255, .06);
      color: #f8fafc;
      transform: translateX(3px);
    }

    .side-link.active {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: #fff;
      font-weight: 500;
      box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
    }

    /* ---------- Topbar ---------- */
    .topbar {
      position: sticky;
      top: 0;
      z-index: 1020;
      background: rgba(255, 255, 255, .82);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--pp-border);
    }

    .search {
      max-width: 320px;
    }

    .search .form-control {
      background: var(--pp-bg);
      border-color: var(--pp-border);
      border-radius: 0 10px 10px 0;
      font-size: .9rem;
    }

    .search .input-group-text {
      background: var(--pp-bg);
      border-color: var(--pp-border);
      border-radius: 10px 0 0 10px;
      color: var(--pp-muted);
    }

    .icon-btn {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      border: 1px solid var(--pp-border);
      background: var(--pp-surface);
      color: var(--pp-muted);
      position: relative;
      transition: all .15s;
    }

    .icon-btn:hover {
      background: #f1f5f9;
      color: var(--pp-text);
    }

    .icon-btn .dot {
      position: absolute;
      top: 9px;
      right: 10px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--pp-coral);
      border: 2px solid #fff;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
      color: #0284c7;
      font-weight: 700;
    }

    /* ---------- Cards ---------- */
    .panel {
      background: var(--pp-surface);
      border: 1px solid var(--pp-border);
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    .stat-card {
      padding: 1.35rem;
      height: 100%;
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.07);
    }

    .stat-icon {
      width: 52px;
      height: 52px;
      border-radius: 16px;
      display: grid;
      place-items: center;
      font-size: 1.45rem;
      flex-shrink: 0;
    }

    .stat-label {
      color: var(--pp-muted);
      font-size: .88rem;
      font-weight: 500;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      line-height: 1.15;
      color: var(--pp-text);
      letter-spacing: -0.5px;
    }

    .stat-unit {
      color: var(--pp-muted);
      font-size: .82rem;
      margin-top: 2px;
    }

    /* Tone Colors */
    .tone-blue .stat-icon { background: #dbeafe; color: #2563eb; }
    .tone-sky .stat-icon { background: #e0f2fe; color: #0284c7; }
    .tone-coral .stat-icon { background: #ffe4e6; color: #e11d48; }
    .tone-mint .stat-icon { background: #d1fae5; color: #059669; }

    /* ---------- Table ---------- */
    .table {
      --bs-table-bg: transparent;
      margin: 0;
    }

    .table thead th {
      font-size: .78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: var(--pp-muted);
      background: #f8fafc;
      border-bottom: 1px solid var(--pp-border);
      padding: 1rem 1.25rem;
    }

    .table td {
      border-color: #f1f5f9;
      padding: 1rem 1.25rem;
      vertical-align: middle;
    }

    .badge-soft-blue {
      background: #eff6ff;
      color: #2563eb;
      border: 1px solid #bfdbfe;
    }

    .badge-soft-coral {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }

    .badge-soft-mint {
      background: #ecfdf5;
      color: #059669;
      border: 1px solid #a7f3d0;
    }

    .badge-soft-blue, .badge-soft-coral, .badge-soft-mint {
      font-weight: 500;
      padding: .45em .85em;
      border-radius: 8px;
      font-size: .8rem;
    }

    .progress {
      height: 10px;
      background: #e2e8f0;
      border-radius: 999px;
      overflow: hidden;
    }

    .progress-bar {
      background: linear-gradient(90deg, #10b981 0%, #059669 100%);
      border-radius: 999px;
    }

    .btn-primary-custom {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      border: none;
      color: #fff;
      border-radius: 12px;
      padding: .6rem 1.25rem;
      font-weight: 500;
      box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
      transition: all .2s;
    }

    .btn-primary-custom:hover {
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: #fff;
      transform: translateY(-1px);
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="d-flex align-items-start justify-content-between">
      <a class="brand" href="index.php" id="sidebarLabel">
        <span class="brand-logo"><i class="bi bi-people-fill"></i></span>
        <span class="brand-name"><?php echo e(APP_NAME); ?></span>
      </a>
      <button type="button" class="btn-close btn-close-white d-lg-none m-3" data-bs-dismiss="offcanvas"
        data-bs-target="#sidebar" aria-label="Close"></button>
    </div>

    <div class="menu-title">เมนูหลัก</div>
    <nav>
      <?php foreach ($menu as $item): ?>
        <a class="side-link<?php echo !empty($item['active']) ? ' active' : ''; ?>" href="<?php echo e($item['href']); ?>"
          <?php echo !empty($item['active']) ? 'aria-current="page"' : ''; ?>>
          <i class="bi <?php echo e($item['icon']); ?> fs-5"></i>
          <span><?php echo e($item['label']); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="menu-title">ระบบ</div>
    <a class="side-link" href="#"><i class="bi bi-gear fs-5"></i><span>ตั้งค่า</span></a>
    <a class="side-link" href="#"><i class="bi bi-box-arrow-left fs-5"></i><span>ออกจากระบบ</span></a>
  </aside>

  <div class="main">
    <!-- Topbar -->
    <header class="topbar">
      <div class="d-flex align-items-center gap-3 px-3 px-lg-4 py-3">
        <button class="icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
          aria-controls="sidebar" aria-label="เปิดเมนู">
          <i class="bi bi-list fs-5"></i>
        </button>

        <!-- ฟังก์ชันที่ 1: Live Search ดักจับการพิมพ์เพื่อค้นหาในตาราง -->
        <div class="input-group search d-none d-md-flex">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="search" id="tableSearch" class="form-control" placeholder="พิมพ์ชื่อเพื่อค้นหา..." onkeyup="filterTable()">
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
          <button class="icon-btn" type="button" aria-label="การแจ้งเตือน" onclick="alert('มีสมาชิกติดกลุ่มเสี่ยง <?php echo $atRiskCount; ?> คน')">
            <i class="bi bi-bell fs-5"></i>
            <?php if ($atRiskCount > 0): ?><span class="dot"></span><?php endif; ?>
          </button>
          
          <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 p-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="avatar">A</span>
              <span class="text-start d-none d-sm-block lh-sm">
                <span class="d-block fw-semibold text-dark">Admin</span>
                <small class="text-secondary" style="font-size: .75rem;">ผู้ดูแลระบบชมรม</small>
              </span>
              <i class="bi bi-chevron-down small text-secondary"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 14px;">
              <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2 text-primary"></i>โปรไฟล์</a></li>
              <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2 text-primary"></i>ตั้งค่า</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item py-2 text-danger" href="#"><i class="bi bi-box-arrow-left me-2"></i>ออกจากระบบ</a></li>
            </ul>
          </div>
        </div>
      </div>
    </header>

    <main class="p-3 p-lg-4">
      <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-4">
        <div>
          <h1 class="h4 fw-bold mb-1">ระบบเช็คชื่อเข้าร่วมกิจกรรมชมรม</h1>
          <p class="text-secondary mb-0" style="font-size: .9rem;">สรุปข้อมูลการเช็คชื่อ ณ วันที่ <?php echo date('d/m/Y'); ?></p>
        </div>
        
        <!-- ฟังก์ชันที่ 2: ปุ่ม Quick Export Report -->
        <div class="d-flex gap-2">
          <button onclick="window.print()" class="btn btn-outline-secondary border-0 bg-white shadow-sm" style="border-radius:12px;">
            <i class="bi bi-printer me-1"></i> พิมพ์รายงาน
          </button>
          <a href="#" class="btn btn-primary-custom">
            <i class="bi bi-check2-square me-1"></i> เช็คชื่อใหม่
          </a>
        </div>
      </div>

      <!-- การ์ดสรุป 4 ใบ -->
      <div class="row g-3 mb-4">
        <?php foreach ($stats as $s): ?>
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="panel stat-card tone-<?php echo e($s['tone']); ?> d-flex align-items-center gap-3">
              <div class="stat-icon">
                <i class="bi <?php echo e($s['icon']); ?>"></i>
              </div>
              <div>
                <div class="stat-label"><?php echo e($s['label']); ?></div>
                <div class="stat-value"><?php echo e($s['value']); ?></div>
                <div class="stat-unit"><?php echo e($s['unit']); ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="row g-3">
        <!-- รายการเช็คชื่อล่าสุด -->
        <div class="col-12 col-xl-8">
          <div class="panel h-100">
            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 px-4 border-bottom gap-2">
              <h2 class="h6 fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>บันทึกการเช็คชื่อล่าสุด</h2>
              
              <!-- ฟังก์ชันที่ 3: ปุ่ม Filter กรองสถานะแบบเรียลไทม์ -->
              <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary active" onclick="filterStatus('all', this)">ทั้งหมด</button>
                <button type="button" class="btn btn-outline-success" onclick="filterStatus('attended', this)">มา</button>
                <button type="button" class="btn btn-outline-danger" onclick="filterStatus('absent', this)">ขาด</button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table" id="attendanceTable">
                <thead>
                  <tr>
                    <th class="ps-4">รหัสนักศึกษา</th>
                    <th>ชื่อสมาชิก</th>
                    <th>กิจกรรมชมรม</th>
                    <th>วันที่</th>
                    <th class="pe-4">สถานะ (คะแนน)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($members as $item): [$label, $cls] = $statusBadge[$item['status']]; ?>
                    <tr class="data-row" data-status="<?php echo $item['status']; ?>">
                      <td class="ps-4 fw-medium text-secondary"><?php echo e($item['code']); ?></td>
                      <td class="fw-semibold text-dark member-name"><?php echo e($item['name']); ?></td>
                      <td><i class="bi bi-journal-text me-1 text-muted"></i><?php echo e($item['activity']); ?></td>
                      <td><?php echo date('d/m/Y', strtotime($item['date'])); ?></td>
                      <td class="pe-4">
                        <span class="badge <?php echo $cls; ?>"><?php echo $label; ?></span>
                        <?php if ($item['total_absent'] >= MAX_ABSENT): ?>
                          <span class="badge bg-danger text-white ms-1" title="ขาดรวมเกินเกณฑ์">เสี่ยงพ้นสภาพ</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- เปอร์เซ็นต์การเข้าร่วม + ฟังก์ชันที่ 4: กล่องวิเคราะห์เสี่ยงขาดกิจกรรม -->
        <div class="col-12 col-xl-4">
          <div class="panel p-4 mb-3">
            <div class="d-flex justify-content-between align-items-baseline mb-2">
              <h2 class="h6 fw-bold mb-0">อัตราการเข้าร่วมกิจกรรม</h2>
              <span class="fw-bold fs-5 text-success"><?php echo $attendPercent; ?>%</span>
            </div>
            <div class="progress mb-3" role="progressbar" aria-valuenow="<?php echo $attendPercent; ?>" aria-valuemin="0" aria-valuemax="100">
              <div class="progress-bar" style="width: <?php echo $attendPercent; ?>%"></div>
            </div>
            <small class="text-secondary d-flex align-items-center">
              <i class="bi bi-check-circle-fill me-2 text-success fs-6"></i>
              <span>เกณฑ์เข้าร่วมขั้นต่ำกำหนดไว้ที่ <?php echo MIN_ATTEND_PERCENT; ?>%</span>
            </small>
          </div>

          <!-- กล่องวิเคราะห์ความเสี่ยงรายบุคคล -->
          <div class="panel p-4">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>กลุ่มเสี่ยงพ้นสภาพชมรม</h2>
            <?php if ($atRiskCount > 0): ?>
              <div class="alert alert-warning border-0 p-3 mb-0" style="border-radius:12px; background:#fffbe8;">
                <div class="d-flex align-items-center gap-2 text-warning-emphasis fw-bold mb-1">
                  <i class="bi bi-shield-exclamation fs-5"></i>
                  <span>พบสมาชิกเสี่ยงตกกิจกรรม <?php echo $atRiskCount; ?> คน</span>
                </div>
                <p class="small text-muted mb-0">สมาชิกขาดกิจกรรมสะสมเท่ากับหรือเกิน <strong><?php echo MAX_ABSENT; ?> ครั้ง</strong> กรุณาติดตามด่วน</p>
              </div>
            <?php else: ?>
              <div class="text-center text-muted py-3">
                <i class="bi bi-emoji-smile fs-2 text-success d-block mb-1"></i>
                <span class="small">ไม่มีสมาชิกที่เสี่ยงพ้นสภาพในขณะนี้</span>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- JavaScript สำหรับฟังก์ชันการค้นหาและกรองสถานะ -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ฟังก์ชันค้นหาชื่อสมาชิกแบบเรียลไทม์
    function filterTable() {
      const input = document.getElementById('tableSearch').value.toLowerCase();
      const rows = document.querySelectorAll('#attendanceTable tbody tr');
      
      rows.forEach(row => {
        const name = row.querySelector('.member-name').textContent.toLowerCase();
        if (name.includes(input)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    // ฟังก์ชันกรองปุ่มสถานะ (มา / ขาด / ทั้งหมด)
    function filterStatus(status, btn) {
      document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const rows = document.querySelectorAll('.data-row');
      rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-status') === status) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }
  </script>
</body>

</html>