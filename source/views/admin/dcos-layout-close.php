<?php
/**
 * DCOS Layout — closing tags.
 * Include after module content.
 */
if (!defined('IN_SITE')) die('The Request Not Found');
?>
</div><!-- /p-8 -->

<!-- Footer -->
<footer class="mt-auto border-t border-slate-200 bg-white py-4 px-8 flex justify-between items-center text-xs text-slate-400">
    <p>© <?= date('Y') ?> Digital Commerce OS — Powered by ShopClone7</p>
    <div class="flex gap-4">
        <a href="<?= base_url_admin('settings') ?>" class="hover:text-[var(--primary)] transition-colors">Cài đặt</a>
        <a href="<?= base_url() ?>" class="hover:text-[var(--primary)] transition-colors">Về Shop</a>
    </div>
</footer>

</main>
</div><!-- /min-h-screen -->

<!-- Toast container -->
<script>
function showToast(msg, type='success') {
    var t = document.createElement('div');
    t.className = 'fixed bottom-6 right-6 px-5 py-3 rounded-lg text-white text-sm font-bold shadow-lg z-[999] transition-all ' + 
        (type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500');
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.style.opacity='0'; setTimeout(function(){ t.remove(); }, 300); }, 3000);
}

// Sidebar submenu toggle
document.addEventListener('DOMContentLoaded', function() {
    // Toggle submenu on click
    document.querySelectorAll('.has-sub > .side-menu__item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            var parent = this.parentElement;
            parent.classList.toggle('open');
        });
    });
    
    // Auto-open submenu if has active item inside
    document.querySelectorAll('.has-sub').forEach(function(submenu) {
        if (submenu.querySelector('.side-menu__item.active')) {
            submenu.classList.add('open');
        }
    });
});
</script>
</body>
</html>