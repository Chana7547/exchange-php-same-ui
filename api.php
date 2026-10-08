<?php
ini_set('display_errors', '0');
require __DIR__ . '/db.php';
header('Content-Type: application/json; charset=utf-8');
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$act = $in['action'] ?? '';
function out($a) { echo json_encode($a, JSON_UNESCAPED_UNICODE); exit; }
function isMgr() { return in_array($_SESSION['role'] ?? '', ['Manager', 'Owner']); }
function validDate($d) { return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$d); }

try {
  if ($act === 'login') {
    $st = db()->prepare("SELECT * FROM users WHERE username=?");
    $st->execute([trim($in['username'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($in['password'] ?? '', $u['password_hash'])) {
      session_regenerate_id(true);
      $_SESSION['user'] = $u['username']; $_SESSION['role'] = $u['role']; $_SESSION['branch'] = $u['branch'];
      out(['status' => 'success', 'role' => $u['role'], 'branch' => $u['branch']]);
    }
    out(['status' => 'fail', 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!']);
  }
  if ($act === 'logout') { session_destroy(); out(['status' => 'success']); }
  if (empty($_SESSION['user'])) out(['status' => 'auth', 'message' => 'กรุณาเข้าสู่ระบบ']);

  if ($act === 'me') out(['status' => 'success', 'role' => $_SESSION['role'], 'branch' => $_SESSION['branch']]);

  // ---- พนักงาน: บันทึก (ทับของเดิมของวันนั้น ไม่ล้างค่าหน้าจอ) ----
  if ($act === 'save') {
    if (isMgr()) out(['status' => 'error', 'message' => 'ผู้บริหารไม่มีสิทธิ์บันทึก']);
    if (!validDate($in['date'] ?? '')) out(['status' => 'error', 'message' => 'วันที่ไม่ถูกต้อง']);
    if (!in_array($in['type'] ?? '', ['สรุปยอดเงินบาท','สรุปยอดรับชื้อ','จำนวนตปท_เหลือ','ยอดขายประจำวัน','บัญชีเช็คซื้อขาย'], true)) out(['status' => 'error', 'message' => 'ประเภทรายงานไม่ถูกต้อง']);
    $st = db()->prepare("REPLACE INTO reports(branch,report_type,report_date,inputs,grid,updated_at) VALUES(?,?,?,?,?,?)");
    $st->execute([$_SESSION['branch'], $in['type'], $in['date'],
      json_encode($in['inputs'] ?? [], JSON_UNESCAPED_UNICODE),
      json_encode($in['grid'] ?? [], JSON_UNESCAPED_UNICODE), date('Y-m-d H:i:s')]);
    out(['status' => 'success', 'time' => date('H:i:s')]);
  }

  // ---- พนักงาน: โหลดงานที่เคยบันทึกไว้ของวันนั้น ----
  if ($act === 'load') {
    if (!validDate($in['date'] ?? '')) out(['status' => 'error', 'message' => 'วันที่ไม่ถูกต้อง']);
    $st = db()->prepare("SELECT report_type, inputs, updated_at FROM reports WHERE branch=? AND report_date=?");
    $st->execute([$_SESSION['branch'], $in['date']]);
    $res = [];
    foreach ($st->fetchAll() as $r) $res[$r['report_type']] = ['inputs' => json_decode($r['inputs'], true), 'time' => $r['updated_at']];
    out(['status' => 'success', 'data' => $res]);
  }

  // ---- ผู้บริหาร ----
  if (!isMgr()) out(['status' => 'error', 'message' => 'ไม่มีสิทธิ์']);
  $all = in_array($in['branch'] ?? '', ['ทุกสาขา', 'ผู้บริหาร']);

  if ($act === 'daily') {
    $sql = "SELECT branch, grid FROM reports WHERE report_type=? AND report_date=?";
    $p = [$in['type'], $in['date']];
    if (!$all) { $sql .= " AND branch=?"; $p[] = $in['branch']; }
    $st = db()->prepare($sql . " ORDER BY branch"); $st->execute($p);
    $grid = [];
    foreach ($st->fetchAll() as $r) foreach (json_decode($r['grid'], true) as $row) $grid[] = $row;
    if (!$grid) out(['status' => 'empty', 'message' => 'ไม่พบข้อมูลประวัติของ ' . ($all ? 'ทุกสาขา' : 'ตู้ ' . $in['branch']) . ' ในวันที่ ' . $in['date']]);
    out(['status' => 'success', 'data' => $grid]);
  }

  if ($act === 'range') {
    $sql = "SELECT grid FROM reports WHERE report_type=? AND report_date BETWEEN ? AND ?";
    $p = [$in['type'], $in['start'], $in['end']];
    if (!$all) { $sql .= " AND branch=?"; $p[] = $in['branch']; }
    $st = db()->prepare($sql); $st->execute($p);
    $grid = [];
    foreach ($st->fetchAll() as $r) foreach (json_decode($r['grid'], true) as $row) $grid[] = $row;
    if (!$grid) out(['status' => 'empty', 'message' => 'ไม่มีการบันทึกข้อมูลของ ' . $in['branch'] . ' ในช่วงเวลาที่คุณเลือกครับ']);
    out(['status' => 'success', 'data' => $grid]);
  }

  out(['status' => 'error', 'message' => 'unknown action']);
} catch (Throwable $e) {
  error_log('exchange api: ' . $e->getMessage());
  out(['status' => 'error', 'message' => 'เชื่อมต่อฐานข้อมูลหรือบันทึกไม่สำเร็จ ตรวจสอบ config.php และ error log']);
}
