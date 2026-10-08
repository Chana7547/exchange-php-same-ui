<?php
require_once __DIR__ . '/config.php';
// session อยู่ได้ 12 ชม. (พนักงานพักเบรคแล้วกลับมาไม่ต้อง login ใหม่)
ini_set('session.gc_maxlifetime', 43200);
session_set_cookie_params(['lifetime' => 43200, 'httponly' => true, 'samesite' => 'Lax']);
session_start();
date_default_timezone_set('Asia/Bangkok');

function db() {
  static $pdo = null;
  if ($pdo) return $pdo;
  $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  // ครั้งแรกที่ตาราง users ว่าง: สร้างผู้ใช้เริ่มต้น (รหัสผ่านเก็บแบบแฮช)
  if ($pdo->query("SELECT COUNT(*) c FROM users")->fetch()['c'] == 0) {
    $seed = [
      ['admin','9999','Manager','ผู้บริหาร'],
      ['jc1_staff','1111','Staff','JC1'], ['jc2_staff','2222','Staff','JC2'],
      ['jc3_staff','3333','Staff','JC3'], ['jc4_staff','4444','Staff','JC4'],
      ['jc5_staff','5555','Staff','JC5'],
    ];
    $st = $pdo->prepare("INSERT IGNORE INTO users(username,password_hash,role,branch) VALUES(?,?,?,?)");
    foreach ($seed as $u) $st->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2], $u[3]]);
  }
  return $pdo;
}
