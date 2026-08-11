<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['reply_to'])) {
  $orig=$CMSNT->get_row("SELECT * FROM messages WHERE id=".intval($_POST['reply_to']));
  if($orig){$CMSNT->insert('messages',['sender_id'=>$getUser['id'],'receiver_id'=>$orig['sender_id'],'subject'=>'Re: '.($orig['subject']??''),'message'=>check_string($_POST['message']),'reply_to'=>$orig['id']]); $CMSNT->update('messages',['is_read'=>1],"id=".intval($_POST['reply_to'])); $msg='<div class="alert alert-success">Đã trả lời!</div>';}
}
if(isset($_GET['read'])){$CMSNT->update('messages',['is_read'=>1],"id=".intval($_GET['read']));}
$unread=$CMSNT->num_rows("SELECT id FROM messages WHERE (receiver_id={$getUser['id']} OR receiver_id IS NULL) AND is_read=0");
$uid = intval($getUser['id']);
$messages=$CMSNT->get_list("SELECT m.*,u.username as sender_name FROM messages m LEFT JOIN users u ON m.sender_id=u.id WHERE m.receiver_id=$uid OR m.receiver_id IS NULL ORDER BY m.is_read ASC,m.id DESC LIMIT 50");
$body=['title'=>'Messages — Admin'];
require_once(__DIR__.'/../admin/header.php'); require_once(__DIR__.'/../admin/sidebar.php');
?>
<div class="main-content app-content"><div class="container-fluid">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h1 class="page-title mb-0">📬 Messages <span class="badge bg-danger ml-2"><?= $unread ?> chưa đọc</span></h1>
  </div>
  <?= $msg ?>
  <div class="card custom-card"><div class="card-body table-responsive p-0">
    <table class="table table-hover table-striped mb-0">
      <thead><tr><th style="width:50px">ID</th><th>Người gửi</th><th>Tiêu đề</th><th>Nội dung</th><th style="width:90px">TT</th><th style="width:100px">Thời gian</th><th style="width:120px"></th></tr></thead>
      <tbody>
        <?php if(empty($messages)): ?><tr><td colspan="7" class="text-center text-muted py-4">Không có tin nhắn</td></tr>
        <?php else: foreach($messages as $m): ?>
        <tr style="<?= $m['is_read']?'':'font-weight:bold;background:#f0f7ff' ?>">
          <td>#<?= $m['id'] ?></td><td><b><?= htmlspecialchars($m['sender_name']??'System') ?></b></td>
          <td><?= htmlspecialchars($m['subject']??'—') ?></td>
          <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($m['message']) ?></td>
          <td><?= $m['is_read']?'<span class="badge bg-success">Đã đọc</span>':'<span class="badge bg-warning">Mới</span>' ?></td>
          <td><small><?= date('d/m H:i',strtotime($m['created_at'])) ?></small></td>
          <td><?php if(!$m['is_read']):?><a href="?module=adcp&action=messages&read=<?= $m['id'] ?>" class="btn btn-xs btn-info mr-1">✓</a><?php endif ?><button class="btn btn-xs btn-primary" onclick="replyTo(<?= $m['id'] ?>,'<?= htmlspecialchars(addslashes($m['sender_name']??'System')) ?>')">↩ TL</button></td></tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div></div>
</div></div>
<div class="modal fade" id="replyModal"><div class="modal-dialog"><div class="modal-content">
  <form method="POST"><div class="modal-header"><h5>↩ Trả lời <span id="replyToName"></span></h5><button class="close" data-dismiss="modal">&times;</button></div>
  <div class="modal-body"><input type="hidden" name="reply_to" id="replyToId"><textarea name="message" class="form-control" rows="4" required placeholder="Nhập nội dung..."></textarea></div>
  <div class="modal-footer"><button class="btn btn-primary">📩 Gửi</button></div></form>
</div></div></div>
<script>function replyTo(i,n){document.getElementById('replyToId').value=i;document.getElementById('replyToName').textContent=n;$('#replyModal').modal('show')}</script>
<?php require_once(__DIR__.'/../admin/footer.php'); ?>
