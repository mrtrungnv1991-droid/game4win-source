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
