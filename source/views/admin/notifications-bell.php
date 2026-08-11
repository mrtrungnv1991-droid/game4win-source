<?php
/**
 * Notification Bell (Punch list #2 — Master Prompt v3 §4.5)
 * Gom toàn bộ alert không nghiêm trọng của Dashboard vào dropdown bell ở topbar.
 * Alert installer.php GIỮ NỔI BẬT riêng trên trang (rủi ro bảo mật thật).
 * Mỗi item dismissible độc lập, lưu localStorage 24h (giữ hành vi cũ).
 * Require: $CMSNT, $getUser available in scope (sidebar.php include sau is_admin.php).
 */
if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$notif_items = [];

// 1) Version / quick-fix bar (quyền view_license)
if (checkPermission($getUser['admin'], 'view_license') == true) {
    $install_hidden_file = $CMSNT->site('install_file_name');
    $has_quick_fix = !empty($install_hidden_file) && file_exists(__DIR__ . '/../../' . $install_hidden_file);
    $auto_update = $CMSNT->site('status_update') == 1;
    $html = '<div class="d-flex align-items-center justify-content-between mb-1">'
        . '<strong><i class="fas fa-code-branch text-primary me-1"></i>' . htmlspecialchars($config['project'], ENT_QUOTES, 'UTF-8') . '</strong>'
        . ($has_quick_fix ? '<button type="button" class="btn btn-sm btn-info py-0 px-1" onclick="runQuickFix()"><i class="fas fa-wrench"></i> ' . __('Sửa lỗi nhanh') . '</button>' : '')
        . '</div>'
        . '<small class="text-muted d-block mb-1">'
        . ($auto_update
            ? '<i class="fas fa-sync-alt text-success me-1"></i>' . __('Tự động cập nhật phiên bản mới đang BẬT.')
            : '<i class="fas fa-sync-alt text-danger me-1"></i>' . __('Tự động cập nhật phiên bản đang TẮT (Cài Đặt → Cài đặt chung).'))
        . '</small>'
        . '<a href="' . base_url_admin('logs&user_id=&username=&content=' . urlencode('Cập nhật hệ thống từ phiên bản') . '&ip=&device=&createdate=&limit=20&shortByDate=') . '" class="small text-primary"><i class="fas fa-history me-1"></i>' . __('Nhật ký update') . '</a>';
    $notif_items[] = ['id' => 'notif_version', 'level' => 'secondary', 'html' => $html];
}

// 2) Thông báo business CMSNT (ngừng bán mã nguồn)
$notif_items[] = [
    'id' => 'notif_cmsnt_business',
    'level' => 'warning',
    'html' => '<strong><i class="fas fa-exclamation-triangle me-1"></i>' . __('THÔNG BÁO QUAN TRỌNG') . '</strong>'
        . '<small class="d-block text-muted">' . __('CMSNT ngừng bán & ngừng hỗ trợ SHOPCLONE6/7. Website đã mua vẫn tiếp tục sử dụng bình thường, không còn hỗ trợ kỹ thuật/cập nhật.') . '</small>'
];

// 3) SMTP chưa cấu hình
if ($CMSNT->site('smtp_status') != 1) {
    $notif_items[] = [
        'id' => 'notif_smtp',
        'level' => 'warning',
        'html' => '<strong><i class="fas fa-envelope me-1"></i>SMTP</strong> ' . __('chưa cấu hình — một số tiện ích Mail không hoạt động.')
            . ' <a class="text-primary" href="https://help.cmsnt.co/huong-dan/huong-dan-cau-hinh-smtp-vao-website-shopclone7/" target="_blank">' . __('Xem Hướng Dẫn') . '</a>'
    ];
}

// 4) Email Queue cron
if ($CMSNT->site('smtp_status') == 1 && time() - $CMSNT->site('check_time_cron_email_queue') >= 120) {
    $cron_url = base_url('cron/process_email_queue.php?key=' . $CMSNT->site('key_cron_job'));
    $notif_items[] = [
        'id' => 'notif_email_queue',
        'level' => 'danger',
        'html' => '<strong><i class="ri-mail-send-line me-1"></i>Email Queue:</strong> ' . __('cần CRON JOB')
            . ' <a class="text-primary" target="_blank" href="' . $cron_url . '">' . __('chạy 1 phút/lần') . '</a> ' . __('để hệ thống tự gửi email.')
    ];
}

// 5) Telegram Queue cron
if ($CMSNT->site('telegram_status') == 1 && time() - $CMSNT->site('check_time_cron_telegram_queue') >= 120) {
    $cron_url = base_url('cron/process_telegram_queue.php?key=' . $CMSNT->site('key_cron_job'));
    $notif_items[] = [
        'id' => 'notif_telegram_queue',
        'level' => 'danger',
        'html' => '<strong><i class="fab fa-telegram me-1"></i>Telegram Queue:</strong> ' . __('cần CRON JOB')
            . ' <a class="text-primary" target="_blank" href="' . $cron_url . '">' . __('chạy 1 phút/lần') . '</a> ' . __('để hệ thống tự gửi thông báo Telegram.')
    ];
}

