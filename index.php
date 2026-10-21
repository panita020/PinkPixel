<?php
require_once 'config.php';

// ===================================================================
// ข้อมูลสมาชิกชมรมและบันทึกการเข้าร่วมกิจกรรม (ข้อมูลแน่นสมบูรณ์)
// ===================================================================
$totalActivities = 20; // จำนวนกิจกรรมทั้งหมดในเทอมนี้

$members = [
  [
    'code' => 'STD-001', 
    'name' => 'กรกนก สุขสวัสดิ์', 
    'dept' => 'เทคโนโลยีสารสนเทศ (ปวส.1)', 
    'activity' => 'อบรมการเข้าหัวสาย LAN Cat6', 
    'date' => '2026-10-20', 
    'status' => 'attended', 
    'total_absent' => 0, 
    'total_points' => 90, 
    'note' => 'ปฏิบัติงานได้ถูกต้องเรียบร้อย'
  ],
  [
    'code' => 'STD-002', 
    'name' => 'ปณิตา วงศ์สว่าง', 
    'dept' => 'คอมพิวเตอร์ธุรกิจ (ปวส.1)', 
    'activity' => 'สัมมนาความปลอดภัยบนเครือข่าย', 
    'date' => '2026-10-18', 
    'status' => 'absent', 
    'total_absent' => 3, 
    'total_points' => 35, 
    'note' => 'ขาดโดยไม่แจ้งใบลา (ติดกลุ่มเสี่ยง)'
  ],
  [
    'code' => 'STD-003', 
    'name' => 'ธนกร เจริญพร', 
    'dept' => 'เทคโนโลยีสารสนเทศ (ปวส.2)', 
    'activity' => 'จัดตั้งเซิร์ฟเวอร์ปฏิบัติการ', 
    'date' => '2026-10-15', 
    'status' => 'leave', 
    'total_absent' => 1, 
    'total_points' => 65, 
    'note' => 'ยื่นใบลาป่วย มีใบรับรองแพทย์'
  ],
  [
    'code' => 'STD-004', 
    'name' => 'สุชาดา มีสุข', 
    'dept' => 'เทคโนโลยีสารสนเทศ (ปวส.1)', 
    'activity' => 'อบรมการเข้าหัวสาย LAN Cat6', 
    'date' => '2026-10-20', 
    'status' => 'attended', 
    'total_absent' => 0, 
    'total_points' => 85, 
    'note' => 'มาตรงเวลา มีส่วนร่วมดี'
  ],
  [
    'code' => 'STD-005', 
    'name' => 'วรเมธ ศรีวิไล', 
    'dept' => 'อิเล็กทรอนิกส์ (ปวส.1)', 
    'activity' => 'สัมมนาความปลอดภัยบนเครือข่าย', 
    'date' => '2026-10-18', 
    'status' => 'attended', 
    'total_absent' => 2, 
    'total_points' => 50, 
    'note' => 'มาเข้าร่วมตรงเวลา'
  ],
  [
    'code' => 'STD-006', 
    'name' => 'ภัทรา นามสมบูรณ์', 
    'dept' => 'คอมพิวเตอร์กราฟิก (ปวส.2)', 
    'activity' => 'จัดตั้งเซิร์ฟเวอร์ปฏิบัติการ', 
    'date' => '2026-10-15', 
    'status' => 'absent', 
    'total_absent' => 4, 
    'total_points' => 20, 
    'note' => 'ขาดกิจกรรมเกินเกณฑ์สะสม'
  ],
];

// คำนวณสถิติภาพรวม
$attendedCount = 0;
$absentCount   = 0;
$leaveCount    = 0;
$atRiskCount   = 0;

foreach ($members as $item) {
  if ($item['status'] === 'attended') {
    $attendedCount++;
  } elseif ($item['status'] === 'absent') {
    $absentCount++;
  } elseif ($item['status'] === 'leave') {
    $leaveCount++;
  }

  if ($item['total_absent'] >= MAX_ABSENT) {
    $atRiskCount++;
  }
}

$totalCheckCount = count($members);
$attendPercent   = $totalCheckCount > 0 ? round(($attendedCount / $totalCheckCount) * 100) : 0;
$totalPoints     = ($attendedCount * 10) - ($absentCount * 5);

// จัดอันดับ Leaderboard ตามคะแนนสะสม (Top Performers)
$leaderboard = $members;
usort($leaderboard, function($a, $b) {
    return $b['total_points'] <=> $a['total_points'];
});
$topLeaders = array_slice($leaderboard, 0, 4);

