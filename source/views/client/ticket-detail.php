<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$is_logged_in = false; $user_id = 0;
if (isset($_COOKIE['user_login'])) {
    $u = $CMSNT->get_row_safe("SELECT * FROM users WHERE token = ? AND banned = 0", [check_string($_COOKIE['user_login'])]);
    if ($u) { $is_logged_in = true; $user_id = $u['id']; }
}
if (!$is_logged_in) { redirect(base_url('client/login')); }

$tid = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ticket = $CMSNT->get_row_safe("SELECT * FROM tickets WHERE id = ? AND user_id = ?", [$tid, $user_id]);
if (!$ticket) { die('Ticket không tồn tại!'); }

// Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['reply'])) {
    $CMSNT->insert('ticket_replies', ['ticket_id'=>$tid, 'user_id'=>$user_id, 'message'=>check_string($_POST['reply'])]);
    $CMSNT->update('tickets', ['status'=>'open'], "id = $tid");
}

$replies = $CMSNT->get_list_safe("SELECT tr.*, u.username FROM ticket_replies tr LEFT JOIN users u ON tr.user_id = u.id WHERE tr.ticket_id = ? ORDER BY tr.id ASC", [$tid]);

$body = ['title' => 'Ticket #'.$tid.' — '.$CMSNT->site('title')];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/nav.php');
?>

<style>
.ticket-detail { max-width: 800px; margin: 80px auto 40px; padding: 0 20px; }
.reply-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 10px; }
.reply-header { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: .82rem; }
.reply-admin { border-left: 3px solid #3b82f6; }
.reply-user { border-left: 3px solid #10b981; }
.reply-message { font-size: .9rem; line-height: 1.6; white-space: pre-wrap; }
.form-group textarea { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #d1d5db; font-size: .9rem; font-family: inherit; min-height: 80px; resize: vertical; }
.btn { padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: .9rem; cursor: pointer; border: none; font-family: inherit; }
.btn-primary { background: #3b82f6; color: #fff; }
.back-link { display: inline-block; margin-bottom: 16px; color: #3b82f6; text-decoration: none; font-size: .85rem; }
</style>

<div class="ticket-detail">
  <a href="<?= base_url('client/support') ?>" class="back-link">← Quay lại danh sách</a>
  <h1>🎫 #<?= $tid ?> — <?= htmlspecialchars($ticket['subject']) ?></h1>
  <p style="color:#6b7280;font-size:.85rem;margin-bottom:24px">
    Trạng thái: <?= ['open'=>'🟡 Đang mở','answered'=>'🔵 Đã trả lời','closed'=>'⚫ Đã đóng'][$ticket['status']] ?> · 
    Tạo lúc: <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?>
  </p>

  <?php foreach($replies as $r): ?>
  <div class="reply-card <?= $r['is_admin'] ? 'reply-admin' : 'reply-user' ?>">
    <div class="reply-header">
      <strong><?= $r['is_admin'] ? '🛡️ Admin' : '👤 '.htmlspecialchars($r['username']) ?></strong>
      <span style="color:#9ca3af"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></span>
    </div>
    <div class="reply-message"><?= nl2br(htmlspecialchars($r['message'])) ?></div>
  </div>
  <?php endforeach; ?>

  <?php if($ticket['status'] !== 'closed'): ?>
  <form method="POST" style="margin-top:20px">
    <div class="form-group"><textarea name="reply" required placeholder="Nhập phản hồi..."></textarea></div>
    <button type="submit" class="btn btn-primary">📩 Gửi phản hồi</button>
  </form>
  <?php endif; ?>
</div>

<?php require_once(__DIR__ . '/footer.php'); ?>
