-- BẮT BUỘC chạy migration này để chống double-credit triệt để (race condition).
-- Nếu không có UNIQUE INDEX này, 2 webhook đến cùng lúc (cùng transid) vẫn có thể
-- lọt qua bước kiểm tra ở code và cộng tiền 2 lần.
--
-- Trước khi chạy: kiểm tra xem đã có transid trùng nhau trong bảng chưa (dữ liệu cũ
-- có thể đã bị double-credit trước khi vá lỗi này). Chạy câu lệnh kiểm tra bên dưới
-- trước, nếu có kết quả thì cần xử lý thủ công (hoàn tiền/điều chỉnh) trước khi thêm
-- UNIQUE INDEX, nếu không lệnh ALTER TABLE sẽ báo lỗi và không chạy được.

-- 1. Kiểm tra transid trùng lặp đã tồn tại trong dữ liệu cũ chưa:
SELECT `transid`, COUNT(*) as so_lan
FROM `dongtien`
WHERE `transid` IS NOT NULL AND `transid` != ''
GROUP BY `transid`
HAVING COUNT(*) > 1;

-- 2. Nếu bước 1 không trả về dòng nào (không có trùng lặp), chạy lệnh này:
ALTER TABLE `dongtien` ADD UNIQUE INDEX `uniq_transid` (`transid`);


-- =====================================================================
-- Bảng payment_bank (webhook_pay2s.php, webhook_web2m.php) cũng có cùng
-- kiểu lỗ hổng: kiểm tra trùng lặp (tid + description) rồi mới INSERT,
-- có khoảng hở race condition giữa lúc kiểm tra và lúc insert nếu 2
-- webhook đến cùng lúc báo cùng 1 giao dịch ngân hàng.
-- Money vẫn được bảo vệ nhờ UNIQUE INDEX ở dongtien.transid phía trên,
-- nhưng nên thêm UNIQUE ở đây để tránh sinh ra 2 dòng payment_bank trùng
-- nhau cho cùng 1 giao dịch ngân hàng thực tế (dữ liệu audit sạch hơn).

-- Kiểm tra trùng lặp dữ liệu cũ trước:
SELECT `tid`, `description`, COUNT(*) as so_lan
FROM `payment_bank`
GROUP BY `tid`, `description`
HAVING COUNT(*) > 1;

-- Nếu không có dòng nào trùng, chạy:
ALTER TABLE `payment_bank` ADD UNIQUE INDEX `uniq_tid_description` (`tid`, `description`(191));
