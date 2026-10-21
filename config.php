<?php
// config.php - การตั้งค่าระบบเช็คชื่อชมรม
const APP_NAME = 'ระบบเช็คชื่อเข้าร่วมกิจกรรมชมรม';
const MIN_ATTEND_PERCENT = 80;  // เกณฑ์การเข้าร่วมกิจกรรมขั้นต่ำ (%)
const MAX_ABSENT = 3;           // จำนวนครั้งที่ขาดกิจกรรมได้สูงสุด
date_default_timezone_set('Asia/Bangkok');