$stats = [
  ['label' => 'กิจกรรมทั้งหมด', 'value' => $totalActivities, 'unit' => 'ครั้ง / ภาคเรียน', 'icon' => 'bi-calendar2-week-fill', 'grad' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'shadow' => 'rgba(99, 102, 241, 0.35)'],
  ['label' => 'เข้าร่วม (มา)', 'value' => $attendedCount, 'unit' => "คน · คิดเป็น {$attendPercent}%", 'icon' => 'bi-patch-check-fill', 'grad' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)', 'shadow' => 'rgba(16, 185, 129, 0.35)'],
  ['label' => 'ขาดกิจกรรม', 'value' => $absentCount, 'unit' => "เสี่ยงติด มส. {$atRiskCount} คน", 'icon' => 'bi-exclamation-triangle-fill', 'grad' => 'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)', 'shadow' => 'rgba(244, 63, 94, 0.35)'],
  ['label' => 'แต้มกิจกรรมรวม', 'value' => number_format($totalPoints), 'unit' => 'คะแนนสะสมภาพรวม', 'icon' => 'bi-trophy-fill', 'grad' => 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)', 'shadow' => 'rgba(14, 165, 233, 0.35)'],
];

$statusBadge = [
  'attended' => ['มาเข้าร่วม (+10)', 'bg-success-soft text-success'],
  'absent'   => ['ขาดกิจกรรม (-5)', 'bg-danger-soft text-danger'],
  'leave'    => ['ลากิจ/ลาป่วย (0)', 'bg-warning-soft text-warning-emphasis'],
];

$menu = [
  ['icon' => 'bi-grid-1x2-fill', 'label' => 'แดชบอร์ด', 'href' => 'index.php', 'active' => true],
  ['icon' => 'bi-card-checklist', 'label' => 'เช็คชื่อกิจกรรม', 'href' => '#'],
  ['icon' => 'bi-people-fill', 'label' => 'รายชื่อสมาชิก', 'href' => '#'],
  ['icon' => 'bi-award-fill', 'label' => 'คะแนนพฤติกรรม', 'href' => '#'],
  ['icon' => 'bi-bar-chart-line-fill', 'label' => 'รายงานวิเคราะห์', 'href' => '#'],
  ['icon' => 'bi-info-circle-fill', 'label' => 'เกี่ยวกับระบบ', 'href' => 'about.php'],
];