// 6) Debug Auto Bank đang bật
if ($CMSNT->site('debug_auto_bank') == 1) {
    $notif_items[] = [
        'id' => 'notif_debug_auto_bank',
        'level' => 'danger',
        'html' => '<strong><i class="fas fa-bug me-1"></i>Debug Auto Bank:</strong> ' . __('đang BẬT — hãy tắt trong Cài Đặt khi không cần debug.')
    ];
}

// 7) PHP version mismatch
if (version_compare(PHP_VERSION, '7.4.0', '<') || version_compare(PHP_VERSION, '7.5.0', '>=')) {
    $notif_items[] = [
        'id' => 'notif_php_version',
        'level' => 'danger',
        'html' => '<strong><i class="ri-error-warning-line me-1"></i>' . __('Phiên bản PHP không tương thích!') . '</strong> '
            . __('Yêu cầu PHP 7.4, hiện tại:') . ' <b class="text-danger">PHP ' . PHP_VERSION . '</b>.'
    ];
}

$notif_count = count($notif_items);
$level_dot = ['danger' => '#e6533c', 'warning' => '#f5b849', 'secondary' => '#23b7e5', 'info' => '#49b6f5'];
?>
<!-- Start::notification-bell (Punch #2) -->
<div class="header-element header-notifications dropdown">
    <a href="javascript:void(0);" class="header-link position-relative" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifications">
        <i class="bx bx-bell header-link-icon"></i>
        <?php if ($notif_count > 0): ?>
            <span class="notif-badge"><?= $notif_count ?></span>
        <?php endif; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-end notif-dropdown shadow">
        <div class="notif-header">
            <strong>🔔 <?= __('Thông báo hệ thống') ?> (<?= $notif_count ?>)</strong>
        </div>
        <?php if ($notif_count === 0): ?>
            <div class="notif-empty text-muted"><?= __('Không có thông báo nào.') ?></div>
        <?php else: ?>
            <?php foreach ($notif_items as $n): ?>
                <div class="notif-item border-start border-3" id="<?= $n['id'] ?>" style="border-color: <?= $level_dot[$n['level']] ?? '#845adf' ?> !important;">
                    <div class="notif-body"><?= $n['html'] ?></div>
                    <button type="button" class="notif-dismiss" title="<?= __('Ẩn 24 giờ') ?>" onclick="dismissNotif('<?= $n['id'] ?>')">&times;</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<style>
    .notif-badge {
        position: absolute; top: 2px; right: 0;
        background: #e6533c; color: #fff; font-size: 10px; font-weight: 700;
        min-width: 16px; height: 16px; line-height: 16px; text-align: center;
        border-radius: 8px; padding: 0 4px;
    }
    .notif-dropdown { width: 360px; max-width: 90vw; padding: 0; }
    .notif-header { padding: .65rem .9rem; border-bottom: 1px solid #f3f3f3; background: #fff; }
    .notif-empty { padding: 1rem; text-align: center; font-size: .85rem; }
    .notif-item {
        position: relative; padding: .6rem 1.6rem .6rem .9rem;
        border-bottom: 1px solid #f3f3f3; font-size: .82rem; line-height: 1.45;
        background: #fff;
    }
    .notif-item:last-child { border-bottom: 0; }
    .notif-dismiss {
        position: absolute; top: .35rem; right: .45rem; border: 0; background: transparent;
        color: #8c9097; font-size: 1rem; line-height: 1; cursor: pointer; padding: .1rem .3rem;
    }
    .notif-dismiss:hover { color: #e6533c; }
    .notif-dropdown .dropdown-menu { max-height: 420px; overflow-y: auto; }
</style>
<script>
    function dismissNotif(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.style.display = 'none';
        try { localStorage.setItem('hide_' + id, Date.now() + 24 * 60 * 60 * 1000); } catch (e) {}
        var badge = document.querySelector('.notif-badge');
        if (badge) {
            var n = parseInt(badge.textContent, 10) - 1;
            if (n <= 0) { badge.remove(); } else { badge.textContent = n; }
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.notif-item').forEach(function(el) {
            try {
                var until = localStorage.getItem('hide_' + el.id);
                if (until && Date.now() < parseInt(until, 10)) { el.style.display = 'none'; }
            } catch (e) {}
        });
    });
    <?php if (checkPermission($getUser['admin'], 'view_license') == true && !empty($install_hidden_file) && file_exists(__DIR__ . '/../../' . $install_hidden_file)): ?>
    function runQuickFix() {
        Swal.fire({
            title: '<?= __('Xác nhận sửa lỗi nhanh'); ?>',
            text: '<?= __('Hệ thống sẽ chạy lại cập nhật cơ sở dữ liệu. Bạn có muốn tiếp tục?'); ?>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= __('Thực hiện'); ?>',
            cancelButtonText: '<?= __('Hủy'); ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= BASE_URL($install_hidden_file); ?>',
                    method: 'GET',
                    success: function() {
                        Swal.fire({
                            title: '<?= __('Thành công!'); ?>',
                            text: '<?= __('Đã chạy sửa lỗi nhanh thành công.'); ?>',
                            icon: 'success',
                            confirmButtonText: '<?= __('Đóng'); ?>'
                        }).then(() => { location.reload(); });
                    },
                    error: function() {
                        Swal.fire('<?= __('Lỗi!'); ?>', '<?= __('Có lỗi xảy ra.'); ?>', 'error');
                    }
                });
            }
        });
    }
    <?php endif; ?>
</script>
<!-- End::notification-bell -->
