<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ระบบบัญชีแลกเปลี่ยนเงินตรา</title>
<style>
body{font-family:'Sarabun','Segoe UI',Tahoma,sans-serif;background:#f3f2f1;margin:0;padding:15px}
#loginBox{max-width:350px;margin:80px auto;background:#fff;padding:30px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.1);text-align:center}
.login-input{width:90%;padding:10px;margin:10px 0;border:1px solid #ccc;border-radius:4px;font-size:14px}
.btn-login{background:#107c41;color:#fff;border:none;padding:10px 20px;cursor:pointer;border-radius:4px;font-weight:bold;width:100%;font-size:16px}
#appArea{display:none}
.header-bar{background:#107c41;color:#fff;padding:12px 20px;font-weight:bold;font-size:18px;border-radius:4px 4px 0 0;display:flex;justify-content:space-between;align-items:center}
.branch-badge{background:#ffb900;color:#333;padding:2px 10px;border-radius:12px;font-size:14px;margin-left:10px}
.btn-logout{background:#d9534f;color:#fff;border:none;padding:6px 14px;cursor:pointer;border-radius:4px;font-weight:bold}
.nav-tabs{display:flex;background:#e1dfdd;border-bottom:2px solid #107c41;margin-bottom:15px;flex-wrap:wrap}
.tab-btn{padding:10px 15px;cursor:pointer;border:none;background:none;font-weight:bold;font-size:13px;color:#333}
.tab-btn.active{background:#fff;color:#107c41;border-top:3px solid #107c41;border-left:1px solid #ccc;border-right:1px solid #ccc}
.tab-content{display:none;background:#fff;padding:15px;border:1px solid #ccc;overflow-x:auto}
.tab-content.active{display:block}
table.excel-grid{border-collapse:collapse;width:max-content;font-size:13px;margin-bottom:15px;background:#fff}
table.excel-grid th,table.excel-grid td{border:1px solid #d4d4d4;padding:4px 6px;text-align:center;min-width:65px;height:26px}
table.excel-grid th{background:#f3f3f3;color:#333;font-weight:bold;position:sticky;top:0;z-index:1}
input.excel-input{width:100%;border:none;border-bottom:1px solid #ccc;text-align:right;font-family:inherit;font-size:13px;padding:2px 0;background:transparent;color:#000}
input.excel-input:focus{outline:none;border-bottom:2px solid #107c41;background:#e8f5e9}
.read-only{background:#f9f9f9;font-weight:bold;color:#107c41;text-align:right}
.cur-title{text-align:left;font-weight:bold;background:#fafafa;position:sticky;left:0;z-index:2}
.btn-save{background:#107c41;color:#fff;border:none;padding:8px 18px;border-radius:4px;font-weight:bold;cursor:pointer;margin-top:15px}
.status-msg{margin-left:10px;color:#107c41;font-weight:bold}
.sys-date{padding:4px 8px;border-radius:4px;border:none;margin-left:10px;font-family:inherit;font-size:14px}
.manager-filter-box{background:#e8f5e9;padding:15px;border-radius:6px;margin-bottom:15px;display:flex;gap:15px;align-items:center;flex-wrap:wrap;border:1px solid #c8e6c9}
.manager-filter-box select,.manager-filter-box input{padding:6px 10px;font-size:14px;border:1px solid #ccc;border-radius:4px}
.btn-view{background:#107c41;color:#fff;border:none;padding:7px 15px;border-radius:4px;font-weight:bold;cursor:pointer}
.cell-yellow{background:#FF0 !important;color:#F00 !important;font-weight:bold;text-align:right}
.input-cur-name{width:100px;font-weight:bold;color:navy;text-align:left;background:transparent;border:none;border-bottom:1px dashed #ccc;outline:none}
.input-header-name{width:100%;font-weight:bold;text-align:center;background:transparent;border:none;border-bottom:1px dashed #666;outline:none;color:#333}
.blank{border:none !important;background:#fff !important}
</style>
</head>
<body>
<div id="loginBox">
  <h3>🟢 เข้าสู่ระบบบัญชีแลกเงิน</h3>
  <input type="text" id="username" class="login-input" placeholder="ชื่อผู้ใช้">
  <input type="password" id="password" class="login-input" placeholder="รหัสผ่าน" onkeydown="if(event.key==='Enter')doLogin()">
  <button class="btn-login" onclick="doLogin()">เข้าสู่ระบบ</button>
  <p id="loginMsg" style="color:red;font-size:13px;margin-top:10px"></p>
</div>

<div id="appArea">
  <div class="header-bar">
    <div>📊 ระบบบัญชี <span id="branchBadge" class="branch-badge"></span>
      <input type="date" id="sysDate" class="sys-date" onchange="changeDate()"></div>
    <div><button class="btn-logout" onclick="doLogout()">🚪 ออกจากระบบ</button></div>
  </div>

  <div id="staffView" style="display:none">
    <div class="nav-tabs">
      <button class="tab-btn active" onclick="switchTab(event,'tab1')">1. สรุปยอดเงินบาท(ปริ้น2)</button>
      <button class="tab-btn" onclick="switchTab(event,'tab2')">2. สรุปยอดรับชื้อ(ปริ้น2)</button>
      <button class="tab-btn" onclick="switchTab(event,'tab3')">3. จำนวนตปท.เหลือ(ปริ้น1)</button>
      <button class="tab-btn" onclick="switchTab(event,'tab4')">4. ยอดขายประจำวัน</button>
      <button class="tab-btn" onclick="switchTab(event,'tab5')">5. บัญชีเช็คซื้อขาย</button>
    </div>
    <div id="tab1" class="tab-content active">
      <table class="excel-grid"><tbody id="thbTbodyFull"></tbody></table>
      <button class="btn-save" onclick="saveTab('tab1','สรุปยอดเงินบาท','thbTbodyFull')">💾 บันทึก</button><span class="status-msg" id="status_tab1"></span>
    </div>
    <div id="tab2" class="tab-content">
      <table class="excel-grid"><thead id="buyThead"></thead><tbody id="buyTbody"></tbody></table>
      <button class="btn-save" onclick="saveTab('tab2','สรุปยอดรับชื้อ','buyTbody')">💾 บันทึก</button><span class="status-msg" id="status_tab2"></span>
    </div>
    <div id="tab3" class="tab-content">
      <table class="excel-grid"><thead id="foreignThead"></thead><tbody id="foreignTbody"></tbody></table>
      <button class="btn-save" onclick="saveTab('tab3','จำนวนตปท_เหลือ','foreignTbody')">💾 บันทึก</button><span class="status-msg" id="status_tab3"></span>
    </div>
    <div id="tab4" class="tab-content">
      <table class="excel-grid"><thead id="sellThead"></thead><tbody id="sellTbody"></tbody></table>
      <button class="btn-save" onclick="saveTab('tab4','ยอดขายประจำวัน','sellTbody')">💾 บันทึก</button><span class="status-msg" id="status_tab4"></span>
    </div>
    <div id="tab5" class="tab-content">
      <table class="excel-grid">
        <thead><tr><th>สกุลเงิน</th><th>ขาย(ที่บูท)</th><th>เรตขาย(ที่บูท)</th><th>รวม(ที่บูท)</th><th>รวม(รับ)</th><th>จำนวน(รับ)</th><th>เรต(รับ)</th><th>เรต(ขาย)</th><th>จำนวน(ขาย)</th><th>เรต(ขาย2)</th><th>จำนวน(ขาย2)</th><th>รวม(ขาย)</th><th>ต่าง(รอขาย)</th><th>ผลต่างเงิน</th></tr></thead>
        <tbody id="checkTbody"></tbody>
      </table>
      <button class="btn-save" onclick="saveTab('tab5','บัญชีเช็คซื้อขาย','checkTbody')">💾 บันทึก</button><span class="status-msg" id="status_tab5"></span>
    </div>
    <div style="margin-top:10px"><button class="btn-save" style="background:#0a5c2f" onclick="saveAll()">💾 บันทึกทุกหน้า (ก่อนพักเบรค)</button><span class="status-msg" id="status_all"></span></div>
  </div>

  <div id="managerView" style="display:none">
    <h2 style="color:#107c41;margin-top:0">👑 รายงานสำหรับผู้บริหาร</h2>
    <div class="manager-filter-box">
      <div><label><b>สาขา: </b></label><select id="mgrBranch"><option>JC1</option><option>JC2</option><option>JC3</option><option>JC4</option><option>JC5</option><option value="ทุกสาขา">รวมทุกสาขา</option></select></div>
      <div><label><b>รายงาน: </b></label><select id="mgrReportType">
        <option value="สรุปยอดเงินบาท">1. สรุปยอดเงินบาท</option><option value="สรุปยอดรับชื้อ">2. สรุปยอดรับชื้อ</option>
        <option value="จำนวนตปท_เหลือ">3. จำนวนตปท.เหลือ (ดูได้เฉพาะรายวัน)</option><option value="ยอดขายประจำวัน">4. ยอดขายประจำวัน</option><option value="บัญชีเช็คซื้อขาย">5. บัญชีเช็คซื้อขาย</option></select></div>
      <div><label><b>รูปแบบ: </b></label><select id="mgrViewMode" onchange="toggleDateInputs()"><option value="daily">ดูแบบรายวัน (Daily)</option><option value="range">สรุปยอด (รายสัปดาห์/เดือน)</option></select></div>
      <div id="dateDaily"><label><b>วันที่: </b></label><input type="date" id="mgrDate"></div>
      <div id="dateRange" style="display:none;align-items:center;gap:5px"><label><b>ตั้งแต่: </b></label><input type="date" id="mgrStartDate"><label><b>ถึง: </b></label><input type="date" id="mgrEndDate"></div>
      <div><button class="btn-view" onclick="loadManagerData()">🔍 เรียกดูข้อมูล</button></div>
    </div>
    <div id="mgrTableContainer" style="background:#fff;padding:15px;border:1px solid #ccc;overflow-x:auto"><i>เลือก สาขา, ประเภทรายงาน, รูปแบบการดู จากนั้นกดปุ่ม "เรียกดูข้อมูล"</i></div>
  </div>
</div>

<script>
let currentBranch = "", dirty = false, curDate = "";
const TABS = [['tab1','สรุปยอดเงินบาท','thbTbodyFull'],['tab2','สรุปยอดรับชื้อ','buyTbody'],['tab3','จำนวนตปท_เหลือ','foreignTbody'],['tab4','ยอดขายประจำวัน','sellTbody'],['tab5','บัญชีเช็คซื้อขาย','checkTbody']];
const curAll = ["USD (50-100)","USD (5-10-20)","USD (1-2)","GBP","MYR(50-100)","MYR(5-20)","MYR(1-2)","SGD","HKD","AUD","JPY","CHF","CAD","DKK","NOK","SEK","TWD","KRW(5,000+)","KRW(1,000)","NZD","CNY(50-100)","CNY(10-20)","CNY(1-5)","EUR","RUB","OMR","QAR","SAR","KWD","IDR","INR","AED","อื่นๆ 1","อื่นๆ 2","อื่นๆ 3","อื่นๆ 4","อื่นๆ 5"];
const banknotes = [1000,500,100,50,20,10,5,1,"คละ"];
const $ = id => document.getElementById(id);

async function api(action, data = {}) {
  try {
    const r = await fetch('api.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action, ...data})});
    const j = await r.json();
    if (j.status === 'auth') { showLogin(); }
    return j;
  } catch (e) { return {status:'error', message:'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้: ' + e.message}; }
}
function localDate() { const d = new Date(); return new Date(d - d.getTimezoneOffset()*60000).toISOString().slice(0,10); }
function syncOtherCurName(val, idx) { document.querySelectorAll('.sync-cur-'+idx).forEach(el => { if (el.value !== val) el.value = val; }); }

// ---------- login / session ----------
async function doLogin() {
  $("loginMsg").innerText = "กำลังตรวจสอบ...";
  const res = await api('login', {username:$("username").value, password:$("password").value});
  if (res.status === 'success') { $("loginMsg").innerText = ""; startApp(res); } else $("loginMsg").innerText = res.message || 'เกิดข้อผิดพลาด';
}
async function doLogout() {
  if (dirty && !confirm("มีข้อมูลที่ยังไม่ได้บันทึก ออกจากระบบเลยหรือไม่?")) return;
  await api('logout'); dirty = false; showLogin();
}
function showLogin() { $("username").value = ""; $("password").value = ""; $("appArea").style.display = "none"; $("loginBox").style.display = "block"; }
async function startApp(res) {
  currentBranch = res.branch;
  $("loginBox").style.display = "none"; $("appArea").style.display = "block";
  $("branchBadge").innerText = "สาขา: " + res.branch;
  const today = localDate();
  ["sysDate","mgrDate","mgrStartDate","mgrEndDate"].forEach(id => $(id).value = today);
  if (res.role === "Manager" || res.role === "Owner") { $("staffView").style.display = "none"; $("managerView").style.display = "block"; }
  else { $("staffView").style.display = "block"; $("managerView").style.display = "none"; curDate = $("sysDate").value; buildFullTables(); updateDates(); await loadAll(); }
}
window.addEventListener('load', async () => { const r = await api('me'); if (r.status === 'success') startApp(r); });
window.addEventListener('beforeunload', e => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

function switchTab(evt, tabId) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
  evt.target.classList.add('active'); $(tabId).classList.add('active');
}
function updateDates() {
  const p = $("sysDate").value.split('-');
  if ($("dispDate1")) $("dispDate1").innerText = p[2] + "/" + p[1] + "/" + p[0];
}
async function changeDate() {
  if (dirty && !confirm("มีข้อมูลที่ยังไม่ได้บันทึก เปลี่ยนวันที่แล้วข้อมูลหน้าจอจะหายไป ต้องการเปลี่ยนหรือไม่?")) { $("sysDate").value = curDate; return; }
  if (!$("sysDate").value) { $("sysDate").value = curDate; return; }
  curDate = $("sysDate").value; buildFullTables(); updateDates(); await loadAll();
}
function toggleDateInputs() {
  const d = $("mgrViewMode").value === 'daily';
  $("dateDaily").style.display = d ? "block" : "none"; $("dateRange").style.display = d ? "none" : "flex";
}

// ---------- build tables ----------
function buildFullTables() {
  let t = `
  <tr><th colspan="2" style="text-align:left">สรุปยอดเงินบาทประจำวัน</th><th>วันที่</th><th id="dispDate1" style="color:#107c41"></th><th>สาขา ${currentBranch}</th><th colspan="2" class="blank"></th></tr>
  <tr><td class="cur-title">เงินสดยกมา</td><td><input type="number" class="excel-input" id="cf_bring"></td><td colspan="5" class="blank"></td></tr>
  <tr><td class="cur-title">รับมา</td><td><input type="number" class="excel-input" id="cf_r1"></td><td colspan="5" class="blank"></td></tr>
  <tr><td class="cur-title">รับมา</td><td><input type="number" class="excel-input" id="cf_r2"></td><td colspan="5" class="blank"></td></tr>
  <tr><td class="cur-title">รับมา</td><td><input type="number" class="excel-input" id="cf_r3"></td><td class="blank"></td><th>วันที่</th><th>ที่มาจ่าย</th><th>ยอดเงินจ่าย</th><th>หมายเหตุ</th></tr>
  <tr><td class="cur-title">ขาย</td><td class="read-only" id="cf_sell">0</td><td class="blank"></td><td><input type="text" class="excel-input"></td><td><input type="text" class="excel-input"></td><td><input type="number" class="excel-input" id="cf_exp1"></td><td><input type="text" class="excel-input"></td></tr>
  <tr><td class="cur-title">จ่ายไป</td><td class="read-only" id="cf_pay">0</td><td class="blank"></td><td><input type="text" class="excel-input"></td><td><input type="text" class="excel-input"></td><td><input type="number" class="excel-input" id="cf_exp2"></td><td><input type="text" class="excel-input"></td></tr>
  <tr><td class="cur-title">เงินสดคงเหลือ</td><td class="read-only" id="cf_net" style="color:red">0</td><td colspan="5" class="blank"></td></tr>
  <tr><td colspan="7" class="blank" style="height:15px"></td></tr>
  <tr><th>ชนิดธนบัตร</th><th>จำนวนใบ</th><th>ยอดเงินบาท</th><th>หมายเหตุ</th><td colspan="3" class="blank"></td></tr>`;
  banknotes.forEach((bn, i) => {
    if (bn === "คละ") t += `<tr><td class="cur-title">${bn}</td><td class="read-only">(หักอัตโนมัติ)</td><td class="read-only" id="bn_t_${i}">0</td><td><input type="text" class="excel-input"></td><td colspan="3" class="blank"></td></tr>`;
    else t += `<tr><td class="cur-title">${bn}</td><td><input type="number" class="excel-input" id="bn_q_${i}"></td><td class="read-only" id="bn_t_${i}">0</td><td><input type="text" class="excel-input"></td><td colspan="3" class="blank"></td></tr>`;
  });
  t += `<tr style="background:#e8f5e9;font-weight:bold"><td class="cur-title">รวม</td><td>-</td><td class="read-only" id="bn_tott">0</td><td></td><td colspan="3" class="blank"></td></tr>`;
  $("thbTbodyFull").innerHTML = t;

  const nameCell = (c, i) => c.startsWith("อื่นๆ") ? `<input type="text" class="input-cur-name sync-cur-${i}" value="${c}" oninput="syncOtherCurName(this.value,${i})">` : c;
  const triple = (p, r, i) => `<td><input type="number" class="excel-input" id="${p}_q_${r}_${i}"></td><td><input type="number" class="excel-input" id="${p}_r_${r}_${i}"></td><td class="read-only" id="${p}_t_${r}_${i}">0</td>`;

  let bh = "<tr><th rowspan='2'>สกุลเงิน</th>";
  for (let r = 1; r <= 7; r++) bh += `<th colspan='3'>รอบ ${r}</th>`;
  bh += "<th colspan='3'>ขาย(ห้ามแก้ช่องนี้)</th><th colspan='3'>Total Balance</th></tr><tr>";
  for (let r = 0; r < 8; r++) bh += "<th>จำนวน</th><th>Rate</th><th>บาท</th>";
  $("buyThead").innerHTML = bh + "<th>รวมจำนวน</th><th>Rate เฉลี่ย</th><th>รวมเงินบาท</th></tr>";
  let b = "";
  curAll.forEach((c, i) => {
    b += `<tr><td class="cur-title">${nameCell(c, i)}</td>`;
    for (let r = 1; r <= 7; r++) b += triple('b', r, i);
    b += ['sq','sr','sb','totq','totr','tott'].map(k => `<td class="read-only" id="b_${k}_${i}">0</td>`).join('') + "</tr>";
  });
  $("buyTbody").innerHTML = b;

  $("foreignThead").innerHTML = "<tr><th style='color:blue'>สกุลเงิน</th><th style='color:blue'>จำนวน</th></tr>";
  let f = "";
  curAll.forEach((c, i) => f += `<tr><td class="cur-title" style="color:navy">${nameCell(c, i)}</td><td class="cell-yellow" id="f_lq_${i}">0.00</td></tr>`);
  $("foreignTbody").innerHTML = f;

  let sh = "<tr><th rowspan='2'>สกุลเงิน</th>";
  ["บิล 2 YES","บิล 2 M.M.","บิล 3 M.M.","บิล 4 M.M.","พี่เค"].forEach((c, k) => sh += `<th colspan='3'><input type="text" class="input-header-name" value="${c}"></th>`);
  sh += "<th colspan='3'>Total Balance</th></tr><tr>";
  for (let r = 0; r < 5; r++) sh += "<th>จำนวน</th><th>Rate</th><th>บาท</th>";
  $("sellThead").innerHTML = sh + "<th>รวมจำนวน</th><th>Rate เฉลี่ย</th><th>รวมเงินบาท</th></tr>";
  let s = "";
  curAll.forEach((c, i) => {
    s += `<tr><td class="cur-title">${nameCell(c, i)}</td>`;
    for (let r = 1; r <= 5; r++) s += triple('s', r, i);
    s += ['totq','totr','tott'].map(k => `<td class="read-only" id="s_${k}_${i}">0</td>`).join('') + "</tr>";
  });
  $("sellTbody").innerHTML = s;

  let c5 = "";
  curAll.forEach((c, i) => {
    const ro = k => `<td class="read-only" id="c_${k}_${i}">0</td>`, inp = k => `<td><input type="number" class="excel-input" id="c_${k}_${i}"></td>`;
    c5 += `<tr><td class="cur-title">${nameCell(c, i)}</td>${ro('bq')}${ro('br')}${ro('bt')}${ro('rt')}${ro('rq')}${ro('rr')}${inp('sr1')}${inp('sq1')}${inp('sr2')}${inp('sq2')}${ro('stot')}${ro('dq')}${ro('dm')}</tr>`;
  });
  $("checkTbody").innerHTML = c5;

  // ผูก event เดียวทั้งหน้า: พิมพ์แล้วคำนวณใหม่ + ทำเครื่องหมายว่ายังไม่บันทึก
  $("staffView").oninput = () => { dirty = true; triggerCalc(); };
  triggerCalc(); dirty = false;
}

function triggerCalc() {
  const v = id => parseFloat($(id).value) || 0;
  let grandSell = 0, grandBuy = 0;
  curAll.forEach((_, i) => {
    let stq = 0, stb = 0;
    for (let r = 1; r <= 5; r++) { const th = v(`s_q_${r}_${i}`) * v(`s_r_${r}_${i}`); $(`s_t_${r}_${i}`).innerText = th.toLocaleString(); stq += v(`s_q_${r}_${i}`); stb += th; }
    const str = stq > 0 ? (stb/stq).toFixed(4) : 0;
    $(`s_totq_${i}`).innerText = stq.toLocaleString(); $(`s_totr_${i}`).innerText = str; $(`s_tott_${i}`).innerText = stb.toLocaleString();
    grandSell += stb;
    let btq = 0, btb = 0;
    for (let r = 1; r <= 7; r++) { const th = v(`b_q_${r}_${i}`) * v(`b_r_${r}_${i}`); $(`b_t_${r}_${i}`).innerText = th.toLocaleString(); btq += v(`b_q_${r}_${i}`); btb += th; }
    const btr = btq > 0 ? (btb/btq).toFixed(4) : 0;
    $(`b_sq_${i}`).innerText = stq.toLocaleString(); $(`b_sr_${i}`).innerText = str; $(`b_sb_${i}`).innerText = stb.toLocaleString();
    $(`b_totq_${i}`).innerText = btq.toLocaleString(); $(`b_totr_${i}`).innerText = btr; $(`b_tott_${i}`).innerText = btb.toLocaleString();
    grandBuy += btb;
    $(`f_lq_${i}`).innerText = (btq - stq).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
    $(`c_bq_${i}`).innerText = stq.toLocaleString(); $(`c_br_${i}`).innerText = str; $(`c_bt_${i}`).innerText = stb.toLocaleString();
    $(`c_rt_${i}`).innerText = btb.toLocaleString(); $(`c_rq_${i}`).innerText = btq.toLocaleString(); $(`c_rr_${i}`).innerText = btr;
    const sq1 = v(`c_sq1_${i}`), sr1 = v(`c_sr1_${i}`), sq2 = v(`c_sq2_${i}`), sr2 = v(`c_sr2_${i}`);
    const cStot = sq1*sr1 + sq2*sr2;
    $(`c_stot_${i}`).innerText = cStot.toLocaleString();
    $(`c_dq_${i}`).innerText = (btq - stq - sq1 - sq2).toLocaleString();
    $(`c_dm_${i}`).innerText = (cStot + stb - btb).toLocaleString();
  });
  $('cf_sell').innerText = grandSell.toLocaleString(); $('cf_pay').innerText = grandBuy.toLocaleString();
  const net = v("cf_bring") + v("cf_r1") + v("cf_r2") + v("cf_r3") + grandSell - grandBuy;
  $("cf_net").innerText = net.toLocaleString();
  let totBank = 0;
  banknotes.forEach((bn, i) => { if (bn !== "คละ") { const x = v(`bn_q_${i}`) * bn; $(`bn_t_${i}`).innerText = x.toLocaleString(); totBank += x; } });
  const mixedVal = net - totBank - (v("cf_exp1") + v("cf_exp2"));
  $(`bn_t_${banknotes.indexOf("คละ")}`).innerText = mixedVal.toLocaleString();
  $("bn_tott").innerText = (totBank + mixedVal).toLocaleString();
}

// ---------- scrape ตาราง (สำหรับรายงานผู้บริหาร) ----------
function scrape(id) {
  const tbody = $(id), thead = tbody.parentElement.querySelector('thead');
  const data = [], rowSpans = {}; let maxLen = 0;
  function extract(rows) {
    for (let i = 0; i < rows.length; i++) {
      const rd = []; let col = 0;
      const fill = () => { while (rowSpans[col] > 0) { rd.push(""); rowSpans[col]--; col++; } };
      fill();
      for (let j = 0; j < rows[i].cells.length; j++) {
        fill();
        const cell = rows[i].cells[j], inp = cell.querySelector('input');
        rd.push(inp ? (inp.value || "") : cell.innerText.replace(/,/g, '').trim());
        const rs = cell.rowSpan || 1, cs = cell.colSpan || 1;
        if (rs > 1) rowSpans[col] = rs - 1;
        for (let c = 1; c < cs; c++) { rd.push(""); if (rs > 1) rowSpans[col + c] = rs - 1; }
        col += cs;
      }
      maxLen = Math.max(maxLen, rd.length); data.push(rd);
    }
  }
  if (thead) extract(thead.rows);
  extract(tbody.rows);
  data.forEach(r => { while (r.length < maxLen) r.push(""); });
  return data;
}

// ---------- บันทึก / โหลด (ไม่ล้างค่า) ----------
const inputsOf = tabId => [...document.querySelectorAll(`#${tabId} input`)].map(e => e.value);
function nowStr() { return new Date().toLocaleTimeString('th-TH'); }

async function saveTab(tabId, type, tbodyId, silent) {
  const date = $("sysDate").value;
  if (!date) { alert("กรุณาเลือกวันที่ก่อนบันทึกครับ"); return false; }
  const st = $("status_" + tabId); st.style.color = "#107c41"; st.innerText = "⏳ กำลังบันทึก...";
  triggerCalc();
  const res = await api('save', {type, date, inputs: inputsOf(tabId), grid: scrape(tbodyId)});
  if (res.status === 'success') { st.innerText = "✅ บันทึกแล้ว " + res.time + " (ข้อมูลยังอยู่ในหน้านี้ ทำต่อได้เลย)"; if (!silent) dirty = false; return true; }
  st.style.color = "red"; st.innerText = "❌ " + (res.message || 'บันทึกไม่สำเร็จ'); return false;
}
async function saveAll() {
  $("status_all").innerText = "⏳ กำลังบันทึก...";
  let ok = true;
  for (const [tab, type, body] of TABS) ok = (await saveTab(tab, type, body, true)) && ok;
  if (ok) dirty = false;
  $("status_all").style.color = ok ? "#107c41" : "red";
  $("status_all").innerText = ok ? "✅ บันทึกครบทุกหน้า " + nowStr() : "❌ บางหน้าบันทึกไม่สำเร็จ";
}
async function loadAll() {
  const res = await api('load', {date: $("sysDate").value});
  if (res.status !== 'success') return;
  TABS.forEach(([tab, type]) => {
    const d = res.data[type]; if (!d) return;
    const els = document.querySelectorAll(`#${tab} input`);
    (d.inputs || []).forEach((val, k) => { if (els[k]) els[k].value = val; });
    // ชื่อสกุล "อื่นๆ" ให้ตรงกันทุกแท็บ
    document.querySelectorAll(`#${tab} .input-cur-name`).forEach(el => { const m = el.className.match(/sync-cur-(\d+)/); if (m) syncOtherCurName(el.value, m[1]); });
    $("status_" + tab).style.color = "#107c41"; $("status_" + tab).innerText = "📂 โหลดงานที่บันทึกไว้ (" + d.time + ")";
  });
  triggerCalc(); dirty = false;
}

// ---------- ผู้บริหาร ----------
async function loadManagerData() {
  const b = $("mgrBranch").value, r = $("mgrReportType").value, box = $("mgrTableContainer");
  if ($("mgrViewMode").value === 'daily') {
    const d = $("mgrDate").value;
    if (!d) { alert("กรุณาเลือกวันที่ต้องการดูข้อมูลครับ"); return; }
    const disp = d.split('-').reverse().join('/');
    box.innerHTML = `⏳ กำลังค้นหาข้อมูลประวัติของวันที่ ${disp}...`;
    const res = await api('daily', {branch:b, type:r, date:d});
    if (res.status === "success") {
      let h = `<b style='color:#107c41'>รายงาน: ${r} | ตู้: ${b} | ประจำวันที่: ${disp}</b><br><br><table class='excel-grid'>`;
      res.data.forEach((row, ri) => {
        h += "<tr>";
        row.forEach(c => { const hd = ri < 2 || c === "สกุลเงิน" || (typeof c === 'string' && (c.includes("รวมบิล") || c.includes("ชนิดธนบัตร"))); const tg = hd ? "th" : "td"; h += `<${tg}>${esc(c)}</${tg}>`; });
        h += "</tr>";
      });
      box.innerHTML = h + "</table>";
    } else box.innerHTML = `<span style='color:red;font-weight:bold'>${esc(res.message)}</span>`;
  } else {
    if (r === "จำนวนตปท_เหลือ") { alert("รายงาน 'จำนวนตปท.เหลือ' เป็นยอดคงเหลือของแต่ละวัน ไม่สามารถนำมาบวกสะสมได้ กรุณาดูแบบรายวันครับ"); return; }
    const sd = $("mgrStartDate").value, ed = $("mgrEndDate").value;
    if (!sd || !ed) { alert("กรุณาเลือกช่วงวันที่ให้ครบถ้วนครับ"); return; }
    box.innerHTML = `⏳ กำลังประมวลผลสรุปยอดสะสม ${sd.split('-').reverse().join('/')} ถึง ${ed.split('-').reverse().join('/')}...`;
    renderSummaryTable(await api('range', {branch:b, type:r, start:sd, end:ed}));
  }
}
function esc(s) { return String(s ?? "").replace(/[&<>"]/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m])); }

function renderSummaryTable(res) {
  const box = $("mgrTableContainer");
  if (res.status !== "success") { box.innerHTML = `<span style='color:red;font-weight:bold'>${esc(res.message || 'ไม่พบข้อมูลในช่วงเวลานี้')}</span>`; return; }
  const r = $("mgrReportType").value, b = $("mgrBranch").value;
  const sd = $("mgrStartDate").value.split('-').reverse().join('/'), ed = $("mgrEndDate").value.split('-').reverse().join('/');
  let maxCols = 0, headers = [];
  res.data.forEach(row => { maxCols = Math.max(maxCols, row.length); if (!headers.length && ["สกุลเงิน","ชนิดธนบัตร","เงินสดยกมา"].includes(row[0])) headers = row; });
  const agg = {}, list = [];
  res.data.forEach(row => {
    let key = typeof row[0] === 'string' ? row[0].trim() : row[0];
    if (!key || key === "สกุลเงิน" || key === "ชนิดธนบัตร" || key === "รวม" || ["รวมบิล","รอบ","วันที่"].some(w => String(key).includes(w))) return;
    if (!agg[key]) { agg[key] = new Array(maxCols).fill(0); list.push(key); }
    for (let i = 1; i < row.length; i++) { const x = parseFloat(String(row[i]).replace(/,/g, '')); if (!isNaN(x)) agg[key][i] += x; }
  });
  const th = t => `<th style='background:#107c41;color:white'>${esc(t)}</th>`;
  let h = `<b style='color:#107c41'>📊 สรุปยอดสะสม: ${r} | ตู้: ${b}</b><br><i>(รวมข้อมูลตั้งแต่วันที่ ${sd} ถึง ${ed})</i><br><span style='color:red;font-size:12px'>*ระบบบวกตัวเลขทุกช่องที่มีการกรอกให้อัตโนมัติ (ช่อง Rate จะเป็นผลรวมสะสม ไม่ใช่ค่าเฉลี่ย)</span><br><br><table class='excel-grid'><tr>`;
  if (headers.length && r !== "สรุปยอดเงินบาท") headers.forEach(x => h += th(x));
  else { h += th("รายการ / สกุลเงิน"); for (let i = 1; i < maxCols; i++) h += th("ผลรวมช่อง " + i); }
  h += "</tr>";
  list.forEach(key => {
    h += `<tr><td class='cur-title' style='color:navy'>${esc(key)}</td>`;
    for (let i = 1; i < maxCols; i++) { const x = agg[key][i]; h += `<td>${x !== 0 ? x.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}) : "-"}</td>`; }
    h += "</tr>";
  });
  box.innerHTML = h + "</table>";
}
</script>
</body>
</html>
