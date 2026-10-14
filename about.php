<?php
$goals = [
    "เพื่อให้การเช็กชื่อสมาชิกชมรมสะดวกและรวดเร็วขึ้น",
    "เพื่อจัดเก็บประวัติการเข้าร่วมกิจกรรมอย่างเป็นระบบ",
    "เพื่อให้ตรวจสอบและสรุปสถิติการเข้าร่วมกิจกรรมย้อนหลังได้"
];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เกี่ยวกับระบบ</title>
</head>
<body>
    <h1>เกี่ยวกับระบบ Club Check</h1>

    <h2>เป้าหมายของระบบ</h2>
    <ol>
        <?php foreach ($goals as $goal) { ?>
            <li><?php echo $goal; ?></li>
        <?php } ?>
    </ol>

    <h2>เมนู</h2>
    <a href="index.php">หน้าแรก</a> |
    <a href="hello.php">Hello PHP</a> |
    <a href="server.php">ข้อมูลเซิร์ฟเวอร์</a> |
    <a href="about.php">เกี่ยวกับระบบ</a>
</body>
</html>
