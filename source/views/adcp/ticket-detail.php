<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$tid = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ticket = $CMSNT->get_row_safe("SELECT t.*, u.username FROM tickets t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = ?", [$tid]);
if (!$ticket) { die('Ticket không tồn tại!'); }

// Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reply'])) {
        $CMSNT->insert('ticket_replies', ['ticket_id'=>$tid, 'user_id'=>$getUser['id'], 'message'=>check_string($_POST['reply']), 'is_admin'=>1]);
        $CMSNT->update('tickets', ['status'=>'answered'], "id = $tid");
    }
    if (isset($_POST['close'])) {
        $CMSNT->update('tickets', ['status'=>'closed'], "id = $tid");
    }
    if (isset($_POST['reopen'])) {
        $CMSNT->update('tickets', ['status'=>'open'], "id = $tid");
    }
}

$replies = $CMSNT->get_list_safe("SELECT tr.*, u.username FROM ticket_replies tr LEFT JOIN users u ON tr.user_id = u.id WHERE tr.ticket_id = ? ORDER BY tr.id ASC", [$tid]);

$body = ['title' => "Ticket #{$tid} — Admin"];
require_once(__DIR__ . '/../admin/header.php');
require_once(__DIR__ . '/../admin/sidebar.php');
require_once(__DIR__ . '/../admin/nav.php');
?>

<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h1>🎫 Ticket #<?= $tid ?></h1>
    <a href="?module=adcp&action=ticket-list" class="btn btn-sm btn-default" style="margin-left:12px">← Danh sách</a>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><?= htmlspecialchars($ticket['subject']) ?></h3>
            <span class="badge badge-<?= ['open'=>'warning','answered'=>'info','closed'=>'secondary'][$ticket['status']] ?> ml-2">
              <?= ['open'=>'🟡 Đang mở','answered'=>'🔵 Đã TL','closed'=>'⚫ Đã đóng'][$ticket['status']] ?>
            </span>
          </div>
          <div class="card-body">
            <?php foreach($replies as $r): ?>
            <div style="padding:12px;margin-bottom:10px;border-radius:8px;border-left:3px solid <?= $r['is_admin']?'#3b82f6':'#10b981' ?>;background:<?= $r['is_admin']?'#eff6ff':'#f0fdf4' ?>">
              <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:.82rem">
                <strong><?= $r['is_admin'] ? '🛡️ Admin' : '👤 '.htmlspecialchars($r['username']) ?></strong>
                <span style="color:#9ca3af"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></span>
              </div>
              <div style="font-size:.9rem;line-height:1.6;white-space:pre-wrap"><?= nl2br(htmlspecialchars($r['message'])) ?></div>
            </div>
            <?php endforeach; ?>

            <?php if($ticket['status'] !== 'closed'): ?>
            <form method="POST" style="margin-top:16px">
              <textarea name="reply" class="form-control" rows="3" required placeholder="Nhập phản hồi..."></textarea>
              <button type="submit" class="btn btn-primary mt-2">📩 Gửi phản hồi</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Thông tin</h3></div>
          <div class="card-body">
            <p><b>User:</b> <?= htmlspecialchars($ticket['username']) ?></p>
            <p><b>Ưu tiên:</b> <?= ['low'=>'🟢 Thấp','medium'=>'🟡 TB','high'=>'🔴 Cao'][$ticket['priority']] ?></p>
            <p><b>Tạo lúc:</b> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></p>
            <hr>
            <?php if($ticket['status'] !== 'closed'): ?>
            <form method="POST"><button type="submit" name="close" class="btn btn-warning btn-block" onclick="return confirm('Đóng ticket này?')">🔒 Đóng ticket</button></form>
            <?php else: ?>
            <form method="POST"><button type="submit" name="reopen" class="btn btn-success btn-block">🔓 Mở lại</button></form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once(__DIR__ . '/../admin/footer.php'); ?>