function e($s) {
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
      --pp-bg: #f1f5f9;
      --pp-surface: #ffffff;
      --pp-sidebar: #0f172a;
      --pp-sidebar-text: #94a3b8;
      --pp-border: #e2e8f0;
      --pp-text: #1e293b;
      --pp-muted: #64748b;
      --pp-primary: #4f46e5;
      --sidebar-w: 260px;
    }

    body {
      font-family: 'Prompt', sans-serif;
      background: var(--pp-bg);
      color: var(--pp-text);
      -webkit-font-smoothing: antialiased;
    }

    /* Sidebar Design */
    .sidebar {
      width: var(--sidebar-w);
      background-color: var(--pp-sidebar) !important;
      border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    @media (min-width: 992px) {
      .sidebar { position: fixed; inset: 0 auto 0 0; }
      .main { margin-left: var(--sidebar-w); }
    }

    .brand {
      display: flex;
      align-items: center;
      gap: .85rem;
      padding: 1.5rem 1.25rem;
      color: #fff;
      text-decoration: none;
    }

    .brand-logo {
      width: 44px;
      height: 44px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
      color: #fff;
      font-size: 1.3rem;
      box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
    }

    .side-link {
      display: flex;
      align-items: center;
      gap: .85rem;
      margin: .25rem .85rem;
      padding: .75rem 1rem;
      border-radius: 12px;
      color: var(--pp-sidebar-text);
      text-decoration: none;
      font-size: .92rem;
      transition: all .2s;
    }

    .side-link:hover {
      background: rgba(255, 255, 255, .08);
      color: #fff;
      transform: translateX(4px);
    }

    .side-link.active {
      background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
      color: #fff;
      font-weight: 500;
      box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
    }

    /* Topbar Design */
    .topbar {
      position: sticky;
      top: 0;
      z-index: 1020;
      background: rgba(255, 255, 255, .85);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--pp-border);
    }

    /* Stat Cards */
    .stat-card-gradient {
      border: none;
      border-radius: 20px;
      padding: 1.4rem;
      color: #fff;
      position: relative;
      overflow: hidden;
      transition: transform .25s ease, box-shadow .25s ease;
    }

    .stat-card-gradient:hover {
      transform: translateY(-5px);
    }

    .stat-icon-bg {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(6px);
      display: grid;
      place-items: center;
      font-size: 1.5rem;
    }

    /* Panels & Tables */
    .panel-card {
      background: var(--pp-surface);
      border: 1px solid var(--pp-border);
      border-radius: 22px;
      box-shadow: 0 4px 25px rgba(0, 0, 0, 0.03);
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

    /* Custom Badges */
    .bg-success-soft { background: #d1fae5; border: 1px solid #a7f3d0; }
    .bg-danger-soft { background: #ffe4e6; border: 1px solid #fecdd3; }
    .bg-warning-soft { background: #fef3c7; border: 1px solid #fde68a; }

    .badge-custom {
      font-weight: 600;
      padding: .5em .9em;
      border-radius: 10px;
      font-size: .8rem;
    }

    .btn-gradient {
      background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
      border: none;
      color: #fff;
      border-radius: 14px;
      padding: .65rem 1.4rem;
      font-weight: 500;
      box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
      transition: all .2s;
    }

    .btn-gradient:hover {
      color: #fff;
      transform: translateY(-2px);
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar">
    <div class="d-flex align-items-center justify-content-between">
      <a class="brand" href="index.php">
        <span class="brand-logo"><i class="bi bi-people-fill"></i></span>
        <span class="fw-bold fs-6 text-white"><?php echo e(APP_NAME); ?></span>
      </a>
      <button type="button" class="btn-close btn-close-white d-lg-none m-3" data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
    </div>

    <div class="px-3 text-uppercase text-secondary fw-semibold small mb-2" style="letter-spacing: 0.1em; font-size: 0.7rem;">เมนูหลัก</div>
    <nav>
      <?php foreach ($menu as $item): ?>
        <a class="side-link<?php echo !empty($item['active']) ? ' active' : ''; ?>" href="<?php echo e($item['href']); ?>">
          <i class="bi <?php echo e($item['icon']); ?> fs-5"></i>
          <span><?php echo e($item['label']); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="main">
    <!-- Topbar -->
    <header class="topbar">
      <div class="d-flex align-items-center gap-3 px-3 px-lg-4 py-3">
        <button class="btn btn-light d-lg-none border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
          <i class="bi bi-list fs-4"></i>
        </button>

        <!-- Live Search -->
        <div class="input-group d-none d-md-flex" style="max-width: 320px;">
          <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
          <input type="search" id="tableSearch" class="form-control bg-light border-0" placeholder="ค้นหาสมาชิก, แผนก หรือกิจกรรม..." onkeyup="filterTable()">
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
          <!-- Notification Dropdown -->
          <div class="dropdown">
            <button class="btn btn-light position-relative border-0" style="border-radius: 12px; width: 42px; height: 42px;" data-bs-toggle="dropdown">
              <i class="bi bi-bell-fill text-secondary"></i>
              <?php if ($atRiskCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
              <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-3 mt-2" style="border-radius: 16px; width: 300px;">
              <li class="fw-bold mb-2 pb-2 border-bottom d-flex justify-content-between align-items-center">
                <span>🔔 การแจ้งเตือน</span>
                <span class="badge bg-danger rounded-pill"><?php echo $atRiskCount; ?></span>
              </li>
              <?php if ($atRiskCount > 0): ?>
                <li class="small text-muted py-1"><i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> พบสมาชิกขาดกิจกรรมเกินเกณฑ์ <?php echo $atRiskCount; ?> คน เสี่ยงพ้นสภาพชมรม</li>
              <?php else: ?>
                <li class="small text-muted py-1">ไม่มีแจ้งเตือนใหม่ในขณะนี้</li>
              <?php endif; ?>
            </ul>
          </div>
          
          <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown">
              <div class="rounded-circle text-white d-grid place-items-center fw-bold" style="width: 40px; height: 40px; background: #6366f1;">A</div>
              <span class="text-start d-none d-sm-block">
                <span class="d-block fw-semibold text-dark">Admin User</span>
                <small class="text-muted" style="font-size: .75rem;">ผู้ดูแลระบบชมรม</small>
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 14px;">
              <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2 text-primary"></i>โปรไฟล์ผู้ใช้</a></li>
              <li><a class="dropdown-item py-2 text-danger" href="#"><i class="bi bi-box-arrow-left me-2"></i>ออกจากระบบ</a></li>
            </ul>
          </div>
        </div>
      </div>
    </header>

    <main class="p-3 p-lg-4">
      <!-- Header Bar -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
          <h1 class="h4 fw-bold mb-1">ระบบเช็คชื่อและประเมินผลกิจกรรมชมรม</h1>
          <p class="text-secondary mb-0" style="font-size: .9rem;">รายงานข้อมูลประจำวันที่ <?php echo date('d/m/Y'); ?> | ทีมพัฒนา: <?php echo $teamName; ?></p>
        </div>
        
        <div class="d-flex gap-2">
          <!-- ปุ่ม Export CSV -->
          <button onclick="exportTableToCSV('attendance_report.csv')" class="btn btn-white bg-white border shadow-sm px-3" style="border-radius: 14px;">
            <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i> ส่งออก CSV
          </button>
          <button onclick="window.print()" class="btn btn-white bg-white border shadow-sm px-3" style="border-radius: 14px;">
            <i class="bi bi-printer me-1 text-secondary"></i> พิมพ์รายงาน
          </button>
          <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#checkInModal">
            <i class="bi bi-plus-lg me-1"></i> บันทึกเช็คชื่อใหม่
          </button>
        </div>
      </div>

      <!-- Stat Cards Grid -->
      <div class="row g-3 mb-4">
        <?php foreach ($stats as $s): ?>
          <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card-gradient" style="background: <?php echo $s['grad']; ?>; box-shadow: 0 10px 25px -5px <?php echo $s['shadow']; ?>;">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-medium opacity-90 small"><?php echo e($s['label']); ?></span>
                <div class="stat-icon-bg"><i class="bi <?php echo e($s['icon']); ?>"></i></div>
              </div>
              <div class="fs-2 fw-bold mb-1"><?php echo e($s['value']); ?></div>
              <div class="small opacity-80"><?php echo e($s['unit']); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Content Area -->
      <div class="row g-3 mb-4">
        <!-- Main Attendance Table -->
        <div class="col-12 col-xl-8">
          <div class="panel-card h-100">
            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 px-4 border-bottom gap-2">
              <h2 class="h6 fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>ประวัติการเช็คชื่อกิจกรรมล่าสุด</h2>
              
              <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary active" onclick="filterStatus('all', this)">ทั้งหมด</button>
                <button type="button" class="btn btn-outline-success" onclick="filterStatus('attended', this)">มา</button>
                <button type="button" class="btn btn-outline-danger" onclick="filterStatus('absent', this)">ขาด</button>
                <button type="button" class="btn btn-outline-warning" onclick="filterStatus('leave', this)">ลา</button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table" id="attendanceTable">
                <thead>
                  <tr>
                    <th class="ps-4">รหัสนักศึกษา</th>
                    <th>ชื่อ-นามสกุล / แผนกวิชา</th>
                    <th>กิจกรรมชมรม</th>
                    <th>สถานะ (คะแนน)</th>
                    <th class="pe-4">หมายเหตุ</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($members as $item): [$label, $cls] = $statusBadge[$item['status']]; ?>
                    <tr class="data-row" data-status="<?php echo $item['status']; ?>">
                      <td class="ps-4 text-muted font-monospace small"><?php echo e($item['code']); ?></td>
                      <td>
                        <div class="fw-bold text-dark member-name"><?php echo e($item['name']); ?></div>
                        <div class="small text-muted" style="font-size: 0.75rem;"><?php echo e($item['dept']); ?></div>
                      </td>
                      <td class="text-secondary">
                        <div><i class="bi bi-journal-text me-1"></i><?php echo e($item['activity']); ?></div>
                        <div class="small text-muted" style="font-size: 0.75rem;">วันที่: <?php echo date('d/m/Y', strtotime($item['date'])); ?></div>
                      </td>
                      <td>
                        <span class="badge badge-custom <?php echo $cls; ?>"><?php echo $label; ?></span>
                        <?php if ($item['total_absent'] >= MAX_ABSENT): ?>
                          <span class="badge bg-danger text-white ms-1" style="border-radius: 8px;">เสี่ยงพ้นสภาพ</span>
                        <?php endif; ?>
                      </td>
                      <td class="pe-4 small text-secondary"><?php echo e($item['note']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Right Side Analytics Panel -->
        <div class="col-12 col-xl-4">
          <!-- Leaderboard Box -->
          <div class="panel-card p-4 mb-3">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>อันดับคะแนนกิจกรรมสูงสุด (Top 4)</h2>
            <div class="d-grid gap-3">
              <?php foreach ($topLeaders as $index => $leader): ?>
                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-circle <?php echo $index === 0 ? 'bg-warning text-dark' : 'bg-secondary'; ?>" style="width: 24px; height: 24px; display: grid; place-items: center;">
                      <?php echo $index + 1; ?>
                    </span>
                    <div>
                      <div class="fw-semibold small text-dark"><?php echo e($leader['name']); ?></div>
                      <div class="text-muted" style="font-size: 0.7rem;"><?php echo e($leader['dept']); ?></div>
                    </div>
                  </div>
                  <span class="fw-bold text-primary small"><?php echo $leader['total_points']; ?> แต้ม</span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Attendance Progress Bar -->
          <div class="panel-card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-baseline mb-2">
              <h2 class="h6 fw-bold mb-0">อัตราการเข้าร่วมรวม</h2>
              <span class="fw-bold fs-5 text-success"><?php echo $attendPercent; ?>%</span>
            </div>
            <div class="progress mb-3" style="height: 10px; border-radius: 999px;">
              <div class="progress-bar bg-success" style="width: <?php echo $attendPercent; ?>%; border-radius: 999px;"></div>
            </div>
            <div class="d-flex align-items-center small text-muted">
              <i class="bi bi-shield-check text-success fs-5 me-2"></i>
              <span>เกณฑ์เข้าร่วมกิจกรรมชมรมขั้นต่ำ <?php echo MIN_ATTEND_PERCENT; ?>%</span>
            </div>
          </div>

          <!-- Risk Alert Box -->
          <div class="panel-card p-4">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>กลุ่มเสี่ยงพ้นสภาพชมรม</h2>
            <?php if ($atRiskCount > 0): ?>
              <div class="p-3 bg-danger-soft text-danger border-0 rounded-4">
                <div class="fw-bold mb-1"><i class="bi bi-shield-exclamation me-1"></i> พบสมาชิกเสี่ยงติด มส. <?php echo $atRiskCount; ?> คน</div>
                <div class="small opacity-90">สมาชิกที่ขาดกิจกรรมตั้งแต่ <strong><?php echo MAX_ABSENT; ?> ครั้งขึ้นไป</strong> จะถูกส่งชื่อประเมินสภาพชมรม</div>
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

  <!-- Modal บันทึกเช็คชื่อใหม่ -->
  <div class="modal fade" id="checkInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow" style="border-radius: 20px;">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold">บันทึกการเช็คชื่อเข้าร่วมกิจกรรม</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form>
            <div class="mb-3">
              <label class="form-label small fw-semibold">ชื่อกิจกรรมชมรม</label>
              <input type="text" class="form-control" value="อบรมการใช้งานระบบเครือข่ายเบื้องต้น">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">สมาชิกชมรม</label>
              <select class="form-select">
                <?php foreach ($members as $m): ?>
                  <option><?php echo e($m['code']); ?> - <?php echo e($m['name']); ?> (<?php echo e($m['dept']); ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">สถานะการเข้าร่วม</label>
              <select class="form-select">
                <option value="attended">มาเข้าร่วม (+10 แต้ม)</option>
                <option value="leave">ลากิจ/ลาป่วย (0 แต้ม)</option>
                <option value="absent">ขาดกิจกรรม (-5 แต้ม)</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-semibold">หมายเหตุ / เหตุผล</label>
              <input type="text" class="form-control" placeholder="ระบุเหตุผลกรณีลา หรือหมายเหตุเพิ่มเติม">
            </div>
          </form>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="button" class="btn btn-gradient" onclick="alert('บันทึกข้อมูลสำเร็จ!'); location.reload();">บันทึกข้อมูล</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Live Search Function
    function filterTable() {
      const input = document.getElementById('tableSearch').value.toLowerCase();
      const rows = document.querySelectorAll('#attendanceTable tbody tr');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
      });
    }

    // Status Filter Function
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

    // Export Table to CSV Function
    function exportTableToCSV(filename) {
      const csv = [];
      const rows = document.querySelectorAll("#attendanceTable tr");
      
      for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length; j++) 
          row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
        csv.push(row.join(","));
      }

      const csvFile = new Blob(["\uFEFF" + csv.join("\n")], {type: "text/csv;charset=utf-8;"});
      const downloadLink = document.createElement("a");
      downloadLink.download = filename;
      downloadLink.href = window.URL.createObjectURL(csvFile);
      downloadLink.style.display = "none";
      document.body.appendChild(downloadLink);
      downloadLink.click();
    }
  </script>
</body>

</html>