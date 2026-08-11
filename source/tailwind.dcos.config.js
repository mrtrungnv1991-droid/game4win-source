/** Tailwind build config cho DCOS shell (Punch #4 — self-host thay CDN).
 *  Content quét 6 module DCOS + 2 layout file. Build ra dcos-tailwind.css. */
module.exports = {
  content: [
    './views/admin/dcos-layout.php',
    './views/admin/dcos-layout-close.php',
    './views/admin/api-keys.php',
    './views/admin/group-buy-admin.php',
    './views/admin/competitor-research.php',
    './views/admin/dynamic-pricing.php',
    './views/admin/smart-routing.php',
    './views/admin/trend-detection.php',
    './views/admin/manual-orders.php',
  ],
  // Class được toggle bằng JS (classList.add/remove) — phải safelist để không bị purge.
  safelist: [
    'hidden', 'open',
    'bg-emerald-500', 'bg-red-500', 'bg-blue-500',
    'opacity-0', 'opacity-100',
  ],
  theme: { extend: {} },
  plugins: [],
};
