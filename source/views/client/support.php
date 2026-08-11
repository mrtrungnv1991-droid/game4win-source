<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$is_logged_in = false; $user_id = 0; $user_name = '';
if (isset($_COOKIE['user_login'])) {
    $u = $CMSNT->get_row_safe("SELECT * FROM users WHERE token = ? AND banned = 0", [check_string($_COOKIE['user_login'])]);
    if ($u) { $is_logged_in = true; $user_id = $u['id']; $user_name = $u['username']; }
}
if (!$is_logged_in) { redirect(base_url('client/login')); }

$msg = '';
// Xử lý tạo ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject'])) {
    $subject = check_string($_POST['subject']);
    $message = check_string($_POST['message']);
    if (strlen($subject) >= 5 && strlen($message) >= 10) {
        $CMSNT->insert('tickets', ['user_id'=>$user_id, 'subject'=>$subject, 'priority'=>$_POST['priority']??'medium']);
        $tid = $CMSNT->insert_id();
        $CMSNT->insert('ticket_replies', ['ticket_id'=>$tid, 'user_id'=>$user_id, 'message'=>$message]);
        $msg = '<div class="alert alert-success">✅ Đã tạo ticket #'.$tid.'! Admin sẽ phản hồi sớm.</div>';
    } else {
        $msg = '<div class="alert alert-danger">❌ Tiêu đề ít nhất 5 ký tự, nội dung 10 ký tự.</div>';
    }
}

$tickets = $CMSNT->get_list_safe("SELECT * FROM tickets WHERE user_id = ? ORDER BY updated_at DESC LIMIT 20", [$user_id]);

$body = ['title' => 'Hỗ trợ — ' . $CMSNT->site('title')];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/nav.php');
?>

<style>
.support-page { max-width: 800px; margin: 80px auto 40px; padding: 0 20px; }
.support-page h1 { font-size: 1.5rem; margin-bottom: 24px; }
.alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: .88rem; }
.alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #10b981; }
.alert-danger { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; }
.ticket-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 10px; }
.ticket-subject { font-weight: 600; font-size: .95rem; }
.ticket-meta { font-size: .78rem; color: #6b7280; margin-top: 4px; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: .7rem; font-weight: 700; }
.badge-open { background: #fef3c7; color: #92400e; }
.badge-answered { background: #dbeafe; color: #1e40af; }
.badge-closed { background: #e5e7eb; color: #374151; }
.form-group { margin-bottom: 12px; }
.form-group label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 4px; color: #374151; }
.form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: .9rem; font-family: inherit; }
.form-group textarea { min-height: 100px; resize: vertical; }
.btn { padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: .9rem; cursor: pointer; border: none; font-family: inherit; }
.btn-primary { background: #3b82f6; color: #fff; }
</style>

<div class="support-page">
  <h1>🎫 Hỗ trợ khách hàng</h1>
  <?= $msg ?>

  <div class="card" style="padding:20px;margin-bottom:24px">
    <h3 style="margin-bottom:16px">Tạo Ticket Mới</h3>
    <form method="POST">
      <div class="form-group">
        <label>Tiêu đề</label>
        <input type="text" name="subject" required placeholder="VD: Cần hỗ trợ nạp game..." minlength="5">
      </div>
      <div class="form-group">
        <label>Nội dung</label>
        <textarea name="message" required placeholder="Mô tả chi tiết vấn đề..." minlength="10"></textarea>
      </div>
      <div class="form-group">
        <label>Mức độ ưu tiên</label>
        <select name="priority">
          <option value="medium">🟡 Bình thường</option>
          <option value="high">🔴 Khẩn cấp</option>
          <option value="low">🟢 Thấp</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">📩 Gửi Ticket</button>
    </form>
  </div>

  <h3 style="margin-bottom:16px">📋 Ticket của bạn (<?= count($tickets) ?>)</h3>
  <?php if(empty($tickets)): ?>
  <p style="color:#6b7280;text-align:center;padding:40px">Chưa có ticket nào</p>
  <?php else: foreach($tickets as $t):
    $statusLabels = ['open'=>'🟡 Đang mở','answered'=>'🔵 Đã trả lời','closed'=>'⚫ Đã đóng'];
    $statusClass = ['open'=>'badge-open','answered'=>'badge-answered','closed'=>'badge-closed'];
    $replyCount = $CMSNT->num_rows("SELECT id FROM ticket_replies WHERE ticket_id = {$t['id']}");
  ?>
  <div class="ticket-card" style="cursor:pointer" onclick="location.href='<?= base_url('client/ticket-detail?id='.$t['id']) ?>'">
    <div style="display:flex;justify-content:space-between;align-items:start">
      <span class="ticket-subject">#<?= $t['id'] ?> — <?= htmlspecialchars($t['subject']) ?></span>
      <span class="badge <?= $statusClass[$t['status']] ?>"><?= $statusLabels[$t['status']] ?></span>
    </div>
    <div class="ticket-meta"><?= $replyCount ?> phản hồi · <?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></div>
  </div>
  <?php endforeach; endif; ?>
</div>

<?php require_once(__DIR__ . '/footer.php'); ?>
