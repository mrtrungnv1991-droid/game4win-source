-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th7 01, 2026 lúc 11:09 AM
-- Phiên bản máy phục vụ: 11.4.12-MariaDB-log
-- Phiên bản PHP: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `gamewinn_shopclone7`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_request_logs`
--

CREATE TABLE `admin_request_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_url` mediumtext NOT NULL,
  `request_method` varchar(10) NOT NULL,
  `request_params` mediumtext DEFAULT NULL,
  `ip` varchar(45) NOT NULL,
  `user_agent` mediumtext NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_role`
--

CREATE TABLE `admin_role` (
  `id` int(11) NOT NULL,
  `name` mediumtext DEFAULT NULL,
  `role` longtext DEFAULT NULL CHECK (json_valid(`role`)),
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_role`
--

INSERT INTO `admin_role` (`id`, `name`, `role`, `create_gettime`, `update_gettime`) VALUES
(1, 'Super Admin', '[\"view_license\",\"view_statistical\",\"view_recent_transactions\",\"view_logs\",\"view_transactions\",\"view_block_ip\",\"edit_block_ip\",\"view_automations\",\"edit_automations\",\"view_user\",\"edit_user\",\"login_user\",\"view_role\",\"edit_role\",\"view_recharge\",\"edit_recharge\",\"view_affiliate\",\"view_withdraw_affiliate\",\"edit_withdraw_affiliate\",\"edit_affiliate\",\"view_email_campaigns\",\"edit_email_campaigns\",\"view_coupon\",\"edit_coupon\",\"view_promotion\",\"edit_promotion\",\"view_blog\",\"edit_blog\",\"view_product\",\"edit_product\",\"edit_stock_product\",\"view_orders_product\",\"refund_orders_product\",\"view_order_product\",\"delete_order_product\",\"manager_suppliers\",\"view_sold_product\",\"view_menu\",\"edit_menu\",\"view_lang\",\"edit_lang\",\"view_currency\",\"edit_currency\",\"edit_theme\",\"edit_setting\"]', '2023-11-16 20:28:54', '2024-08-10 12:57:40'),
(2, 'Sales', '[\"view_logs\",\"view_transactions\",\"view_user\",\"view_affiliate\",\"view_withdraw_affiliate\",\"view_coupon\"]', '2023-11-16 20:41:10', '2023-11-16 20:53:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `aff_log`
--

CREATE TABLE `aff_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `reason` mediumtext DEFAULT NULL,
  `sotientruoc` float NOT NULL DEFAULT 0,
  `sotienthaydoi` float NOT NULL DEFAULT 0,
  `sotienhientai` float NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `aff_log`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `aff_withdraw`
--

CREATE TABLE `aff_withdraw` (
  `id` int(11) NOT NULL,
  `trans_id` mediumtext DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `bank` mediumtext DEFAULT NULL,
  `stk` mediumtext DEFAULT NULL,
  `name` mediumtext DEFAULT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `status` varchar(25) NOT NULL DEFAULT 'pending',
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `reason` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `automations`
--

CREATE TABLE `automations` (
  `id` int(11) NOT NULL,
  `name` mediumtext DEFAULT NULL,
  `type` varchar(55) DEFAULT NULL,
  `product_id` longtext DEFAULT NULL,
  `schedule` int(11) NOT NULL DEFAULT 0,
  `other` mediumtext DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banks`
--

CREATE TABLE `banks` (
  `id` int(11) NOT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `image` mediumtext DEFAULT NULL,
  `accountName` mediumtext DEFAULT NULL,
  `accountNumber` mediumtext DEFAULT NULL,
  `password` mediumtext DEFAULT NULL,
  `token` mediumtext DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `banks`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `block_ip`
--

CREATE TABLE `block_ip` (
  `id` int(11) NOT NULL,
  `ip` mediumtext DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `banned` int(11) NOT NULL DEFAULT 0,
  `reason` mediumtext DEFAULT NULL,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `block_ip`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `trans_id` varchar(255) DEFAULT NULL,
  `telco` varchar(255) DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `price` int(11) NOT NULL DEFAULT 0,
  `serial` mediumtext DEFAULT NULL,
  `pin` mediumtext DEFAULT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'pending',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `reason` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cards`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `id_api` int(11) NOT NULL DEFAULT 0,
  `supplier_id` int(11) NOT NULL DEFAULT 0,
  `stt` int(11) NOT NULL DEFAULT 0,
  `icon` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `keywords` mediumtext DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `create_date` datetime NOT NULL,
  `api_time_update` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(64) DEFAULT NULL,
  `product_id` longtext NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `used` int(11) NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `min` int(11) NOT NULL DEFAULT 1000,
  `max` int(11) NOT NULL DEFAULT 10000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupon_used`
--

CREATE TABLE `coupon_used` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `trans_id` varchar(255) DEFAULT NULL,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ctv_withdraw`
--

CREATE TABLE `ctv_withdraw` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `bank` text DEFAULT NULL,
  `stk` text DEFAULT NULL,
  `name` text NOT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `fee` float NOT NULL DEFAULT 0,
  `receive` float NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` enum('pending','completed','cancel') NOT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `name` mediumtext DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `rate` float NOT NULL DEFAULT 0,
  `symbol_left` mediumtext DEFAULT NULL,
  `symbol_right` mediumtext DEFAULT NULL,
  `seperator` mediumtext DEFAULT NULL,
  `display` int(11) NOT NULL DEFAULT 1,
  `default_currency` int(11) NOT NULL DEFAULT 0,
  `decimal_currency` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`, `rate`, `symbol_left`, `symbol_right`, `seperator`, `display`, `default_currency`, `decimal_currency`) VALUES
(3, 'Đồng', 'VND', 1, '', 'đ', 'dot', 1, 1, 0),
(4, 'Dollar', 'USD', 25000, '$', '', 'dot', 1, 0, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `deposit_log`
--

CREATE TABLE `deposit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `method` varchar(255) DEFAULT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `received` float NOT NULL DEFAULT 0,
  `create_time` int(11) DEFAULT 0,
  `is_virtual` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `deposit_log`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dongtien`
--

CREATE TABLE `dongtien` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `sotientruoc` decimal(20,2) NOT NULL DEFAULT 0.00,
  `sotienthaydoi` decimal(20,2) NOT NULL DEFAULT 0.00,
  `sotiensau` decimal(20,2) NOT NULL DEFAULT 0.00,
  `thoigian` datetime NOT NULL,
  `noidung` mediumtext DEFAULT NULL,
  `transid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dongtien`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_campaigns`
--

CREATE TABLE `email_campaigns` (
  `id` int(11) NOT NULL,
  `name` mediumtext DEFAULT NULL,
  `subject` mediumtext DEFAULT NULL,
  `cc` mediumtext DEFAULT NULL,
  `bcc` mediumtext DEFAULT NULL,
  `content` longblob DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_queue`
--

CREATE TABLE `email_queue` (
  `id` int(11) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `to_name` varchar(255) DEFAULT '',
  `subject` varchar(998) NOT NULL,
  `body` longtext NOT NULL,
  `priority` tinyint(4) DEFAULT 3 COMMENT '1=high, 5=low',
  `status` enum('pending','processing','sent','failed') DEFAULT 'pending',
  `attempts` int(11) DEFAULT 0,
  `max_attempts` int(11) DEFAULT 3,
  `error_message` text DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `attachment_data` longtext DEFAULT NULL COMMENT 'Base64 encoded file content',
  `attachment_name` varchar(255) DEFAULT NULL COMMENT 'Filename for attachment',
  `created_at` datetime NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `last_attempt_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `email_sending`
--

CREATE TABLE `email_sending` (
  `id` int(11) NOT NULL,
  `camp_id` int(11) DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `response` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_attempts`
--

CREATE TABLE `failed_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL,
  `type` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `failed_attempts`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `lang` varchar(255) DEFAULT NULL,
  `code` varchar(55) DEFAULT NULL,
  `stt` int(11) NOT NULL DEFAULT 0,
  `icon` mediumtext DEFAULT NULL,
  `lang_default` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `languages`
--

INSERT INTO `languages` (`id`, `lang`, `code`, `stt`, `icon`, `lang_default`, `status`) VALUES
(1, 'Vietnamese', 'vi', 0, 'assets/storage/flags/flag_Vietnamese.png', 0, 1),
(2, 'English', 'en', 0, 'assets/storage/flags/flag_English.png', 1, 1),
(19, 'Thailand', 'th', 0, 'assets/storage/flags/flag_Thailand.png', 0, 1),
(20, 'Chinese', 'zh', 0, 'assets/storage/flags/flag_Chinese.png', 0, 1),
(21, 'Korea', 'KOR', 0, 'assets/storage/flags/flag_Korea.png', 0, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `createdate` datetime NOT NULL,
  `action` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `logs`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `log_bank_auto`
--

CREATE TABLE `log_bank_auto` (
  `id` int(11) NOT NULL,
  `tid` varchar(55) DEFAULT NULL,
  `method` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `type` mediumtext DEFAULT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `log_bank_auto`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `log_ref`
--

CREATE TABLE `log_ref` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `reason` mediumtext DEFAULT NULL,
  `sotientruoc` float NOT NULL DEFAULT 0,
  `sotienthaydoi` float NOT NULL DEFAULT 0,
  `sotienhientai` float NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `slug` mediumtext DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `href` mediumtext DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `target` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 3,
  `content` longtext DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `momo`
--

CREATE TABLE `momo` (
  `id` int(11) NOT NULL,
  `request_id` varchar(64) DEFAULT NULL,
  `tranId` varchar(255) DEFAULT NULL,
  `partnerId` mediumtext DEFAULT NULL,
  `partnerName` mediumtext DEFAULT NULL,
  `amount` mediumtext DEFAULT NULL,
  `received` int(11) NOT NULL DEFAULT 0,
  `comment` mediumtext DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT 0,
  `status` varchar(32) DEFAULT 'xuly'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_log`
--

CREATE TABLE `order_log` (
  `id` int(11) NOT NULL,
  `buyer` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `pay` float NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL,
  `is_virtual` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_log`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_bakong`
--

CREATE TABLE `payment_bakong` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái giao dịch: 0=pending,1=success,2=fail...',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `checkout_url` varchar(255) DEFAULT NULL,
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_bank`
--

CREATE TABLE `payment_bank` (
  `id` int(11) NOT NULL,
  `method` varchar(55) DEFAULT NULL,
  `tid` varchar(255) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `amount` int(11) DEFAULT 0,
  `received` int(11) DEFAULT 0,
  `create_gettime` datetime DEFAULT NULL,
  `create_time` int(11) DEFAULT 0,
  `user_id` int(11) DEFAULT 0,
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Đang đổ dữ liệu cho bảng `payment_bank`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_crypto`
--

CREATE TABLE `payment_crypto` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(55) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `request_id` varchar(55) DEFAULT NULL,
  `amount` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `received` float NOT NULL DEFAULT 0,
  `exchange_rate` decimal(20,2) NOT NULL DEFAULT 0.00 COMMENT 'Tỷ giá USDT tại thời điểm tạo hóa đơn',
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'waiting',
  `msg` mediumtext DEFAULT NULL,
  `url_payment` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payment_crypto`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_dsociopay`
--

CREATE TABLE `payment_dsociopay` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL COMMENT 'Mã giao dịch nội bộ',
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận (VND)',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán (NGN)',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái: 0=pending, 1=success, 2=failed',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL COMMENT 'Số tài khoản ảo',
  `account_name` varchar(255) DEFAULT NULL COMMENT 'Tên tài khoản',
  `bank_name` varchar(100) DEFAULT NULL COMMENT 'Tên ngân hàng',
  `webhook_transaction_id` varchar(255) DEFAULT NULL COMMENT 'Mã giao dịch webhook',
  `notication` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái thông báo: 0=chưa, 1=đã thông báo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_flutterwave`
--

CREATE TABLE `payment_flutterwave` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `tx_ref` varchar(55) DEFAULT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `price` float NOT NULL DEFAULT 0,
  `currency` mediumtext DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `status` varchar(55) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_korapay`
--

CREATE TABLE `payment_korapay` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thực nhận',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái giao dịch: 0=pending,1=success,2=fail...',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `checkout_url` varchar(255) NOT NULL,
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_lempay`
--

CREATE TABLE `payment_lempay` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL COMMENT 'Mã đơn hàng nội bộ (out_trade_no)',
  `trade_no` varchar(64) DEFAULT NULL COMMENT 'Mã giao dịch LemPay',
  `type` varchar(20) DEFAULT NULL COMMENT 'Phương thức: alipay, wxpay, usdt',
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận (VND)',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán (CNY)',
  `status` tinyint(4) DEFAULT 0 COMMENT '0=pending, 1=success, 2=failed',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `payurl` text DEFAULT NULL COMMENT 'Link thanh toán',
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_manual`
--

CREATE TABLE `payment_manual` (
  `id` int(11) NOT NULL,
  `icon` mediumtext DEFAULT NULL,
  `title` mediumtext DEFAULT NULL,
  `slug` mediumtext DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `display` int(11) NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_momo`
--

CREATE TABLE `payment_momo` (
  `id` int(11) NOT NULL,
  `method` varchar(55) DEFAULT NULL,
  `tid` varchar(55) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `amount` int(11) DEFAULT 0,
  `received` int(11) DEFAULT 0,
  `create_gettime` datetime DEFAULT NULL,
  `create_time` int(11) DEFAULT 0,
  `user_id` int(11) DEFAULT 0,
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_openpix`
--

CREATE TABLE `payment_openpix` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái giao dịch: 0=pending,1=success,2=fail...',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `checkout_url` varchar(255) NOT NULL,
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_paymentpoint`
--

CREATE TABLE `payment_paymentpoint` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL COMMENT 'Mã giao dịch nội bộ',
  `customer_id` varchar(100) DEFAULT NULL COMMENT 'Customer ID từ PaymentPoint',
  `reserved_account_id` varchar(100) DEFAULT NULL COMMENT 'Reserved Account ID',
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận (VND)',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán (NGN)',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái: 0=pending, 1=success, 2=failed',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL COMMENT 'Số tài khoản ảo',
  `account_name` varchar(255) DEFAULT NULL COMMENT 'Tên tài khoản',
  `bank_name` varchar(100) DEFAULT NULL COMMENT 'Tên ngân hàng',
  `bank_code` varchar(20) DEFAULT NULL COMMENT 'Mã ngân hàng',
  `webhook_transaction_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_paypal`
--

CREATE TABLE `payment_paypal` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trans_id` varchar(255) DEFAULT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `price` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_pm`
--

CREATE TABLE `payment_pm` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `payment_id` varchar(255) DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `price` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_date` datetime NOT NULL,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_pocketfi`
--

CREATE TABLE `payment_pocketfi` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL COMMENT 'Mã giao dịch nội bộ',
  `payment_id` varchar(64) DEFAULT NULL COMMENT 'Mã giao dịch từ PocketFi',
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận (VND)',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán (NGN)',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái: 0=pending, 1=success, 2=failed',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `checkout_url` varchar(255) DEFAULT NULL,
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_squadco`
--

CREATE TABLE `payment_squadco` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `transaction_ref` varchar(55) DEFAULT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_thesieure`
--

CREATE TABLE `payment_thesieure` (
  `id` int(11) NOT NULL,
  `method` varchar(55) DEFAULT NULL,
  `tid` varchar(55) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `received` int(11) NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_tmweasyapi`
--

CREATE TABLE `payment_tmweasyapi` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận',
  `amount` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thanh toán',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái giao dịch: 0=pending,1=success,2=fail...',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `checkout_url` varchar(255) NOT NULL,
  `notication` int(11) NOT NULL DEFAULT 0,
  `id_pay` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_toyyibpay`
--

CREATE TABLE `payment_toyyibpay` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `trans_id` varchar(50) DEFAULT NULL,
  `billName` mediumtext DEFAULT NULL,
  `amount` float NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `BillCode` varchar(50) DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `reason` mediumtext DEFAULT NULL,
  `notication` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_tripay`
--

CREATE TABLE `payment_tripay` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL COMMENT 'Mã đơn hàng nội bộ (merchant_ref)',
  `reference` varchar(64) DEFAULT NULL COMMENT 'Mã tham chiếu TriPay',
  `method` varchar(20) DEFAULT NULL COMMENT 'Phương thức: BRIVA, QRIS, OVO...',
  `price` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thực nhận (VND)',
  `amount` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền thanh toán (IDR)',
  `status` tinyint(4) DEFAULT 0 COMMENT '0=pending, 1=success, 2=failed/expired',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `checkout_url` text DEFAULT NULL COMMENT 'Link thanh toán',
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_xipay`
--

CREATE TABLE `payment_xipay` (
  `id` int(11) NOT NULL,
  `out_trade_no` varchar(64) NOT NULL,
  `transaction_id` varchar(64) DEFAULT NULL COMMENT 'Mã giao dịch do Xipay trả về',
  `type` varchar(20) DEFAULT NULL COMMENT 'Phương thức thanh toán (alipay, wxpay...)',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thực nhận',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán',
  `param` varchar(255) DEFAULT NULL COMMENT 'Tham số mở rộng',
  `product_name` varchar(255) DEFAULT NULL COMMENT 'Tên sản phẩm/dịch vụ',
  `status` tinyint(4) DEFAULT 0 COMMENT 'Trạng thái giao dịch: 0=pending,1=success,2=fail...',
  `notify_data` mediumtext DEFAULT NULL COMMENT 'Lưu dữ liệu notify (nếu cần)',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'ID user trong hệ thống (nếu có)',
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment_zinipay`
--

CREATE TABLE `payment_zinipay` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(64) NOT NULL COMMENT 'Mã đơn hàng nội bộ',
  `trade_no` varchar(64) DEFAULT NULL COMMENT 'Invoice ID từ ZiniPay',
  `type` varchar(20) DEFAULT NULL COMMENT 'Phương thức: bkash, nagad...',
  `price` decimal(20,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Số tiền thanh toán (BDT)',
  `status` tinyint(4) DEFAULT 0 COMMENT '0=pending, 1=success, 2=failed',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `payurl` text DEFAULT NULL COMMENT 'Link thanh toán',
  `notication` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `stt` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `title` mediumtext DEFAULT NULL,
  `image` mediumtext DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `view` int(11) NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `post_category`
--

CREATE TABLE `post_category` (
  `id` int(11) NOT NULL,
  `name` mediumtext DEFAULT NULL,
  `slug` mediumtext NOT NULL,
  `content` longtext NOT NULL,
  `icon` mediumtext DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `stt` int(11) NOT NULL DEFAULT 0,
  `code` varchar(55) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `short_desc` mediumtext DEFAULT NULL,
  `images` mediumtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `note` mediumtext DEFAULT NULL,
  `price` float NOT NULL DEFAULT 0,
  `cost` float NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `min` int(111) NOT NULL DEFAULT 1,
  `max` int(11) NOT NULL DEFAULT 1000000,
  `flag` mediumtext DEFAULT NULL,
  `sold` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `check_live` varchar(55) DEFAULT 'None',
  `supplier_id` int(11) NOT NULL DEFAULT 0,
  `api_id` mediumtext DEFAULT NULL,
  `api_name` mediumtext DEFAULT NULL,
  `api_stock` int(11) NOT NULL DEFAULT 0,
  `api_time_update` int(11) NOT NULL DEFAULT 0,
  `text_txt` mediumtext DEFAULT NULL,
  `order_by` int(11) NOT NULL DEFAULT 1,
  `allow_api` int(11) NOT NULL DEFAULT 1,
  `hide_in_shop` int(11) NOT NULL DEFAULT 0,
  `preview_uid` int(11) NOT NULL DEFAULT 0,
  `pending` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_die`
--

CREATE TABLE `product_die` (
  `id` int(11) NOT NULL,
  `product_code` varchar(55) DEFAULT NULL,
  `seller` int(11) NOT NULL DEFAULT 0,
  `uid` varchar(55) DEFAULT NULL,
  `account` mediumtext DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `type` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_discount`
--

CREATE TABLE `product_discount` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `min` int(11) NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_order`
--

CREATE TABLE `product_order` (
  `id` int(11) NOT NULL,
  `trans_id` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `supplier_id` int(11) NOT NULL DEFAULT 0,
  `product_name` mediumtext DEFAULT NULL,
  `buyer` int(11) NOT NULL DEFAULT 0,
  `seller` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  `money` float NOT NULL DEFAULT 0,
  `pay` float NOT NULL DEFAULT 0,
  `cost` int(11) NOT NULL DEFAULT 0,
  `commission_fee` float NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `trash` int(11) NOT NULL DEFAULT 0,
  `refund` int(11) NOT NULL DEFAULT 0,
  `ip` mediumtext DEFAULT NULL,
  `device` mediumtext DEFAULT NULL,
  `status_view_order` int(11) NOT NULL DEFAULT 0,
  `api_transid` mediumtext DEFAULT NULL,
  `note` mediumtext DEFAULT NULL,
  `topup_tier_id` int(11) DEFAULT NULL,
  `topup_status` enum('pending','processing','success','failed','refunded') DEFAULT 'pending',
  `game_uid` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_order`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_sold`
--

CREATE TABLE `product_sold` (
  `id` int(11) NOT NULL,
  `product_code` varchar(55) DEFAULT NULL,
  `trans_id` mediumtext DEFAULT NULL,
  `supplier_id` int(11) NOT NULL DEFAULT 0,
  `buyer` int(11) NOT NULL DEFAULT 0,
  `seller` int(11) NOT NULL DEFAULT 0,
  `uid` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `account` mediumtext DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `time_check_live` int(11) NOT NULL DEFAULT 0,
  `type` varchar(55) DEFAULT 'WEB'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_sold`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_stock`
--

CREATE TABLE `product_stock` (
  `id` int(11) NOT NULL,
  `product_code` varchar(55) DEFAULT NULL,
  `seller` int(11) NOT NULL DEFAULT 0,
  `uid` varchar(55) DEFAULT NULL,
  `account` mediumtext DEFAULT NULL,
  `create_gettime` datetime NOT NULL,
  `type` varchar(55) DEFAULT 'WEB',
  `time_check_live` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_stock`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `min` float NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `create_gettime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `min`, `discount`, `create_gettime`) VALUES
(5, 7000000, 10, '2026-06-10 15:54:07'),
(6, 5000000, 7, '2026-06-10 21:40:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'status_demo', '0'),
(2, 'type_password', 'bcrypt'),
(3, 'title', 'Game4win'),
(4, 'description', 'ALL GAME ROBLOX BEST PRICE, LOW PRICE'),
(5, 'keywords', 'ALL GAME ROBLOX BEST PRICE, LOW PRICE'),
(6, 'author', 'Game4win'),
(7, 'timezone', 'Asia/Ho_Chi_Minh'),
(8, 'email', ''),
(9, 'status', '1'),
(10, 'status_update', '1'),
(12, 'session_login', '10000000'),
(13, 'javascript_header', ''),
(14, 'javascript_footer', ''),
(16, 'logo_light', 'assets/storage/images/logo_light_YM4.jpg'),
(17, 'logo_dark', 'assets/storage/images/logo_dark_WEU.jpg'),
(18, 'favicon', 'assets/storage/images/favicon_W58.png'),
(19, 'image', 'assets/storage/images/image_YH3.jpg'),
(20, 'bg_login', 'assets/storage/images/bg_loginBYI.png'),
(21, 'bg_register', 'assets/storage/images/bg_registerMOU.png'),
(26, 'telegram_token', ''),
(27, 'telegram_chat_id', ''),
(30, 'prefix_autobank', 'Donate'),
(35, 'bank_status', '0'),
(36, 'bank_notice', '<ul>\r\n	<li>Nạp ít nh&acirc;́t 27k (27.000 VND)&nbsp;- Min pay1$ ( dưới 27k, &lt;1$ kh&ocirc;ng h&ocirc;̃ trợ).&nbsp;Nhập đ&uacute;ng nội dung chuyển tiền.</li>\r\n	<li>Cộng tiền trong v&agrave;i gi&acirc;y.</li>\r\n	<li>Li&ecirc;n hệ Admin nếu nhập sai nội dung chuyển.</li>\r\n	<li>Enter the correct payment note.</li>\r\n	<li>Funds will be added within seconds.</li>\r\n	<li>Contact Admin if the payment note is incorrect.</li>\r\n</ul>\r\n'),
(43, 'notice_home', '<h4>Warranty Policy</h4>\r\n\r\n<h4>&bull; All digital profiles are verified before listing.</h4>\r\n\r\n<h4>&bull; For incorrect password claims, a full video recording from purchase to first login is required.</h4>\r\n\r\n<h4>&bull; Customers must update login credentials within 24 hours of purchase.</h4>\r\n\r\n<h4>&bull; Products marked &quot;Not Recommended for Exploits/Hacking&quot; are not covered if security issues, restrictions, or suspensions occur due to such activities.</h4>\r\n\r\n<h4>&bull; Security-related issues are covered for 10 days after purchase (subject to verification).</h4>\r\n\r\n<h4>&bull; Access restrictions, suspensions, or terminations caused by exploits, unauthorized software, automation tools, or rule violations are not covered.</h4>\r\n\r\n<h4>&bull; Additional proof may be requested before any claim is approved.</h4>\r\n'),
(44, 'font_family', 'font-family: \'Saira Semi Condensed\', sans-serif;'),
(59, 'popup_status', '1'),
(60, 'popup_noti', '<p>If you need support regarding issues such as Security lock, Wrong Password, Banned&nbsp;please contact The Discord account #mrtee6868<br />\r\nIf you encounter Face ID verification issues, you can use your smartphone to unlock the account by logging in and scanning the QR code. This is a new security feature from Roblox and does not cause any problems. Comes with a 3-day warranty from the time of purchase if the account gets banned.</p>\r\n'),
(64, 'license_key', '8e47cbf341dbb56e9cfbc91fed119a06'),
(69, 'home_page', 'topup-home'),
(70, 'smtp_host', 'smtp.gmail.com'),
(71, 'smtp_encryption', 'tls'),
(72, 'smtp_port', '587'),
(73, 'smtp_email', ''),
(74, 'smtp_password', ''),
(76, 'default_product_image', 'assets/storage/images/default_product_imageJK1.jpg'),
(77, 'status_captcha', '0'),
(78, 'crypto_note', '<p>When your customer deposits funds using crypto through the website, the amount will be transferred directly to your wallet. The system charges a 0.25% fee on each successful transaction.<br />\r\nFor example, if a customer deposits 100 USD, the system will deduct 0.25 USD from your balance.<br />\r\n<strong><em>(Remember to add an extra 1 USD for the USDT transfer fee.)</em></strong></p>\r\n'),
(79, 'crypto_address', ''),
(80, 'crypto_token', ''),
(81, 'crypto_min', '10'),
(82, 'crypto_max', '100000'),
(83, 'crypto_status', '1'),
(84, 'crypto_rate', '25000'),
(85, 'reCAPTCHA_site_key', ''),
(86, 'reCAPTCHA_secret_key', ''),
(87, 'reCAPTCHA_status', '0'),
(88, 'telegram_status', '0'),
(89, 'smtp_status', '0'),
(93, 'affiliate_ck', '10'),
(94, 'affiliate_status', '1'),
(95, 'affiliate_min', '10000'),
(96, 'affiliate_banks', 'Vietcombank\r\nMBBank\r\nTechcombank'),
(97, 'affiliate_note', '<p>Chia sẻ&nbsp;li&ecirc;n kết n&agrave;y l&ecirc;n mạng x&atilde; hội hoặc bạn b&egrave; của bạn.</p>\r\n'),
(98, 'affiliate_chat_id_telegram', '1048444403'),
(99, 'check_time_cron_cron2', '0'),
(100, 'bank_min', '5000'),
(101, 'bank_max', '1000000000'),
(102, 'paypal_clientId', 'AbaDGw_KzL_-Q-4o_KmdENgjbZLrBugI_vio5gh5-ArNZg2I8ATYVfQ78u0wGzxkJkl8zE6lSScBasdr'),
(103, 'paypal_clientSecret', 'EDnx_dB2JXplDt43LQSmy7iaQmn8K1MqntQKB6qUumZhlErLmV6zCpkymNj4gt1A5HZC3R8rewUSzLtb'),
(104, 'paypal_status', '1'),
(105, 'paypal_rate', '24000'),
(108, 'paypal_note', ''),
(109, 'noti_recharge', '[{time}] <b>{username}</b> vừa nạp {amount} vào {method} thực nhận {price}.'),
(110, 'noti_action', '[{time}] \r\n- <b>Username</b>: <code>{username}</code>\r\n- <b>Action</b>:  <code>{action}</code>\r\n- <b>IP</b>: <code>{ip}</code>'),
(111, 'theme_color', '#f66504'),
(112, 'hotline', ''),
(113, 'type_notification', 'telegram'),
(114, 'perfectmoney_status', '0'),
(115, 'perfectmoney_account', ''),
(116, 'perfectmoney_pass', ''),
(117, 'perfectmoney_rate', '23000'),
(118, 'perfectmoney_units', ''),
(119, 'perfectmoney_notice', ''),
(120, 'fanpage', ''),
(121, 'address', '#mrtee6868'),
(122, 'toyyibpay_status', '0'),
(123, 'toyyibpay_userSecretKey', ''),
(124, 'toyyibpay_categoryCode', ''),
(125, 'toyyibpay_min', '1'),
(126, 'toyyibpay_billChargeToCustomer', '0'),
(127, 'toyyibpay_rate', '5258'),
(128, 'toyyibpay_notice', ''),
(129, 'noti_affiliate_withdraw', '[{time}] \r\n- <b>Username</b>: <code>{username}</code>\r\n- <b>Action</b>:  <code>Tạo lệnh rút {amount} về ngân hàng {bank} | {account_number} | {account_name}</code>\r\n- <b>IP</b>: <code>{ip}</code>'),
(130, 'check_time_cron_sending_email', '1715250984'),
(131, 'squadco_status', '0'),
(132, 'squadco_Secret_Key', ''),
(133, 'squadco_Public_Key', ''),
(134, 'squadco_rate', '51'),
(135, 'squadco_currency_code', 'NGN'),
(136, 'squadco_notice', ''),
(137, 'theme_color1', '#142850'),
(138, 'product_photo_display', '1'),
(139, 'product_rating_display', '0'),
(140, 'product_sold_display', '1'),
(141, 'banner_singer', 'assets/storage/images/banner_singerUZ4.jpg'),
(142, 'image_empty_state', 'assets/storage/images/image_empty_stateNPV.png'),
(143, 'copyright_footer', 'Software By <a href=\"https://www.cmsnt.co/\">CMSNT.CO</a>'),
(144, 'menu_category_right', '1'),
(145, 'crypto_trial', '5'),
(146, 'type_show_product', 'BOX_5'),
(147, 'check_time_cron_bank', '1782878943'),
(148, 'google_analytics_status', '0'),
(149, 'google_analytics_id', ''),
(150, 'card_status', '1'),
(151, 'card_partner_id', '16654919157'),
(152, 'card_partner_key', 'bc3299820230bb1ed2b2b729cac744e3'),
(153, 'card_ck', '20'),
(154, 'card_notice', '<pre>\r\nA 20% conversion fee applies. Please select your information carefully. Adding the wrong information will result in the card being automatically deleted and no refunds will be possible.\r\n\r\nMin 10k VND\r\n</pre>\r\n'),
(155, 'api_status', '1'),
(156, 'time_cron_suppliers_shopclone6', '1778845234'),
(157, 'time_cron_suppliers_api1', '1711653105'),
(158, 'language_type', 'gtranslate'),
(159, 'gtranslate_script', '<div class=\"gtranslate_wrapper\"></div>\r\n<script>window.gtranslateSettings = {\"default_language\":\"vi\",\"languages\":[\"vi\",\"fr\",\"de\",\"it\",\"es\",\"zh-CN\",\"ar\",\"tr\",\"ru\",\"uk\",\"km\",\"th\",\"en\"],\"wrapper_selector\":\".gtranslate_wrapper\"}</script>\r\n<script src=\"https://cdn.gtranslate.net/widgets/latest/dropdown.js\" defer></script>'),
(160, 'notice_top_left', 'Welcome to Game4Win – Your #1 destination for cheap and trusted Roblox deals!'),
(161, 'page_contact', ''),
(162, 'page_policy', '<p>All sales records and transactions conducted through our platform are authentic and accurately maintained.</p>\r\n\r\n<p>We are committed to protecting customer information and utilize industry-standard security practices to safeguard personal data and payment information. All data transmitted during the payment process is encrypted to provide a secure transaction environment.</p>\r\n\r\n<p>Users are strictly prohibited from using software, automated tools, scripts, or any other methods to interfere with the operation of the website or alter its data structure. Any attempt to disrupt services, gain unauthorized access, manipulate data, or damage system resources is prohibited. Violations may result in the suspension of access privileges and, where applicable, legal action.</p>\r\n\r\n<p>Our platform provides digital products, virtual assets, and related gaming services in accordance with the Terms of Service published on Game4win.net.</p>\r\n\r\n<p>Customers must provide accurate and up-to-date registration information and are responsible for maintaining the confidentiality of their login credentials and access information. Users are also responsible for all activities conducted through their registered profile and must promptly report any unauthorized access or security concerns. We shall not be liable for losses arising from a user&#39;s failure to protect their access information.</p>\r\n\r\n<p>Unless expressly authorized in writing, no portion of the website, its services, digital products, or related content may be used for commercial redistribution, brokerage, or third-party business activities. Violations may result in immediate restriction or termination of service access without prior notice.</p>\r\n\r\n<p>By using our platform, customers acknowledge and agree to comply with all applicable policies, guidelines, and terms governing the use of our services.</p>\r\n'),
(163, 'page_faq', ''),
(164, 'page_block_ip', NULL),
(165, 'email_temp_content_warning_login', '<p>Ch&uacute;ng t&ocirc;i vừa ph&aacute;t hiện t&agrave;i khoản <strong>{username}</strong> của bạn đang được đăng nhập v&agrave;o hệ thống {domain}.<br />\r\nNếu kh&ocirc;ng phải bạn vui l&ograve;ng thay đổi th&ocirc;ng tin t&agrave;i khoản ngay hoặc li&ecirc;n hệ ngay cho ch&uacute;ng t&ocirc;i để hỗ trợ kiểm tra an to&agrave;n cho qu&yacute; kh&aacute;ch.</p>\r\n\r\n<ul>\r\n	<li>Thời gian: {time}</li>\r\n	<li>IP: {ip}</li>\r\n	<li>Thiết bị: {device}</li>\r\n</ul>\r\n'),
(166, 'email_temp_subject_warning_login', 'Cảnh báo đăng nhập tài khoản - {title}'),
(167, 'email_temp_content_otp_mail', '<p>OTP x&aacute;c minh đăng nhập v&agrave;o t&agrave;i khoản <strong>{username}</strong> của bạn l&agrave; <strong>{otp}</strong><br />\r\nNếu kh&ocirc;ng phải bạn vui l&ograve;ng thay đổi th&ocirc;ng tin t&agrave;i khoản ngay hoặc li&ecirc;n hệ ngay cho ch&uacute;ng t&ocirc;i để hỗ trợ kiểm tra an to&agrave;n cho qu&yacute; kh&aacute;ch.</p>\r\n\r\n<ul>\r\n	<li>Thời gian: {time}</li>\r\n	<li>IP: {ip}</li>\r\n	<li>Thiết bị: {device}</li>\r\n</ul>\r\n'),
(168, 'email_temp_subject_otp_mail', 'OTP xác minh đăng nhập website - {title}'),
(169, 'email_temp_content_forgot_password', '<p>Để x&aacute;c minh kh&ocirc;i phục mật khẩu t&agrave;i khoản <strong>{username}</strong> tại website <strong>{domain}</strong><br />\r\nVui l&ograve;ng nhấn v&agrave;o li&ecirc;n kết dưới đ&acirc;y để ho&agrave;n tất qu&aacute; tr&igrave;nh x&aacute;c minh: {link}<br />\r\nNếu kh&ocirc;ng phải bạn y&ecirc;u cầu kh&ocirc;i phục mật khẩu, vui l&ograve;ng bỏ qua mail n&agrave;y.</p>\r\n\r\n<ul>\r\n	<li>Thời gian: {time}</li>\r\n	<li>IP: {ip}</li>\r\n	<li>Thiết bị: {device}</li>\r\n</ul>\r\n'),
(170, 'email_temp_subject_forgot_password', 'Xác nhận khôi phục mật khẩu website - {title}'),
(171, 'time_cron_suppliers_api6', '1723709086'),
(172, 'time_cron_checklive_clone', '1740738217'),
(173, 'time_cron_checklive_hotmail', '1711615443'),
(174, 'product_hide_outstock', '1'),
(175, 'time_cron_suppliers_api14', '1710930652'),
(176, 'max_show_product_home', '20'),
(177, 'email_temp_content_buy_order', '<p><span style=\"font-size:16px\">Cảm ơn bạn đ&atilde; mua h&agrave;ng tại {title}, dưới đ&acirc;y l&agrave; th&ocirc;ng tin đơn h&agrave;ng của bạn. Nếu kh&ocirc;ng phải bạn vui l&ograve;ng thay đổi th&ocirc;ng tin t&agrave;i khoản ngay hoặc li&ecirc;n hệ ngay cho ch&uacute;ng t&ocirc;i để hỗ trợ kiểm tra an to&agrave;n cho qu&yacute; kh&aacute;ch.</span></p>\r\n\r\n<ul>\r\n	<li><span style=\"font-size:14px\">M&atilde; đơn h&agrave;ng: <strong>#{trans_id}</strong></span></li>\r\n	<li><span style=\"font-size:14px\">Sản phẩm:<strong> {product}</strong></span></li>\r\n	<li><span style=\"font-size:14px\">Số lượng: <span style=\"color:#3498db\"><strong>{amount}</strong></span></span></li>\r\n	<li><span style=\"font-size:14px\">Thanh to&aacute;n: <span style=\"color:#e74c3c\"><strong>{pay}</strong></span></span></li>\r\n</ul>\r\n\r\n<p><span style=\"font-size:14px\">Để đảm bảo an to&agrave;n, ch&uacute;ng t&ocirc;i khuy&ecirc;n bạn n&ecirc;n x&oacute;a lịch sử đơn h&agrave;ng tr&ecirc;n hệ thống sau khi nhận được Email n&agrave;y.</span></p>\r\n\r\n<p><em>Thiết bị: {device} - IP: {ip}</em></p>\r\n'),
(178, 'email_temp_subject_buy_order', 'Chi tiết đơn hàng {product} - {title}'),
(179, 'time_cron_suppliers_shopclone7', '1782878918'),
(180, 'time_cron_suppliers_api18', '1711615441'),
(181, 'avatar', 'assets/storage/images/avatarDUL.jpg'),
(182, 'check_time_cron_momo', '1747313677'),
(183, 'momo_number', '0817966668'),
(184, 'momo_name', 'NGUYEN VIET TRUNG'),
(185, 'momo_token', ''),
(186, 'momo_notice', ''),
(187, 'momo_status', '0'),
(188, 'script_footer_admin', ''),
(189, 'time_cron_suppliers_api19', '1711555019'),
(190, 'cot_so_du_ben_phai', '1'),
(191, 'time_cron_suppliers_api4', '1711863683'),
(192, 'status_giao_dich_gan_day', '0'),
(193, 'content_gd_mua_gan_day', '<b style=\"color: green;\">...{username}</b> mua <b style=\"color: red;\">{amount}</b> <b>{product_name}</b> với giá <b style=\"color:blue;\">{price}</b>'),
(194, 'content_gd_nap_tien_gan_day', '<b style=\"color: green;\">...{username}</b> thực hiện nạp <b style=\"color:blue;\">{amount}</b> bằng <b style=\"color:red;\">{method}</b> thực nhận <b style=\"color:blue;\">{received}</b>'),
(195, 'status_tao_gd_ao', '1'),
(196, 'sl_mua_toi_thieu_gd_ao', '1'),
(197, 'sl_mua_toi_da_gd_ao', '7'),
(198, 'toc_do_gd_mua_ao', '100'),
(199, 'menh_gia_nap_ao_ngau_nhien', '40000\r\n50000\r\n60000\r\n70000\r\n100000\r\n200000\r\n300000\r\n500000\r\n400000\r\n40000\r\n15000\r\n25000\r\n35000\r\n45000\r\n55000\r\n65000\r\n45000\r\n100000\r\n1500000\r\n200000'),
(200, 'toc_do_gd_nap_ao', '100'),
(201, 'method_nap_ao', 'MB\r\nUSDT\r\nPayPal'),
(202, 'tao_gd_ao_sp_het_hang', '1'),
(203, 'check_time_cron_cron', '1772082427'),
(204, 'blog_status', '0'),
(205, 'cong_tien_nguoi_ban', '0'),
(206, 'noti_buy_product', '[{time}] <b>{username}</b> vừa mua {amount} tài khoản {product} với giá {pay} - #{trans_id}'),
(207, 'check_time_cron_task', '1726908868'),
(208, 'thoi_gian_mua_cach_nhau', '3'),
(209, 'max_register_ip', '5'),
(210, 'time_cron_suppliers_api20', '1715439606'),
(211, 'status_menu_tools', '0'),
(212, 'debug_auto_bank', '1'),
(213, 'time_cron_suppliers_api9', '1721537978'),
(214, 'debug_api_suppliers', '0'),
(215, 'order_by_product_home', '1'),
(216, 'token_webhook_web2m', 'EBF3F38F-2B5C-E140-4939-93992DF2D552'),
(217, 'time_cron_suppliers_api21', '0'),
(218, 'time_cron_suppliers_api17', '1722102324'),
(219, 'api_check_live_gmail', ''),
(220, 'api_key_check_live_gmail', ''),
(221, 'time_cron_checklive_gmail', '1722164111'),
(222, 'time_limit_check_live_gmail', '1800'),
(223, 'widget_zalo1_status', '0'),
(224, 'widget_zalo1_sdt', ''),
(225, 'widget_phone1_status', '0'),
(226, 'widget_phone1_sdt', ''),
(227, 'flutterwave_status', '0'),
(228, 'flutterwave_rate', '16'),
(229, 'flutterwave_currency_code', 'NGN'),
(230, 'flutterwave_publicKey', ''),
(231, 'flutterwave_secretKey', ''),
(232, 'flutterwave_notice', ''),
(233, 'limit_block_ip_login', '10'),
(234, 'limit_block_client_login', '15'),
(235, 'limit_block_ip_api', '5'),
(236, 'limit_block_ip_admin_access', '10'),
(237, 'time_cron_suppliers_api22', '1724076154'),
(238, 'isPurchaseIpVerified', '0'),
(239, 'isPurchaseDeviceVerified', '0'),
(240, 'footer_card', ''),
(241, 'notice_orders', ''),
(242, 'widget_fbzalo2_status', '0'),
(243, 'widget_fbzalo2_zalo', ''),
(244, 'widget_fbzalo2_fb', ''),
(245, 'time_cron_suppliers_api23', '0'),
(246, 'show_btn_category_home', '1'),
(247, 'time_cron_suppliers_api24', '0'),
(248, 'status_only_ip_login_admin', '0'),
(249, 'time_cron_checklive_instagram', '1735476466'),
(250, 'check_time_cron_thesieure', '0'),
(251, 'thesieure_status', '0'),
(252, 'thesieure_number', '0999999999'),
(253, 'thesieure_email', 'mail@mail.com'),
(254, 'thesieure_token', ''),
(255, 'thesieure_notice', ''),
(256, 'thesieure_name', 'NGUYEN TAN THANH'),
(257, 'crypto_type_api', 'fpayment.net'),
(258, 'crypto_merchant_id', '6825dff077536'),
(259, 'crypto_api_key', '155edc1c2974c584e2e7ef2d1ad66eef6825dff077549'),
(260, 'time_cron_suppliers_api25', '1734801278'),
(261, 'api_check_live_instagram', ''),
(262, 'api_key_check_live_instagram', ''),
(263, 'time_limit_check_live_instagram', '10'),
(266, 'isLoginRequiredToViewProduct', '0'),
(267, 'telegram_assistant_status', '0'),
(268, 'telegram_assistant_token', ''),
(269, 'telegram_assistant_list_username', ''),
(271, 'telegram_assistant_LicenseKey', ''),
(272, 'status_only_device_client', '1'),
(273, 'status_only_device_admin', '1'),
(274, 'is_uid_visible', '1'),
(275, 'list_network_topup_card', 'VIETTEL|Viettel\r\nVINAPHONE|Vinaphone\r\nMOBIFONE|Mobifone\r\nVNMOBI|Vietnamobile\r\nZING|Zing\r\nVCOIN|Vcoin\r\nGARENA|Garena (chỉ nhận thẻ trên 10k)\r\n'),
(276, 'gateway_xipay_status', '0'),
(277, 'xipay_notice', ''),
(278, 'xipay_min', '1'),
(279, 'xipay_max', '1000000'),
(280, 'gateway_xipay_md5key', ''),
(281, 'gateway_xipay_pid', ''),
(282, 'gateway_xipay_rate', '3508'),
(283, 'gateway_xipay_license', ''),
(284, 'domains', 'game4win.net,www.game4win.net'),
(285, 'telegram_assistant_secret_token', '5f2b0e7decf4d5e18ca1f7d038f4afa52a83645b3390bc3677f4005476385ca1'),
(286, 'korapay_status', '0'),
(287, 'korapay_secretKey', ''),
(288, 'korapay_min', '1'),
(289, 'korapay_max', '1000000'),
(290, 'korapay_notice', ''),
(291, 'korapay_currency_code', 'NGN'),
(292, 'korapay_rate', '17'),
(293, 'korapay_proxy', ''),
(294, 'korapay_license', ''),
(295, 'tmweasyapi_status', '0'),
(296, 'tmweasyapi_username', ''),
(297, 'tmweasyapi_password', ''),
(298, 'tmweasyapi_con_id', ''),
(299, 'tmweasyapi_license', ''),
(300, 'tmweasyapi_rate', '756'),
(301, 'tmweasyapi_notice', ''),
(302, 'tmweasyapi_min', '1'),
(303, 'tmweasyapi_max', '1000000'),
(304, 'chatgpt_api_key', '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'),
(305, 'chatgpt_model', 'gpt-3.5-turbo'),
(306, 'openpix_status', '0'),
(307, 'openpix_api_key', ''),
(308, 'openpix_HMAC_key', ''),
(309, 'openpix_HMAC_key_completed', ''),
(310, 'openpix_license', ''),
(311, 'openpix_rate', '4357'),
(312, 'openpix_notice', ''),
(313, 'openpix_min', '1'),
(314, 'openpix_max', '1000000'),
(315, 'limit_block_ip_reset_password', '10'),
(316, 'limit_block_ip_otp', '10'),
(317, 'limit_block_ip_2fa', '5'),
(318, 'task_24h', '1772022187'),
(319, 'limit_block_ip_spam', '10'),
(320, 'limit_block_ip_payment', '10'),
(321, 'bakong_status', '0'),
(322, 'bakong_profile_id', ''),
(323, 'bakong_profile_key', ''),
(324, 'bakong_license', ''),
(325, 'bakong_rate', '25000'),
(326, 'bakong_notice', ''),
(327, 'bakong_min', '1'),
(328, 'bakong_max', '1000000'),
(329, 'bakong_proxy', ''),
(330, 'icon_hotline', '<i class=\"fa-solid fa-phone\"></i>'),
(331, 'icon_address', '<i class=\"fab fa-discord\"></i>'),
(332, 'icon_email', '<i class=\"fa-solid fa-envelope\"></i>'),
(333, 'time_cron_suppliers_api26', '0'),
(334, 'time_cron_suppliers_api27', '0'),
(335, 'time_cron_suppliers_api28', '0'),
(336, 'time_cron_suppliers_api29', '0'),
(337, 'time_cron_suppliers_api30', '0'),
(338, 'telegram_proxy_type', 'HTTP'),
(339, 'telegram_proxy', ''),
(340, 'telegram_url', 'https://bypass-telegram.cmsnt.workers.dev/'),
(341, 'tax_vat', '0'),
(342, 'time_cron_suppliers_api31', '0'),
(343, 'auto_refund_order_failed', '1'),
(344, 'google_ads_status', '0'),
(345, 'google_ads_id', ''),
(346, 'auto_refund_order_failed_api', '0'),
(347, 'time_cron_suppliers_api32', '0'),
(348, 'captcha_status', '0'),
(349, 'captcha_type', 'reCAPTCHA'),
(350, 'captcha_site_key', ''),
(351, 'captcha_secret_key', ''),
(352, 'captcha_modules', 'forgot_password,register,login,verify_2fa,verify_otp'),
(353, 'tmweasyapi_watermark_text', ''),
(354, 'tmweasyapi_watermark_color', '#ff0000'),
(355, 'tmweasyapi_watermark_opacity', '0.28'),
(356, 'tmweasyapi_watermark_font_size', '0.08'),
(357, 'limit_check_live_clone', '500'),
(358, 'isConfirmPolicyRegister', '0'),
(359, 'policy_register', ''),
(360, 'db_collation_migrated', '1'),
(361, 'ctv_min_withdraw', '100000'),
(362, 'ctv_prefix_withdraw', 'CTV'),
(363, 'ctv_banks_withdraw', 'USDT\nVCB\nMBBank\nACB'),
(364, 'ctv_notice_withdraw', 'Nội dung thông báo tại trang rút tiền của CTV Panel'),
(365, 'ctv_notice', 'Nội dung thông báo tại trang home của CTV Panel'),
(366, 'ctv_status', '0'),
(367, 'ctv_panel_license', ''),
(368, 'ctv_fee_withdraw', '10'),
(369, 'noti_refund_orders', '[{time}] \r\n- <b>Username</b>: <code>{username}</code>\r\n- <b>Action</b>:  <code>{action}</code>\r\n- <b>IP</b>: <code>{ip}</code>'),
(370, 'noti_api_out_of_money', '<b>⚠️ API đã hết số dư</b>\n\n<b>Website:</b> {domain}\n<b>Người dùng:</b> {username}\n<b>Sản phẩm:</b> {product_name} (ID: <code>{product_id}</code>)\n<b>Nhà cung cấp:</b> {supplier_name}\n<b>Số lượng:</b> {amount} — <b>Tổng:</b> <code>{pay} VND</code>\n<b>Thời gian:</b> {time}\n<b>IP:</b> {ip}\n\nVui lòng nạp thêm số dư để hệ thống tiếp tục xử lý.\n'),
(371, 'limit_block_ip_load_products', '30'),
(372, 'key_cron_job', ''),
(373, 'time_cron_checklive_via', '0'),
(374, 'check_time_cron_gmail', '0'),
(375, 'check_time_cron_hotmail', '0'),
(376, 'check_time_cron_clone', '0'),
(377, 'check_time_cron_via', '0'),
(378, 'check_time_cron_instagram', '0'),
(379, 'crypto_promotions', ''),
(380, 'popup_vat', '0'),
(381, 'time_cron_suppliers_api33', '0'),
(382, 'pocketfi_status', '0'),
(383, 'pocketfi_api_token', ''),
(384, 'pocketfi_business_id', ''),
(385, 'pocketfi_min', '100'),
(386, 'pocketfi_max', '1000000'),
(387, 'pocketfi_notice', ''),
(388, 'pocketfi_currency_code', 'NGN'),
(389, 'pocketfi_rate', '17'),
(390, 'pocketfi_license', ''),
(391, 'paymentpoint_status', '0'),
(392, 'paymentpoint_api_secret', ''),
(393, 'paymentpoint_api_key', ''),
(394, 'paymentpoint_business_id', ''),
(395, 'paymentpoint_bank_codes', '20946,20897'),
(396, 'paymentpoint_currency_code', 'NGN'),
(397, 'paymentpoint_rate', '1'),
(398, 'paymentpoint_min', '100'),
(399, 'paymentpoint_max', '1000000'),
(400, 'paymentpoint_notice', ''),
(401, 'paymentpoint_license', ''),
(402, 'isForUpdateBuy', '0'),
(403, 'time_cron_suppliers_api34', '0'),
(404, 'time_cron_suppliers_api35', '0'),
(405, 'time_cron_suppliers_api36', '0'),
(406, 'sidebar_vat_invoice_status', '1'),
(407, 'time_cron_suppliers_api37', '0'),
(408, 'lempay_status', '0'),
(409, 'lempay_pid', ''),
(410, 'lempay_key', ''),
(411, 'lempay_api_url', 'https://a119a.lempay.com'),
(412, 'lempay_rate', '3500'),
(413, 'lempay_rate_alipay', ''),
(414, 'lempay_rate_wxpay', ''),
(415, 'lempay_rate_usdt', ''),
(416, 'lempay_min', '1'),
(417, 'lempay_max', '10000'),
(418, 'lempay_notice', ''),
(419, 'lempay_license', ''),
(420, 'lempay_name', 'LemPay (AliPay, WeChat, USDT)'),
(421, 'lempay_icon', 'mod/img/logo-lempay.webp'),
(422, 'tripay_status', '0'),
(423, 'tripay_api_key', ''),
(424, 'tripay_private_key', ''),
(425, 'tripay_merchant_code', ''),
(426, 'tripay_sandbox', '0'),
(427, 'tripay_rate', '1'),
(428, 'tripay_min', '10000'),
(429, 'tripay_max', '10000000'),
(430, 'tripay_notice', ''),
(431, 'tripay_name', 'TriPay Indonesia'),
(432, 'tripay_icon', 'mod/img/logo-tripay.webp'),
(433, 'tripay_license', ''),
(434, 'time_cron_suppliers_api38', '0'),
(435, 'time_cron_suppliers_shopkey', '0'),
(436, 'dsociopay_status', '0'),
(437, 'dsociopay_private_key', ''),
(438, 'dsociopay_currency_code', 'NGN'),
(439, 'dsociopay_rate', '1'),
(440, 'dsociopay_notice', ''),
(441, 'dsociopay_license', ''),
(442, 'dsociopay_name', 'DSocioPay'),
(443, 'dsociopay_icon', 'mod/img/dsociopay.png'),
(444, 'dsociopay_webhook_secret', '5ed42b6da44258db'),
(445, 'dsociopay_promotions', ''),
(446, 'bank_recharge_type', 'prefix_id'),
(447, 'bank_recharge_type_license', ''),
(448, 'paymentpoint_name', 'PaymentPoint'),
(449, 'paymentpoint_icon', 'mod/img/paymentpoint.png'),
(450, 'bank_api_type', 'web2m'),
(451, 'smtp_from_email', ''),
(452, 'check_time_cron_email_queue', '0'),
(453, 'check_time_cron_telegram_queue', '0'),
(454, 'preview_uid_license', ''),
(455, 'install_file_name', 'install_wjzm2yciqesx.php'),
(456, 'time_cron_suppliers_api39', '0'),
(457, 'api_check_live_hotmail', ''),
(458, 'api_key_check_live_hotmail', ''),
(459, 'time_limit_check_live_hotmail', '1800'),
(460, 'api_check_live_tiktok', ''),
(461, 'api_key_check_live_tiktok', ''),
(462, 'time_limit_check_live_tiktok', '10'),
(463, 'time_cron_checklive_tiktok', '0'),
(464, 'telegram_shop_status', '0'),
(465, 'telegram_shop_license', ''),
(466, 'telegram_shop_bot_token', ''),
(467, 'telegram_shop_webhook_code', '04a50a54a8e74bcb8ad82c0a0850d85e441c655e1969e9a6d98efadd0e5542cb'),
(468, 'time_cron_suppliers_api40', '0'),
(469, 'time_cron_suppliers_api41', '0'),
(470, 'time_cron_suppliers_api42', '0'),
(471, 'status_google_login', '0'),
(472, 'google_login_client_id', ''),
(473, 'google_login_client_secret', ''),
(474, 'time_cron_suppliers_api43', '0'),
(475, 'leaderboard_status', '0'),
(476, 'leaderboard_periods', 'daily,weekly,monthly,all_time'),
(477, 'leaderboard_limit', '10'),
(478, 'korapay_promotions', ''),
(479, 'check_time_cron_tiktok', '0'),
(480, 'time_cron_suppliers_api44', '0'),
(481, 'time_cron_suppliers_api45', '0'),
(482, 'time_cron_suppliers_api46', '0'),
(483, 'time_cron_suppliers_api47', '0'),
(484, 'time_cron_suppliers_api48', '0'),
(485, 'time_cron_suppliers_api49', '0'),
(486, 'time_cron_suppliers_api50', '0'),
(487, 'telegram_shop_bot_username', ''),
(488, 'time_cron_suppliers_api51', '0'),
(489, 'zinipay_status', '0'),
(490, 'zinipay_api_key', ''),
(491, 'zinipay_api_url', 'https://api.zinipay.com'),
(492, 'zinipay_rate', '300'),
(493, 'zinipay_min', '10'),
(494, 'zinipay_max', '100000'),
(495, 'zinipay_notice', ''),
(496, 'zinipay_license', ''),
(497, 'zinipay_callback_secret', '12e229f600512646efb127bd5f7480e8'),
(498, 'zinipay_name', 'ZiniPay (bKash, Nagad)'),
(499, 'zinipay_icon', 'mod/img/logo-zinipay.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` mediumtext DEFAULT NULL,
  `domain` mediumtext DEFAULT NULL,
  `username` mediumtext DEFAULT NULL,
  `password` mediumtext DEFAULT NULL,
  `api_key` mediumtext DEFAULT NULL,
  `token` mediumtext DEFAULT NULL,
  `coupon` mediumtext DEFAULT NULL,
  `price` mediumtext DEFAULT NULL,
  `discount` float NOT NULL DEFAULT 0,
  `rate` float NOT NULL DEFAULT 1,
  `update_name` mediumtext DEFAULT NULL,
  `proxy` mediumtext DEFAULT NULL,
  `isAutoShow` int(11) NOT NULL DEFAULT 0,
  `child` int(11) NOT NULL DEFAULT 0,
  `sync_category` varchar(55) NOT NULL DEFAULT 'OFF',
  `sync_category_image` varchar(10) NOT NULL DEFAULT 'ON',
  `update_price` mediumtext DEFAULT NULL,
  `roundMoney` varchar(55) NOT NULL DEFAULT 'ON',
  `status` int(11) NOT NULL DEFAULT 1,
  `create_gettime` datetime NOT NULL,
  `update_gettime` datetime NOT NULL,
  `notes` text DEFAULT NULL COMMENT 'Lưu trữ JSON metadata (sync progress, settings...)',
  `list_api_id` longtext DEFAULT NULL COMMENT 'Danh sách Product ID cần lấy từ API',
  `check_string_api` varchar(55) NOT NULL DEFAULT 'ON'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `telegram_logs`
--

CREATE TABLE `telegram_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `command` varchar(100) DEFAULT NULL,
  `params` mediumtext DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `telegram_queue`
--

CREATE TABLE `telegram_queue` (
  `id` int(11) NOT NULL,
  `chat_id` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `priority` tinyint(4) DEFAULT 3 COMMENT '1=high, 5=low',
  `status` enum('pending','processing','sent','failed') DEFAULT 'pending',
  `attempts` int(11) DEFAULT 0,
  `max_attempts` int(11) DEFAULT 3,
  `error_message` text DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `last_attempt_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `translate`
--

CREATE TABLE `translate` (
  `id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL DEFAULT 0,
  `name` longtext DEFAULT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `translate`
--

INSERT INTO `translate` (`id`, `lang_id`, `name`, `value`) VALUES
(1, 1, 'Vui lòng nhập username', 'Vui lòng nhập username'),
(2, 2, 'Vui lòng nhập username', 'Please enter username'),
(3, 1, 'Vui lòng nhập mật khẩu', 'Vui lòng nhập mật khẩu'),
(4, 2, 'Vui lòng nhập mật khẩu', 'Please enter a password'),
(5, 1, 'Vui lòng xác minh Captcha', 'Vui lòng xác minh Captcha'),
(6, 2, 'Vui lòng xác minh Captcha', 'Please verify Captcha'),
(7, 1, 'Thông tin đăng nhập không chính xác', 'Thông tin đăng nhập không chính xác'),
(8, 2, 'Thông tin đăng nhập không chính xác', 'Login information is incorrect'),
(9, 1, 'Vui lòng nhập địa chỉ Email', 'Vui lòng nhập địa chỉ Email'),
(10, 2, 'Vui lòng nhập địa chỉ Email', 'Please enter your email address'),
(11, 1, 'Vui lòng nhập lại mật khẩu', 'Vui lòng nhập lại mật khẩu'),
(12, 2, 'Vui lòng nhập lại mật khẩu', 'Please re-enter your password'),
(13, 1, 'Xác minh mật khẩu không chính xác', 'Xác minh mật khẩu không chính xác'),
(14, 2, 'Xác minh mật khẩu không chính xác', 'Verify password is incorrect'),
(15, 1, 'Tên đăng nhập đã tồn tại trong hệ thống', 'Tên đăng nhập đã tồn tại trong hệ thống'),
(16, 2, 'Tên đăng nhập đã tồn tại trong hệ thống', 'Username already exists in the system'),
(17, 1, 'Địa chỉ email đã tồn tại trong hệ thống', 'Địa chỉ email đã tồn tại trong hệ thống'),
(18, 2, 'Địa chỉ email đã tồn tại trong hệ thống', 'Email address already exists in the system'),
(19, 1, 'IP của bạn đã đạt đến giới hạn tạo tài khoản cho phép', 'IP của bạn đã đạt đến giới hạn tạo tài khoản cho phép'),
(20, 2, 'IP của bạn đã đạt đến giới hạn tạo tài khoản cho phép', 'Your IP has reached the allowable account creation limit'),
(21, 1, 'Đăng ký thành công!', 'Đăng ký thành công!'),
(22, 2, 'Đăng ký thành công!', 'Sign Up Success!'),
(23, 1, 'Tạo tài khoản không thành công, vui lòng thử lại', 'Tạo tài khoản không thành công, vui lòng thử lại'),
(24, 2, 'Tạo tài khoản không thành công, vui lòng thử lại', 'Account creation failed, please try again'),
(25, 1, 'Vui lòng đăng nhập', 'Vui lòng đăng nhập'),
(26, 2, 'Vui lòng đăng nhập', 'please log in'),
(27, 1, 'Lưu thành công', 'Lưu thành công'),
(28, 2, 'Lưu thành công', 'Save successfully'),
(29, 1, 'Lưu thất bại', 'Lưu thất bại'),
(30, 2, 'Lưu thất bại', 'Save failed'),
(31, 1, 'Vui lòng nhập mật khẩu hiện tại', 'Vui lòng nhập mật khẩu hiện tại'),
(32, 2, 'Vui lòng nhập mật khẩu hiện tại', 'Please enter your current password'),
(33, 1, 'Vui lòng nhập mật khẩu mới', 'Vui lòng nhập mật khẩu mới'),
(34, 2, 'Vui lòng nhập mật khẩu mới', 'Please enter a new password'),
(35, 1, 'Mật khẩu mới quá ngắn', 'Mật khẩu mới quá ngắn'),
(36, 2, 'Mật khẩu mới quá ngắn', 'New password is too short'),
(37, 1, 'Xác nhận mật khẩu không chính xác', 'Xác nhận mật khẩu không chính xác'),
(38, 2, 'Xác nhận mật khẩu không chính xác', 'Confirm password is incorrect'),
(39, 1, 'Mật khẩu hiện tại không đúng', 'Mật khẩu hiện tại không đúng'),
(40, 2, 'Mật khẩu hiện tại không đúng', 'Current password is incorrect'),
(41, 1, 'Địa chỉ Email này không tồn tại trong hệ thống', 'Địa chỉ Email này không tồn tại trong hệ thống'),
(42, 2, 'Địa chỉ Email này không tồn tại trong hệ thống', 'This email address does not exist in the system'),
(43, 1, 'Vui lòng thử lại trong ít phút', 'Vui lòng thử lại trong ít phút'),
(44, 2, 'Vui lòng thử lại trong ít phút', 'Please try again in a few minutes'),
(45, 1, 'Nếu bạn yêu cầu đặt lại mật khẩu, vui lòng nhấp vào liên kết bên dưới để xác minh.', 'Nếu bạn yêu cầu đặt lại mật khẩu, vui lòng nhấp vào liên kết bên dưới để xác minh.'),
(46, 2, 'Nếu bạn yêu cầu đặt lại mật khẩu, vui lòng nhấp vào liên kết bên dưới để xác minh.', 'If you require a password reset, please click the link below to verify.'),
(47, 1, 'Nếu không phải là bạn, vui lòng liên hệ ngay với Quản trị viên của bạn để được hỗ trợ về bảo mật.', 'Nếu không phải là bạn, vui lòng liên hệ ngay với Quản trị viên của bạn để được hỗ trợ về bảo mật.'),
(48, 2, 'Nếu không phải là bạn, vui lòng liên hệ ngay với Quản trị viên của bạn để được hỗ trợ về bảo mật.', 'If not you, please contact your Administrator immediately for security assistance.'),
(49, 1, 'Xác nhận tìm mật khẩu website', 'Xác nhận tìm mật khẩu website'),
(50, 2, 'Xác nhận tìm mật khẩu website', 'Confirm to find the website password'),
(51, 1, 'Xác nhận khôi phục mật khẩu', 'Xác nhận khôi phục mật khẩu'),
(52, 2, 'Xác nhận khôi phục mật khẩu', 'Confirm Password Recovery'),
(53, 1, 'Vui lòng kiểm tra Email của bạn để hoàn tất quá trình đặt lại mật khẩu', 'Vui lòng kiểm tra Email của bạn để hoàn tất quá trình đặt lại mật khẩu'),
(54, 2, 'Vui lòng kiểm tra Email của bạn để hoàn tất quá trình đặt lại mật khẩu', 'Please check your Email to complete the password reset process'),
(55, 1, 'Có lỗi hệ thống, vui lòng liên hệ Developer', 'Có lỗi hệ thống, vui lòng liên hệ Developer'),
(56, 2, 'Có lỗi hệ thống, vui lòng liên hệ Developer', 'There is a system error, please contact Developer'),
(57, 1, 'Liên kết không tồn tại', 'Liên kết không tồn tại'),
(58, 2, 'Liên kết không tồn tại', 'Link does not exist'),
(59, 1, 'Thay đổi mật khẩu thành công', 'Thay đổi mật khẩu thành công'),
(60, 2, 'Thay đổi mật khẩu thành công', 'Change password successfully'),
(61, 1, 'Thay đổi mật khẩu thất bại', 'Thay đổi mật khẩu thất bại'),
(62, 2, 'Thay đổi mật khẩu thất bại', 'Password change failed'),
(63, 1, 'Hồ sơ của bạn', 'Hồ sơ của bạn'),
(64, 2, 'Hồ sơ của bạn', 'Your Profile'),
(65, 1, 'Tên đăng nhập', 'Tên đăng nhập'),
(66, 2, 'Tên đăng nhập', 'Username'),
(67, 1, 'Địa chỉ Email', 'Địa chỉ Email'),
(68, 2, 'Địa chỉ Email', 'Email address'),
(69, 1, 'Số điện thoại', 'Số điện thoại'),
(70, 2, 'Số điện thoại', 'Phone number'),
(71, 1, 'Họ và Tên', 'Họ và Tên'),
(72, 2, 'Họ và Tên', 'Full name'),
(73, 1, 'Địa chỉ IP', 'Địa chỉ IP'),
(74, 2, 'Địa chỉ IP', 'IP address'),
(75, 1, 'Thiết bị', 'Thiết bị'),
(76, 2, 'Thiết bị', 'Device'),
(77, 1, 'Đăng ký vào lúc', 'Đăng ký vào lúc'),
(78, 2, 'Đăng ký vào lúc', 'Sign up at'),
(79, 1, 'Đăng nhập gần nhất', 'Đăng nhập gần nhất'),
(80, 2, 'Đăng nhập gần nhất', 'Last login'),
(81, 1, 'Chỉnh sửa thông tin', 'Chỉnh sửa thông tin'),
(82, 2, 'Chỉnh sửa thông tin', 'Edit information'),
(83, 1, 'Thay đổi mật khẩu', 'Thay đổi mật khẩu'),
(84, 2, 'Thay đổi mật khẩu', 'Change password'),
(85, 1, 'Thay đổi mật khẩu đăng nhập của bạn là một cách dễ dàng để giữ an toàn cho tài khoản của bạn.', 'Thay đổi mật khẩu đăng nhập của bạn là một cách dễ dàng để giữ an toàn cho tài khoản của bạn.'),
(86, 2, 'Thay đổi mật khẩu đăng nhập của bạn là một cách dễ dàng để giữ an toàn cho tài khoản của bạn.', 'Changing your login password is an easy way to keep your account secure.'),
(87, 1, 'Mật khẩu hiện tại', 'Mật khẩu hiện tại'),
(88, 2, 'Mật khẩu hiện tại', 'Current password'),
(89, 1, 'Mật khẩu mới', 'Mật khẩu mới'),
(90, 2, 'Mật khẩu mới', 'New password'),
(91, 1, 'Nhập lại mật khẩu mới', 'Nhập lại mật khẩu mới'),
(92, 2, 'Nhập lại mật khẩu mới', 'Re-verify new password'),
(93, 1, 'Cập Nhật', 'Cập Nhật'),
(94, 2, 'Cập Nhật', 'Update'),
(95, 1, 'Đăng Xuất', 'Đăng Xuất'),
(96, 2, 'Đăng Xuất', 'Logout'),
(97, 1, 'Bạn có chắc không?', 'Bạn có chắc không?'),
(98, 2, 'Bạn có chắc không?', 'Are you sure?'),
(99, 1, 'Bạn sẽ bị đăng xuất khỏi tài khoản khi nhấn Đồng Ý', 'Bạn sẽ bị đăng xuất khỏi tài khoản khi nhấn Đồng Ý'),
(100, 2, 'Bạn sẽ bị đăng xuất khỏi tài khoản khi nhấn Đồng Ý', 'You will be posted from the account when click Okey'),
(101, 1, 'Đồng ý', 'Đồng ý'),
(102, 2, 'Đồng ý', 'Okey'),
(103, 1, 'Huỷ bỏ', 'Huỷ bỏ'),
(104, 2, 'Huỷ bỏ', 'Cancel'),
(105, 1, 'Đăng Nhập', 'Đăng Nhập'),
(106, 2, 'Đăng Nhập', 'Sign In'),
(107, 1, 'Vui Lòng Đăng Nhập Để Tiếp Tục', 'Vui Lòng Đăng Nhập Để Tiếp Tục'),
(108, 2, 'Vui Lòng Đăng Nhập Để Tiếp Tục', 'Please Login To Continue'),
(109, 1, 'Quên mật khẩu', 'Quên mật khẩu'),
(110, 2, 'Quên mật khẩu', 'Forgot password'),
(111, 1, 'Bạn quên mật khẩu?', 'Bạn quên mật khẩu?'),
(112, 2, 'Bạn quên mật khẩu?', 'Forgot your password?'),
(113, 1, 'Vui lòng nhập thông tin vào ô dưới đây để xác minh', 'Vui lòng nhập thông tin vào ô dưới đây để xác minh'),
(114, 2, 'Vui lòng nhập thông tin vào ô dưới đây để xác minh', 'Please enter information in the box below to verify'),
(115, 1, 'Xác minh', 'Xác minh'),
(116, 2, 'Xác minh', 'Verification'),
(117, 1, 'Bạn đã có tài khoản?', 'Bạn đã có tài khoản?'),
(118, 2, 'Bạn đã có tài khoản?', 'Do you already have an account?'),
(119, 1, 'Ghi nhớ tôi', 'Ghi nhớ tôi'),
(120, 2, 'Ghi nhớ tôi', 'Remember'),
(121, 1, 'Quên mật khẩu?', 'Quên mật khẩu?'),
(122, 2, 'Quên mật khẩu?', 'Forgot password?'),
(123, 1, 'Bạn chưa có tài khoản?', 'Bạn chưa có tài khoản?'),
(124, 2, 'Bạn chưa có tài khoản?', 'Do not have an account?'),
(125, 1, 'Đăng Ký Ngay', 'Đăng Ký Ngay'),
(126, 2, 'Đăng Ký Ngay', 'Register'),
(127, 1, 'Nạp tiền', 'Nạp tiền'),
(128, 2, 'Nạp tiền', 'Recharge'),
(129, 1, 'Ngân hàng', 'Ngân hàng'),
(130, 2, 'Ngân hàng', 'Bank'),
(131, 1, 'Ví của tôi', 'Ví của tôi'),
(132, 2, 'Ví của tôi', 'My Wallet'),
(133, 1, 'Số dư hiện tại', 'Số dư hiện tại'),
(134, 2, 'Số dư hiện tại', 'Current balance'),
(135, 1, 'Tổng tiền nạp', 'Tổng tiền nạp'),
(136, 2, 'Tổng tiền nạp', 'Total Deposit'),
(137, 1, 'Số dư đã sử dụng', 'Số dư đã sử dụng'),
(138, 2, 'Số dư đã sử dụng', 'Used Balance'),
(139, 1, 'THANH TOÁN', 'Thanh toán'),
(141, 1, 'Lưu ý nạp tiền', 'Lưu ý nạp tiền'),
(142, 2, 'Lưu ý nạp tiền', 'Recharge note'),
(143, 1, 'Lịch sử nạp tiền', 'Lịch sử nạp tiền'),
(144, 2, 'Lịch sử nạp tiền', 'Recharge History'),
(145, 1, 'Số tài khoản:', 'Số tài khoản:'),
(146, 2, 'Số tài khoản:', 'Account number:'),
(147, 1, 'Chủ tài khoản:', 'Chủ tài khoản:'),
(148, 2, 'Chủ tài khoản:', 'Account name:'),
(149, 1, 'Ngân hàng:', 'Ngân hàng:'),
(150, 2, 'Ngân hàng:', 'Bank:'),
(151, 1, 'Nội dung chuyển khoản:', 'Nội dung chuyển khoản:'),
(152, 2, 'Nội dung chuyển khoản:', 'Transfer content:'),
(153, 1, 'Mã giao dịch', 'Mã giao dịch'),
(154, 2, 'Mã giao dịch', 'Transaction'),
(155, 1, 'Nội dung', 'Nội dung'),
(156, 2, 'Nội dung', 'Content'),
(157, 1, 'Số tiền nạp', 'Số tiền nạp'),
(158, 2, 'Số tiền nạp', 'Amount'),
(159, 1, 'Thực nhận', 'Thực nhận'),
(160, 2, 'Thực nhận', 'Received'),
(161, 1, 'Thời gian', 'Thời gian'),
(162, 2, 'Thời gian', 'Time'),
(163, 1, 'Trạng thái', 'Trạng thái'),
(164, 2, 'Trạng thái', 'Status'),
(165, 1, 'Đã thanh toán', 'Đã thanh toán'),
(166, 2, 'Đã thanh toán', 'Paid'),
(167, 1, 'Tất cả', 'Tất cả'),
(168, 2, 'Tất cả', 'ALL'),
(169, 1, 'Hôm nay', 'Hôm nay'),
(170, 2, 'Hôm nay', 'Today'),
(171, 1, 'Tuần này', 'Tuần này'),
(172, 2, 'Tuần này', 'This week'),
(173, 1, 'Tháng này', 'Tháng này'),
(174, 2, 'Tháng này', 'This month'),
(175, 1, 'Đã thanh toán:', 'Đã thanh toán:'),
(176, 2, 'Đã thanh toán:', 'Paid:'),
(177, 1, 'Thực nhận:', 'Thực nhận:'),
(178, 2, 'Thực nhận:', 'Received:'),
(179, 1, 'Thao tác', 'Thao tác'),
(180, 2, 'Thao tác', 'Action'),
(181, 1, 'Nhật ký hoạt động', 'Nhật ký hoạt động'),
(182, 2, 'Nhật ký hoạt động', 'Activity Log'),
(183, 1, 'Tìm kiếm', 'Tìm kiếm'),
(184, 2, 'Tìm kiếm', 'Search'),
(185, 1, 'Bỏ lọc', 'Bỏ lọc'),
(186, 2, 'Bỏ lọc', 'Clear Filter'),
(187, 1, 'Hiển thị', 'Hiển thị'),
(188, 2, 'Hiển thị', 'Show'),
(189, 1, 'Ẩn', 'Ẩn'),
(190, 2, 'Ẩn', 'Hide'),
(191, 1, 'Biến động số dư', 'Biến động số dư'),
(192, 2, 'Biến động số dư', 'Transactions'),
(193, 1, 'Số dư ban đầu', 'Số dư ban đầu'),
(194, 2, 'Số dư ban đầu', 'Initial balance'),
(195, 1, 'Số dư thay đổi', 'Số dư thay đổi'),
(196, 2, 'Số dư thay đổi', 'Balance change'),
(197, 1, 'Lý do', 'Lý do'),
(198, 2, 'Lý do', 'Reason'),
(199, 1, 'Chọn thời gian cần tìm', 'Chọn thời gian cần tìm'),
(200, 2, 'Chọn thời gian cần tìm', 'Choose a time to search'),
(203, 2, 'Hiển thị thêm', 'Show more'),
(204, 1, 'Hiển thị thêm', 'Hiển thị thêm'),
(205, 1, 'Ẩn bớt', 'Ẩn bớt'),
(206, 2, 'Ẩn bớt', 'Hide'),
(207, 1, 'Nội dung chuyển khoản', 'Nội dung chuyển khoản'),
(208, 2, 'Nội dung chuyển khoản', 'Transfer contents'),
(209, 1, 'Đăng nhập bằng Google', 'Đăng nhập bằng Google'),
(210, 2, 'Đăng nhập bằng Google', 'Login with Google'),
(211, 1, 'Đăng nhập bằng Facebook', 'Đăng nhập bằng Facebook'),
(212, 2, 'Đăng nhập bằng Facebook', 'Login with Google'),
(213, 1, 'Đăng ký tài khoản', 'Đăng ký tài khoản'),
(214, 2, 'Đăng ký tài khoản', 'Sign up for an account'),
(215, 1, 'Tài khoản đăng nhập', 'Tài khoản đăng nhập'),
(216, 2, 'Tài khoản đăng nhập', 'Username'),
(217, 1, 'Mật khẩu', 'Mật khẩu'),
(218, 2, 'Mật khẩu', 'Password'),
(219, 1, 'Nhập lại mật khẩu', 'Nhập lại mật khẩu'),
(220, 2, 'Nhập lại mật khẩu', 'Confirm password'),
(221, 1, 'Đăng Ký', 'Đăng Ký'),
(222, 2, 'Đăng Ký', 'Register'),
(223, 1, 'Vui lòng nhập thông tin đăng ký', 'Vui lòng nhập thông tin đăng ký'),
(224, 2, 'Vui lòng nhập thông tin đăng ký', 'Please enter registration information'),
(225, 1, 'Vui lòng nhập thông tin đăng nhập', 'Vui lòng nhập thông tin đăng nhập'),
(226, 2, 'Vui lòng nhập thông tin đăng nhập', 'Please enter login information'),
(227, 1, 'Thông tin cá nhân', 'Thông tin cá nhân'),
(228, 2, 'Thông tin cá nhân', 'Personal information'),
(229, 1, 'Cấu hình nạp tiền Crypto', 'Cấu hình nạp tiền Crypto'),
(230, 2, 'Cấu hình nạp tiền Crypto', 'Configuration Recharge Crypto'),
(231, 1, 'All Time', 'All Time'),
(232, 2, 'All Time', 'Toàn thời gian'),
(235, 1, 'Thống kê thanh toán tháng', 'Thống kê thanh toán tháng'),
(236, 2, 'Thống kê thanh toán tháng', 'Payment Statistics Month'),
(237, 1, 'Lịch sử nạp tiền Crypto', 'Lịch sử nạp tiền Crypto'),
(238, 2, 'Lịch sử nạp tiền Crypto', 'Crypto Deposit History'),
(239, 1, 'Thống kê', 'Thống kê'),
(240, 2, 'Thống kê', 'Statistical'),
(241, 1, 'Cấu hình', 'Cấu hình'),
(242, 2, 'Cấu hình', 'Configuration'),
(243, 1, 'Nạp tối đa', 'Nạp tối đa'),
(244, 2, 'Nạp tối đa', 'Maximum deposit amount'),
(245, 1, 'Nạp tối thiểu', 'Nạp tối thiểu'),
(246, 2, 'Nạp tối thiểu', 'Minimum deposit amount'),
(247, 1, 'Nạp tiền bằng Crypto', 'Nạp tiền bằng Crypto'),
(248, 2, 'Nạp tiền bằng Crypto', 'Deposit with Crypto'),
(249, 1, 'Lưu ý', 'Lưu ý'),
(250, 2, 'Lưu ý', 'Note'),
(251, 1, 'Lịch sử nạp Crypto', 'Lịch sử nạp Crypto'),
(252, 2, 'Lịch sử nạp Crypto', 'Crypto Deposit History'),
(253, 1, 'Số lượng', 'Số lượng'),
(254, 2, 'Số lượng', 'Amount'),
(255, 1, 'Thời gian tạo', 'Thời gian tạo'),
(256, 2, 'Thời gian tạo', 'Create date'),
(257, 1, 'Xem thêm', 'Xem thêm'),
(258, 2, 'Xem thêm', 'See more'),
(259, 1, 'The minimum deposit amount is:', 'The minimum deposit amount is:'),
(261, 1, 'Số tiền gửi tối đa là:', 'Số tiền gửi tối đa là:'),
(262, 2, 'Số tiền gửi tối đa là:', 'The maximum deposit amount is:'),
(263, 1, 'Số tiền gửi tối thiểu là:', 'Số tiền gửi tối thiểu là:'),
(264, 2, 'Số tiền gửi tối thiểu là:', 'The minimum deposit amount is:'),
(265, 1, 'Chức năng này đang được bảo trì', 'Chức năng này đang được bảo trì'),
(266, 2, 'Chức năng này đang được bảo trì', 'This function is under maintenance'),
(267, 1, 'Không thể tạo hóa đơn do lỗi API, vui lòng thử lại sau', 'Không thể tạo hóa đơn do lỗi API, vui lòng thử lại sau'),
(268, 2, 'Không thể tạo hóa đơn do lỗi API, vui lòng thử lại sau', 'Invoice could not be generated due to API error, please try again later'),
(269, 1, 'Tạo hoá đơn nạp tiền thành công', 'Tạo hoá đơn nạp tiền thành công'),
(270, 2, 'Tạo hoá đơn nạp tiền thành công', 'Deposit request created successfully'),
(271, 1, 'Nạp tiền bằng PayPal', 'Nạp tiền bằng PayPal'),
(272, 2, 'Nạp tiền bằng PayPal', 'Pay with PayPal'),
(273, 1, 'Lịch sử nạp PayPal', 'Lịch sử nạp PayPal'),
(274, 2, 'Lịch sử nạp PayPal', 'PayPal Recharge History'),
(275, 1, 'Số tiền gửi', 'Số tiền gửi'),
(276, 2, 'Số tiền gửi', 'Amount'),
(277, 1, 'Vui lòng nhập số tiền cần nạp', 'Vui lòng nhập số tiền cần nạp'),
(278, 2, 'Vui lòng nhập số tiền cần nạp', 'Please enter the amount to deposit'),
(279, 1, 'Mặc định', 'Mặc định'),
(280, 2, 'Mặc định', 'Default'),
(281, 1, 'Phổ biến', 'Phổ biến'),
(282, 2, 'Phổ biến', 'Popular'),
(283, 1, 'Tìm kiếm bài viết', 'Tìm kiếm bài viết'),
(284, 2, 'Tìm kiếm bài viết', 'Find Blogs'),
(285, 1, 'Bài viết phổ biến', 'Bài viết phổ biến'),
(286, 2, 'Bài viết phổ biến', 'Popular Feeds'),
(287, 1, 'Liên kết giới thiệu của bạn', 'Liên kết giới thiệu của bạn'),
(288, 2, 'Liên kết giới thiệu của bạn', 'Your referral link'),
(289, 1, 'Đã sao chép vào bộ nhớ tạm', 'Đã sao chép vào bộ nhớ tạm'),
(290, 2, 'Đã sao chép vào bộ nhớ tạm', 'Copied to clipboard'),
(291, 1, 'Số tài khoản', 'Số tài khoản'),
(292, 2, 'Số tài khoản', 'Account number'),
(293, 1, 'Tên chủ tài khoản', 'Tên chủ tài khoản'),
(294, 2, 'Tên chủ tài khoản', 'Account name'),
(295, 1, 'Số tiền cần rút', 'Số tiền cần rút'),
(296, 2, 'Số tiền cần rút', 'Amount to withdraw'),
(297, 1, 'Rút số dư hoa hồng', 'Rút số dư hoa hồng'),
(298, 2, 'Rút số dư hoa hồng', 'Affiliate Withdraw'),
(299, 1, 'Lịch sử rút tiền', 'Lịch sử rút tiền'),
(300, 2, 'Lịch sử rút tiền', 'Withdraw history'),
(301, 1, 'Rút tiền', 'Rút tiền'),
(302, 2, 'Rút tiền', 'Withdraw'),
(303, 1, 'Lịch sử', 'Lịch sử'),
(304, 2, 'Lịch sử', 'History'),
(305, 1, 'Thao tác quá nhanh, vui lòng chờ', 'Thao tác quá nhanh, vui lòng chờ'),
(306, 2, 'Thao tác quá nhanh, vui lòng chờ', 'You are working too fast, please wait'),
(307, 1, 'Vui lòng chọn ngân hàng cần rút', 'Vui lòng chọn ngân hàng cần rút'),
(308, 2, 'Vui lòng chọn ngân hàng cần rút', 'Please select the bank to withdraw'),
(309, 1, 'Vui lòng nhập số tài khoản cần rút', 'Vui lòng nhập số tài khoản cần rút'),
(310, 2, 'Vui lòng nhập số tài khoản cần rút', 'Please enter the account number to withdraw'),
(311, 1, 'Vui lòng nhập tên chủ tài khoản', 'Vui lòng nhập tên chủ tài khoản'),
(312, 2, 'Vui lòng nhập tên chủ tài khoản', 'Please enter the account name'),
(313, 1, 'Vui lòng nhập số tiền cần rút', 'Vui lòng nhập số tiền cần rút'),
(314, 2, 'Vui lòng nhập số tiền cần rút', 'Please enter the amount to withdraw'),
(315, 1, 'Số tiền rút tối thiểu phải là', 'Số tiền rút tối thiểu phải là'),
(316, 2, 'Số tiền rút tối thiểu phải là', 'Minimum withdrawal amount should be'),
(317, 1, 'Số dư hoa hồng khả dụng của bạn không đủ', 'Số dư hoa hồng khả dụng của bạn không đủ'),
(318, 2, 'Số dư hoa hồng khả dụng của bạn không đủ', 'Your available commission balance is not enough'),
(319, 1, 'Gian lận khi rút số dư hoa hồng', 'Gian lận khi rút số dư hoa hồng'),
(320, 2, 'Gian lận khi rút số dư hoa hồng', 'Fraud when withdrawing commission balance'),
(321, 1, 'Tài khoản của bạn đã bị khóa vì gian lận', 'Tài khoản của bạn đã bị khóa vì gian lận'),
(322, 2, 'Tài khoản của bạn đã bị khóa vì gian lận', 'Your account has been blocked for cheating'),
(323, 1, 'Yêu cầu rút tiền được tạo thành công, vui lòng đợi ADMIN xử lý', 'Yêu cầu rút tiền được tạo thành công, vui lòng đợi ADMIN xử lý'),
(324, 2, 'Yêu cầu rút tiền được tạo thành công, vui lòng đợi ADMIN xử lý', 'Withdrawal request created successfully, please wait for ADMIN to process'),
(325, 1, 'Số tiền rút', 'Số tiền rút'),
(326, 2, 'Số tiền rút', 'Withdrawal amount'),
(327, 1, 'Thông kê của bạn', 'Thông kê của bạn'),
(328, 2, 'Thông kê của bạn', 'Your stats'),
(329, 1, 'Số tiền hoa hồng khả dụng', 'Số tiền hoa hồng khả dụng'),
(330, 2, 'Số tiền hoa hồng khả dụng', 'Amount of available commission'),
(331, 1, 'Tổng số tiền hoa hồng đã nhận', 'Tổng số tiền hoa hồng đã nhận'),
(332, 2, 'Tổng số tiền hoa hồng đã nhận', 'Total commission received'),
(333, 1, 'Số lần nhấp vào liên kết', 'Số lần nhấp vào liên kết'),
(334, 2, 'Số lần nhấp vào liên kết', 'Clicks'),
(335, 1, 'Lịch sử hoa hồng', 'Lịch sử hoa hồng'),
(336, 2, 'Lịch sử hoa hồng', 'History commission'),
(337, 1, 'Hoa hồng ban đầu', 'Hoa hồng ban đầu'),
(338, 2, 'Hoa hồng ban đầu', 'Initial commission balance'),
(339, 1, 'Hoa hồng thay đổi', 'Hoa hồng thay đổi'),
(340, 2, 'Hoa hồng thay đổi', 'Change commission balance'),
(341, 1, 'Hoa hồng hiện tại', 'Hoa hồng hiện tại'),
(342, 2, 'Hoa hồng hiện tại', 'Current commission balance'),
(343, 1, 'Vui lòng nhập số lượng cần mua', 'Vui lòng nhập số lượng cần mua'),
(344, 2, 'Vui lòng nhập số lượng cần mua', 'Please enter the quantity'),
(345, 1, 'Tổng tiền thanh toán:', 'Tổng tiền thanh toán:'),
(346, 2, 'Tổng tiền thanh toán:', 'Total payment:'),
(347, 1, 'Số tiền giảm:', 'Số tiền giảm:'),
(348, 2, 'Số tiền giảm:', 'Discount:'),
(349, 1, 'Thành tiền:', 'Thành tiền:'),
(350, 2, 'Thành tiền:', 'Price:'),
(351, 1, 'Mã giảm giá:', 'Mã giảm giá:'),
(352, 2, 'Mã giảm giá:', 'Coupon:'),
(353, 1, 'Nhập mã giảm giá nếu có', 'Nhập mã giảm giá nếu có'),
(354, 2, 'Nhập mã giảm giá nếu có', 'Enter discount code if available'),
(355, 1, 'THÔNG TIN MUA HÀNG', 'THÔNG TIN MUA HÀNG'),
(356, 2, 'THÔNG TIN MUA HÀNG', 'PURCHASE INFORMATION'),
(357, 1, 'Số lượng cần mua:', 'Số lượng cần mua:'),
(358, 2, 'Số lượng cần mua:', 'Amount:'),
(359, 1, 'Chia sẻ:', 'Chia sẻ:'),
(360, 2, 'Chia sẻ:', 'Share:'),
(361, 1, 'Mua Ngay', 'Mua Ngay'),
(362, 2, 'Mua Ngay', 'Buy Now'),
(363, 1, 'Kho hàng:', 'Kho hàng:'),
(364, 2, 'Kho hàng:', 'Stock:'),
(365, 1, 'Đã bán:', 'Đã bán:'),
(366, 2, 'Đã bán:', 'Sold:'),
(367, 1, 'Yêu Thích', 'Yêu Thích'),
(368, 2, 'Yêu Thích', 'Add Favourite'),
(369, 1, 'Bỏ Thích', 'Bỏ Thích'),
(370, 2, 'Bỏ Thích', 'Remove Favourite'),
(371, 1, 'Danh sách sản phẩm yêu thích', 'Danh sách sản phẩm yêu thích'),
(372, 2, 'Danh sách sản phẩm yêu thích', 'Favorites'),
(373, 1, 'Sản phẩm', 'Sản phẩm'),
(374, 2, 'Sản phẩm', 'Product'),
(375, 1, 'Kho hàng', 'Kho hàng'),
(376, 2, 'Kho hàng', 'Stock'),
(377, 1, 'Giá', 'Giá'),
(378, 2, 'Giá', 'Price'),
(379, 1, 'Mua', 'Mua'),
(380, 2, 'Mua', 'Buy'),
(381, 1, 'Xem', 'Xem'),
(382, 2, 'Xem', 'View'),
(383, 1, 'Xóa', 'Xóa'),
(384, 2, 'Xóa', 'Delete'),
(385, 1, 'Hết hàng', 'Hết hàng'),
(386, 2, 'Hết hàng', 'Out of Stock'),
(387, 1, 'Thêm vào mục yêu thích', 'Thêm vào mục yêu thích'),
(388, 2, 'Thêm vào mục yêu thích', 'Add to Favorites'),
(389, 1, 'Đã thêm vào mục yêu thích', 'Đã thêm vào mục yêu thích'),
(390, 2, 'Đã thêm vào mục yêu thích', 'Added to Favorites'),
(393, 2, 'Lịch sử đơn hàng', 'Order History'),
(394, 1, 'Xóa đơn hàng', 'Xóa đơn hàng'),
(395, 2, 'Xóa đơn hàng', 'Delete Order'),
(396, 1, 'Xóa đơn hàng đã chọn khỏi lịch sử của bạn', 'Xóa đơn hàng đã chọn khỏi lịch sử của bạn'),
(397, 2, 'Xóa đơn hàng đã chọn khỏi lịch sử của bạn', 'Delete selected orders from your history'),
(398, 1, 'Mã đơn hàng', 'Mã đơn hàng'),
(399, 2, 'Mã đơn hàng', 'Transaction'),
(400, 2, 'Thanh toán', 'Pay'),
(401, 1, 'Xem chi tiết', 'Xem chi tiết'),
(402, 2, 'Xem chi tiết', 'See details'),
(403, 1, 'Tải về máy', 'Tải về máy'),
(404, 2, 'Tải về máy', 'Download'),
(405, 1, 'Xóa khỏi lịch sử', 'Xóa khỏi lịch sử'),
(406, 2, 'Xóa khỏi lịch sử', 'Delete from history'),
(407, 1, 'Liên hệ', 'Liên hệ'),
(408, 2, 'Liên hệ', 'Contact'),
(409, 1, 'Chính sách', 'Chính sách'),
(410, 2, 'Chính sách', 'Policy'),
(411, 1, 'Tài liệu API', 'Tài liệu API'),
(412, 2, 'Tài liệu API', 'API Document'),
(413, 1, 'Trang chủ', 'Trang chủ'),
(414, 2, 'Trang chủ', 'Home'),
(415, 1, 'Liên kết', 'Liên kết'),
(416, 2, 'Liên kết', 'Links'),
(417, 1, 'Câu hỏi thường gặp', 'Câu hỏi thường gặp'),
(418, 2, 'Câu hỏi thường gặp', 'FAQ'),
(419, 1, 'Liên hệ chúng tôi', 'Liên hệ chúng tôi'),
(420, 2, 'Liên hệ chúng tôi', 'Contact us'),
(421, 1, 'Sản phẩm:', 'Sản phẩm:'),
(422, 2, 'Sản phẩm:', 'Product:'),
(423, 1, 'Số lượng mua:', 'Số lượng mua:'),
(424, 2, 'Số lượng mua:', 'Quantity purchased:'),
(425, 1, 'Thanh toán:', 'Thanh toán:'),
(426, 2, 'Thanh toán:', 'Pay:'),
(427, 1, 'Mã đơn hàng:', 'Mã đơn hàng:'),
(428, 2, 'Mã đơn hàng:', 'Transaction:'),
(429, 1, 'Chi tiết đơn hàng', 'Chi tiết đơn hàng'),
(430, 2, 'Chi tiết đơn hàng', 'Order details'),
(431, 1, 'Tài khoản', 'Tài khoản'),
(432, 2, 'Tài khoản', 'Account'),
(433, 1, 'Lưu các tài khoản đã chọn vào tệp .txt', 'Lưu các tài khoản đã chọn vào tệp .txt'),
(434, 2, 'Lưu các tài khoản đã chọn vào tệp .txt', 'Save selected accounts to a .txt file'),
(435, 1, 'Sao chép các tài khoản đã chọn', 'Sao chép các tài khoản đã chọn'),
(436, 2, 'Sao chép các tài khoản đã chọn', 'Copy selected accounts'),
(437, 1, 'Chỉ sao chép UID các tài khoản đã chọn', 'Chỉ sao chép UID các tài khoản đã chọn'),
(438, 2, 'Chỉ sao chép UID các tài khoản đã chọn', 'Copy only the UID of the selected accounts'),
(439, 1, 'Số dư của tôi:', 'Số dư của tôi:'),
(440, 2, 'Số dư của tôi:', 'My balance:'),
(441, 1, 'Khuyến mãi', 'Khuyến mãi'),
(442, 2, 'Khuyến mãi', 'Promotion'),
(443, 1, 'Số tiền nạp lớn hơn hoặc bằng', 'Số tiền nạp lớn hơn hoặc bằng'),
(444, 2, 'Số tiền nạp lớn hơn hoặc bằng', 'The deposit amount is greater than or equal to'),
(445, 1, 'Khuyến mãi thêm', 'Khuyến mãi thêm'),
(446, 2, 'Khuyến mãi thêm', 'Extra'),
(447, 1, 'Thông tin chi tiết khách hàng', 'Thông tin chi tiết khách hàng'),
(448, 2, 'Thông tin chi tiết khách hàng', 'Customer details'),
(449, 1, 'Chia sẻ liên kết này lên mạng xã hội hoặc bạn bè của bạn.', 'Chia sẻ liên kết này lên mạng xã hội hoặc bạn bè của bạn.'),
(451, 1, 'Tài liệu tích hợp API', 'Tài liệu tích hợp API'),
(452, 2, 'Tài liệu tích hợp API', 'API integration documentation'),
(453, 1, 'Lấy thông tin tài khoản', 'Lấy thông tin tài khoản'),
(454, 2, 'Lấy thông tin tài khoản', 'Get account information'),
(455, 1, 'Lấy danh sách chuyên mục và sản phẩm', 'Lấy danh sách chuyên mục và sản phẩm'),
(456, 2, 'Lấy danh sách chuyên mục và sản phẩm', 'Get a list of categories and products'),
(457, 1, 'Mua hàng', 'Mua hàng'),
(458, 2, 'Mua hàng', 'Purchase'),
(459, 1, 'ID sản phẩm cần mua', 'ID sản phẩm cần mua'),
(460, 2, 'ID sản phẩm cần mua', 'Product ID to buy'),
(461, 1, 'Số lượng cần mua', 'Số lượng cần mua'),
(462, 2, 'Số lượng cần mua', 'Quantity to buy'),
(463, 1, 'Mã giảm giá nếu có', 'Mã giảm giá nếu có'),
(464, 2, 'Mã giảm giá nếu có', 'Discount code if available'),
(465, 1, 'Bảo mật', 'Bảo mật'),
(466, 2, 'Bảo mật', 'Security'),
(467, 1, 'Bảo mật tài khoản', 'Bảo mật tài khoản'),
(468, 2, 'Bảo mật tài khoản', 'Account security'),
(469, 1, 'Xác minh đăng nhập bằng', 'Xác minh đăng nhập bằng'),
(470, 2, 'Xác minh đăng nhập bằng', 'Verify login with'),
(471, 1, 'Gửi thông báo về mail khi đăng nhập thành công:', 'Gửi thông báo về mail khi đăng nhập thành công:'),
(472, 2, 'Gửi thông báo về mail khi đăng nhập thành công:', 'Send email notification upon successful login:'),
(473, 1, 'Đúng Trình Duyệt và IP mua hàng mới có thể xem đơn hàng:', 'Đúng Trình Duyệt và IP mua hàng mới có thể xem đơn hàng:'),
(474, 2, 'Đúng Trình Duyệt và IP mua hàng mới có thể xem đơn hàng:', 'Only the correct browser and purchase IP can view orders:'),
(475, 1, '- Sử dụng điện thoại tải App Google Authenticator sau đó quét mã QR để nhận mã xác minh.', '- Sử dụng điện thoại tải App Google Authenticator sau đó quét mã QR để nhận mã xác minh.'),
(476, 2, '- Sử dụng điện thoại tải App Google Authenticator sau đó quét mã QR để nhận mã xác minh.', '- Use your phone to download the Google Authenticator App then scan the QR code to receive the verification code.'),
(477, 1, '- Mã QR sẽ được thay đổi khi bạn tắt xác minh.', '- Mã QR sẽ được thay đổi khi bạn tắt xác minh.'),
(478, 2, '- Mã QR sẽ được thay đổi khi bạn tắt xác minh.', '- The QR code will be changed when you turn off verification.'),
(479, 1, '- Nếu bật Xác minh đăng nhập bằng OTP Mail thì không bật Google Authenticator và ngược lại.', '- Nếu bật Xác minh đăng nhập bằng OTP Mail thì không bật Google Authenticator và ngược lại.'),
(480, 2, '- Nếu bật Xác minh đăng nhập bằng OTP Mail thì không bật Google Authenticator và ngược lại.', '- If you enable Login Verification using OTP Mail, do not enable Google Authenticator and vice versa.'),
(481, 1, 'Lưu', 'Lưu'),
(482, 2, 'Lưu', 'Save'),
(483, 1, 'Nhập mã xác minh để lưu', 'Nhập mã xác minh để lưu'),
(484, 2, 'Nhập mã xác minh để lưu', 'Enter the verification code to save'),
(485, 1, 'Sản phẩm liên quan đến từ khóa', 'Sản phẩm liên quan đến từ khóa'),
(486, 2, 'Sản phẩm liên quan đến từ khóa', 'Products related to keyword'),
(487, 1, 'trong số', 'trong số'),
(488, 2, 'trong số', 'of'),
(489, 1, 'Quay lại', 'Quay lại'),
(490, 2, 'Quay lại', 'Back'),
(491, 1, 'Tải về đơn hàng', 'Tải về đơn hàng'),
(492, 2, 'Tải về đơn hàng', 'Download Order'),
(493, 1, 'Hệ thống sẽ tải về đơn hàng khi bạn nhấn đồng ý', 'Hệ thống sẽ tải về đơn hàng khi bạn nhấn đồng ý'),
(494, 2, 'Hệ thống sẽ tải về đơn hàng khi bạn nhấn đồng ý', 'The system will download the order when you click Okey'),
(495, 1, 'Hệ thống sẽ xóa đơn hàng khỏi lịch sử của bạn khi bạn nhấn đồng ý', 'Hệ thống sẽ xóa đơn hàng khỏi lịch sử của bạn khi bạn nhấn đồng ý'),
(496, 2, 'Hệ thống sẽ xóa đơn hàng khỏi lịch sử của bạn khi bạn nhấn đồng ý', 'The system will delete the order from your history when you click Okey'),
(497, 1, 'Đóng', 'Đóng'),
(498, 2, 'Đóng', 'Cancel'),
(499, 1, 'Xuất tất cả tài khoản ra tệp .txt', 'Xuất tất cả tài khoản ra tệp .txt'),
(500, 2, 'Xuất tất cả tài khoản ra tệp .txt', 'Export all accounts to a .txt file'),
(501, 1, 'Xóa đơn hàng này khỏi lịch sử của bạn', 'Xóa đơn hàng này khỏi lịch sử của bạn'),
(502, 2, 'Xóa đơn hàng này khỏi lịch sử của bạn', 'Delete this order from your history'),
(503, 1, 'Thành công !', 'Thành công !'),
(504, 2, 'Thành công !', 'Success !'),
(505, 1, 'Xem chi tiết đơn hàng', 'Xem chi tiết đơn hàng'),
(506, 2, 'Xem chi tiết đơn hàng', 'View order details'),
(507, 1, 'Mua thêm', 'Mua thêm'),
(508, 2, 'Mua thêm', 'Buy more'),
(509, 1, 'Tạo đơn hàng thành công !', 'Tạo đơn hàng thành công !'),
(510, 2, 'Tạo đơn hàng thành công !', 'Create order successfully!'),
(511, 1, 'Đang xử lý...', 'Đang xử lý...'),
(512, 2, 'Đang xử lý...', 'Processing...'),
(513, 1, 'tài khoản giảm', 'tài khoản giảm'),
(514, 2, 'tài khoản giảm', 'account discount'),
(515, 1, 'Chi tiết', 'Chi tiết'),
(516, 2, 'Chi tiết', 'Detail'),
(517, 1, 'Tích hợp API', 'Tích hợp API'),
(518, 2, 'Tích hợp API', 'API integration'),
(519, 1, 'Lấy chi tiết sản phẩm', 'Lấy chi tiết sản phẩm'),
(520, 2, 'Lấy chi tiết sản phẩm', 'Get product details'),
(521, 1, 'Ghi chú cá nhân', 'Ghi chú cá nhân'),
(522, 2, 'Ghi chú cá nhân', 'Personal note'),
(523, 1, 'ngày trước', 'ngày trước'),
(524, 2, 'ngày trước', 'days ago'),
(525, 1, 'tiếng trước', 'tiếng trước'),
(526, 2, 'tiếng trước', 'hours ago'),
(527, 1, 'phút trước', 'phút trước'),
(528, 2, 'phút trước', 'minutes ago'),
(529, 1, 'giây trước', 'giây trước'),
(530, 2, 'giây trước', 'seconds ago'),
(531, 1, 'Hôm qua', 'Hôm qua'),
(532, 2, 'Hôm qua', 'Yesterday'),
(533, 1, 'tuần trước', 'tuần trước'),
(534, 2, 'tuần trước', 'weeks ago'),
(535, 1, 'tháng trước', 'tháng trước'),
(536, 2, 'tháng trước', 'months ago'),
(537, 1, 'năm trước', 'năm trước'),
(538, 2, 'năm trước', 'last year'),
(539, 1, 'Đơn hàng đã bị xóa', 'Đơn hàng đã bị xóa'),
(540, 2, 'Đơn hàng đã bị xóa', 'Order has been deleted'),
(541, 1, 'Bạn có chắc không', 'Bạn có chắc không'),
(543, 1, 'Hệ thống sẽ xóa', 'Hệ thống sẽ xóa'),
(544, 2, 'Hệ thống sẽ xóa', 'The system will delete'),
(545, 1, 'đơn hàng bạn chọn khi nhấn Đồng Ý', 'đơn hàng bạn chọn khi nhấn Đồng Ý'),
(546, 2, 'đơn hàng bạn chọn khi nhấn Đồng Ý', 'order you select when you click Agree'),
(547, 1, 'Vui lòng chọn ít nhất một đơn hàng.', 'Vui lòng chọn ít nhất một đơn hàng.'),
(548, 2, 'Vui lòng chọn ít nhất một đơn hàng.', 'Please select at least one order.'),
(549, 1, 'Thất bại!', 'Thất bại!'),
(550, 2, 'Thất bại!', 'Failure!'),
(551, 1, 'Thành công!', 'Thành công!'),
(552, 2, 'Thành công!', 'Success!'),
(553, 1, 'Xóa đơn hàng thành công', 'Xóa đơn hàng thành công'),
(554, 2, 'Xóa đơn hàng thành công', 'Order deleted successfully'),
(555, 1, 'Miễn phí', 'Miễn phí'),
(556, 2, 'Miễn phí', 'Free'),
(557, 1, 'Lấy mã 2FA', 'Lấy mã 2FA'),
(558, 2, 'Lấy mã 2FA', 'Get 2FA code'),
(559, 1, 'Bạn đang xem', 'Bạn đang xem'),
(560, 2, 'Bạn đang xem', 'You are viewing'),
(561, 1, 'Nhập danh sách UID', 'Nhập danh sách UID'),
(562, 2, 'Nhập danh sách UID', 'Import UID list'),
(563, 1, 'Mỗi dòng 1 UID', 'Mỗi dòng 1 UID'),
(564, 2, 'Mỗi dòng 1 UID', '1 UID per line'),
(565, 1, 'Tài khoản Live', 'Tài khoản Live'),
(566, 2, 'Tài khoản Live', 'UID Live'),
(567, 1, 'Tài khoản Die', 'Tài khoản Die'),
(568, 2, 'Tài khoản Die', 'UID Die'),
(569, 1, 'Giảm giá', 'Giảm giá'),
(570, 2, 'Giảm giá', 'Discount'),
(571, 1, 'Tỷ lệ hoa hồng', 'Tỷ lệ hoa hồng'),
(572, 2, 'Tỷ lệ hoa hồng', 'Commission Rate'),
(573, 1, 'Thành viên đã giới thiệu', 'Thành viên đã giới thiệu'),
(574, 2, 'Thành viên đã giới thiệu', 'Referred Member'),
(575, 1, 'Không có dữ liệu', 'Không có dữ liệu'),
(576, 2, 'Không có dữ liệu', 'No data available'),
(577, 1, 'Khách hàng', 'Khách hàng'),
(578, 2, 'Khách hàng', 'Username'),
(579, 1, 'Ngày đăng ký', 'Ngày đăng ký'),
(580, 2, 'Ngày đăng ký', 'Registration date'),
(581, 1, 'Hoa hồng', 'Hoa hồng'),
(582, 2, 'Hoa hồng', 'Commission'),
(583, 1, 'Mật khẩu mạnh', 'Mật khẩu mạnh'),
(584, 2, 'Mật khẩu mạnh', 'Strong password'),
(585, 1, 'Mật khẩu trung bình', 'Mật khẩu trung bình'),
(586, 2, 'Mật khẩu trung bình', 'Average Password'),
(587, 1, 'Mật khẩu rất yếu', 'Mật khẩu rất yếu'),
(588, 2, 'Mật khẩu rất yếu', 'Password is very weak'),
(589, 1, 'Vui lòng nhập mã xác minh 2FA', 'Vui lòng nhập mã xác minh 2FA'),
(590, 2, 'Vui lòng nhập mã xác minh 2FA', 'Please enter 2FA verification code'),
(591, 1, 'Mã xác minh không chính xác', 'Mã xác minh không chính xác'),
(592, 2, 'Mã xác minh không chính xác', 'Verification code is incorrect'),
(593, 1, 'Bật xác thực Google Authenticator', 'Bật xác thực Google Authenticator'),
(594, 2, 'Bật xác thực Google Authenticator', 'Enable Google Authenticator'),
(595, 1, 'Tắt xác thực Google Authenticator', 'Tắt xác thực Google Authenticator'),
(596, 2, 'Tắt xác thực Google Authenticator', 'Disable Google Authenticator'),
(597, 1, 'Vui lòng đăng nhập để sử dụng tính năng này', 'Vui lòng đăng nhập để sử dụng tính năng này'),
(598, 2, 'Vui lòng đăng nhập để sử dụng tính năng này', 'Please login to use this feature'),
(599, 1, 'Chọn phương thức nạp tiền', 'Chọn phương thức nạp tiền'),
(600, 2, 'Chọn phương thức nạp tiền', 'Select deposit method'),
(601, 1, 'Không hiển thị lại trong 2 giờ', 'Không hiển thị lại trong 2 giờ'),
(602, 2, 'Không hiển thị lại trong 2 giờ', 'hide for 2 hours'),
(603, 1, 'Thông báo', 'Thông báo'),
(604, 2, 'Thông báo', 'Notification'),
(605, 1, 'Tìm kiếm sản phẩm...', 'Tìm kiếm sản phẩm...'),
(606, 2, 'Tìm kiếm sản phẩm...', 'Search for products...'),
(607, 1, 'Chat hỗ trợ', 'Chat hỗ trợ'),
(608, 2, 'Chat hỗ trợ', 'Chat support'),
(609, 1, 'Chat ngay', 'Chat ngay'),
(610, 2, 'Chat ngay', 'Chat now'),
(611, 1, 'ĐƠN HÀNG GẦN ĐÂY', 'ĐƠN HÀNG GẦN ĐÂY'),
(612, 2, 'ĐƠN HÀNG GẦN ĐÂY', 'RECENT ORDERS'),
(613, 1, 'NẠP TIỀN GẦN ĐÂY', 'NẠP TIỀN GẦN ĐÂY'),
(614, 2, 'NẠP TIỀN GẦN ĐÂY', 'RECENT DEPOSIT'),
(615, 1, 'Chức năng này chưa được cấu hình, vui lòng liên hệ Admin', 'Chức năng này chưa được cấu hình, vui lòng liên hệ Admin'),
(616, 2, 'Chức năng này chưa được cấu hình, vui lòng liên hệ Admin', 'This function is not configured yet, please contact Admin'),
(617, 1, 'Số dư không đủ, vui lòng nạp thêm', 'Số dư không đủ, vui lòng nạp thêm'),
(618, 2, 'Số dư không đủ, vui lòng nạp thêm', 'Insufficient balance, please top up'),
(619, 1, 'Công cụ Check Live UID Facebook', 'Công cụ Check Live UID Facebook'),
(620, 2, 'Công cụ Check Live UID Facebook', 'Facebook Live UID Check Tool'),
(621, 1, 'Tiếp thị liên kết', 'Tiếp thị liên kết'),
(622, 2, 'Tiếp thị liên kết', 'Affiliate Marketing'),
(623, 1, 'Liên kết sản phẩm', 'Liên kết sản phẩm'),
(624, 2, 'Liên kết sản phẩm', 'Product Links'),
(625, 1, 'Chia sẻ liên kết sản phẩm dưới đây cho bạn bè của bạn, bạn sẽ nhận được hoa hồng khi bạn bè của bạn mua hàng thông qua liên kết phía dưới.', 'Chia sẻ liên kết sản phẩm dưới đây cho bạn bè của bạn, bạn sẽ nhận được hoa hồng khi bạn bè của bạn mua hàng thông qua liên kết phía dưới.'),
(626, 2, 'Chia sẻ liên kết sản phẩm dưới đây cho bạn bè của bạn, bạn sẽ nhận được hoa hồng khi bạn bè của bạn mua hàng thông qua liên kết phía dưới.', 'Share the product link below to your friends, you will receive commission when your friends purchase through the link below.'),
(627, 1, 'Tất cả sản phẩm', 'Tất cả sản phẩm'),
(628, 2, 'Tất cả sản phẩm', 'All products'),
(629, 19, 'Vui lòng nhập username', 'กรุณากรอกชื่อผู้ใช้'),
(630, 19, 'Vui lòng nhập mật khẩu', 'กรุณากรอกรหัสผ่าน'),
(631, 19, 'Vui lòng xác minh Captcha', 'กรุณาตรวจสอบ Captcha'),
(632, 19, 'Thông tin đăng nhập không chính xác', 'ข้อมูลการเข้าสู่ระบบไม่ถูกต้อง'),
(633, 19, 'Vui lòng nhập địa chỉ Email', 'กรุณากรอกที่อยู่อีเมล์'),
(634, 19, 'Vui lòng nhập lại mật khẩu', 'กรุณากรอกรหัสผ่านอีกครั้ง'),
(635, 19, 'Xác minh mật khẩu không chính xác', 'ตรวจสอบรหัสผ่านไม่ถูกต้อง'),
(636, 19, 'Tên đăng nhập đã tồn tại trong hệ thống', 'ชื่อเข้าระบบมีอยู่แล้วในระบบ'),
(637, 19, 'Địa chỉ email đã tồn tại trong hệ thống', 'ที่อยู่อีเมลมีอยู่ในระบบแล้ว'),
(638, 19, 'IP của bạn đã đạt đến giới hạn tạo tài khoản cho phép', 'IP ของคุณถึงขีดจำกัดการสร้างบัญชีที่อนุญาตแล้ว'),
(639, 19, 'Đăng ký thành công!', 'ลงทะเบียนสำเร็จ!'),
(640, 19, 'Tạo tài khoản không thành công, vui lòng thử lại', 'การสร้างบัญชีล้มเหลว กรุณาลองอีกครั้ง'),
(641, 19, 'Vui lòng đăng nhập', 'กรุณาเข้าสู่ระบบ'),
(642, 19, 'Lưu thành công', 'บันทึกสำเร็จแล้ว'),
(643, 19, 'Lưu thất bại', 'การบันทึกล้มเหลว'),
(644, 19, 'Vui lòng nhập mật khẩu hiện tại', 'กรุณากรอกรหัสผ่านปัจจุบัน'),
(645, 19, 'Vui lòng nhập mật khẩu mới', 'กรุณากรอกรหัสผ่านใหม่'),
(646, 19, 'Mật khẩu mới quá ngắn', 'รหัสผ่านใหม่สั้นเกินไป'),
(647, 19, 'Xác nhận mật khẩu không chính xác', 'ยืนยันรหัสผ่านไม่ถูกต้อง'),
(648, 19, 'Mật khẩu hiện tại không đúng', 'รหัสผ่านปัจจุบันไม่ถูกต้อง'),
(649, 19, 'Địa chỉ Email này không tồn tại trong hệ thống', 'ที่อยู่อีเมลนี้ไม่มีอยู่ในระบบ'),
(650, 19, 'Vui lòng thử lại trong ít phút', 'โปรดลองอีกครั้งในอีกไม่กี่นาที'),
(651, 19, 'Nếu bạn yêu cầu đặt lại mật khẩu, vui lòng nhấp vào liên kết bên dưới để xác minh.', 'หากคุณต้องการรีเซ็ตรหัสผ่าน โปรดคลิกลิงก์ด้านล่างเพื่อยืนยัน'),
(652, 19, 'Nếu không phải là bạn, vui lòng liên hệ ngay với Quản trị viên của bạn để được hỗ trợ về bảo mật.', 'หากคุณไม่ใช่ โปรดติดต่อผู้ดูแลระบบของคุณทันทีเพื่อขอความช่วยเหลือด้านความปลอดภัย'),
(653, 19, 'Xác nhận tìm mật khẩu website', 'ยืนยันการค้นหารหัสผ่านเว็บไซต์'),
(654, 19, 'Xác nhận khôi phục mật khẩu', 'ยืนยันการกู้คืนรหัสผ่าน'),
(655, 19, 'Vui lòng kiểm tra Email của bạn để hoàn tất quá trình đặt lại mật khẩu', 'กรุณาตรวจสอบอีเมลของคุณเพื่อเสร็จสิ้นกระบวนการรีเซ็ตรหัสผ่าน'),
(656, 19, 'Có lỗi hệ thống, vui lòng liên hệ Developer', 'มีข้อผิดพลาดของระบบกรุณาติดต่อผู้พัฒนา'),
(657, 19, 'Liên kết không tồn tại', 'ลิงค์ไม่ได้อยู่'),
(658, 19, 'Thay đổi mật khẩu thành công', 'เปลี่ยนรหัสผ่านสำเร็จแล้ว'),
(659, 19, 'Thay đổi mật khẩu thất bại', 'การเปลี่ยนรหัสผ่านล้มเหลว'),
(660, 19, 'Hồ sơ của bạn', 'โปรไฟล์ของคุณ'),
(661, 19, 'Tên đăng nhập', 'ชื่อผู้ใช้'),
(662, 19, 'Địa chỉ Email', 'ที่อยู่อีเมล์'),
(663, 19, 'Số điện thoại', 'เบอร์โทรศัพท์'),
(664, 19, 'Họ và Tên', 'ชื่อ-นามสกุล'),
(665, 19, 'Địa chỉ IP', 'ที่อยู่ IP'),
(666, 19, 'Thiết bị', 'อุปกรณ์'),
(667, 19, 'Đăng ký vào lúc', 'สมัครสมาชิกได้ที่'),
(668, 19, 'Đăng nhập gần nhất', 'การเข้าสู่ระบบครั้งสุดท้าย'),
(669, 19, 'Chỉnh sửa thông tin', 'แก้ไขข้อมูล'),
(670, 19, 'Thay đổi mật khẩu', 'เปลี่ยนรหัสผ่าน'),
(671, 19, 'Thay đổi mật khẩu đăng nhập của bạn là một cách dễ dàng để giữ an toàn cho tài khoản của bạn.', 'การเปลี่ยนรหัสผ่านการเข้าสู่ระบบเป็นวิธีง่ายๆ ในการรักษาบัญชีของคุณให้ปลอดภัย'),
(672, 19, 'Mật khẩu hiện tại', 'รหัสผ่านปัจจุบัน'),
(673, 19, 'Mật khẩu mới', 'รหัสผ่านใหม่'),
(674, 19, 'Nhập lại mật khẩu mới', 'กรอกรหัสผ่านใหม่อีกครั้ง'),
(675, 19, 'Cập Nhật', 'อัปเดต'),
(676, 19, 'Đăng Xuất', 'ออกจากระบบ'),
(677, 19, 'Bạn có chắc không?', 'คุณแน่ใจมั้ย?'),
(678, 19, 'Bạn sẽ bị đăng xuất khỏi tài khoản khi nhấn Đồng Ý', 'คุณจะออกจากระบบบัญชีของคุณเมื่อคุณคลิกตกลง'),
(679, 19, 'Đồng ý', 'เห็นด้วย'),
(680, 19, 'Huỷ bỏ', 'ยกเลิก'),
(681, 19, 'Đăng Nhập', 'เข้าสู่ระบบ'),
(682, 19, 'Vui Lòng Đăng Nhập Để Tiếp Tục', 'กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ'),
(683, 19, 'Quên mật khẩu', 'ลืมรหัสผ่าน'),
(684, 19, 'Bạn quên mật khẩu?', 'ลืมรหัสผ่านใช่ไหม?'),
(685, 19, 'Vui lòng nhập thông tin vào ô dưới đây để xác minh', 'กรุณากรอกข้อมูลลงในช่องด้านล่างเพื่อยืนยัน'),
(686, 19, 'Xác minh', 'ตรวจสอบ'),
(687, 19, 'Bạn đã có tài khoản?', 'มีบัญชีอยู่แล้วใช่ไหม?'),
(688, 19, 'Ghi nhớ tôi', 'จำฉันไว้'),
(689, 19, 'Quên mật khẩu?', 'ลืมรหัสผ่าน?'),
(690, 19, 'Bạn chưa có tài khoản?', 'ยังไม่มีบัญชีใช่ไหม?'),
(691, 19, 'Đăng Ký Ngay', 'สมัครสมาชิกตอนนี้'),
(692, 19, 'Nạp tiền', 'เงินฝาก'),
(693, 19, 'Ngân hàng', 'ธนาคาร'),
(694, 19, 'Ví của tôi', 'กระเป๋าสตางค์ของฉัน'),
(695, 19, 'Số dư hiện tại', 'ยอดคงเหลือปัจจุบัน'),
(696, 19, 'Tổng tiền nạp', 'ยอดฝากรวม'),
(697, 19, 'Số dư đã sử dụng', 'ยอดคงเหลือที่ใช้แล้ว'),
(698, 19, 'THANH TOÁN', 'จ่าย'),
(699, 19, 'Lưu ý nạp tiền', 'หมายเหตุการฝากเงิน'),
(700, 19, 'Lịch sử nạp tiền', 'ประวัติการฝากเงิน'),
(701, 19, 'Số tài khoản:', 'หมายเลขบัญชี :'),
(702, 19, 'Chủ tài khoản:', 'ผู้ถือบัญชี:'),
(703, 19, 'Ngân hàng:', 'ธนาคาร:'),
(704, 19, 'Nội dung chuyển khoản:', 'โอนเนื้อหา:'),
(705, 19, 'Mã giao dịch', 'รหัสธุรกรรม'),
(706, 19, 'Nội dung', 'เนื้อหา'),
(707, 19, 'Số tiền nạp', 'จำนวนเงินมัดจำ'),
(708, 19, 'Thực nhận', 'การตระหนักรู้'),
(709, 19, 'Thời gian', 'เวลา'),
(710, 19, 'Trạng thái', 'สถานะ'),
(711, 19, 'Đã thanh toán', 'จ่าย'),
(712, 19, 'Tất cả', 'ทั้งหมด'),
(713, 19, 'Hôm nay', 'วันนี้'),
(714, 19, 'Tuần này', 'สัปดาห์นี้'),
(715, 19, 'Tháng này', 'เดือนนี้'),
(716, 19, 'Đã thanh toán:', 'จ่าย:'),
(717, 19, 'Thực nhận:', 'ใบเสร็จจริง:'),
(718, 19, 'Thao tác', 'การดำเนินการ'),
(719, 19, 'Nhật ký hoạt động', 'บันทึกกิจกรรม'),
(720, 19, 'Tìm kiếm', 'ค้นหา'),
(721, 19, 'Bỏ lọc', 'ยกเลิกตัวกรอง'),
(722, 19, 'Hiển thị', 'แสดง'),
(723, 19, 'Ẩn', 'ซ่อน'),
(724, 19, 'Biến động số dư', 'ความผันผวนของความสมดุล'),
(725, 19, 'Số dư ban đầu', 'ยอดคงเหลือเริ่มต้น'),
(726, 19, 'Số dư thay đổi', 'การเปลี่ยนแปลงสมดุล'),
(727, 19, 'Lý do', 'เหตุผล'),
(728, 19, 'Chọn thời gian cần tìm', 'เลือกเวลาที่ต้องการค้นหา'),
(729, 19, 'Hiển thị thêm', 'แสดงเพิ่มเติม'),
(730, 19, 'Ẩn bớt', 'ซ่อน'),
(731, 19, 'Nội dung chuyển khoản', 'ถ่ายโอนเนื้อหา'),
(732, 19, 'Đăng nhập bằng Google', 'ลงชื่อเข้าใช้ด้วย Google'),
(733, 19, 'Đăng nhập bằng Facebook', 'เข้าสู่ระบบด้วย Facebook'),
(734, 19, 'Đăng ký tài khoản', 'ลงทะเบียนบัญชีผู้ใช้'),
(735, 19, 'Tài khoản đăng nhập', 'เข้าสู่ระบบบัญชี'),
(736, 19, 'Mật khẩu', 'รหัสผ่าน'),
(737, 19, 'Nhập lại mật khẩu', 'กรอกรหัสผ่านอีกครั้ง'),
(738, 19, 'Đăng Ký', 'ลงทะเบียน'),
(739, 19, 'Vui lòng nhập thông tin đăng ký', 'กรุณากรอกข้อมูลลงทะเบียน'),
(740, 19, 'Vui lòng nhập thông tin đăng nhập', 'กรุณากรอกข้อมูลการเข้าสู่ระบบของคุณ'),
(741, 19, 'Thông tin cá nhân', 'ข้อมูลส่วนตัว'),
(742, 19, 'Cấu hình nạp tiền Crypto', 'การกำหนดค่าการฝากเงิน Crypto'),
(743, 19, 'All Time', 'ตลอดเวลา'),
(744, 19, 'Thống kê thanh toán tháng', 'สถิติการชำระเงินรายเดือน'),
(745, 19, 'Lịch sử nạp tiền Crypto', 'ประวัติการฝากเงินคริปโต'),
(746, 19, 'Thống kê', 'สถิติ'),
(747, 19, 'Cấu hình', 'การกำหนดค่า'),
(748, 19, 'Nạp tối đa', 'โหลดสูงสุด'),
(749, 19, 'Nạp tối thiểu', 'เงินฝากขั้นต่ำ'),
(750, 19, 'Nạp tiền bằng Crypto', 'ฝากเงินด้วยคริปโต'),
(751, 19, 'Lưu ý', 'บันทึก'),
(752, 19, 'Lịch sử nạp Crypto', 'ประวัติการฝากเงินคริปโต'),
(753, 19, 'Số lượng', 'ปริมาณ'),
(754, 19, 'Thời gian tạo', 'เวลาการสร้าง'),
(755, 19, 'Xem thêm', 'ดูเพิ่มเติม'),
(756, 19, 'The minimum deposit amount is:', 'จำนวนเงินฝากขั้นต่ำคือ:'),
(757, 19, 'Số tiền gửi tối đa là:', 'จำนวนเงินฝากสูงสุดคือ:'),
(758, 19, 'Số tiền gửi tối thiểu là:', 'จำนวนเงินฝากขั้นต่ำคือ:'),
(759, 19, 'Chức năng này đang được bảo trì', 'ฟังก์ชั่นนี้อยู่ระหว่างการบำรุงรักษา'),
(760, 19, 'Không thể tạo hóa đơn do lỗi API, vui lòng thử lại sau', 'ไม่สามารถสร้างใบแจ้งหนี้ได้เนื่องจากข้อผิดพลาดของ API โปรดลองอีกครั้งในภายหลัง'),
(761, 19, 'Tạo hoá đơn nạp tiền thành công', 'สร้างใบแจ้งหนี้เติมเงินสำเร็จแล้ว'),
(762, 19, 'Nạp tiền bằng PayPal', 'ฝากเงินด้วย PayPal'),
(763, 19, 'Lịch sử nạp PayPal', 'ประวัติการฝากเงิน PayPal'),
(764, 19, 'Số tiền gửi', 'จำนวนเงินมัดจำ'),
(765, 19, 'Vui lòng nhập số tiền cần nạp', 'กรุณากรอกจำนวนเงินที่ต้องการฝาก'),
(766, 19, 'Mặc định', 'ค่าเริ่มต้น'),
(767, 19, 'Phổ biến', 'เป็นที่นิยม'),
(768, 19, 'Tìm kiếm bài viết', 'ค้นหาบทความ'),
(769, 19, 'Bài viết phổ biến', 'กระทู้ยอดนิยม'),
(770, 19, 'Liên kết giới thiệu của bạn', 'ลิงค์อ้างอิงของคุณ'),
(771, 19, 'Đã sao chép vào bộ nhớ tạm', 'คัดลอกไปยังคลิปบอร์ดแล้ว'),
(772, 19, 'Số tài khoản', 'หมายเลขบัญชี'),
(773, 19, 'Tên chủ tài khoản', 'ชื่อเจ้าของบัญชี'),
(774, 19, 'Số tiền cần rút', 'จำนวนเงินที่ต้องการถอน'),
(775, 19, 'Rút số dư hoa hồng', 'ถอนเงินค่าคอมมิชชั่นคงเหลือ'),
(776, 19, 'Lịch sử rút tiền', 'ประวัติการถอนเงิน'),
(777, 19, 'Rút tiền', 'ถอนเงิน'),
(778, 19, 'Lịch sử', 'ประวัติศาสตร์'),
(779, 19, 'Thao tác quá nhanh, vui lòng chờ', 'การดำเนินการรวดเร็วเกินไป กรุณารอสักครู่'),
(780, 19, 'Vui lòng chọn ngân hàng cần rút', 'กรุณาเลือกธนาคารที่คุณต้องการถอนเงิน'),
(781, 19, 'Vui lòng nhập số tài khoản cần rút', 'กรุณากรอกหมายเลขบัญชีที่ต้องการถอน'),
(782, 19, 'Vui lòng nhập tên chủ tài khoản', 'กรุณากรอกชื่อเจ้าของบัญชี'),
(783, 19, 'Vui lòng nhập số tiền cần rút', 'กรุณากรอกจำนวนเงินที่ต้องการถอน'),
(784, 19, 'Số tiền rút tối thiểu phải là', 'จำนวนเงินถอนขั้นต่ำจะต้องเป็น'),
(785, 19, 'Số dư hoa hồng khả dụng của bạn không đủ', 'ยอดคอมมิชชั่นคงเหลือของคุณไม่เพียงพอ'),
(786, 19, 'Gian lận khi rút số dư hoa hồng', 'การฉ้อโกงในการถอนเงินค่าคอมมิชชั่นคงเหลือ'),
(787, 19, 'Tài khoản của bạn đã bị khóa vì gian lận', 'บัญชีของคุณถูกล็อคเนื่องจากการฉ้อโกง'),
(788, 19, 'Yêu cầu rút tiền được tạo thành công, vui lòng đợi ADMIN xử lý', 'สร้างคำขอถอนเงินสำเร็จแล้ว กรุณารอให้ผู้ดูแลระบบดำเนินการ'),
(789, 19, 'Số tiền rút', 'จำนวนเงินที่ถอนออก'),
(790, 19, 'Thông kê của bạn', 'สถิติของคุณ'),
(791, 19, 'Số tiền hoa hồng khả dụng', 'จำนวนคอมมิชชั่นที่สามารถใช้ได้'),
(792, 19, 'Tổng số tiền hoa hồng đã nhận', 'รวมค่าคอมมิชชั่นที่ได้รับ'),
(793, 19, 'Số lần nhấp vào liên kết', 'จำนวนการคลิกลิงก์'),
(794, 19, 'Lịch sử hoa hồng', 'ประวัติความเป็นมาของดอกกุหลาบ'),
(795, 19, 'Hoa hồng ban đầu', 'ค่าคอมมิชชั่นเบื้องต้น'),
(796, 19, 'Hoa hồng thay đổi', 'การเปลี่ยนแปลงค่าคอมมิชชั่น'),
(797, 19, 'Hoa hồng hiện tại', 'ค่าคอมมิชชั่นปัจจุบัน'),
(798, 19, 'Vui lòng nhập số lượng cần mua', 'กรุณากรอกจำนวนที่ต้องการซื้อ'),
(799, 19, 'Tổng tiền thanh toán:', 'รวมชำระเงิน:'),
(800, 19, 'Số tiền giảm:', 'จำนวนส่วนลด:'),
(801, 19, 'Thành tiền:', 'ยอดรวม :'),
(802, 19, 'Mã giảm giá:', 'โค้ดส่วนลด:'),
(803, 19, 'Nhập mã giảm giá nếu có', 'กรอกรหัสส่วนลดหากมี'),
(804, 19, 'THÔNG TIN MUA HÀNG', 'ข้อมูลการซื้อ'),
(805, 19, 'Số lượng cần mua:', 'จำนวนที่ต้องการซื้อ:'),
(806, 19, 'Chia sẻ:', 'แบ่งปัน:'),
(807, 19, 'Mua Ngay', 'ซื้อเลย'),
(808, 19, 'Kho hàng:', 'คลังสินค้า:'),
(809, 19, 'Đã bán:', 'ขายแล้ว:'),
(810, 19, 'Yêu Thích', 'ที่ชื่นชอบ'),
(811, 19, 'Bỏ Thích', 'ชอบ'),
(812, 19, 'Danh sách sản phẩm yêu thích', 'รายการสินค้าที่ชื่นชอบ'),
(813, 19, 'Sản phẩm', 'ผลิตภัณฑ์'),
(814, 19, 'Kho hàng', 'คลังสินค้า'),
(815, 19, 'Giá', 'ราคา'),
(816, 19, 'Mua', 'อันดับแรก'),
(817, 19, 'Xem', 'ดู'),
(818, 19, 'Xóa', 'ลบ'),
(819, 19, 'Hết hàng', 'สินค้าหมด'),
(820, 19, 'Thêm vào mục yêu thích', 'เพิ่มไปยังรายการโปรด'),
(821, 19, 'Đã thêm vào mục yêu thích', 'เพิ่มไปยังรายการโปรด'),
(822, 19, 'Xóa đơn hàng', 'ลบคำสั่งซื้อ'),
(823, 19, 'Xóa đơn hàng đã chọn khỏi lịch sử của bạn', 'ลบคำสั่งซื้อที่เลือกจากประวัติของคุณ'),
(824, 19, 'Mã đơn hàng', 'รหัสการสั่งซื้อ'),
(825, 19, 'Xem chi tiết', 'ดูรายละเอียดเพิ่มเติม'),
(826, 19, 'Tải về máy', 'ดาวน์โหลด'),
(827, 19, 'Xóa khỏi lịch sử', 'ลบออกจากประวัติ'),
(828, 19, 'Liên hệ', 'ติดต่อ'),
(829, 19, 'Chính sách', 'นโยบาย'),
(830, 19, 'Tài liệu API', 'เอกสารประกอบ API'),
(831, 19, 'Trang chủ', 'บ้าน'),
(832, 19, 'Liên kết', 'ลิงค์'),
(833, 19, 'Câu hỏi thường gặp', 'คำถามที่พบบ่อย'),
(834, 19, 'Liên hệ chúng tôi', 'ติดต่อเรา'),
(835, 19, 'Sản phẩm:', 'ผลิตภัณฑ์:'),
(836, 19, 'Số lượng mua:', 'ปริมาณการซื้อ:'),
(837, 19, 'Thanh toán:', 'จ่าย:'),
(838, 19, 'Mã đơn hàng:', 'รหัสสั่งซื้อ :'),
(839, 19, 'Chi tiết đơn hàng', 'รายละเอียดการสั่งซื้อ'),
(840, 19, 'Tài khoản', 'บัญชี'),
(841, 19, 'Lưu các tài khoản đã chọn vào tệp .txt', 'บันทึกบัญชีที่เลือกลงในไฟล์ .txt'),
(842, 19, 'Sao chép các tài khoản đã chọn', 'คัดลอกบัญชีที่เลือก'),
(843, 19, 'Chỉ sao chép UID các tài khoản đã chọn', 'คัดลอกเฉพาะ UID ของบัญชีที่เลือก'),
(844, 19, 'Số dư của tôi:', 'ความสมดุลของฉัน:'),
(845, 19, 'Khuyến mãi', 'การส่งเสริม'),
(846, 19, 'Số tiền nạp lớn hơn hoặc bằng', 'จำนวนเงินฝากมากกว่าหรือเท่ากับ'),
(847, 19, 'Khuyến mãi thêm', 'โปรโมชั่นเพิ่มเติม'),
(848, 19, 'Thông tin chi tiết khách hàng', 'รายละเอียดลูกค้า'),
(849, 19, 'Chia sẻ liên kết này lên mạng xã hội hoặc bạn bè của bạn.', 'แบ่งปันลิงก์นี้บนเครือข่ายสังคมหรือกับเพื่อนของคุณ'),
(850, 19, 'Tài liệu tích hợp API', 'เอกสารประกอบการรวม API'),
(851, 19, 'Lấy thông tin tài khoản', 'รับข้อมูลบัญชี'),
(852, 19, 'Lấy danh sách chuyên mục và sản phẩm', 'รับรายการหมวดหมู่และสินค้า'),
(853, 19, 'Mua hàng', 'ซื้อ'),
(854, 19, 'ID sản phẩm cần mua', 'รหัสสินค้าที่ต้องการซื้อ'),
(855, 19, 'Số lượng cần mua', 'จำนวนที่ต้องการซื้อ'),
(856, 19, 'Mã giảm giá nếu có', 'โค้ดส่วนลดหากมี'),
(857, 19, 'Bảo mật', 'ความปลอดภัย'),
(858, 19, 'Bảo mật tài khoản', 'ความปลอดภัยของบัญชี'),
(859, 19, 'Xác minh đăng nhập bằng', 'ยืนยันการเข้าสู่ระบบด้วย'),
(860, 19, 'Gửi thông báo về mail khi đăng nhập thành công:', 'ส่งการแจ้งเตือนทางอีเมล์เมื่อเข้าสู่ระบบสำเร็จ:'),
(861, 19, 'Đúng Trình Duyệt và IP mua hàng mới có thể xem đơn hàng:', 'ต้องใช้เบราว์เซอร์และที่อยู่ IP ที่ถูกต้องเพื่อดูคำสั่งซื้อ:'),
(862, 19, '- Sử dụng điện thoại tải App Google Authenticator sau đó quét mã QR để nhận mã xác minh.', '- ใช้โทรศัพท์ของคุณดาวน์โหลดแอป Google Authenticator จากนั้นสแกนรหัส QR เพื่อรับรหัสยืนยัน'),
(863, 19, '- Mã QR sẽ được thay đổi khi bạn tắt xác minh.', '- รหัส QR จะเปลี่ยนแปลงเมื่อคุณปิดการยืนยัน'),
(864, 19, '- Nếu bật Xác minh đăng nhập bằng OTP Mail thì không bật Google Authenticator và ngược lại.', '- หากคุณเปิดใช้งานการยืนยันการเข้าสู่ระบบด้วย OTP Mail อย่าเปิดใช้งาน Google Authenticator และในทางกลับกัน'),
(865, 19, 'Lưu', 'บันทึก'),
(866, 19, 'Nhập mã xác minh để lưu', 'กรอกรหัสยืนยันเพื่อบันทึก'),
(867, 19, 'Sản phẩm liên quan đến từ khóa', 'สินค้าที่เกี่ยวข้องกับคีย์เวิร์ด'),
(868, 19, 'trong số', 'ท่ามกลาง'),
(869, 19, 'Quay lại', 'กลับมาอีกครั้ง'),
(870, 19, 'Tải về đơn hàng', 'ดาวน์โหลดคำสั่ง'),
(871, 19, 'Hệ thống sẽ tải về đơn hàng khi bạn nhấn đồng ý', 'ระบบจะดาวน์โหลดคำสั่งซื้อเมื่อคุณกดยอมรับ'),
(872, 19, 'Hệ thống sẽ xóa đơn hàng khỏi lịch sử của bạn khi bạn nhấn đồng ý', 'ระบบจะลบคำสั่งซื้อออกจากประวัติของคุณเมื่อคุณคลิกยอมรับ'),
(873, 19, 'Đóng', 'ปิด'),
(874, 19, 'Xuất tất cả tài khoản ra tệp .txt', 'ส่งออกบัญชีทั้งหมดไปยังไฟล์ .txt'),
(875, 19, 'Xóa đơn hàng này khỏi lịch sử của bạn', 'ลบคำสั่งนี้ออกจากประวัติของคุณ'),
(876, 19, 'Thành công !', 'ความสำเร็จ !'),
(877, 19, 'Xem chi tiết đơn hàng', 'ดูรายละเอียดการสั่งซื้อ'),
(878, 19, 'Mua thêm', 'ซื้อเพิ่ม'),
(879, 19, 'Tạo đơn hàng thành công !', 'สร้างคำสั่งซื้อสำเร็จแล้ว!'),
(880, 19, 'Đang xử lý...', 'กำลังประมวลผล...'),
(881, 19, 'tài khoản giảm', 'การลดบัญชี'),
(882, 19, 'Chi tiết', 'รายละเอียด'),
(883, 19, 'Tích hợp API', 'การรวม API'),
(884, 19, 'Lấy chi tiết sản phẩm', 'รับรายละเอียดผลิตภัณฑ์'),
(885, 19, 'Ghi chú cá nhân', 'บันทึกส่วนตัว'),
(886, 19, 'ngày trước', 'วันก่อน'),
(887, 19, 'tiếng trước', 'ก่อนหน้า'),
(888, 19, 'phút trước', 'นาทีที่แล้ว'),
(889, 19, 'giây trước', 'วินาทีที่แล้ว'),
(890, 19, 'Hôm qua', 'เมื่อวาน'),
(891, 19, 'tuần trước', 'สัปดาห์ที่แล้ว'),
(892, 19, 'tháng trước', 'เดือนที่แล้ว'),
(893, 19, 'năm trước', 'เมื่อปีที่แล้ว'),
(894, 19, 'Đơn hàng đã bị xóa', 'คำสั่งถูกลบแล้ว'),
(895, 19, 'Bạn có chắc không', 'คุณแน่ใจมั้ย?'),
(896, 19, 'Hệ thống sẽ xóa', 'ระบบจะทำการลบ');
INSERT INTO `translate` (`id`, `lang_id`, `name`, `value`) VALUES
(897, 19, 'đơn hàng bạn chọn khi nhấn Đồng Ý', 'ลำดับที่คุณเลือกเมื่อคุณคลิกตกลง'),
(898, 19, 'Vui lòng chọn ít nhất một đơn hàng.', 'กรุณาเลือกอย่างน้อยหนึ่งคำสั่งซื้อ'),
(899, 19, 'Thất bại!', 'ความล้มเหลว!'),
(900, 19, 'Thành công!', 'ความสำเร็จ!'),
(901, 19, 'Xóa đơn hàng thành công', 'ลบคำสั่งซื้อสำเร็จแล้ว'),
(902, 19, 'Miễn phí', 'ฟรีไม่มีค่าใช้จ่าย'),
(903, 19, 'Lấy mã 2FA', 'รับรหัส 2FA'),
(904, 19, 'Bạn đang xem', 'คุณกำลังดู'),
(905, 19, 'Nhập danh sách UID', 'นำเข้ารายการ UID'),
(906, 19, 'Mỗi dòng 1 UID', '1 UID ต่อบรรทัด'),
(907, 19, 'Tài khoản Live', 'บัญชีออนไลน์'),
(908, 19, 'Tài khoản Die', 'บัญชีของฉัน'),
(909, 19, 'Giảm giá', 'การลดราคา'),
(910, 19, 'Tỷ lệ hoa hồng', 'อัตราคอมมิชชั่น'),
(911, 19, 'Thành viên đã giới thiệu', 'สมาชิกที่ถูกอ้างถึง'),
(912, 19, 'Không có dữ liệu', 'ไม่มีข้อมูล'),
(913, 19, 'Khách hàng', 'ลูกค้า'),
(914, 19, 'Ngày đăng ký', 'วันที่ลงทะเบียน'),
(915, 19, 'Hoa hồng', 'ดอกกุหลาบ'),
(916, 19, 'Mật khẩu mạnh', 'รหัสผ่านที่แข็งแกร่ง'),
(917, 19, 'Mật khẩu trung bình', 'รหัสผ่านเฉลี่ย'),
(918, 19, 'Mật khẩu rất yếu', 'รหัสผ่านอ่อนแอมาก'),
(919, 19, 'Vui lòng nhập mã xác minh 2FA', 'กรุณากรอกรหัสยืนยัน 2FA'),
(920, 19, 'Mã xác minh không chính xác', 'รหัสตรวจสอบไม่ถูกต้อง'),
(921, 19, 'Bật xác thực Google Authenticator', 'เปิดใช้งาน Google Authenticator'),
(922, 19, 'Tắt xác thực Google Authenticator', 'ปิดใช้งานการตรวจสอบสิทธิ์ของ Google Authenticator'),
(923, 19, 'Vui lòng đăng nhập để sử dụng tính năng này', 'กรุณาเข้าสู่ระบบเพื่อใช้ฟีเจอร์นี้'),
(924, 19, 'Chọn phương thức nạp tiền', 'เลือกวิธีการฝากเงิน'),
(925, 19, 'Không hiển thị lại trong 2 giờ', 'ไม่แสดงผลอีกเป็นเวลา 2 ชั่วโมง'),
(926, 19, 'Thông báo', 'การแจ้งเตือน'),
(927, 19, 'Tìm kiếm sản phẩm...', 'ค้นหาผลิตภัณฑ์...'),
(928, 19, 'Chat hỗ trợ', 'การสนับสนุนการแชท'),
(929, 19, 'Chat ngay', 'แชทตอนนี้'),
(930, 19, 'ĐƠN HÀNG GẦN ĐÂY', 'คำสั่งซื้อล่าสุด'),
(931, 19, 'NẠP TIỀN GẦN ĐÂY', 'เงินฝากล่าสุด'),
(932, 19, 'Chức năng này chưa được cấu hình, vui lòng liên hệ Admin', 'ยังไม่ได้กำหนดค่าฟังก์ชันนี้ กรุณาติดต่อผู้ดูแลระบบ'),
(933, 19, 'Số dư không đủ, vui lòng nạp thêm', 'เงินคงเหลือไม่พอ กรุณาเติมเงิน'),
(934, 19, 'Công cụ Check Live UID Facebook', 'เครื่องมือตรวจสอบ UID ของ Facebook Live'),
(935, 19, 'Tiếp thị liên kết', 'การตลาดแบบพันธมิตร'),
(936, 19, 'Liên kết sản phẩm', 'ลิงค์ผลิตภัณฑ์'),
(937, 19, 'Chia sẻ liên kết sản phẩm dưới đây cho bạn bè của bạn, bạn sẽ nhận được hoa hồng khi bạn bè của bạn mua hàng thông qua liên kết phía dưới.', 'แชร์ลิงก์ผลิตภัณฑ์ด้านล่างนี้ให้เพื่อนของคุณ คุณจะได้รับคอมมิชชั่นเมื่อเพื่อนของคุณซื้อผ่านลิงก์ด้านล่าง'),
(938, 19, 'Tất cả sản phẩm', 'สินค้าทั้งหมด'),
(939, 1, 'Sản phẩm yêu thích', 'Sản phẩm yêu thích'),
(940, 19, 'Sản phẩm yêu thích', 'สินค้าที่ชื่นชอบ'),
(941, 2, 'Sản phẩm yêu thích', 'Favorites'),
(942, 20, 'Vui lòng nhập username', '请输入用户名'),
(943, 20, 'Vui lòng nhập mật khẩu', '请输入密码'),
(944, 20, 'Vui lòng xác minh Captcha', '请验证验证码'),
(945, 20, 'Thông tin đăng nhập không chính xác', '登录信息不正确'),
(946, 20, 'Vui lòng nhập địa chỉ Email', '请输入电子邮件地址'),
(947, 20, 'Vui lòng nhập lại mật khẩu', '请重新输入密码'),
(948, 20, 'Xác minh mật khẩu không chính xác', '确认密码不正确'),
(949, 20, 'Tên đăng nhập đã tồn tại trong hệ thống', '该登录名在系统中已经存在。'),
(950, 20, 'Địa chỉ email đã tồn tại trong hệ thống', '电子邮件地址已存在于系统中'),
(951, 20, 'IP của bạn đã đạt đến giới hạn tạo tài khoản cho phép', '您的 IP 已达到允许的帐户创建限制。'),
(952, 20, 'Đăng ký thành công!', '注册成功！'),
(953, 20, 'Tạo tài khoản không thành công, vui lòng thử lại', '账户创建失败，请重试'),
(954, 20, 'Vui lòng đăng nhập', '请登录'),
(955, 20, 'Lưu thành công', '保存成功'),
(956, 20, 'Lưu thất bại', '保存失败'),
(957, 20, 'Vui lòng nhập mật khẩu hiện tại', '请输入当前密码'),
(958, 20, 'Vui lòng nhập mật khẩu mới', '请输入新密码'),
(959, 20, 'Mật khẩu mới quá ngắn', '新密码太短'),
(960, 20, 'Xác nhận mật khẩu không chính xác', '确认密码不正确'),
(961, 20, 'Mật khẩu hiện tại không đúng', '当前密码不正确'),
(962, 20, 'Địa chỉ Email này không tồn tại trong hệ thống', '系统中不存在该电子邮件地址'),
(963, 20, 'Vui lòng thử lại trong ít phút', '请几分钟后重试'),
(964, 20, 'Nếu bạn yêu cầu đặt lại mật khẩu, vui lòng nhấp vào liên kết bên dưới để xác minh.', '如果您需要重置密码，请点击下面的链接进行验证。'),
(965, 20, 'Nếu không phải là bạn, vui lòng liên hệ ngay với Quản trị viên của bạn để được hỗ trợ về bảo mật.', '如果不是，请立即联系您的管理员寻求安全帮助。'),
(966, 20, 'Xác nhận tìm mật khẩu website', '确认查找网站密码'),
(967, 20, 'Xác nhận khôi phục mật khẩu', '确认密码恢复'),
(968, 20, 'Vui lòng kiểm tra Email của bạn để hoàn tất quá trình đặt lại mật khẩu', '请查看您的电子邮件以完成密码重置过程。'),
(969, 20, 'Có lỗi hệ thống, vui lòng liên hệ Developer', '系统错误，请联系开发者'),
(970, 20, 'Liên kết không tồn tại', '链接不存在'),
(971, 20, 'Thay đổi mật khẩu thành công', '密码修改成功'),
(972, 20, 'Thay đổi mật khẩu thất bại', '密码更改失败'),
(973, 20, 'Hồ sơ của bạn', '您的个人资料'),
(974, 20, 'Tên đăng nhập', '用户名'),
(975, 20, 'Địa chỉ Email', '电子邮件'),
(976, 20, 'Số điện thoại', '电话号码'),
(977, 20, 'Họ và Tên', '姓名'),
(978, 20, 'Địa chỉ IP', 'IP 地址'),
(979, 20, 'Thiết bị', '设备'),
(980, 20, 'Đăng ký vào lúc', '注册于'),
(981, 20, 'Đăng nhập gần nhất', '上次登录'),
(982, 20, 'Chỉnh sửa thông tin', '编辑信息'),
(983, 20, 'Thay đổi mật khẩu', '更改密码'),
(984, 20, 'Thay đổi mật khẩu đăng nhập của bạn là một cách dễ dàng để giữ an toàn cho tài khoản của bạn.', '更改登录密码是保证帐户安全的简单方法。'),
(985, 20, 'Mật khẩu hiện tại', '当前密码'),
(986, 20, 'Mật khẩu mới', '新密码'),
(987, 20, 'Nhập lại mật khẩu mới', '重新输入新密码'),
(988, 20, 'Cập Nhật', '更新'),
(989, 20, 'Đăng Xuất', '登出'),
(990, 20, 'Bạn có chắc không?', '你确定吗？'),
(991, 20, 'Bạn sẽ bị đăng xuất khỏi tài khoản khi nhấn Đồng Ý', '单击“同意”后，您将退出帐户。'),
(992, 20, 'Đồng ý', '同意'),
(993, 20, 'Huỷ bỏ', '取消'),
(994, 20, 'Đăng Nhập', '登录'),
(995, 20, 'Vui Lòng Đăng Nhập Để Tiếp Tục', '请登录后继续'),
(996, 20, 'Quên mật khẩu', '忘记密码'),
(997, 20, 'Bạn quên mật khẩu?', '忘记密码了吗？'),
(998, 20, 'Vui lòng nhập thông tin vào ô dưới đây để xác minh', '请在下面的框中输入信息以进行验证'),
(999, 20, 'Xác minh', '核实'),
(1000, 20, 'Bạn đã có tài khoản?', '已有账户？'),
(1001, 20, 'Ghi nhớ tôi', '记住账号'),
(1002, 20, 'Quên mật khẩu?', '忘记密码？'),
(1003, 20, 'Bạn chưa có tài khoản?', '沒有帳戶？'),
(1004, 20, 'Đăng Ký Ngay', '立即注册'),
(1005, 20, 'Nạp tiền', '订金'),
(1006, 20, 'Ngân hàng', '银行'),
(1007, 20, 'Ví của tôi', '我的钱包'),
(1008, 20, 'Số dư hiện tại', '当前余额'),
(1009, 20, 'Tổng tiền nạp', '总存款'),
(1010, 20, 'Số dư đã sử dụng', '已使用余额'),
(1011, 20, 'THANH TOÁN', '支付'),
(1012, 20, 'Lưu ý nạp tiền', '存款须知'),
(1013, 20, 'Lịch sử nạp tiền', '存款历史'),
(1014, 20, 'Số tài khoản:', '帐号：'),
(1015, 20, 'Chủ tài khoản:', '帐户持有人：'),
(1016, 20, 'Ngân hàng:', '银行：'),
(1017, 20, 'Nội dung chuyển khoản:', '转让内容：'),
(1018, 20, 'Mã giao dịch', '交易代码'),
(1019, 20, 'Nội dung', '内容'),
(1020, 20, 'Số tiền nạp', '存款金额'),
(1021, 20, 'Thực nhận', '实现'),
(1022, 20, 'Thời gian', '时间'),
(1023, 20, 'Trạng thái', '地位'),
(1024, 20, 'Đã thanh toán', '有薪酬的'),
(1025, 20, 'Tất cả', '全部'),
(1026, 20, 'Hôm nay', '今天'),
(1027, 20, 'Tuần này', '本星期'),
(1028, 20, 'Tháng này', '本月'),
(1029, 20, 'Đã thanh toán:', '有薪酬的：'),
(1030, 20, 'Thực nhận:', '实际收到：'),
(1031, 20, 'Thao tác', '手术'),
(1032, 20, 'Nhật ký hoạt động', '活动日志'),
(1033, 20, 'Tìm kiếm', '搜索'),
(1034, 20, 'Bỏ lọc', '取消过滤'),
(1035, 20, 'Hiển thị', '展示'),
(1036, 20, 'Ẩn', '隐藏'),
(1037, 20, 'Biến động số dư', '余额波动'),
(1038, 20, 'Số dư ban đầu', '期初余额'),
(1039, 20, 'Số dư thay đổi', '平衡调整'),
(1040, 20, 'Lý do', '原因'),
(1041, 20, 'Chọn thời gian cần tìm', '选择时间进行搜索'),
(1042, 20, 'Hiển thị thêm', '显示更多'),
(1043, 20, 'Ẩn bớt', '隐藏'),
(1044, 20, 'Nội dung chuyển khoản', '传输内容'),
(1045, 20, 'Đăng nhập bằng Google', '使用 Google 登录'),
(1046, 20, 'Đăng nhập bằng Facebook', '使用 Facebook 登录'),
(1047, 20, 'Đăng ký tài khoản', '注册账户'),
(1048, 20, 'Tài khoản đăng nhập', '登录账户'),
(1049, 20, 'Mật khẩu', '密码'),
(1050, 20, 'Nhập lại mật khẩu', '重新输入密码'),
(1051, 20, 'Đăng Ký', '登记'),
(1052, 20, 'Vui lòng nhập thông tin đăng ký', '请输入注册信息'),
(1053, 20, 'Vui lòng nhập thông tin đăng nhập', '请输入您的登录信息'),
(1054, 20, 'Thông tin cá nhân', '个人信息'),
(1055, 20, 'Cấu hình nạp tiền Crypto', '加密货币存款配置'),
(1056, 20, 'All Time', '所有时间'),
(1057, 20, 'Thống kê thanh toán tháng', '每月付款统计'),
(1058, 20, 'Lịch sử nạp tiền Crypto', '加密货币存款历史记录'),
(1059, 20, 'Thống kê', '统计'),
(1060, 20, 'Cấu hình', '配置'),
(1061, 20, 'Nạp tối đa', '最大负载'),
(1062, 20, 'Nạp tối thiểu', '最低存款'),
(1063, 20, 'Nạp tiền bằng Crypto', '使用加密货币存款'),
(1064, 20, 'Lưu ý', '笔记'),
(1065, 20, 'Lịch sử nạp Crypto', '加密货币存款历史记录'),
(1066, 20, 'Số lượng', '数量'),
(1067, 20, 'Thời gian tạo', '创建时间'),
(1068, 20, 'Xem thêm', '查看更多'),
(1069, 20, 'The minimum deposit amount is:', '最低存款金额为：'),
(1070, 20, 'Số tiền gửi tối đa là:', '最高存款额为：'),
(1071, 20, 'Số tiền gửi tối thiểu là:', '最低存款金额为：'),
(1072, 20, 'Chức năng này đang được bảo trì', '该功能正在维护中。'),
(1073, 20, 'Không thể tạo hóa đơn do lỗi API, vui lòng thử lại sau', '由于 API 错误，无法生成发票，请稍后重试'),
(1074, 20, 'Tạo hoá đơn nạp tiền thành công', '充值发票创建成功'),
(1075, 20, 'Nạp tiền bằng PayPal', '通过 PayPal 存款'),
(1076, 20, 'Lịch sử nạp PayPal', 'PayPal 存款历史记录'),
(1077, 20, 'Số tiền gửi', '存款金额'),
(1078, 20, 'Vui lòng nhập số tiền cần nạp', '请输入存款金额'),
(1079, 20, 'Mặc định', '默认'),
(1080, 20, 'Phổ biến', '受欢迎的'),
(1081, 20, 'Tìm kiếm bài viết', '搜索文章'),
(1082, 20, 'Bài viết phổ biến', '热门文章'),
(1083, 20, 'Liên kết giới thiệu của bạn', '您的推荐链接'),
(1084, 20, 'Đã sao chép vào bộ nhớ tạm', '已复制到剪贴板'),
(1085, 20, 'Số tài khoản', '帐号'),
(1086, 20, 'Tên chủ tài khoản', '帐户持有人姓名'),
(1087, 20, 'Số tiền cần rút', '提款金额'),
(1088, 20, 'Rút số dư hoa hồng', '提取佣金余额'),
(1089, 20, 'Lịch sử rút tiền', '提款记录'),
(1090, 20, 'Rút tiền', '提款'),
(1091, 20, 'Lịch sử', '历史'),
(1092, 20, 'Thao tác quá nhanh, vui lòng chờ', '操作太快，请等待。'),
(1093, 20, 'Vui lòng chọn ngân hàng cần rút', '请选择您要提款的银行。'),
(1094, 20, 'Vui lòng nhập số tài khoản cần rút', '请输入提款账号'),
(1095, 20, 'Vui lòng nhập tên chủ tài khoản', '请输入帐户持有人姓名'),
(1096, 20, 'Vui lòng nhập số tiền cần rút', '请输入提款金额'),
(1097, 20, 'Số tiền rút tối thiểu phải là', '最低提款金额必须为'),
(1098, 20, 'Số dư hoa hồng khả dụng của bạn không đủ', '您的可用佣金余额不足'),
(1099, 20, 'Gian lận khi rút số dư hoa hồng', '提取佣金余额存在欺诈行为'),
(1100, 20, 'Tài khoản của bạn đã bị khóa vì gian lận', '您的帐户因欺诈已被锁定'),
(1101, 20, 'Yêu cầu rút tiền được tạo thành công, vui lòng đợi ADMIN xử lý', '提款请求创建成功，请等待管理员处理'),
(1102, 20, 'Số tiền rút', '提款金额'),
(1103, 20, 'Thông kê của bạn', '您的统计数据'),
(1104, 20, 'Số tiền hoa hồng khả dụng', '可用佣金额'),
(1105, 20, 'Tổng số tiền hoa hồng đã nhận', '收到的佣金总额'),
(1106, 20, 'Số lần nhấp vào liên kết', '链接点击次数'),
(1107, 20, 'Lịch sử hoa hồng', '玫瑰的历史'),
(1108, 20, 'Hoa hồng ban đầu', '初始佣金'),
(1109, 20, 'Hoa hồng thay đổi', '佣金变动'),
(1110, 20, 'Hoa hồng hiện tại', '现任委员会'),
(1111, 20, 'Vui lòng nhập số lượng cần mua', '请输入购买数量'),
(1112, 20, 'Tổng tiền thanh toán:', '总付款：'),
(1113, 20, 'Số tiền giảm:', '折扣金额：'),
(1114, 20, 'Thành tiền:', '总金额：'),
(1115, 20, 'Mã giảm giá:', '折扣代码：'),
(1116, 20, 'Nhập mã giảm giá nếu có', '如果有折扣码请输入'),
(1117, 20, 'THÔNG TIN MUA HÀNG', '购买信息'),
(1118, 20, 'Số lượng cần mua:', '购买数量：'),
(1119, 20, 'Chia sẻ:', '分享：'),
(1120, 20, 'Mua Ngay', '立即购买'),
(1121, 20, 'Kho hàng:', '仓库：'),
(1122, 20, 'Đã bán:', '卖：'),
(1123, 20, 'Yêu Thích', '最喜欢的'),
(1124, 20, 'Bỏ Thích', '喜欢'),
(1125, 20, 'Danh sách sản phẩm yêu thích', '最喜爱产品列表'),
(1126, 20, 'Sản phẩm', '产品'),
(1127, 20, 'Kho hàng', '仓库'),
(1128, 20, 'Giá', '价格'),
(1129, 20, 'Mua', '第一的'),
(1130, 20, 'Xem', '看'),
(1131, 20, 'Xóa', '擦除'),
(1132, 20, 'Hết hàng', '缺货'),
(1133, 20, 'Thêm vào mục yêu thích', '添加到收藏夹'),
(1134, 20, 'Đã thêm vào mục yêu thích', '已添加到收藏夹'),
(1135, 20, 'Xóa đơn hàng', '删除订单'),
(1136, 20, 'Xóa đơn hàng đã chọn khỏi lịch sử của bạn', '从历史记录中删除选定的订单'),
(1137, 20, 'Mã đơn hàng', '订购代码'),
(1138, 20, 'Xem chi tiết', '查看详细信息'),
(1139, 20, 'Tải về máy', '下载'),
(1140, 20, 'Xóa khỏi lịch sử', '从历史记录中删除'),
(1141, 20, 'Liên hệ', '接触'),
(1142, 20, 'Chính sách', '政策'),
(1143, 20, 'Tài liệu API', 'API 文档'),
(1144, 20, 'Trang chủ', '家'),
(1145, 20, 'Liên kết', '关联'),
(1146, 20, 'Câu hỏi thường gặp', '常见问题'),
(1147, 20, 'Liên hệ chúng tôi', '联系我们'),
(1148, 20, 'Sản phẩm:', '产品：'),
(1149, 20, 'Số lượng mua:', '购买数量：'),
(1150, 20, 'Thanh toán:', '支付：'),
(1151, 20, 'Mã đơn hàng:', '订单代码：'),
(1152, 20, 'Chi tiết đơn hàng', '订单详情'),
(1153, 20, 'Tài khoản', '帐户'),
(1154, 20, 'Lưu các tài khoản đã chọn vào tệp .txt', '将选定的帐户保存到 .txt 文件'),
(1155, 20, 'Sao chép các tài khoản đã chọn', '复制选定的帐户'),
(1156, 20, 'Chỉ sao chép UID các tài khoản đã chọn', '仅复制选定帐户的 UID'),
(1157, 20, 'Số dư của tôi:', '我的余额：'),
(1158, 20, 'Khuyến mãi', '晋升'),
(1159, 20, 'Số tiền nạp lớn hơn hoặc bằng', '存款金额大于或等于'),
(1160, 20, 'Khuyến mãi thêm', '更多促销活动'),
(1161, 20, 'Thông tin chi tiết khách hàng', '客户详细信息'),
(1162, 20, 'Chia sẻ liên kết này lên mạng xã hội hoặc bạn bè của bạn.', '在社交网络上或与您的朋友分享此链接。'),
(1163, 20, 'Tài liệu tích hợp API', 'API 集成文档'),
(1164, 20, 'Lấy thông tin tài khoản', '获取帐户信息'),
(1165, 20, 'Lấy danh sách chuyên mục và sản phẩm', '获取类别和产品列表'),
(1166, 20, 'Mua hàng', '购买'),
(1167, 20, 'ID sản phẩm cần mua', '要购买的产品ID'),
(1168, 20, 'Số lượng cần mua', '购买数量'),
(1169, 20, 'Mã giảm giá nếu có', 'Mã giảm giá nếu có'),
(1170, 20, 'Bảo mật', '安全'),
(1171, 20, 'Bảo mật tài khoản', '账户安全'),
(1172, 20, 'Xác minh đăng nhập bằng', '使用以下方式验证登录'),
(1173, 20, 'Gửi thông báo về mail khi đăng nhập thành công:', '登录成功时发送电子邮件通知：'),
(1174, 20, 'Đúng Trình Duyệt và IP mua hàng mới có thể xem đơn hàng:', '必须使用正确的浏览器和 IP 地址才能查看订单：'),
(1175, 20, '- Sử dụng điện thoại tải App Google Authenticator sau đó quét mã QR để nhận mã xác minh.', '- 使用您的手机下载 Google Authenticator App，然后扫描二维码以接收验证码。'),
(1176, 20, '- Mã QR sẽ được thay đổi khi bạn tắt xác minh.', '- 关闭验证时，二维码将会改变。'),
(1177, 20, '- Nếu bật Xác minh đăng nhập bằng OTP Mail thì không bật Google Authenticator và ngược lại.', '- 如果您启用使用 OTP 邮件登录验证，请不要启用 Google Authenticator，反之亦然。'),
(1178, 20, 'Lưu', '节省'),
(1179, 20, 'Nhập mã xác minh để lưu', '输入验证码保存'),
(1180, 20, 'Sản phẩm liên quan đến từ khóa', '与关键词相关的产品'),
(1181, 20, 'trong số', '之中'),
(1182, 20, 'Quay lại', '回来'),
(1183, 20, 'Tải về đơn hàng', '下载订单'),
(1184, 20, 'Hệ thống sẽ tải về đơn hàng khi bạn nhấn đồng ý', '点击同意后系统将下载订单。'),
(1185, 20, 'Hệ thống sẽ xóa đơn hàng khỏi lịch sử của bạn khi bạn nhấn đồng ý', '当您点击同意时，系统将从您的历史记录中删除该订单。'),
(1186, 20, 'Đóng', '关闭'),
(1187, 20, 'Xuất tất cả tài khoản ra tệp .txt', '将所有帐户导出到 .txt 文件'),
(1188, 20, 'Xóa đơn hàng này khỏi lịch sử của bạn', '从历史记录中删除此订单'),
(1189, 20, 'Thành công !', '成功 ！'),
(1190, 20, 'Xem chi tiết đơn hàng', '查看订单详情'),
(1191, 20, 'Mua thêm', '购买更多'),
(1192, 20, 'Tạo đơn hàng thành công !', '订单创建成功！'),
(1193, 20, 'Đang xử lý...', '加工...'),
(1194, 20, 'tài khoản giảm', '帐户减少'),
(1195, 20, 'Chi tiết', '细节'),
(1196, 20, 'Tích hợp API', 'API 集成'),
(1197, 20, 'Lấy chi tiết sản phẩm', '获取产品详细信息'),
(1198, 20, 'Ghi chú cá nhân', '个人笔记'),
(1199, 20, 'ngày trước', '前一天'),
(1200, 20, 'tiếng trước', '以前的'),
(1201, 20, 'phút trước', '分钟前'),
(1202, 20, 'giây trước', '几秒前'),
(1203, 20, 'Hôm qua', '昨天'),
(1204, 20, 'tuần trước', '上星期'),
(1205, 20, 'tháng trước', '上个月'),
(1206, 20, 'năm trước', '去年'),
(1207, 20, 'Đơn hàng đã bị xóa', '订单已删除'),
(1208, 20, 'Bạn có chắc không', '你确定吗？'),
(1209, 20, 'Hệ thống sẽ xóa', '系统将删除'),
(1210, 20, 'đơn hàng bạn chọn khi nhấn Đồng Ý', '单击“同意”时选择的顺序'),
(1211, 20, 'Vui lòng chọn ít nhất một đơn hàng.', 'Vui lòng chọn ít nhất một đơn hàng.'),
(1212, 20, 'Thất bại!', '失败！'),
(1213, 20, 'Thành công!', '成功！'),
(1214, 20, 'Xóa đơn hàng thành công', '订单删除成功'),
(1215, 20, 'Miễn phí', '免费'),
(1216, 20, 'Lấy mã 2FA', '获取 2FA 代码'),
(1217, 20, 'Bạn đang xem', '您正在查看'),
(1218, 20, 'Nhập danh sách UID', '导入UID列表'),
(1219, 20, 'Mỗi dòng 1 UID', '每行 1 个 UID'),
(1220, 20, 'Tài khoản Live', '真实账户'),
(1221, 20, 'Tài khoản Die', '死账户'),
(1222, 20, 'Giảm giá', '折扣'),
(1223, 20, 'Tỷ lệ hoa hồng', '佣金率'),
(1224, 20, 'Thành viên đã giới thiệu', '推荐会员'),
(1225, 20, 'Không có dữ liệu', '没有可用数据'),
(1226, 20, 'Khách hàng', '客户'),
(1227, 20, 'Ngày đăng ký', '注册日期'),
(1228, 20, 'Hoa hồng', '玫瑰'),
(1229, 20, 'Mật khẩu mạnh', '强密码'),
(1230, 20, 'Mật khẩu trung bình', '平均密码'),
(1231, 20, 'Mật khẩu rất yếu', '密码强度太弱'),
(1232, 20, 'Vui lòng nhập mã xác minh 2FA', '请输入2FA验证码'),
(1233, 20, 'Mã xác minh không chính xác', '验证码不正确'),
(1234, 20, 'Bật xác thực Google Authenticator', '启用 Google 身份验证器'),
(1235, 20, 'Tắt xác thực Google Authenticator', '关闭 Google Authenticator 身份验证'),
(1236, 20, 'Vui lòng đăng nhập để sử dụng tính năng này', '请登录以使用此功能'),
(1237, 20, 'Chọn phương thức nạp tiền', '选择存款方式'),
(1238, 20, 'Không hiển thị lại trong 2 giờ', '2小时后再无显示'),
(1239, 20, 'Thông báo', '通知'),
(1240, 20, 'Tìm kiếm sản phẩm...', '搜索产品...'),
(1241, 20, 'Chat hỗ trợ', '聊天支持'),
(1242, 20, 'Chat ngay', '立即聊天'),
(1243, 20, 'ĐƠN HÀNG GẦN ĐÂY', '近期订单'),
(1244, 20, 'NẠP TIỀN GẦN ĐÂY', '最近存款'),
(1245, 20, 'Chức năng này chưa được cấu hình, vui lòng liên hệ Admin', '该功能尚未配置，请联系管理员'),
(1246, 20, 'Số dư không đủ, vui lòng nạp thêm', '余额不足，请充值'),
(1247, 20, 'Công cụ Check Live UID Facebook', 'Facebook Live UID 检查工具'),
(1248, 20, 'Tiếp thị liên kết', '联盟营销'),
(1249, 20, 'Liên kết sản phẩm', '产品链接'),
(1250, 20, 'Chia sẻ liên kết sản phẩm dưới đây cho bạn bè của bạn, bạn sẽ nhận được hoa hồng khi bạn bè của bạn mua hàng thông qua liên kết phía dưới.', '分享以下产品链接给您的朋友，当您的朋友通过以下链接购买时，您将获得佣金。'),
(1251, 20, 'Tất cả sản phẩm', '所有产品'),
(1252, 20, 'Sản phẩm yêu thích', '最喜欢的产品');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `google_id` varchar(191) DEFAULT NULL,
  `google_linked_at` datetime DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `prefix_fullname` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `admin` int(11) NOT NULL DEFAULT 0,
  `ctv` int(11) NOT NULL DEFAULT 0,
  `banned` int(11) NOT NULL DEFAULT 0,
  `reason_banned` mediumtext DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `time_session` int(11) DEFAULT 0,
  `time_request` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `token_2fa` varchar(255) DEFAULT NULL,
  `token_forgot_password` varchar(255) DEFAULT NULL,
  `time_forgot_password` int(11) NOT NULL DEFAULT 0,
  `money` decimal(20,2) NOT NULL DEFAULT 0.00,
  `total_money` decimal(20,2) NOT NULL DEFAULT 0.00,
  `debit` decimal(20,2) NOT NULL DEFAULT 0.00,
  `gender` varchar(255) NOT NULL DEFAULT 'Male',
  `device` mediumtext DEFAULT NULL,
  `avatar` mediumtext DEFAULT NULL,
  `status_2fa` int(11) NOT NULL DEFAULT 0,
  `SecretKey_2fa` varchar(255) DEFAULT NULL,
  `limit_2fa` int(11) NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `trial` int(11) NOT NULL DEFAULT 0,
  `ref_id` int(11) NOT NULL DEFAULT 0,
  `ref_ck` float NOT NULL DEFAULT 0,
  `ref_click` int(11) NOT NULL DEFAULT 0,
  `ref_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `ref_price` decimal(20,2) NOT NULL DEFAULT 0.00,
  `ref_total_price` decimal(20,2) NOT NULL DEFAULT 0.00,
  `vat_info` text DEFAULT NULL,
  `telegram_chat_id` mediumtext DEFAULT NULL,
  `telegram_state` varchar(100) DEFAULT NULL,
  `telegram_search` varchar(100) DEFAULT NULL,
  `telegram_lang` varchar(10) DEFAULT NULL,
  `telegram_currency` int(11) DEFAULT NULL,
  `api_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `status_otp_mail` int(11) NOT NULL DEFAULT 0,
  `otp_mail` varchar(55) DEFAULT NULL,
  `token_otp_mail` varchar(255) DEFAULT NULL,
  `limit_otp_mail` int(11) NOT NULL DEFAULT 0,
  `status_noti_login_to_mail` int(11) NOT NULL DEFAULT 0,
  `status_view_order` int(11) NOT NULL DEFAULT 0,
  `utm_source` varchar(55) NOT NULL DEFAULT 'web',
  `ip_whitelist_api` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--


-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users_1`
--

CREATE TABLE `users_1` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `admin` int(11) NOT NULL DEFAULT 0,
  `ctv` int(11) NOT NULL DEFAULT 0,
  `banned` int(11) NOT NULL DEFAULT 0,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `otp` varchar(55) DEFAULT NULL,
  `otp_limit` int(11) NOT NULL DEFAULT 0,
  `otp_token` mediumtext DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `time_session` int(11) DEFAULT 0,
  `time_request` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(255) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `money` float NOT NULL DEFAULT 0,
  `total_money` float NOT NULL DEFAULT 0,
  `rankings` int(11) NOT NULL DEFAULT 0,
  `icon_ranking` mediumtext DEFAULT NULL,
  `gender` varchar(255) NOT NULL DEFAULT 'Male',
  `device` mediumtext DEFAULT NULL,
  `avatar` mediumtext DEFAULT NULL,
  `status_2fa` int(11) NOT NULL DEFAULT 0,
  `SecretKey_2fa` varchar(255) DEFAULT NULL,
  `token_2fa` mediumtext DEFAULT NULL,
  `limit_2fa` int(11) NOT NULL DEFAULT 0,
  `chietkhau` float NOT NULL DEFAULT 0,
  `spin` int(11) NOT NULL DEFAULT 0,
  `ref_id` int(11) NOT NULL DEFAULT 0,
  `ref_click` int(11) NOT NULL DEFAULT 0,
  `ref_money` float NOT NULL DEFAULT 0,
  `ref_total_money` float NOT NULL DEFAULT 0,
  `ref_amount` float NOT NULL DEFAULT 0,
  `ref_ck` float NOT NULL DEFAULT 0,
  `change_password` int(11) NOT NULL DEFAULT 0,
  `token_forgot_password` varchar(255) DEFAULT NULL,
  `time_forgot_password` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users_1`
--


--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_request_logs`
--
ALTER TABLE `admin_request_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `admin_role`
--
ALTER TABLE `admin_role`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `aff_log`
--
ALTER TABLE `aff_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `aff_withdraw`
--
ALTER TABLE `aff_withdraw`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `automations`
--
ALTER TABLE `automations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `block_ip`
--
ALTER TABLE `block_ip`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trans_id` (`trans_id`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_parent_status` (`parent_id`,`status`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `coupon_used`
--
ALTER TABLE `coupon_used`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_coupon_user` (`coupon_id`,`user_id`);

--
-- Chỉ mục cho bảng `ctv_withdraw`
--
ALTER TABLE `ctv_withdraw`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Chỉ mục cho bảng `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `deposit_log`
--
ALTER TABLE `deposit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `dongtien`
--
ALTER TABLE `dongtien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transid` (`transid`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_user_thoigian` (`user_id`,`thoigian`);

--
-- Chỉ mục cho bảng `email_campaigns`
--
ALTER TABLE `email_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `email_queue`
--
ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_priority` (`status`,`priority`,`created_at`),
  ADD KEY `idx_scheduled` (`scheduled_at`);

--
-- Chỉ mục cho bảng `email_sending`
--
ALTER TABLE `email_sending`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_camp_status` (`camp_id`,`status`);

--
-- Chỉ mục cho bảng `failed_attempts`
--
ALTER TABLE `failed_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_type` (`ip_address`,`type`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_product` (`user_id`,`product_id`);

--
-- Chỉ mục cho bảng `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `log_bank_auto`
--
ALTER TABLE `log_bank_auto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tid` (`tid`);

--
-- Chỉ mục cho bảng `log_ref`
--
ALTER TABLE `log_ref`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `momo`
--
ALTER TABLE `momo`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `tranId` (`tranId`);

--
-- Chỉ mục cho bảng `order_log`
--
ALTER TABLE `order_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_buyer` (`buyer`);

--
-- Chỉ mục cho bảng `payment_bakong`
--
ALTER TABLE `payment_bakong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`);

--
-- Chỉ mục cho bảng `payment_bank`
--
ALTER TABLE `payment_bank`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `tid` (`tid`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `payment_crypto`
--
ALTER TABLE `payment_crypto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`);

--
-- Chỉ mục cho bảng `payment_dsociopay`
--
ALTER TABLE `payment_dsociopay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_account_number` (`account_number`);

--
-- Chỉ mục cho bảng `payment_flutterwave`
--
ALTER TABLE `payment_flutterwave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Chỉ mục cho bảng `payment_korapay`
--
ALTER TABLE `payment_korapay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`);

--
-- Chỉ mục cho bảng `payment_lempay`
--
ALTER TABLE `payment_lempay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `payment_manual`
--
ALTER TABLE `payment_manual`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `payment_momo`
--
ALTER TABLE `payment_momo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tid` (`tid`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `payment_openpix`
--
ALTER TABLE `payment_openpix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`);

--
-- Chỉ mục cho bảng `payment_paymentpoint`
--
ALTER TABLE `payment_paymentpoint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `payment_paypal`
--
ALTER TABLE `payment_paypal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`);

--
-- Chỉ mục cho bảng `payment_pm`
--
ALTER TABLE `payment_pm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_status` (`user_id`,`status`);

--
-- Chỉ mục cho bảng `payment_pocketfi`
--
ALTER TABLE `payment_pocketfi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`);

--
-- Chỉ mục cho bảng `payment_squadco`
--
ALTER TABLE `payment_squadco`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_transaction_ref` (`transaction_ref`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `payment_thesieure`
--
ALTER TABLE `payment_thesieure`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tid` (`tid`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `payment_tmweasyapi`
--
ALTER TABLE `payment_tmweasyapi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`);

--
-- Chỉ mục cho bảng `payment_toyyibpay`
--
ALTER TABLE `payment_toyyibpay`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trans_id` (`trans_id`),
  ADD UNIQUE KEY `BillCode` (`BillCode`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `payment_tripay`
--
ALTER TABLE `payment_tripay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`),
  ADD KEY `idx_reference` (`reference`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `payment_xipay`
--
ALTER TABLE `payment_xipay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_out_trade_no` (`out_trade_no`);

--
-- Chỉ mục cho bảng `payment_zinipay`
--
ALTER TABLE `payment_zinipay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trans_id` (`trans_id`),
  ADD KEY `idx_trade_no` (`trade_no`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_category_id` (`category_id`);

--
-- Chỉ mục cho bảng `post_category`
--
ALTER TABLE `post_category`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_category_status` (`category_id`,`status`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_supplier_id` (`supplier_id`);

--
-- Chỉ mục cho bảng `product_die`
--
ALTER TABLE `product_die`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_code` (`product_code`);

--
-- Chỉ mục cho bảng `product_discount`
--
ALTER TABLE `product_discount`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_order`
--
ALTER TABLE `product_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trans_id` (`trans_id`),
  ADD KEY `idx_buyer` (`buyer`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_seller` (`seller`);

--
-- Chỉ mục cho bảng `product_sold`
--
ALTER TABLE `product_sold`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_code` (`product_code`),
  ADD KEY `idx_trans_id` (`trans_id`(64)),
  ADD KEY `idx_buyer` (`buyer`);

--
-- Chỉ mục cho bảng `product_stock`
--
ALTER TABLE `product_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_code` (`product_code`),
  ADD KEY `idx_seller` (`seller`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `idx_name` (`name`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `telegram_logs`
--
ALTER TABLE `telegram_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`);

--
-- Chỉ mục cho bảng `telegram_queue`
--
ALTER TABLE `telegram_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_priority` (`status`,`priority`,`created_at`),
  ADD KEY `idx_scheduled` (`scheduled_at`);

--
-- Chỉ mục cho bảng `translate`
--
ALTER TABLE `translate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lang_id` (`lang_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uniq_google_id` (`google_id`);

--
-- Chỉ mục cho bảng `users_1`
--
ALTER TABLE `users_1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_request_logs`
--
ALTER TABLE `admin_request_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `admin_role`
--
ALTER TABLE `admin_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `aff_log`
--
ALTER TABLE `aff_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `aff_withdraw`
--
ALTER TABLE `aff_withdraw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `automations`
--
ALTER TABLE `automations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `block_ip`
--
ALTER TABLE `block_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `coupon_used`
--
ALTER TABLE `coupon_used`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ctv_withdraw`
--
ALTER TABLE `ctv_withdraw`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `deposit_log`
--
ALTER TABLE `deposit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38796;

--
-- AUTO_INCREMENT cho bảng `dongtien`
--
ALTER TABLE `dongtien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14989;

--
-- AUTO_INCREMENT cho bảng `email_campaigns`
--
ALTER TABLE `email_campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `email_queue`
--
ALTER TABLE `email_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `email_sending`
--
ALTER TABLE `email_sending`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_attempts`
--
ALTER TABLE `failed_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28168;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14271;

--
-- AUTO_INCREMENT cho bảng `log_bank_auto`
--
ALTER TABLE `log_bank_auto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10613;

--
-- AUTO_INCREMENT cho bảng `log_ref`
--
ALTER TABLE `log_ref`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `momo`
--
ALTER TABLE `momo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_log`
--
ALTER TABLE `order_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31262;

--
-- AUTO_INCREMENT cho bảng `payment_bakong`
--
ALTER TABLE `payment_bakong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_bank`
--
ALTER TABLE `payment_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9283;

--
-- AUTO_INCREMENT cho bảng `payment_crypto`
--
ALTER TABLE `payment_crypto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=531;

--
-- AUTO_INCREMENT cho bảng `payment_dsociopay`
--
ALTER TABLE `payment_dsociopay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_flutterwave`
--
ALTER TABLE `payment_flutterwave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_korapay`
--
ALTER TABLE `payment_korapay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_lempay`
--
ALTER TABLE `payment_lempay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_manual`
--
ALTER TABLE `payment_manual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_momo`
--
ALTER TABLE `payment_momo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_openpix`
--
ALTER TABLE `payment_openpix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_paymentpoint`
--
ALTER TABLE `payment_paymentpoint`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_paypal`
--
ALTER TABLE `payment_paypal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_pm`
--
ALTER TABLE `payment_pm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_pocketfi`
--
ALTER TABLE `payment_pocketfi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_squadco`
--
ALTER TABLE `payment_squadco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_thesieure`
--
ALTER TABLE `payment_thesieure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_tmweasyapi`
--
ALTER TABLE `payment_tmweasyapi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_toyyibpay`
--
ALTER TABLE `payment_toyyibpay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_tripay`
--
ALTER TABLE `payment_tripay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_xipay`
--
ALTER TABLE `payment_xipay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payment_zinipay`
--
ALTER TABLE `payment_zinipay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `post_category`
--
ALTER TABLE `post_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1920;

--
-- AUTO_INCREMENT cho bảng `product_die`
--
ALTER TABLE `product_die`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `product_discount`
--
ALTER TABLE `product_discount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `product_order`
--
ALTER TABLE `product_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10457;

--
-- AUTO_INCREMENT cho bảng `product_sold`
--
ALTER TABLE `product_sold`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34152;

--
-- AUTO_INCREMENT cho bảng `product_stock`
--
ALTER TABLE `product_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32232;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=500;

--
-- AUTO_INCREMENT cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `telegram_logs`
--
ALTER TABLE `telegram_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `telegram_queue`
--
ALTER TABLE `telegram_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `translate`
--
ALTER TABLE `translate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1253;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485;

--
-- AUTO_INCREMENT cho bảng `users_1`
--
ALTER TABLE `users_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

-- ============================================
-- GameTopup Tables + Seed Data
-- ============================================

DROP TABLE IF EXISTS `topup_tiers`;
DROP TABLE IF EXISTS `game_servers`;
DROP TABLE IF EXISTS `games`;
DROP TABLE IF EXISTS `topup_api_logs`;

CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `full_name` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `currency_name` varchar(100) DEFAULT NULL,
  `currency_unit` varchar(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `topup_tiers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `type` enum('gem','pack','allpack') NOT NULL DEFAULT 'gem',
  `label` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `price` int(11) NOT NULL DEFAULT 0,
  `cost` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_game_type` (`game_id`, `type`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `game_servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_game` (`game_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `topup_api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL,
  `request_data` text DEFAULT NULL,
  `response_data` text DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `duration_ms` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `topup_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `type` enum('rest_api','mock','webhook') NOT NULL DEFAULT 'rest_api',
  `api_endpoint` varchar(500) DEFAULT NULL,
  `api_key` varchar(500) DEFAULT NULL,
  `api_secret` varchar(500) DEFAULT NULL,
  `http_method` enum('GET','POST') DEFAULT 'POST',
  `timeout_ms` int(11) DEFAULT 15000,
  `retry_count` int(11) DEFAULT 3,
  `retry_delay_ms` int(11) DEFAULT 2000,
  `status` tinyint(1) DEFAULT 1,
  `priority` int(11) DEFAULT 0,
  `fee_percent` decimal(5,2) DEFAULT 0.00,
  `fee_fixed` int(11) DEFAULT 0,
  `last_check` datetime DEFAULT NULL,
  `response_time_ms` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `topup_providers` (`id`, `name`, `slug`, `type`, `status`, `priority`) VALUES
(1, 'Mock Provider', 'mock', 'mock', 1, 0);

-- Seed 121 games

INSERT INTO `games` (`id`, `name`, `full_name`, `category`, `icon`, `currency_name`, `currency_unit`, `status`, `sort_order`) VALUES
(1, 'Immortal Rising: Return Event', 'Immortal Rising: Return Event (Dark Fantasy Idle RPG)', 'Idle RPG', '👑', 'Kim cương', '💎', 1, 1),
(2, 'XP Hero', 'XP Hero', 'Idle RPG', '⚔️', 'Xu', '🪙', 1, 2),
(3, 'Dino King: Survival Idle', 'Dino King: Survival Idle', 'Idle RPG', '🎯', 'Vàng', '🟡', 1, 3),
(4, 'Stickman War: Epic Idle RPG', 'Stickman War: Epic Idle RPG', 'Idle RPG', '💎', 'Ngọc', '🔮', 1, 4),
(5, 'Summoner\'s Greed: Idle TD Hero', 'Summoner\'s Greed: Idle TD Hero', 'Idle RPG', '🏰', 'Đá quý', '💠', 1, 5),
(6, 'Falltopia: Epic Space Idle RPG', 'Falltopia: Epic Space Idle RPG', 'Idle RPG', '🗡️', 'Token', '🎫', 1, 6),
(7, 'Shadow War: Idle RPG Survival', 'Shadow War: Idle RPG Survival', 'Idle RPG', '🌟', 'Coin', '💰', 1, 7),
(8, 'Monster Slayer Offline RPG', 'Monster Slayer Offline RPG', 'Idle RPG', '🏹', 'Gem', '✨', 1, 8),
(9, 'Epic Shadow: Idle RPG War', 'Epic Shadow: Idle RPG War', 'Idle RPG', '🔥', 'Ruby', '🔴', 1, 9),
(10, 'Fortias Saga: Idle RPG', 'Fortias Saga: Idle RPG', 'Idle RPG', '🎮', 'Soul', '💜', 1, 10),
(11, 'Ghost Hunter Idle', 'Ghost Hunter Idle', 'Idle RPG', '🛡️', 'Mana', '🌀', 1, 11),
(12, 'Self-Service Knight: Idle RPG', 'Self-Service Knight: Idle RPG', 'Idle RPG', '⚡', 'Rune', '❄️', 1, 12),
(13, 'FireWizardRPG', 'FireWizardRPG', 'Idle RPG', '🌙', 'Kim cương', '💎', 1, 13),
(14, 'Otherworld Legends', 'Otherworld Legends', 'Idle RPG', '💀', 'Xu', '🪙', 1, 14),
(15, 'G.A.N.G | Gang Management RPG', 'G.A.N.G | Gang Management RPG', 'Idle RPG', '👾', 'Vàng', '🟡', 1, 15),
(16, 'Undead Slayer: Offline Premium', 'Undead Slayer: Offline Premium / Offline Action', 'Idle RPG', '🎪', 'Ngọc', '🔮', 1, 16),
(17, 'World Beast War', 'World Beast War (Idle Merge Game)', 'Idle RPG', '🎭', 'Đá quý', '💠', 1, 17),
(18, 'Avalar: Raid of Shadow', 'Avalar: Raid of Shadow / Shadow War', 'Idle RPG', '🦊', 'Token', '🎫', 1, 18),
(19, 'Stickman Survivor: Idle RPG', 'Stickman Survivor: Idle RPG', 'Idle RPG', '🐉', 'Coin', '💰', 1, 19),
(20, 'Last Survivor: Fantasy Land', 'Last Survivor: Fantasy Land', 'Idle RPG', '🦅', 'Gem', '✨', 1, 20),
(21, 'Shadow Adventure: RPG Journey', 'Shadow Adventure: RPG Journey', 'Idle RPG', '🧙', 'Ruby', '🔴', 1, 21),
(22, 'Shadow Hunt: Idle Survival RPG', 'Shadow Hunt: Idle Survival RPG', 'Idle RPG', '🗿', 'Soul', '💜', 1, 22),
(23, 'Epic Stickman: RPG Idle War', 'Epic Stickman: RPG Idle War', 'Idle RPG', '🎲', 'Mana', '🌀', 1, 23),
(24, 'Hero Quest: Idle RPG War Games', 'Hero Quest: Idle RPG War Games', 'Idle RPG', '🎰', 'Rune', '❄️', 1, 24),
(25, 'God of World', 'God of World', 'Idle RPG', '🃏', 'Kim cương', '💎', 1, 25),
(26, 'Forge & Fortune', 'Forge & Fortune', 'Idle RPG', '🔮', 'Xu', '🪙', 1, 26),
(27, 'Goldvale Mines', 'Goldvale Mines', 'Idle RPG', '💣', 'Vàng', '🟡', 1, 27),
(28, 'My Supermarket: Shop Rush', 'My Supermarket: Shop Rush', 'Simulation', '👻', 'Ngọc', '🔮', 1, 28),
(29, 'Daily Farm: Harvest Empire', 'Daily Farm: Harvest Empire', 'Simulation', '🤖', 'Đá quý', '💠', 1, 29),
(30, 'My Dream Store!', 'My Dream Store!', 'Simulation', '🐺', 'Token', '🎫', 1, 30),
(31, 'Master Hospital', 'Master Hospital', 'Simulation', '👑', 'Coin', '💰', 1, 31),
(32, 'Terminal Master - Bus Tycoon', 'Terminal Master - Bus Tycoon', 'Simulation', '⚔️', 'Gem', '✨', 1, 32),
(33, 'Prison Life: Idle Game', 'Prison Life: Idle Game', 'Simulation', '🎯', 'Ruby', '🔴', 1, 33),
(34, 'Car Dealer Idle 3D', 'Car Dealer Idle 3D', 'Simulation', '💎', 'Soul', '💜', 1, 34),
(35, 'Shopping Empire', 'Shopping Empire', 'Simulation', '🏰', 'Mana', '🌀', 1, 35),
(36, 'My Burger Diner!', 'My Burger Diner!', 'Simulation', '🗡️', 'Rune', '❄️', 1, 36),
(37, 'Idle Oil Empire', 'Idle Oil Empire', 'Simulation', '🌟', 'Kim cương', '💎', 1, 37),
(38, 'Cafe Life: Cơn Sốt Cà Phê', 'Cafe Life: Cơn Sốt Cà Phê', 'Simulation', '🏹', 'Xu', '🪙', 1, 38),
(39, 'Theme Park Manager', 'Theme Park Manager', 'Simulation', '🔥', 'Vàng', '🟡', 1, 39),
(40, 'My Beach Resort!', 'My Beach Resort!', 'Simulation', '🎮', 'Ngọc', '🔮', 1, 40),
(41, 'Cruise World!', 'Cruise World!', 'Simulation', '🛡️', 'Đá quý', '💠', 1, 41),
(42, 'L.I.F.E. Sim Life Simulator', 'L.I.F.E. Sim Life Simulator', 'Simulation', '⚡', 'Token', '🎫', 1, 42),
(43, 'God of World', 'God of World (PIXIO LIMITED)', 'Simulation', '🌙', 'Coin', '💰', 1, 43),
(44, 'Happy Fruit: Merge Puzzle Game', 'Happy Fruit: Merge Puzzle Game', 'Casual', '💀', 'Gem', '✨', 1, 44),
(45, 'Seaside Secrets: Merge & Story', 'Seaside Secrets: Merge & Story', 'Casual', '👾', 'Ruby', '🔴', 1, 45),
(46, 'World Beast War', 'World Beast War (Idle Merge)', 'Casual', '🎪', 'Soul', '💜', 1, 46),
(47, 'Meowar - PvP Cat Merge Defense', 'Meowar - PvP Cat Merge Defense', 'Casual', '🎭', 'Mana', '🌀', 1, 47),
(48, 'Save The Kingdom: Merge Towers', 'Save The Kingdom: Merge Towers', 'Casual', '🦊', 'Rune', '❄️', 1, 48),
(49, 'Merge Number: Puzzle Game', 'Merge Number: Puzzle Game', 'Casual', '🐉', 'Kim cương', '💎', 1, 49),
(50, 'Hero Tower War - Merge Puzzle', 'Hero Tower War - Merge Puzzle', 'Casual', '🦅', 'Xu', '🪙', 1, 50);

INSERT INTO `games` (`id`, `name`, `full_name`, `category`, `icon`, `currency_name`, `currency_unit`, `status`, `sort_order`) VALUES
(51, 'Nightly Knight: Frontier War TD', 'Nightly Knight: Frontier War TD', 'Tower Defense', '🧙', 'Vàng', '🟡', 1, 51),
(52, 'Punko: Tower Defense', 'Punko: Tower Defense', 'Tower Defense', '🗿', 'Ngọc', '🔮', 1, 52),
(53, 'Tower And Bows', 'Tower And Bows', 'Tower Defense', '🎲', 'Đá quý', '💠', 1, 53),
(54, 'Tower And Swords', 'Tower And Swords', 'Tower Defense', '🎰', 'Token', '🎫', 1, 54),
(55, 'Dragon Fever TD', 'Dragon Fever TD', 'Tower Defense', '🃏', 'Coin', '💰', 1, 55),
(56, 'Castle Defender: Idle Defense', 'Castle Defender: Idle Defense', 'Tower Defense', '🔮', 'Gem', '✨', 1, 56),
(57, 'Nighly Knight: Frontier War TD', 'Nighly Knight: Frontier War TD', 'Tower Defense', '💣', 'Ruby', '🔴', 1, 57),
(58, 'Warventure: Stickman Hero TD', 'Warventure: Stickman Hero TD', 'Tower Defense', '👻', 'Soul', '💜', 1, 58),
(59, 'Dragon Burst: Ball Shooter', 'Dragon Burst: Ball Shooter', 'Other', '🤖', 'Mana', '🌀', 1, 59),
(60, 'Frozía: Eternal Frontier', 'Frozía: Eternal Frontier', 'Other', '🐺', 'Rune', '❄️', 1, 60),
(61, 'Gangster Universe!', 'Gangster Universe!', 'Other', '👑', 'Kim cương', '💎', 1, 61),
(62, 'Shark Universe: TG Sinh Tồn', 'Shark Universe: TG Sinh Tồn', 'Other', '⚔️', 'Xu', '🪙', 1, 62),
(63, 'Office Life! Trò chơi ông trùm', 'Office Life! Trò chơi ông trùm', 'Other', '🎯', 'Vàng', '🟡', 1, 63),
(64, 'Subway Surfers City', 'Subway Surfers City', 'Other', '💎', 'Ngọc', '🔮', 1, 64),
(65, 'Planet Defense: Space TD game', 'Planet Defense: Space TD game', 'Other', '🏰', 'Đá quý', '💠', 1, 65),
(66, 'The Battle Cats', 'The Battle Cats', 'Other', '🗡️', 'Token', '🎫', 1, 66),
(67, 'Jurassic World™: The Game', 'Jurassic World™: The Game', 'Other', '🌟', 'Coin', '💰', 1, 67),
(68, 'Dinosaur Universe', 'Dinosaur Universe', 'Other', '🏹', 'Gem', '✨', 1, 68),
(69, 'Tam Quốc: Truyện Lưu Bị', 'Tam Quốc: Truyện Lưu Bị', 'Other', '🔥', 'Ruby', '🔴', 1, 69),
(70, 'Undead City: Zombie Survival', 'Undead City: Zombie Survival', 'Other', '🎮', 'Soul', '💜', 1, 70),
(71, 'Black Deck - Card Battle TCG', 'Black Deck - Card Battle TCG', 'Other', '🛡️', 'Mana', '🌀', 1, 71),
(72, 'Rumble Paws: Ba lô chiến đấu', 'Rumble Paws: Ba lô chiến đấu', 'Other', '⚡', 'Rune', '❄️', 1, 72),
(73, 'Shadow Slayer: Demon Hunter', 'Shadow Slayer: Demon Hunter', 'Other', '🌙', 'Kim cương', '💎', 1, 73),
(74, 'Dice Attack: Roguelike Battle', 'Dice Attack: Roguelike Battle', 'Other', '💀', 'Xu', '🪙', 1, 74),
(75, 'Turnaround Adventure', 'Turnaround Adventure', 'Other', '👾', 'Vàng', '🟡', 1, 75),
(76, 'Arcana Tactics', 'Arcana Tactics', 'Other', '🎪', 'Ngọc', '🔮', 1, 76),
(77, 'Traveler\'s Journey', 'Traveler\'s Journey', 'Other', '🎭', 'Đá quý', '💠', 1, 77),
(78, 'Đảo Rồng Đột Biến', 'Đảo Rồng Đột Biến', 'Other', '🦊', 'Token', '🎫', 1, 78),
(79, 'Hex Warriors', 'Hex Warriors', 'Other', '🐉', 'Coin', '💰', 1, 79),
(80, 'Ninja Turtles: Legends', 'Ninja Turtles: Legends', 'Other', '🦅', 'Gem', '✨', 1, 80),
(81, 'Dragons: Rise of Berk', 'Dragons: Rise of Berk', 'Other', '🧙', 'Ruby', '🔴', 1, 81),
(82, 'K-Devil Hunter', 'K-Devil Hunter', 'Other', '🗿', 'Soul', '💜', 1, 82),
(83, 'Three Kingdoms: Grand Strategy', 'Three Kingdoms: Grand Strategy', 'Other', '🎲', 'Mana', '🌀', 1, 83),
(84, 'Switching Heroes', 'Switching Heroes', 'Other', '🎰', 'Rune', '❄️', 1, 84),
(85, 'BagMaster Isekai – Bag Battle', 'BagMaster Isekai – Bag Battle', 'Other', '🃏', 'Kim cương', '💎', 1, 85),
(86, 'Art Inc. - Collection Clicker', 'Art Inc. - Collection Clicker', 'Mini Games', '🔮', 'Xu', '🪙', 1, 86),
(87, 'What\'s Cooking? - Mama Recipes', 'What\'s Cooking? - Mama Recipes', 'Mini Games', '💣', 'Vàng', '🟡', 1, 87),
(88, 'Game of Earth: Build Your City', 'Game of Earth: Build Your City', 'Mini Games', '👻', 'Ngọc', '🔮', 1, 88),
(89, 'Tap Tap Titan - Evil Clicker', 'Tap Tap Titan - Evil Clicker', 'Mini Games', '🤖', 'Đá quý', '💠', 1, 89),
(90, 'Tap Tap Trillionaire: Invest!', 'Tap Tap Trillionaire: Invest!', 'Mini Games', '🐺', 'Token', '🎫', 1, 90),
(91, 'Like a Celeb', 'Like a Celeb', 'Mini Games', '👑', 'Coin', '💰', 1, 91),
(92, 'Hit & Run: Level Rush', 'Hit & Run: Level Rush', 'Mini Games', '⚔️', 'Gem', '✨', 1, 92),
(93, 'Kingdom Assassin', 'Kingdom Assassin', 'Mini Games', '🎯', 'Ruby', '🔴', 1, 93),
(94, 'Guitar Girl Match 3', 'Guitar Girl Match 3', 'Mini Games', '💎', 'Soul', '💜', 1, 94),
(95, 'Streamer Survival Challenge', 'Streamer Survival Challenge', 'Mini Games', '🏰', 'Mana', '🌀', 1, 95),
(96, 'Downhill Racer', 'Downhill Racer', 'Mini Games', '🗡️', 'Rune', '❄️', 1, 96),
(97, 'WaterPark Boys', 'WaterPark Boys', 'Mini Games', '🌟', 'Kim cương', '💎', 1, 97),
(98, 'Paper Delivery Boy', 'Paper Delivery Boy', 'Mini Games', '🏹', 'Xu', '🪙', 1, 98),
(99, 'Henna Design', 'Henna Design', 'Mini Games', '🔥', 'Vàng', '🟡', 1, 99),
(100, 'Pizza Ready', 'Pizza Ready', 'Mini Games', '🎮', 'Ngọc', '🔮', 1, 100);

INSERT INTO `games` (`id`, `name`, `full_name`, `category`, `icon`, `currency_name`, `currency_unit`, `status`, `sort_order`) VALUES
(101, 'Outlets Rush', 'Outlets Rush', 'Mini Games', '🛡️', 'Đá quý', '💠', 1, 101),
(102, 'Burger Please!', 'Burger Please!', 'Mini Games', '⚡', 'Token', '🎫', 1, 102),
(103, 'Donut Inc.', 'Donut Inc.', 'Mini Games', '🌙', 'Coin', '💰', 1, 103),
(104, 'Order up!: Cook & Serve', 'Order up!: Cook & Serve', 'Mini Games', '💀', 'Gem', '✨', 1, 104),
(105, 'Mob Hunters: Idle RPG', 'Mob Hunters: Idle RPG', 'Mini Games', '👾', 'Ruby', '🔴', 1, 105),
(106, 'Plinko ASMR', 'Plinko ASMR', 'Mini Games', '🎪', 'Soul', '💜', 1, 106),
(107, 'Build The Rail', 'Build The Rail', 'Mini Games', '🎭', 'Mana', '🌀', 1, 107),
(108, 'Snake Clash!', 'Snake Clash!', 'Mini Games', '🦊', 'Rune', '❄️', 1, 108),
(109, 'My Sweet Bakery!', 'My Sweet Bakery!', 'Mini Games', '🐉', 'Kim cương', '💎', 1, 109),
(110, 'My Toy Shop!', 'My Toy Shop!', 'Mini Games', '🦅', 'Xu', '🪙', 1, 110),
(111, 'Super Big Slime: Black Hole 3D', 'Super Big Slime: Black Hole 3D', 'Mini Games', '🧙', 'Vàng', '🟡', 1, 111),
(112, 'Scoot Rush', 'Scoot Rush', 'Mini Games', '🗿', 'Ngọc', '🔮', 1, 112),
(113, 'K-pop Run', 'K-pop Run', 'Mini Games', '🎲', 'Đá quý', '💠', 1, 113),
(114, 'Art Drawing 3D', 'Art Drawing 3D', 'Mini Games', '🎰', 'Token', '🎫', 1, 114),
(115, 'Free Fall Run.io', 'Free Fall Run.io', 'Mini Games', '🃏', 'Coin', '💰', 1, 115),
(116, 'Cut the Enemies.io', 'Cut the Enemies.io', 'Mini Games', '🔮', 'Gem', '✨', 1, 116),
(117, 'ToonyDoll', 'ToonyDoll', 'Mini Games', '💣', 'Ruby', '🔴', 1, 117),
(118, 'Super Salon 3D', 'Super Salon 3D', 'Mini Games', '👻', 'Soul', '💜', 1, 118),
(119, 'Sea Blade', 'Sea Blade', 'Mini Games', '🤖', 'Mana', '🌀', 1, 119),
(120, 'Farm A Boss', 'Farm A Boss', 'Mini Games', '🐺', 'Rune', '❄️', 1, 120),
(121, 'Farm Blade', 'Farm Blade', 'Mini Games', '👑', 'Kim cương', '💎', 1, 121);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(1, 'gem', '60 Kim cương', 60, 25000, 0, 1, 0),
(1, 'gem', '330 Kim cương', 330, 51000, 0, 1, 1),
(1, 'gem', '720 Kim cương', 720, 127000, 0, 1, 2),
(1, 'gem', '1560 Kim cương', 1560, 255000, 0, 1, 3),
(1, 'gem', '3280 Kim cương', 3280, 382000, 0, 1, 4),
(1, 'gem', '6480 Kim cương', 6480, 510000, 0, 1, 5),
(1, 'gem', '14000 Kim cương', 14000, 765000, 0, 1, 6),
(1, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(1, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(1, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(1, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(1, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(1, 'allpack', 'Adventurer Pack', 0, 1281000, 0, 1, 1),
(1, 'allpack', 'Master Pack', 0, 2562000, 0, 1, 2),
(1, 'allpack', 'Ultimate Pack', 0, 5100000, 0, 1, 3),
(2, 'gem', '60 Xu', 60, 25000, 0, 1, 0),
(2, 'gem', '330 Xu', 330, 51000, 0, 1, 1),
(2, 'gem', '720 Xu', 720, 127000, 0, 1, 2),
(2, 'gem', '1560 Xu', 1560, 255000, 0, 1, 3),
(2, 'gem', '3280 Xu', 3280, 382000, 0, 1, 4),
(2, 'gem', '6480 Xu', 6480, 510000, 0, 1, 5),
(2, 'gem', '14000 Xu', 14000, 765000, 0, 1, 6),
(2, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(2, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(2, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(2, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(2, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(2, 'allpack', 'Adventurer Pack', 0, 1281000, 0, 1, 1),
(2, 'allpack', 'Master Pack', 0, 2562000, 0, 1, 2),
(2, 'allpack', 'Ultimate Pack', 0, 5100000, 0, 1, 3),
(3, 'gem', '60 Vàng', 60, 51000, 0, 1, 0),
(3, 'gem', '330 Vàng', 330, 127000, 0, 1, 1),
(3, 'gem', '720 Vàng', 720, 255000, 0, 1, 2),
(3, 'gem', '1560 Vàng', 1560, 382000, 0, 1, 3),
(3, 'gem', '3280 Vàng', 3280, 510000, 0, 1, 4),
(3, 'gem', '6480 Vàng', 6480, 765000, 0, 1, 5),
(3, 'gem', '14000 Vàng', 14000, 1275000, 0, 1, 6),
(3, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(3, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(3, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(3, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(3, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(3, 'allpack', 'Adventurer Pack', 0, 650000, 0, 1, 1),
(3, 'allpack', 'Master Pack', 0, 1300000, 0, 1, 2),
(3, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(4, 'gem', '60 Ngọc', 60, 51000, 0, 1, 0),
(4, 'gem', '330 Ngọc', 330, 127000, 0, 1, 1),
(4, 'gem', '720 Ngọc', 720, 255000, 0, 1, 2),
(4, 'gem', '1560 Ngọc', 1560, 382000, 0, 1, 3),
(4, 'gem', '3280 Ngọc', 3280, 510000, 0, 1, 4),
(4, 'gem', '6480 Ngọc', 6480, 765000, 0, 1, 5),
(4, 'gem', '14000 Ngọc', 14000, 1275000, 0, 1, 6),
(4, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(4, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(4, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(4, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(4, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(4, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(4, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(4, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(5, 'gem', '60 Đá quý', 60, 12000, 0, 1, 0),
(5, 'gem', '330 Đá quý', 330, 25000, 0, 1, 1),
(5, 'gem', '720 Đá quý', 720, 51000, 0, 1, 2),
(5, 'gem', '1560 Đá quý', 1560, 127000, 0, 1, 3),
(5, 'gem', '3280 Đá quý', 3280, 255000, 0, 1, 4),
(5, 'gem', '6480 Đá quý', 6480, 382000, 0, 1, 5),
(5, 'gem', '14000 Đá quý', 14000, 510000, 0, 1, 6),
(5, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(5, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(5, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(5, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(5, 'allpack', 'Starter Pack', 0, 19000, 0, 1, 0),
(5, 'allpack', 'Adventurer Pack', 0, 1278000, 0, 1, 1),
(5, 'allpack', 'Master Pack', 0, 2556000, 0, 1, 2),
(5, 'allpack', 'Ultimate Pack', 0, 5100000, 0, 1, 3),
(6, 'gem', '60 Token', 60, 25000, 0, 1, 0),
(6, 'gem', '330 Token', 330, 51000, 0, 1, 1),
(6, 'gem', '720 Token', 720, 127000, 0, 1, 2),
(6, 'gem', '1560 Token', 1560, 255000, 0, 1, 3),
(6, 'gem', '3280 Token', 3280, 382000, 0, 1, 4),
(6, 'gem', '6480 Token', 6480, 510000, 0, 1, 5),
(6, 'gem', '14000 Token', 14000, 765000, 0, 1, 6),
(6, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(6, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(6, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(6, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(6, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(6, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(6, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(6, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(7, 'gem', '60 Coin', 60, 51000, 0, 1, 0),
(7, 'gem', '330 Coin', 330, 127000, 0, 1, 1),
(7, 'gem', '720 Coin', 720, 255000, 0, 1, 2),
(7, 'gem', '1560 Coin', 1560, 382000, 0, 1, 3),
(7, 'gem', '3280 Coin', 3280, 510000, 0, 1, 4),
(7, 'gem', '6480 Coin', 6480, 765000, 0, 1, 5),
(7, 'gem', '14000 Coin', 14000, 1275000, 0, 1, 6),
(7, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(7, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(7, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(7, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(7, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(7, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(7, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(7, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(8, 'gem', '60 Gem', 60, 51000, 0, 1, 0),
(8, 'gem', '330 Gem', 330, 127000, 0, 1, 1),
(8, 'gem', '720 Gem', 720, 255000, 0, 1, 2),
(8, 'gem', '1560 Gem', 1560, 382000, 0, 1, 3),
(8, 'gem', '3280 Gem', 3280, 510000, 0, 1, 4),
(8, 'gem', '6480 Gem', 6480, 765000, 0, 1, 5),
(8, 'gem', '14000 Gem', 14000, 1275000, 0, 1, 6),
(8, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(8, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(8, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(8, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(8, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(8, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(8, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(8, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(9, 'gem', '60 Ruby', 60, 51000, 0, 1, 0),
(9, 'gem', '330 Ruby', 330, 127000, 0, 1, 1),
(9, 'gem', '720 Ruby', 720, 255000, 0, 1, 2),
(9, 'gem', '1560 Ruby', 1560, 382000, 0, 1, 3),
(9, 'gem', '3280 Ruby', 3280, 510000, 0, 1, 4),
(9, 'gem', '6480 Ruby', 6480, 765000, 0, 1, 5),
(9, 'gem', '14000 Ruby', 14000, 1275000, 0, 1, 6),
(9, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(9, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(9, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(9, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(9, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(9, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(9, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(9, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(10, 'gem', '60 Soul', 60, 25000, 0, 1, 0),
(10, 'gem', '330 Soul', 330, 51000, 0, 1, 1),
(10, 'gem', '720 Soul', 720, 127000, 0, 1, 2),
(10, 'gem', '1560 Soul', 1560, 255000, 0, 1, 3),
(10, 'gem', '3280 Soul', 3280, 382000, 0, 1, 4),
(10, 'gem', '6480 Soul', 6480, 510000, 0, 1, 5),
(10, 'gem', '14000 Soul', 14000, 765000, 0, 1, 6),
(10, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(10, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(10, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(10, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(10, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(10, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(10, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(10, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(11, 'gem', '60 Mana', 60, 127000, 0, 1, 0),
(11, 'gem', '330 Mana', 330, 255000, 0, 1, 1),
(11, 'gem', '720 Mana', 720, 382000, 0, 1, 2),
(11, 'gem', '1560 Mana', 1560, 510000, 0, 1, 3),
(11, 'gem', '3280 Mana', 3280, 765000, 0, 1, 4),
(11, 'gem', '6480 Mana', 6480, 1275000, 0, 1, 5),
(11, 'gem', '14000 Mana', 14000, 1912000, 0, 1, 6),
(11, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(11, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(11, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(11, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(11, 'allpack', 'Starter Pack', 0, 114000, 0, 1, 0),
(11, 'allpack', 'Adventurer Pack', 0, 656000, 0, 1, 1),
(11, 'allpack', 'Master Pack', 0, 1313000, 0, 1, 2),
(11, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(12, 'gem', '60 Rune', 60, 25000, 0, 1, 0),
(12, 'gem', '330 Rune', 330, 51000, 0, 1, 1),
(12, 'gem', '720 Rune', 720, 127000, 0, 1, 2),
(12, 'gem', '1560 Rune', 1560, 255000, 0, 1, 3),
(12, 'gem', '3280 Rune', 3280, 382000, 0, 1, 4),
(12, 'gem', '6480 Rune', 6480, 510000, 0, 1, 5),
(12, 'gem', '14000 Rune', 14000, 765000, 0, 1, 6),
(12, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(12, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(12, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(12, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(12, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(12, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(12, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(12, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(13, 'gem', '60 Kim cương', 60, 25000, 0, 1, 0),
(13, 'gem', '330 Kim cương', 330, 51000, 0, 1, 1),
(13, 'gem', '720 Kim cương', 720, 127000, 0, 1, 2),
(13, 'pack', 'Gói Tháng', 0, 25000, 0, 1, 0),
(13, 'pack', 'Battle Pass', 0, 85000, 0, 1, 1),
(13, 'pack', 'Premium Pass', 0, 127000, 0, 1, 2),
(13, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(13, 'allpack', 'Adventurer Pack', 0, 64000, 0, 1, 1),
(13, 'allpack', 'Master Pack', 0, 127000, 0, 1, 2),
(13, 'allpack', 'Ultimate Pack', 0, 229000, 0, 1, 3),
(14, 'gem', '60 Xu', 60, 25000, 0, 1, 0),
(14, 'gem', '330 Xu', 330, 51000, 0, 1, 1),
(14, 'gem', '720 Xu', 720, 127000, 0, 1, 2),
(14, 'pack', 'Gói Tháng', 0, 25000, 0, 1, 0),
(14, 'pack', 'Battle Pass', 0, 51000, 0, 1, 1),
(14, 'pack', 'Premium Pass', 0, 76000, 0, 1, 2),
(14, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(14, 'allpack', 'Adventurer Pack', 0, 38000, 0, 1, 1),
(14, 'allpack', 'Master Pack', 0, 76000, 0, 1, 2),
(14, 'allpack', 'Ultimate Pack', 0, 127000, 0, 1, 3);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(15, 'gem', '60 Vàng', 60, 51000, 0, 1, 0),
(15, 'gem', '330 Vàng', 330, 127000, 0, 1, 1),
(15, 'gem', '720 Vàng', 720, 255000, 0, 1, 2),
(15, 'gem', '1560 Vàng', 1560, 382000, 0, 1, 3),
(15, 'gem', '3280 Vàng', 3280, 510000, 0, 1, 4),
(15, 'gem', '6480 Vàng', 6480, 765000, 0, 1, 5),
(15, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(15, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(15, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(15, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(15, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(15, 'allpack', 'Adventurer Pack', 0, 315000, 0, 1, 1),
(15, 'allpack', 'Master Pack', 0, 631000, 0, 1, 2),
(15, 'allpack', 'Ultimate Pack', 0, 1224000, 0, 1, 3),
(16, 'gem', '60 Ngọc', 60, 25000, 0, 1, 0),
(16, 'gem', '330 Ngọc', 330, 51000, 0, 1, 1),
(16, 'gem', '720 Ngọc', 720, 127000, 0, 1, 2),
(16, 'gem', '1560 Ngọc', 1560, 255000, 0, 1, 3),
(16, 'gem', '3280 Ngọc', 3280, 382000, 0, 1, 4),
(16, 'gem', '6480 Ngọc', 6480, 510000, 0, 1, 5),
(16, 'gem', '14000 Ngọc', 14000, 765000, 0, 1, 6),
(16, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(16, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(16, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(16, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(16, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(16, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(16, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(16, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(17, 'gem', '60 Đá quý', 60, 51000, 0, 1, 0),
(17, 'gem', '330 Đá quý', 330, 127000, 0, 1, 1),
(17, 'gem', '720 Đá quý', 720, 255000, 0, 1, 2),
(17, 'gem', '1560 Đá quý', 1560, 382000, 0, 1, 3),
(17, 'gem', '3280 Đá quý', 3280, 510000, 0, 1, 4),
(17, 'gem', '6480 Đá quý', 6480, 765000, 0, 1, 5),
(17, 'gem', '14000 Đá quý', 14000, 1275000, 0, 1, 6),
(17, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(17, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(17, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(17, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(17, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(17, 'allpack', 'Adventurer Pack', 0, 682000, 0, 1, 1),
(17, 'allpack', 'Master Pack', 0, 1364000, 0, 1, 2),
(17, 'allpack', 'Ultimate Pack', 0, 2677000, 0, 1, 3),
(18, 'gem', '60 Token', 60, 25000, 0, 1, 0),
(18, 'gem', '330 Token', 330, 51000, 0, 1, 1),
(18, 'gem', '720 Token', 720, 127000, 0, 1, 2),
(18, 'gem', '1560 Token', 1560, 255000, 0, 1, 3),
(18, 'gem', '3280 Token', 3280, 382000, 0, 1, 4),
(18, 'gem', '6480 Token', 6480, 510000, 0, 1, 5),
(18, 'gem', '14000 Token', 14000, 765000, 0, 1, 6),
(18, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(18, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(18, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(18, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(18, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(18, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(18, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(18, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(19, 'gem', '60 Coin', 60, 51000, 0, 1, 0),
(19, 'gem', '330 Coin', 330, 127000, 0, 1, 1),
(19, 'gem', '720 Coin', 720, 255000, 0, 1, 2),
(19, 'gem', '1560 Coin', 1560, 382000, 0, 1, 3),
(19, 'gem', '3280 Coin', 3280, 510000, 0, 1, 4),
(19, 'gem', '6480 Coin', 6480, 765000, 0, 1, 5),
(19, 'gem', '14000 Coin', 14000, 1275000, 0, 1, 6),
(19, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(19, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(19, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(19, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(19, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(19, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(19, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(19, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(20, 'gem', '60 Gem', 60, 25000, 0, 1, 0),
(20, 'gem', '330 Gem', 330, 51000, 0, 1, 1),
(20, 'gem', '720 Gem', 720, 127000, 0, 1, 2),
(20, 'gem', '1560 Gem', 1560, 255000, 0, 1, 3),
(20, 'gem', '3280 Gem', 3280, 382000, 0, 1, 4),
(20, 'gem', '6480 Gem', 6480, 510000, 0, 1, 5),
(20, 'gem', '14000 Gem', 14000, 765000, 0, 1, 6),
(20, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(20, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(20, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(20, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(20, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(20, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(20, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(20, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(21, 'gem', '60 Ruby', 60, 25000, 0, 1, 0),
(21, 'gem', '330 Ruby', 330, 51000, 0, 1, 1),
(21, 'gem', '720 Ruby', 720, 127000, 0, 1, 2),
(21, 'gem', '1560 Ruby', 1560, 255000, 0, 1, 3),
(21, 'gem', '3280 Ruby', 3280, 382000, 0, 1, 4),
(21, 'gem', '6480 Ruby', 6480, 510000, 0, 1, 5),
(21, 'gem', '14000 Ruby', 14000, 765000, 0, 1, 6),
(21, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(21, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(21, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(21, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(21, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(21, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(21, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(21, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(22, 'gem', '60 Soul', 60, 51000, 0, 1, 0),
(22, 'gem', '330 Soul', 330, 127000, 0, 1, 1),
(22, 'gem', '720 Soul', 720, 255000, 0, 1, 2),
(22, 'gem', '1560 Soul', 1560, 382000, 0, 1, 3),
(22, 'gem', '3280 Soul', 3280, 510000, 0, 1, 4),
(22, 'gem', '6480 Soul', 6480, 765000, 0, 1, 5),
(22, 'gem', '14000 Soul', 14000, 1275000, 0, 1, 6),
(22, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(22, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(22, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(22, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(22, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(22, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(22, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(22, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(23, 'gem', '60 Mana', 60, 51000, 0, 1, 0),
(23, 'gem', '330 Mana', 330, 127000, 0, 1, 1),
(23, 'gem', '720 Mana', 720, 255000, 0, 1, 2),
(23, 'gem', '1560 Mana', 1560, 382000, 0, 1, 3),
(23, 'gem', '3280 Mana', 3280, 510000, 0, 1, 4),
(23, 'gem', '6480 Mana', 6480, 765000, 0, 1, 5),
(23, 'gem', '14000 Mana', 14000, 1275000, 0, 1, 6),
(23, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(23, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(23, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(23, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(23, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(23, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(23, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(23, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(24, 'gem', '60 Rune', 60, 24000, 0, 1, 0),
(24, 'gem', '330 Rune', 330, 28000, 0, 1, 1),
(24, 'gem', '1090 Rune', 1090, 32000, 0, 1, 2),
(24, 'gem', '2240 Rune', 2240, 36000, 0, 1, 3),
(24, 'gem', '3880 Rune', 3880, 41000, 0, 1, 4),
(24, 'gem', '8080 Rune', 8080, 46000, 0, 1, 5),
(24, 'pack', 'Gói Tháng', 0, 22000, 0, 1, 0),
(24, 'pack', 'Battle Pass', 0, 26000, 0, 1, 1),
(24, 'pack', 'Premium Pass', 0, 30000, 0, 1, 2),
(24, 'allpack', 'Starter Combo', 0, 34000, 0, 1, 0),
(24, 'allpack', 'Premium Combo', 0, 40000, 0, 1, 1),
(24, 'allpack', 'Whale Pack', 0, 51000, 0, 1, 2),
(24, 'allpack', 'Ultimate Pack', 0, 66000, 0, 1, 3),
(25, 'gem', '60 Kim cương', 60, 51000, 0, 1, 0),
(25, 'gem', '330 Kim cương', 330, 81000, 0, 1, 1),
(25, 'gem', '720 Kim cương', 720, 112000, 0, 1, 2),
(25, 'gem', '1560 Kim cương', 1560, 143000, 0, 1, 3),
(25, 'gem', '3280 Kim cương', 3280, 173000, 0, 1, 4),
(25, 'gem', '6480 Kim cương', 6480, 204000, 0, 1, 5),
(25, 'pack', 'Gói Tháng', 0, 51000, 0, 1, 0),
(25, 'pack', 'Battle Pass', 0, 85000, 0, 1, 1),
(25, 'pack', 'Premium Pass', 0, 127000, 0, 1, 2),
(25, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(25, 'allpack', 'Adventurer Pack', 0, 64000, 0, 1, 1),
(25, 'allpack', 'Master Pack', 0, 127000, 0, 1, 2),
(25, 'allpack', 'Ultimate Pack', 0, 204000, 0, 1, 3),
(26, 'gem', '60 Xu', 60, 25000, 0, 1, 0),
(26, 'gem', '330 Xu', 330, 30000, 0, 1, 1),
(26, 'gem', '1090 Xu', 1090, 34000, 0, 1, 2),
(26, 'gem', '2240 Xu', 2240, 38000, 0, 1, 3),
(26, 'gem', '3880 Xu', 3880, 43000, 0, 1, 4),
(26, 'gem', '8080 Xu', 8080, 49000, 0, 1, 5),
(26, 'pack', 'Gói Tháng', 0, 23000, 0, 1, 0),
(26, 'pack', 'Battle Pass', 0, 28000, 0, 1, 1),
(26, 'pack', 'Premium Pass', 0, 32000, 0, 1, 2),
(26, 'allpack', 'Starter Combo', 0, 36000, 0, 1, 0),
(26, 'allpack', 'Premium Combo', 0, 42000, 0, 1, 1),
(26, 'allpack', 'Whale Pack', 0, 54000, 0, 1, 2),
(26, 'allpack', 'Ultimate Pack', 0, 70000, 0, 1, 3),
(27, 'gem', '60 Vàng', 60, 51000, 0, 1, 0),
(27, 'gem', '330 Vàng', 330, 127000, 0, 1, 1),
(27, 'gem', '720 Vàng', 720, 255000, 0, 1, 2),
(27, 'gem', '1560 Vàng', 1560, 382000, 0, 1, 3),
(27, 'gem', '3280 Vàng', 3280, 510000, 0, 1, 4),
(27, 'gem', '6480 Vàng', 6480, 765000, 0, 1, 5),
(27, 'gem', '14000 Vàng', 14000, 1275000, 0, 1, 6),
(27, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(27, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(27, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(27, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(27, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(27, 'allpack', 'Adventurer Pack', 0, 331000, 0, 1, 1),
(27, 'allpack', 'Master Pack', 0, 663000, 0, 1, 2),
(27, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(28, 'gem', '60 Ngọc', 60, 25000, 0, 1, 0),
(28, 'gem', '330 Ngọc', 330, 51000, 0, 1, 1),
(28, 'gem', '720 Ngọc', 720, 127000, 0, 1, 2),
(28, 'gem', '1560 Ngọc', 1560, 255000, 0, 1, 3),
(28, 'gem', '3280 Ngọc', 3280, 382000, 0, 1, 4),
(28, 'gem', '6480 Ngọc', 6480, 510000, 0, 1, 5),
(28, 'gem', '14000 Ngọc', 14000, 765000, 0, 1, 6),
(28, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(28, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(28, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(28, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(28, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(28, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(28, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(28, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(29, 'gem', '60 Đá quý', 60, 51000, 0, 1, 0),
(29, 'gem', '330 Đá quý', 330, 127000, 0, 1, 1),
(29, 'gem', '720 Đá quý', 720, 255000, 0, 1, 2),
(29, 'gem', '1560 Đá quý', 1560, 382000, 0, 1, 3),
(29, 'gem', '3280 Đá quý', 3280, 510000, 0, 1, 4),
(29, 'gem', '6480 Đá quý', 6480, 765000, 0, 1, 5),
(29, 'gem', '14000 Đá quý', 14000, 1275000, 0, 1, 6),
(29, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(29, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(29, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(29, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(29, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(29, 'allpack', 'Adventurer Pack', 0, 714000, 0, 1, 1),
(29, 'allpack', 'Master Pack', 0, 1428000, 0, 1, 2),
(29, 'allpack', 'Ultimate Pack', 0, 2805000, 0, 1, 3),
(30, 'gem', '60 Token', 60, 51000, 0, 1, 0),
(30, 'gem', '330 Token', 330, 127000, 0, 1, 1),
(30, 'gem', '720 Token', 720, 255000, 0, 1, 2),
(30, 'gem', '1560 Token', 1560, 382000, 0, 1, 3),
(30, 'gem', '3280 Token', 3280, 510000, 0, 1, 4),
(30, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(30, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(30, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(30, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(30, 'allpack', 'Adventurer Pack', 0, 140000, 0, 1, 1),
(30, 'allpack', 'Master Pack', 0, 280000, 0, 1, 2),
(30, 'allpack', 'Ultimate Pack', 0, 510000, 0, 1, 3),
(31, 'gem', '60 Coin', 60, 153000, 0, 1, 0),
(31, 'gem', '330 Coin', 330, 173000, 0, 1, 1),
(31, 'gem', '720 Coin', 720, 194000, 0, 1, 2),
(31, 'gem', '1560 Coin', 1560, 214000, 0, 1, 3),
(31, 'gem', '3280 Coin', 3280, 234000, 0, 1, 4),
(31, 'gem', '6480 Coin', 6480, 255000, 0, 1, 5),
(31, 'pack', 'Gói Tháng', 0, 153000, 0, 1, 0),
(31, 'pack', 'Battle Pass', 0, 136000, 0, 1, 1),
(31, 'pack', 'Premium Pass', 0, 204000, 0, 1, 2),
(31, 'allpack', 'Starter Pack', 0, 229000, 0, 1, 0),
(31, 'allpack', 'Adventurer Pack', 0, 102000, 0, 1, 1),
(31, 'allpack', 'Master Pack', 0, 204000, 0, 1, 2),
(31, 'allpack', 'Ultimate Pack', 0, 255000, 0, 1, 3),
(32, 'gem', '60 Gem', 60, 51000, 0, 1, 0),
(32, 'gem', '330 Gem', 330, 127000, 0, 1, 1),
(32, 'gem', '720 Gem', 720, 255000, 0, 1, 2),
(32, 'gem', '1560 Gem', 1560, 382000, 0, 1, 3),
(32, 'gem', '3280 Gem', 3280, 510000, 0, 1, 4),
(32, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(32, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(32, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(32, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(32, 'allpack', 'Adventurer Pack', 0, 140000, 0, 1, 1),
(32, 'allpack', 'Master Pack', 0, 280000, 0, 1, 2),
(32, 'allpack', 'Ultimate Pack', 0, 510000, 0, 1, 3),
(33, 'gem', '60 Ruby', 60, 25000, 0, 1, 0),
(33, 'gem', '330 Ruby', 330, 51000, 0, 1, 1),
(33, 'gem', '720 Ruby', 720, 127000, 0, 1, 2),
(33, 'gem', '1560 Ruby', 1560, 255000, 0, 1, 3),
(33, 'gem', '3280 Ruby', 3280, 382000, 0, 1, 4),
(33, 'gem', '6480 Ruby', 6480, 510000, 0, 1, 5),
(33, 'gem', '14000 Ruby', 14000, 765000, 0, 1, 6),
(33, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(33, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(33, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(33, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(33, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(33, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(33, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(33, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(34, 'gem', '60 Soul', 60, 127000, 0, 1, 0),
(34, 'gem', '330 Soul', 330, 255000, 0, 1, 1),
(34, 'gem', '720 Soul', 720, 382000, 0, 1, 2),
(34, 'gem', '1560 Soul', 1560, 510000, 0, 1, 3),
(34, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(34, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(34, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(34, 'allpack', 'Starter Pack', 0, 114000, 0, 1, 0),
(34, 'allpack', 'Adventurer Pack', 0, 146000, 0, 1, 1),
(34, 'allpack', 'Master Pack', 0, 293000, 0, 1, 2),
(34, 'allpack', 'Ultimate Pack', 0, 510000, 0, 1, 3),
(35, 'gem', '60 Mana', 60, 31000, 0, 1, 0),
(35, 'gem', '330 Mana', 330, 36000, 0, 1, 1),
(35, 'gem', '1090 Mana', 1090, 41000, 0, 1, 2),
(35, 'gem', '2240 Mana', 2240, 46000, 0, 1, 3),
(35, 'gem', '3880 Mana', 3880, 52000, 0, 1, 4),
(35, 'gem', '8080 Mana', 8080, 59000, 0, 1, 5),
(35, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(35, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(35, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(35, 'allpack', 'Starter Combo', 0, 43000, 0, 1, 0),
(35, 'allpack', 'Premium Combo', 0, 51000, 0, 1, 1),
(35, 'allpack', 'Whale Pack', 0, 65000, 0, 1, 2),
(35, 'allpack', 'Ultimate Pack', 0, 84000, 0, 1, 3),
(36, 'gem', '60 Rune', 60, 31000, 0, 1, 0),
(36, 'gem', '330 Rune', 330, 36000, 0, 1, 1),
(36, 'gem', '1090 Rune', 1090, 42000, 0, 1, 2),
(36, 'gem', '2240 Rune', 2240, 47000, 0, 1, 3),
(36, 'gem', '3880 Rune', 3880, 53000, 0, 1, 4),
(36, 'gem', '8080 Rune', 8080, 60000, 0, 1, 5);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(36, 'pack', 'Gói Tháng', 0, 29000, 0, 1, 0),
(36, 'pack', 'Battle Pass', 0, 34000, 0, 1, 1),
(36, 'pack', 'Premium Pass', 0, 39000, 0, 1, 2),
(36, 'allpack', 'Starter Combo', 0, 44000, 0, 1, 0),
(36, 'allpack', 'Premium Combo', 0, 52000, 0, 1, 1),
(36, 'allpack', 'Whale Pack', 0, 66000, 0, 1, 2),
(36, 'allpack', 'Ultimate Pack', 0, 86000, 0, 1, 3),
(37, 'gem', '60 Kim cương', 60, 25000, 0, 1, 0),
(37, 'gem', '330 Kim cương', 330, 51000, 0, 1, 1),
(37, 'gem', '720 Kim cương', 720, 127000, 0, 1, 2),
(37, 'gem', '1560 Kim cương', 1560, 255000, 0, 1, 3),
(37, 'gem', '3280 Kim cương', 3280, 382000, 0, 1, 4),
(37, 'gem', '6480 Kim cương', 6480, 510000, 0, 1, 5),
(37, 'gem', '14000 Kim cương', 14000, 765000, 0, 1, 6),
(37, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(37, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(37, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(37, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(37, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(37, 'allpack', 'Adventurer Pack', 0, 325000, 0, 1, 1),
(37, 'allpack', 'Master Pack', 0, 650000, 0, 1, 2),
(37, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(38, 'gem', '60 Xu', 60, 127000, 0, 1, 0),
(38, 'gem', '330 Xu', 330, 153000, 0, 1, 1),
(38, 'gem', '720 Xu', 720, 178000, 0, 1, 2),
(38, 'gem', '1560 Xu', 1560, 204000, 0, 1, 3),
(38, 'gem', '3280 Xu', 3280, 229000, 0, 1, 4),
(38, 'gem', '6480 Xu', 6480, 255000, 0, 1, 5),
(38, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(38, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(38, 'allpack', 'Starter Pack', 0, 191000, 0, 1, 0),
(38, 'allpack', 'Adventurer Pack', 0, 95000, 0, 1, 1),
(38, 'allpack', 'Master Pack', 0, 191000, 0, 1, 2),
(38, 'allpack', 'Ultimate Pack', 0, 255000, 0, 1, 3),
(39, 'gem', '60 Vàng', 60, 25000, 0, 1, 0),
(39, 'gem', '330 Vàng', 330, 51000, 0, 1, 1),
(39, 'gem', '720 Vàng', 720, 127000, 0, 1, 2),
(39, 'gem', '1560 Vàng', 1560, 255000, 0, 1, 3),
(39, 'gem', '3280 Vàng', 3280, 382000, 0, 1, 4),
(39, 'gem', '6480 Vàng', 6480, 510000, 0, 1, 5),
(39, 'gem', '14000 Vàng', 14000, 765000, 0, 1, 6),
(39, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(39, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(39, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(39, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(39, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(39, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(39, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(39, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(40, 'gem', '60 Ngọc', 60, 51000, 0, 1, 0),
(40, 'gem', '330 Ngọc', 330, 127000, 0, 1, 1),
(40, 'gem', '720 Ngọc', 720, 255000, 0, 1, 2),
(40, 'gem', '1560 Ngọc', 1560, 382000, 0, 1, 3),
(40, 'gem', '3280 Ngọc', 3280, 510000, 0, 1, 4),
(40, 'gem', '6480 Ngọc', 6480, 765000, 0, 1, 5),
(40, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(40, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(40, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(40, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(40, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(40, 'allpack', 'Adventurer Pack', 0, 204000, 0, 1, 1),
(40, 'allpack', 'Master Pack', 0, 408000, 0, 1, 2),
(40, 'allpack', 'Ultimate Pack', 0, 765000, 0, 1, 3),
(41, 'gem', '60 Đá quý', 60, 127000, 0, 1, 0),
(41, 'gem', '330 Đá quý', 330, 153000, 0, 1, 1),
(41, 'gem', '720 Đá quý', 720, 178000, 0, 1, 2),
(41, 'gem', '1560 Đá quý', 1560, 204000, 0, 1, 3),
(41, 'gem', '3280 Đá quý', 3280, 229000, 0, 1, 4),
(41, 'gem', '6480 Đá quý', 6480, 255000, 0, 1, 5),
(41, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(41, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(41, 'allpack', 'Starter Pack', 0, 191000, 0, 1, 0),
(41, 'allpack', 'Adventurer Pack', 0, 95000, 0, 1, 1),
(41, 'allpack', 'Master Pack', 0, 191000, 0, 1, 2),
(41, 'allpack', 'Ultimate Pack', 0, 255000, 0, 1, 3),
(42, 'gem', '60 Token', 60, 127000, 0, 1, 0),
(42, 'gem', '330 Token', 330, 255000, 0, 1, 1),
(42, 'gem', '720 Token', 720, 382000, 0, 1, 2),
(42, 'gem', '1560 Token', 1560, 510000, 0, 1, 3),
(42, 'gem', '3280 Token', 3280, 765000, 0, 1, 4),
(42, 'gem', '6480 Token', 6480, 1275000, 0, 1, 5),
(42, 'gem', '14000 Token', 14000, 1912000, 0, 1, 6),
(42, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(42, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(42, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(42, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(42, 'allpack', 'Starter Pack', 0, 114000, 0, 1, 0),
(42, 'allpack', 'Adventurer Pack', 0, 911000, 0, 1, 1),
(42, 'allpack', 'Master Pack', 0, 1823000, 0, 1, 2),
(42, 'allpack', 'Ultimate Pack', 0, 3570000, 0, 1, 3),
(43, 'gem', '60 Coin', 60, 51000, 0, 1, 0),
(43, 'gem', '330 Coin', 330, 81000, 0, 1, 1),
(43, 'gem', '720 Coin', 720, 112000, 0, 1, 2),
(43, 'gem', '1560 Coin', 1560, 143000, 0, 1, 3),
(43, 'gem', '3280 Coin', 3280, 173000, 0, 1, 4),
(43, 'gem', '6480 Coin', 6480, 204000, 0, 1, 5),
(43, 'pack', 'Gói Tháng', 0, 51000, 0, 1, 0),
(43, 'pack', 'Battle Pass', 0, 85000, 0, 1, 1),
(43, 'pack', 'Premium Pass', 0, 127000, 0, 1, 2),
(43, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(43, 'allpack', 'Adventurer Pack', 0, 64000, 0, 1, 1),
(43, 'allpack', 'Master Pack', 0, 127000, 0, 1, 2),
(43, 'allpack', 'Ultimate Pack', 0, 204000, 0, 1, 3),
(44, 'gem', '60 Gem', 60, 36000, 0, 1, 0),
(44, 'gem', '330 Gem', 330, 42000, 0, 1, 1),
(44, 'gem', '1090 Gem', 1090, 48000, 0, 1, 2),
(44, 'gem', '2240 Gem', 2240, 54000, 0, 1, 3),
(44, 'gem', '3880 Gem', 3880, 61000, 0, 1, 4),
(44, 'gem', '8080 Gem', 8080, 69000, 0, 1, 5),
(44, 'pack', 'Gói Tháng', 0, 33000, 0, 1, 0),
(44, 'pack', 'Battle Pass', 0, 39000, 0, 1, 1),
(44, 'pack', 'Premium Pass', 0, 45000, 0, 1, 2),
(44, 'allpack', 'Starter Combo', 0, 51000, 0, 1, 0),
(44, 'allpack', 'Premium Combo', 0, 60000, 0, 1, 1),
(44, 'allpack', 'Whale Pack', 0, 76000, 0, 1, 2),
(44, 'allpack', 'Ultimate Pack', 0, 98000, 0, 1, 3),
(45, 'gem', '60 Ruby', 60, 25000, 0, 1, 0),
(45, 'gem', '330 Ruby', 330, 51000, 0, 1, 1),
(45, 'gem', '720 Ruby', 720, 127000, 0, 1, 2),
(45, 'gem', '1560 Ruby', 1560, 255000, 0, 1, 3),
(45, 'gem', '3280 Ruby', 3280, 382000, 0, 1, 4),
(45, 'gem', '6480 Ruby', 6480, 510000, 0, 1, 5),
(45, 'gem', '14000 Ruby', 14000, 765000, 0, 1, 6),
(45, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(45, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(45, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(45, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(45, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(45, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(45, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(45, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(46, 'gem', '60 Soul', 60, 51000, 0, 1, 0),
(46, 'gem', '330 Soul', 330, 127000, 0, 1, 1),
(46, 'gem', '720 Soul', 720, 255000, 0, 1, 2),
(46, 'gem', '1560 Soul', 1560, 382000, 0, 1, 3),
(46, 'gem', '3280 Soul', 3280, 510000, 0, 1, 4),
(46, 'gem', '6480 Soul', 6480, 765000, 0, 1, 5),
(46, 'gem', '14000 Soul', 14000, 1275000, 0, 1, 6),
(46, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(46, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(46, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(46, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(46, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(46, 'allpack', 'Adventurer Pack', 0, 682000, 0, 1, 1),
(46, 'allpack', 'Master Pack', 0, 1364000, 0, 1, 2),
(46, 'allpack', 'Ultimate Pack', 0, 2677000, 0, 1, 3),
(47, 'gem', '60 Mana', 60, 25000, 0, 1, 0),
(47, 'gem', '330 Mana', 330, 51000, 0, 1, 1),
(47, 'gem', '720 Mana', 720, 127000, 0, 1, 2),
(47, 'gem', '1560 Mana', 1560, 255000, 0, 1, 3),
(47, 'gem', '3280 Mana', 3280, 382000, 0, 1, 4),
(47, 'gem', '6480 Mana', 6480, 510000, 0, 1, 5),
(47, 'gem', '14000 Mana', 14000, 765000, 0, 1, 6),
(47, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(47, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(47, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(47, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(47, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(47, 'allpack', 'Adventurer Pack', 0, 676000, 0, 1, 1),
(47, 'allpack', 'Master Pack', 0, 1351000, 0, 1, 2),
(47, 'allpack', 'Ultimate Pack', 0, 2677000, 0, 1, 3),
(48, 'gem', '60 Rune', 60, 25000, 0, 1, 0),
(48, 'gem', '330 Rune', 330, 51000, 0, 1, 1),
(48, 'gem', '720 Rune', 720, 127000, 0, 1, 2),
(48, 'gem', '1560 Rune', 1560, 255000, 0, 1, 3),
(48, 'gem', '3280 Rune', 3280, 382000, 0, 1, 4),
(48, 'gem', '6480 Rune', 6480, 510000, 0, 1, 5),
(48, 'gem', '14000 Rune', 14000, 765000, 0, 1, 6),
(48, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(48, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(48, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(48, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(48, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(48, 'allpack', 'Adventurer Pack', 0, 580000, 0, 1, 1),
(48, 'allpack', 'Master Pack', 0, 1160000, 0, 1, 2),
(48, 'allpack', 'Ultimate Pack', 0, 2295000, 0, 1, 3),
(49, 'gem', '60 Kim cương', 60, 12000, 0, 1, 0),
(49, 'gem', '330 Kim cương', 330, 25000, 0, 1, 1),
(49, 'gem', '720 Kim cương', 720, 51000, 0, 1, 2),
(49, 'gem', '1560 Kim cương', 1560, 127000, 0, 1, 3),
(49, 'gem', '3280 Kim cương', 3280, 255000, 0, 1, 4),
(49, 'gem', '6480 Kim cương', 6480, 382000, 0, 1, 5),
(49, 'gem', '14000 Kim cương', 14000, 510000, 0, 1, 6),
(49, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(49, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(49, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(49, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(49, 'allpack', 'Starter Pack', 0, 19000, 0, 1, 0),
(49, 'allpack', 'Adventurer Pack', 0, 449000, 0, 1, 1),
(49, 'allpack', 'Master Pack', 0, 899000, 0, 1, 2),
(49, 'allpack', 'Ultimate Pack', 0, 1785000, 0, 1, 3),
(50, 'gem', '60 Xu', 60, 25000, 0, 1, 0),
(50, 'gem', '330 Xu', 330, 51000, 0, 1, 1),
(50, 'gem', '720 Xu', 720, 127000, 0, 1, 2),
(50, 'gem', '1560 Xu', 1560, 255000, 0, 1, 3),
(50, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(50, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(50, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(50, 'allpack', 'Adventurer Pack', 0, 70000, 0, 1, 1),
(50, 'allpack', 'Master Pack', 0, 140000, 0, 1, 2);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(50, 'allpack', 'Ultimate Pack', 0, 255000, 0, 1, 3),
(51, 'gem', '60 Vàng', 60, 25000, 0, 1, 0),
(51, 'gem', '330 Vàng', 330, 51000, 0, 1, 1),
(51, 'gem', '720 Vàng', 720, 127000, 0, 1, 2),
(51, 'gem', '1560 Vàng', 1560, 255000, 0, 1, 3),
(51, 'gem', '3280 Vàng', 3280, 382000, 0, 1, 4),
(51, 'gem', '6480 Vàng', 6480, 510000, 0, 1, 5),
(51, 'gem', '14000 Vàng', 14000, 765000, 0, 1, 6),
(51, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(51, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(51, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(51, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(51, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(51, 'allpack', 'Adventurer Pack', 0, 1281000, 0, 1, 1),
(51, 'allpack', 'Master Pack', 0, 2562000, 0, 1, 2),
(51, 'allpack', 'Ultimate Pack', 0, 5100000, 0, 1, 3),
(52, 'gem', '60 Ngọc', 60, 25000, 0, 1, 0),
(52, 'gem', '330 Ngọc', 330, 51000, 0, 1, 1),
(52, 'gem', '720 Ngọc', 720, 127000, 0, 1, 2),
(52, 'gem', '1560 Ngọc', 1560, 255000, 0, 1, 3),
(52, 'gem', '3280 Ngọc', 3280, 382000, 0, 1, 4),
(52, 'gem', '6480 Ngọc', 6480, 510000, 0, 1, 5),
(52, 'gem', '14000 Ngọc', 14000, 765000, 0, 1, 6),
(52, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(52, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(52, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(52, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(52, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(52, 'allpack', 'Adventurer Pack', 0, 1281000, 0, 1, 1),
(52, 'allpack', 'Master Pack', 0, 2562000, 0, 1, 2),
(52, 'allpack', 'Ultimate Pack', 0, 5100000, 0, 1, 3),
(53, 'gem', '60 Đá quý', 60, 25000, 0, 1, 0),
(53, 'gem', '330 Đá quý', 330, 51000, 0, 1, 1),
(53, 'gem', '720 Đá quý', 720, 127000, 0, 1, 2),
(53, 'gem', '1560 Đá quý', 1560, 255000, 0, 1, 3),
(53, 'gem', '3280 Đá quý', 3280, 382000, 0, 1, 4),
(53, 'gem', '6480 Đá quý', 6480, 510000, 0, 1, 5),
(53, 'gem', '14000 Đá quý', 14000, 765000, 0, 1, 6),
(53, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(53, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(53, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(53, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(53, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(53, 'allpack', 'Adventurer Pack', 0, 229000, 0, 1, 1),
(53, 'allpack', 'Master Pack', 0, 459000, 0, 1, 2),
(53, 'allpack', 'Ultimate Pack', 0, 892000, 0, 1, 3),
(54, 'gem', '60 Token', 60, 25000, 0, 1, 0),
(54, 'gem', '330 Token', 330, 51000, 0, 1, 1),
(54, 'gem', '720 Token', 720, 127000, 0, 1, 2),
(54, 'gem', '1560 Token', 1560, 255000, 0, 1, 3),
(54, 'gem', '3280 Token', 3280, 382000, 0, 1, 4),
(54, 'gem', '6480 Token', 6480, 510000, 0, 1, 5),
(54, 'gem', '14000 Token', 14000, 765000, 0, 1, 6),
(54, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(54, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(54, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(54, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(54, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(54, 'allpack', 'Adventurer Pack', 0, 229000, 0, 1, 1),
(54, 'allpack', 'Master Pack', 0, 459000, 0, 1, 2),
(54, 'allpack', 'Ultimate Pack', 0, 892000, 0, 1, 3),
(55, 'gem', '60 Coin', 60, 12000, 0, 1, 0),
(55, 'gem', '330 Coin', 330, 25000, 0, 1, 1),
(55, 'gem', '720 Coin', 720, 51000, 0, 1, 2),
(55, 'gem', '1560 Coin', 1560, 127000, 0, 1, 3),
(55, 'gem', '3280 Coin', 3280, 255000, 0, 1, 4),
(55, 'gem', '6480 Coin', 6480, 382000, 0, 1, 5),
(55, 'gem', '14000 Coin', 14000, 510000, 0, 1, 6),
(55, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(55, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(55, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(55, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(55, 'allpack', 'Starter Pack', 0, 19000, 0, 1, 0),
(55, 'allpack', 'Adventurer Pack', 0, 641000, 0, 1, 1),
(55, 'allpack', 'Master Pack', 0, 1281000, 0, 1, 2),
(55, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(56, 'gem', '60 Gem', 60, 25000, 0, 1, 0),
(56, 'gem', '330 Gem', 330, 51000, 0, 1, 1),
(56, 'gem', '720 Gem', 720, 127000, 0, 1, 2),
(56, 'gem', '1560 Gem', 1560, 255000, 0, 1, 3),
(56, 'gem', '3280 Gem', 3280, 382000, 0, 1, 4),
(56, 'gem', '6480 Gem', 6480, 510000, 0, 1, 5),
(56, 'gem', '14000 Gem', 14000, 765000, 0, 1, 6),
(56, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(56, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(56, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(56, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(56, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(56, 'allpack', 'Adventurer Pack', 0, 325000, 0, 1, 1),
(56, 'allpack', 'Master Pack', 0, 650000, 0, 1, 2),
(56, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(57, 'gem', '60 Ruby', 60, 25000, 0, 1, 0),
(57, 'gem', '330 Ruby', 330, 51000, 0, 1, 1),
(57, 'gem', '720 Ruby', 720, 127000, 0, 1, 2),
(57, 'gem', '1560 Ruby', 1560, 255000, 0, 1, 3),
(57, 'gem', '3280 Ruby', 3280, 382000, 0, 1, 4),
(57, 'gem', '6480 Ruby', 6480, 510000, 0, 1, 5),
(57, 'gem', '14000 Ruby', 14000, 765000, 0, 1, 6),
(57, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(57, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(57, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(57, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(57, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(57, 'allpack', 'Adventurer Pack', 0, 1249000, 0, 1, 1),
(57, 'allpack', 'Master Pack', 0, 2499000, 0, 1, 2),
(57, 'allpack', 'Ultimate Pack', 0, 4972000, 0, 1, 3),
(58, 'gem', '60 Soul', 60, 25000, 0, 1, 0),
(58, 'gem', '330 Soul', 330, 51000, 0, 1, 1),
(58, 'gem', '720 Soul', 720, 127000, 0, 1, 2),
(58, 'gem', '1560 Soul', 1560, 255000, 0, 1, 3),
(58, 'gem', '3280 Soul', 3280, 382000, 0, 1, 4),
(58, 'gem', '6480 Soul', 6480, 510000, 0, 1, 5),
(58, 'gem', '14000 Soul', 14000, 765000, 0, 1, 6),
(58, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(58, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(58, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(58, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(58, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(58, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(58, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(58, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(59, 'gem', '60 Mana', 60, 25000, 0, 1, 0),
(59, 'gem', '330 Mana', 330, 51000, 0, 1, 1),
(59, 'gem', '720 Mana', 720, 127000, 0, 1, 2),
(59, 'gem', '1560 Mana', 1560, 255000, 0, 1, 3),
(59, 'gem', '3280 Mana', 3280, 382000, 0, 1, 4),
(59, 'gem', '6480 Mana', 6480, 510000, 0, 1, 5),
(59, 'gem', '14000 Mana', 14000, 765000, 0, 1, 6),
(59, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(59, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(59, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(59, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(59, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(59, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(59, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(59, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(60, 'gem', '60 Rune', 60, 46000, 0, 1, 0),
(60, 'gem', '330 Rune', 330, 53000, 0, 1, 1),
(60, 'gem', '1090 Rune', 1090, 60000, 0, 1, 2),
(60, 'gem', '2240 Rune', 2240, 68000, 0, 1, 3),
(60, 'gem', '3880 Rune', 3880, 77000, 0, 1, 4),
(60, 'gem', '8080 Rune', 8080, 87000, 0, 1, 5),
(60, 'pack', 'Gói Tháng', 0, 42000, 0, 1, 0),
(60, 'pack', 'Battle Pass', 0, 49000, 0, 1, 1),
(60, 'pack', 'Premium Pass', 0, 57000, 0, 1, 2),
(60, 'allpack', 'Starter Combo', 0, 64000, 0, 1, 0),
(60, 'allpack', 'Premium Combo', 0, 75000, 0, 1, 1),
(60, 'allpack', 'Whale Pack', 0, 96000, 0, 1, 2),
(60, 'allpack', 'Ultimate Pack', 0, 124000, 0, 1, 3),
(61, 'gem', '60 Kim cương', 60, 46000, 0, 1, 0),
(61, 'gem', '330 Kim cương', 330, 54000, 0, 1, 1),
(61, 'gem', '1090 Kim cương', 1090, 61000, 0, 1, 2),
(61, 'gem', '2240 Kim cương', 2240, 69000, 0, 1, 3),
(61, 'gem', '3880 Kim cương', 3880, 78000, 0, 1, 4),
(61, 'gem', '8080 Kim cương', 8080, 88000, 0, 1, 5),
(61, 'pack', 'Gói Tháng', 0, 42000, 0, 1, 0),
(61, 'pack', 'Battle Pass', 0, 50000, 0, 1, 1),
(61, 'pack', 'Premium Pass', 0, 57000, 0, 1, 2),
(61, 'allpack', 'Starter Combo', 0, 65000, 0, 1, 0),
(61, 'allpack', 'Premium Combo', 0, 76000, 0, 1, 1),
(61, 'allpack', 'Whale Pack', 0, 97000, 0, 1, 2),
(61, 'allpack', 'Ultimate Pack', 0, 126000, 0, 1, 3),
(62, 'gem', '60 Xu', 60, 25000, 0, 1, 0),
(62, 'gem', '330 Xu', 330, 51000, 0, 1, 1),
(62, 'gem', '720 Xu', 720, 127000, 0, 1, 2),
(62, 'gem', '1560 Xu', 1560, 255000, 0, 1, 3),
(62, 'gem', '3280 Xu', 3280, 382000, 0, 1, 4),
(62, 'gem', '6480 Xu', 6480, 510000, 0, 1, 5),
(62, 'gem', '14000 Xu', 14000, 765000, 0, 1, 6),
(62, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(62, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(62, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(62, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(62, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(62, 'allpack', 'Adventurer Pack', 0, 261000, 0, 1, 1),
(62, 'allpack', 'Master Pack', 0, 522000, 0, 1, 2),
(62, 'allpack', 'Ultimate Pack', 0, 1020000, 0, 1, 3),
(63, 'gem', '60 Vàng', 60, 25000, 0, 1, 0),
(63, 'gem', '330 Vàng', 330, 51000, 0, 1, 1),
(63, 'gem', '720 Vàng', 720, 127000, 0, 1, 2),
(63, 'gem', '1560 Vàng', 1560, 255000, 0, 1, 3),
(63, 'gem', '3280 Vàng', 3280, 382000, 0, 1, 4),
(63, 'gem', '6480 Vàng', 6480, 510000, 0, 1, 5),
(63, 'gem', '14000 Vàng', 14000, 765000, 0, 1, 6),
(63, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(63, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(63, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(63, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(63, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(63, 'allpack', 'Adventurer Pack', 0, 325000, 0, 1, 1),
(63, 'allpack', 'Master Pack', 0, 650000, 0, 1, 2),
(63, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(64, 'gem', '60 Ngọc', 60, 25000, 0, 1, 0),
(64, 'gem', '330 Ngọc', 330, 51000, 0, 1, 1),
(64, 'gem', '720 Ngọc', 720, 127000, 0, 1, 2),
(64, 'gem', '1560 Ngọc', 1560, 255000, 0, 1, 3),
(64, 'gem', '3280 Ngọc', 3280, 382000, 0, 1, 4),
(64, 'gem', '6480 Ngọc', 6480, 510000, 0, 1, 5),
(64, 'gem', '14000 Ngọc', 14000, 765000, 0, 1, 6),
(64, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(64, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(64, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(64, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(64, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(64, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(64, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(64, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(65, 'gem', '60 Đá quý', 60, 127000, 0, 1, 0),
(65, 'gem', '330 Đá quý', 330, 255000, 0, 1, 1),
(65, 'gem', '720 Đá quý', 720, 382000, 0, 1, 2),
(65, 'gem', '1560 Đá quý', 1560, 510000, 0, 1, 3),
(65, 'gem', '3280 Đá quý', 3280, 765000, 0, 1, 4),
(65, 'gem', '6480 Đá quý', 6480, 1275000, 0, 1, 5),
(65, 'gem', '14000 Đá quý', 14000, 1912000, 0, 1, 6),
(65, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(65, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(65, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(65, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(65, 'allpack', 'Starter Pack', 0, 114000, 0, 1, 0),
(65, 'allpack', 'Adventurer Pack', 0, 2250000, 0, 1, 1),
(65, 'allpack', 'Master Pack', 0, 4500000, 0, 1, 2),
(65, 'allpack', 'Ultimate Pack', 0, 8925000, 0, 1, 3),
(66, 'gem', '60 Token', 60, 12000, 0, 1, 0),
(66, 'gem', '330 Token', 330, 25000, 0, 1, 1),
(66, 'gem', '720 Token', 720, 51000, 0, 1, 2),
(66, 'gem', '1560 Token', 1560, 127000, 0, 1, 3),
(66, 'gem', '3280 Token', 3280, 255000, 0, 1, 4),
(66, 'gem', '6480 Token', 6480, 382000, 0, 1, 5),
(66, 'gem', '14000 Token', 14000, 510000, 0, 1, 6),
(66, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(66, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(66, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(66, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(66, 'allpack', 'Starter Pack', 0, 19000, 0, 1, 0),
(66, 'allpack', 'Adventurer Pack', 0, 940000, 0, 1, 1),
(66, 'allpack', 'Master Pack', 0, 1880000, 0, 1, 2),
(66, 'allpack', 'Ultimate Pack', 0, 3748000, 0, 1, 3),
(67, 'gem', '60 Coin', 60, 25000, 0, 1, 0),
(67, 'gem', '330 Coin', 330, 51000, 0, 1, 1),
(67, 'gem', '720 Coin', 720, 127000, 0, 1, 2),
(67, 'gem', '1560 Coin', 1560, 255000, 0, 1, 3),
(67, 'gem', '3280 Coin', 3280, 382000, 0, 1, 4),
(67, 'gem', '6480 Coin', 6480, 510000, 0, 1, 5),
(67, 'gem', '14000 Coin', 14000, 765000, 0, 1, 6),
(67, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(67, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(67, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(67, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(67, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(67, 'allpack', 'Adventurer Pack', 0, 3194000, 0, 1, 1),
(67, 'allpack', 'Master Pack', 0, 6387000, 0, 1, 2),
(67, 'allpack', 'Ultimate Pack', 0, 12750000, 0, 1, 3),
(68, 'gem', '60 Gem', 60, 12000, 0, 1, 0),
(68, 'gem', '330 Gem', 330, 25000, 0, 1, 1),
(68, 'gem', '720 Gem', 720, 51000, 0, 1, 2),
(68, 'gem', '1560 Gem', 1560, 127000, 0, 1, 3),
(68, 'gem', '3280 Gem', 3280, 255000, 0, 1, 4),
(68, 'gem', '6480 Gem', 6480, 382000, 0, 1, 5),
(68, 'gem', '14000 Gem', 14000, 510000, 0, 1, 6),
(68, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(68, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(68, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(68, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(68, 'allpack', 'Starter Pack', 0, 13000, 0, 1, 0),
(68, 'allpack', 'Adventurer Pack', 0, 831000, 0, 1, 1),
(68, 'allpack', 'Master Pack', 0, 1662000, 0, 1, 2),
(68, 'allpack', 'Ultimate Pack', 0, 3315000, 0, 1, 3),
(69, 'gem', '60 Ruby', 60, 25000, 0, 1, 0),
(69, 'gem', '330 Ruby', 330, 35000, 0, 1, 1),
(69, 'gem', '720 Ruby', 720, 46000, 0, 1, 2),
(69, 'gem', '1560 Ruby', 1560, 56000, 0, 1, 3),
(69, 'gem', '3280 Ruby', 3280, 66000, 0, 1, 4),
(69, 'gem', '6480 Ruby', 6480, 76000, 0, 1, 5),
(69, 'pack', 'Gói Tháng', 0, 25000, 0, 1, 0),
(69, 'pack', 'Battle Pass', 0, 34000, 0, 1, 1),
(69, 'pack', 'Premium Pass', 0, 51000, 0, 1, 2),
(69, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(69, 'allpack', 'Adventurer Pack', 0, 25000, 0, 1, 1),
(69, 'allpack', 'Master Pack', 0, 51000, 0, 1, 2),
(69, 'allpack', 'Ultimate Pack', 0, 76000, 0, 1, 3),
(70, 'gem', '60 Soul', 60, 25000, 0, 1, 0),
(70, 'gem', '330 Soul', 330, 51000, 0, 1, 1),
(70, 'gem', '720 Soul', 720, 127000, 0, 1, 2),
(70, 'gem', '1560 Soul', 1560, 255000, 0, 1, 3),
(70, 'gem', '3280 Soul', 3280, 382000, 0, 1, 4),
(70, 'gem', '6480 Soul', 6480, 510000, 0, 1, 5),
(70, 'gem', '14000 Soul', 14000, 765000, 0, 1, 6),
(70, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(70, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(70, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(70, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(70, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(70, 'allpack', 'Adventurer Pack', 0, 580000, 0, 1, 1),
(70, 'allpack', 'Master Pack', 0, 1160000, 0, 1, 2),
(70, 'allpack', 'Ultimate Pack', 0, 2295000, 0, 1, 3),
(71, 'gem', '60 Mana', 60, 12000, 0, 1, 0),
(71, 'gem', '330 Mana', 330, 25000, 0, 1, 1),
(71, 'gem', '720 Mana', 720, 51000, 0, 1, 2),
(71, 'gem', '1560 Mana', 1560, 127000, 0, 1, 3),
(71, 'gem', '3280 Mana', 3280, 255000, 0, 1, 4);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(71, 'gem', '6480 Mana', 6480, 382000, 0, 1, 5),
(71, 'gem', '14000 Mana', 14000, 510000, 0, 1, 6),
(71, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(71, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(71, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(71, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(71, 'allpack', 'Starter Pack', 0, 19000, 0, 1, 0),
(71, 'allpack', 'Adventurer Pack', 0, 896000, 0, 1, 1),
(71, 'allpack', 'Master Pack', 0, 1791000, 0, 1, 2),
(71, 'allpack', 'Ultimate Pack', 0, 3570000, 0, 1, 3),
(72, 'gem', '60 Rune', 60, 25000, 0, 1, 0),
(72, 'gem', '330 Rune', 330, 51000, 0, 1, 1),
(72, 'gem', '720 Rune', 720, 127000, 0, 1, 2),
(72, 'gem', '1560 Rune', 1560, 255000, 0, 1, 3),
(72, 'gem', '3280 Rune', 3280, 382000, 0, 1, 4),
(72, 'gem', '6480 Rune', 6480, 510000, 0, 1, 5),
(72, 'gem', '14000 Rune', 14000, 765000, 0, 1, 6),
(72, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(72, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(72, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(72, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(72, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(72, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(72, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(72, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(73, 'gem', '60 Kim cương', 60, 25000, 0, 1, 0),
(73, 'gem', '330 Kim cương', 330, 51000, 0, 1, 1),
(73, 'gem', '720 Kim cương', 720, 127000, 0, 1, 2),
(73, 'gem', '1560 Kim cương', 1560, 255000, 0, 1, 3),
(73, 'gem', '3280 Kim cương', 3280, 382000, 0, 1, 4),
(73, 'gem', '6480 Kim cương', 6480, 510000, 0, 1, 5),
(73, 'gem', '14000 Kim cương', 14000, 765000, 0, 1, 6),
(73, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(73, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(73, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(73, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(73, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(73, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(73, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(73, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(74, 'gem', '60 Xu', 60, 25000, 0, 1, 0),
(74, 'gem', '330 Xu', 330, 51000, 0, 1, 1),
(74, 'gem', '720 Xu', 720, 127000, 0, 1, 2),
(74, 'gem', '1560 Xu', 1560, 255000, 0, 1, 3),
(74, 'gem', '3280 Xu', 3280, 382000, 0, 1, 4),
(74, 'gem', '6480 Xu', 6480, 510000, 0, 1, 5),
(74, 'gem', '14000 Xu', 14000, 765000, 0, 1, 6),
(74, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(74, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(74, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(74, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(74, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(74, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(74, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(74, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(75, 'gem', '60 Vàng', 60, 25000, 0, 1, 0),
(75, 'gem', '330 Vàng', 330, 51000, 0, 1, 1),
(75, 'gem', '720 Vàng', 720, 127000, 0, 1, 2),
(75, 'gem', '1560 Vàng', 1560, 255000, 0, 1, 3),
(75, 'gem', '3280 Vàng', 3280, 382000, 0, 1, 4),
(75, 'gem', '6480 Vàng', 6480, 510000, 0, 1, 5),
(75, 'gem', '14000 Vàng', 14000, 765000, 0, 1, 6),
(75, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(75, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(75, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(75, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(75, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(75, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(75, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(75, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(76, 'gem', '60 Ngọc', 60, 12000, 0, 1, 0),
(76, 'gem', '330 Ngọc', 330, 25000, 0, 1, 1),
(76, 'gem', '720 Ngọc', 720, 51000, 0, 1, 2),
(76, 'gem', '1560 Ngọc', 1560, 127000, 0, 1, 3),
(76, 'gem', '3280 Ngọc', 3280, 255000, 0, 1, 4),
(76, 'gem', '6480 Ngọc', 6480, 382000, 0, 1, 5),
(76, 'gem', '14000 Ngọc', 14000, 510000, 0, 1, 6),
(76, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(76, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(76, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(76, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(76, 'allpack', 'Starter Pack', 0, 19000, 0, 1, 0),
(76, 'allpack', 'Adventurer Pack', 0, 577000, 0, 1, 1),
(76, 'allpack', 'Master Pack', 0, 1154000, 0, 1, 2),
(76, 'allpack', 'Ultimate Pack', 0, 2295000, 0, 1, 3),
(77, 'gem', '60 Đá quý', 60, 25000, 0, 1, 0),
(77, 'gem', '330 Đá quý', 330, 51000, 0, 1, 1),
(77, 'gem', '720 Đá quý', 720, 127000, 0, 1, 2),
(77, 'gem', '1560 Đá quý', 1560, 255000, 0, 1, 3),
(77, 'gem', '3280 Đá quý', 3280, 382000, 0, 1, 4),
(77, 'gem', '6480 Đá quý', 6480, 510000, 0, 1, 5),
(77, 'gem', '14000 Đá quý', 14000, 765000, 0, 1, 6),
(77, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(77, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(77, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(77, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(77, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(77, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(77, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(77, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(78, 'gem', '60 Token', 60, 25000, 0, 1, 0),
(78, 'gem', '330 Token', 330, 51000, 0, 1, 1),
(78, 'gem', '720 Token', 720, 127000, 0, 1, 2),
(78, 'gem', '1560 Token', 1560, 255000, 0, 1, 3),
(78, 'gem', '3280 Token', 3280, 382000, 0, 1, 4),
(78, 'gem', '6480 Token', 6480, 510000, 0, 1, 5),
(78, 'gem', '14000 Token', 14000, 765000, 0, 1, 6),
(78, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(78, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(78, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(78, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(78, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(78, 'allpack', 'Adventurer Pack', 0, 452000, 0, 1, 1),
(78, 'allpack', 'Master Pack', 0, 905000, 0, 1, 2),
(78, 'allpack', 'Ultimate Pack', 0, 1785000, 0, 1, 3),
(79, 'gem', '60 Coin', 60, 25000, 0, 1, 0),
(79, 'gem', '330 Coin', 330, 51000, 0, 1, 1),
(79, 'gem', '720 Coin', 720, 127000, 0, 1, 2),
(79, 'gem', '1560 Coin', 1560, 255000, 0, 1, 3),
(79, 'gem', '3280 Coin', 3280, 382000, 0, 1, 4),
(79, 'gem', '6480 Coin', 6480, 510000, 0, 1, 5),
(79, 'gem', '14000 Coin', 14000, 765000, 0, 1, 6),
(79, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(79, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(79, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(79, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(79, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(79, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(79, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(79, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(80, 'gem', '60 Gem', 60, 25000, 0, 1, 0),
(80, 'gem', '330 Gem', 330, 51000, 0, 1, 1),
(80, 'gem', '720 Gem', 720, 127000, 0, 1, 2),
(80, 'gem', '1560 Gem', 1560, 255000, 0, 1, 3),
(80, 'gem', '3280 Gem', 3280, 382000, 0, 1, 4),
(80, 'gem', '6480 Gem', 6480, 510000, 0, 1, 5),
(80, 'gem', '14000 Gem', 14000, 765000, 0, 1, 6),
(80, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(80, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(80, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(80, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(80, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(80, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(80, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(80, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(81, 'gem', '60 Ruby', 60, 25000, 0, 1, 0),
(81, 'gem', '330 Ruby', 330, 51000, 0, 1, 1),
(81, 'gem', '720 Ruby', 720, 127000, 0, 1, 2),
(81, 'gem', '1560 Ruby', 1560, 255000, 0, 1, 3),
(81, 'gem', '3280 Ruby', 3280, 382000, 0, 1, 4),
(81, 'gem', '6480 Ruby', 6480, 510000, 0, 1, 5),
(81, 'gem', '14000 Ruby', 14000, 765000, 0, 1, 6),
(81, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(81, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(81, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(81, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(81, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(81, 'allpack', 'Adventurer Pack', 0, 1919000, 0, 1, 1),
(81, 'allpack', 'Master Pack', 0, 3837000, 0, 1, 2),
(81, 'allpack', 'Ultimate Pack', 0, 7650000, 0, 1, 3),
(82, 'gem', '60 Soul', 60, 25000, 0, 1, 0),
(82, 'gem', '330 Soul', 330, 51000, 0, 1, 1),
(82, 'gem', '720 Soul', 720, 127000, 0, 1, 2),
(82, 'gem', '1560 Soul', 1560, 255000, 0, 1, 3),
(82, 'gem', '3280 Soul', 3280, 382000, 0, 1, 4),
(82, 'gem', '6480 Soul', 6480, 510000, 0, 1, 5),
(82, 'gem', '14000 Soul', 14000, 765000, 0, 1, 6),
(82, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(82, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(82, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(82, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(82, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(82, 'allpack', 'Adventurer Pack', 0, 516000, 0, 1, 1),
(82, 'allpack', 'Master Pack', 0, 1032000, 0, 1, 2),
(82, 'allpack', 'Ultimate Pack', 0, 2040000, 0, 1, 3),
(83, 'gem', '60 Mana', 60, 127000, 0, 1, 0),
(83, 'gem', '330 Mana', 330, 255000, 0, 1, 1),
(83, 'gem', '720 Mana', 720, 382000, 0, 1, 2),
(83, 'gem', '1560 Mana', 1560, 510000, 0, 1, 3),
(83, 'gem', '3280 Mana', 3280, 765000, 0, 1, 4),
(83, 'gem', '6480 Mana', 6480, 1275000, 0, 1, 5),
(83, 'gem', '14000 Mana', 14000, 1912000, 0, 1, 6),
(83, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(83, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(83, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(83, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(83, 'allpack', 'Starter Pack', 0, 95000, 0, 1, 0),
(83, 'allpack', 'Adventurer Pack', 0, 653000, 0, 1, 1),
(83, 'allpack', 'Master Pack', 0, 1307000, 0, 1, 2),
(83, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(84, 'gem', '60 Rune', 60, 51000, 0, 1, 0),
(84, 'gem', '330 Rune', 330, 127000, 0, 1, 1),
(84, 'gem', '720 Rune', 720, 255000, 0, 1, 2),
(84, 'gem', '1560 Rune', 1560, 382000, 0, 1, 3),
(84, 'gem', '3280 Rune', 3280, 510000, 0, 1, 4),
(84, 'gem', '6480 Rune', 6480, 765000, 0, 1, 5),
(84, 'gem', '14000 Rune', 14000, 1275000, 0, 1, 6),
(84, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(84, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(84, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(84, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(84, 'allpack', 'Starter Pack', 0, 57000, 0, 1, 0),
(84, 'allpack', 'Adventurer Pack', 0, 647000, 0, 1, 1),
(84, 'allpack', 'Master Pack', 0, 1294000, 0, 1, 2),
(84, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(85, 'gem', '60 Kim cương', 60, 25000, 0, 1, 0),
(85, 'gem', '330 Kim cương', 330, 51000, 0, 1, 1),
(85, 'gem', '720 Kim cương', 720, 127000, 0, 1, 2),
(85, 'gem', '1560 Kim cương', 1560, 255000, 0, 1, 3),
(85, 'gem', '3280 Kim cương', 3280, 382000, 0, 1, 4),
(85, 'gem', '6480 Kim cương', 6480, 510000, 0, 1, 5),
(85, 'gem', '14000 Kim cương', 14000, 765000, 0, 1, 6),
(85, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(85, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(85, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(85, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(85, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(85, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(85, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(85, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(86, 'gem', '60 Xu', 60, 61000, 0, 1, 0),
(86, 'gem', '330 Xu', 330, 71000, 0, 1, 1),
(86, 'gem', '1090 Xu', 1090, 81000, 0, 1, 2),
(86, 'gem', '2240 Xu', 2240, 91000, 0, 1, 3),
(86, 'gem', '3880 Xu', 3880, 103000, 0, 1, 4),
(86, 'gem', '8080 Xu', 8080, 116000, 0, 1, 5),
(86, 'pack', 'Gói Tháng', 0, 56000, 0, 1, 0),
(86, 'pack', 'Battle Pass', 0, 66000, 0, 1, 1),
(86, 'pack', 'Premium Pass', 0, 76000, 0, 1, 2),
(86, 'allpack', 'Starter Combo', 0, 86000, 0, 1, 0),
(86, 'allpack', 'Premium Combo', 0, 101000, 0, 1, 1),
(86, 'allpack', 'Whale Pack', 0, 128000, 0, 1, 2),
(86, 'allpack', 'Ultimate Pack', 0, 166000, 0, 1, 3),
(87, 'gem', '60 Vàng', 60, 62000, 0, 1, 0),
(87, 'gem', '330 Vàng', 330, 72000, 0, 1, 1),
(87, 'gem', '1090 Vàng', 1090, 82000, 0, 1, 2),
(87, 'gem', '2240 Vàng', 2240, 92000, 0, 1, 3),
(87, 'gem', '3880 Vàng', 3880, 104000, 0, 1, 4),
(87, 'gem', '8080 Vàng', 8080, 117000, 0, 1, 5),
(87, 'pack', 'Gói Tháng', 0, 56000, 0, 1, 0),
(87, 'pack', 'Battle Pass', 0, 67000, 0, 1, 1),
(87, 'pack', 'Premium Pass', 0, 77000, 0, 1, 2),
(87, 'allpack', 'Starter Combo', 0, 87000, 0, 1, 0),
(87, 'allpack', 'Premium Combo', 0, 102000, 0, 1, 1),
(87, 'allpack', 'Whale Pack', 0, 129000, 0, 1, 2),
(87, 'allpack', 'Ultimate Pack', 0, 167000, 0, 1, 3),
(88, 'gem', '60 Ngọc', 60, 62000, 0, 1, 0),
(88, 'gem', '330 Ngọc', 330, 72000, 0, 1, 1),
(88, 'gem', '1090 Ngọc', 1090, 82000, 0, 1, 2),
(88, 'gem', '2240 Ngọc', 2240, 93000, 0, 1, 3),
(88, 'gem', '3880 Ngọc', 3880, 105000, 0, 1, 4),
(88, 'gem', '8080 Ngọc', 8080, 118000, 0, 1, 5),
(88, 'pack', 'Gói Tháng', 0, 57000, 0, 1, 0),
(88, 'pack', 'Battle Pass', 0, 67000, 0, 1, 1),
(88, 'pack', 'Premium Pass', 0, 77000, 0, 1, 2),
(88, 'allpack', 'Starter Combo', 0, 87000, 0, 1, 0),
(88, 'allpack', 'Premium Combo', 0, 103000, 0, 1, 1),
(88, 'allpack', 'Whale Pack', 0, 131000, 0, 1, 2),
(88, 'allpack', 'Ultimate Pack', 0, 169000, 0, 1, 3),
(89, 'gem', '60 Đá quý', 60, 63000, 0, 1, 0),
(89, 'gem', '330 Đá quý', 330, 73000, 0, 1, 1),
(89, 'gem', '1090 Đá quý', 1090, 83000, 0, 1, 2),
(89, 'gem', '2240 Đá quý', 2240, 93000, 0, 1, 3),
(89, 'gem', '3880 Đá quý', 3880, 106000, 0, 1, 4),
(89, 'gem', '8080 Đá quý', 8080, 119000, 0, 1, 5),
(89, 'pack', 'Gói Tháng', 0, 58000, 0, 1, 0),
(89, 'pack', 'Battle Pass', 0, 68000, 0, 1, 1),
(89, 'pack', 'Premium Pass', 0, 78000, 0, 1, 2),
(89, 'allpack', 'Starter Combo', 0, 88000, 0, 1, 0),
(89, 'allpack', 'Premium Combo', 0, 104000, 0, 1, 1),
(89, 'allpack', 'Whale Pack', 0, 132000, 0, 1, 2),
(89, 'allpack', 'Ultimate Pack', 0, 170000, 0, 1, 3),
(90, 'gem', '60 Token', 60, 63000, 0, 1, 0),
(90, 'gem', '330 Token', 330, 74000, 0, 1, 1),
(90, 'gem', '1090 Token', 1090, 84000, 0, 1, 2),
(90, 'gem', '2240 Token', 2240, 94000, 0, 1, 3),
(90, 'gem', '3880 Token', 3880, 107000, 0, 1, 4),
(90, 'gem', '8080 Token', 8080, 120000, 0, 1, 5),
(90, 'pack', 'Gói Tháng', 0, 58000, 0, 1, 0),
(90, 'pack', 'Battle Pass', 0, 68000, 0, 1, 1),
(90, 'pack', 'Premium Pass', 0, 79000, 0, 1, 2),
(90, 'allpack', 'Starter Combo', 0, 89000, 0, 1, 0),
(90, 'allpack', 'Premium Combo', 0, 105000, 0, 1, 1),
(90, 'allpack', 'Whale Pack', 0, 133000, 0, 1, 2),
(90, 'allpack', 'Ultimate Pack', 0, 172000, 0, 1, 3),
(91, 'gem', '60 Coin', 60, 64000, 0, 1, 0),
(91, 'gem', '330 Coin', 330, 74000, 0, 1, 1),
(91, 'gem', '1090 Coin', 1090, 85000, 0, 1, 2),
(91, 'gem', '2240 Coin', 2240, 95000, 0, 1, 3),
(91, 'gem', '3880 Coin', 3880, 108000, 0, 1, 4),
(91, 'gem', '8080 Coin', 8080, 121000, 0, 1, 5),
(91, 'pack', 'Gói Tháng', 0, 59000, 0, 1, 0),
(91, 'pack', 'Battle Pass', 0, 69000, 0, 1, 1),
(91, 'pack', 'Premium Pass', 0, 80000, 0, 1, 2),
(91, 'allpack', 'Starter Combo', 0, 90000, 0, 1, 0),
(91, 'allpack', 'Premium Combo', 0, 106000, 0, 1, 1),
(91, 'allpack', 'Whale Pack', 0, 134000, 0, 1, 2),
(91, 'allpack', 'Ultimate Pack', 0, 174000, 0, 1, 3),
(92, 'gem', '60 Gem', 60, 64000, 0, 1, 0),
(92, 'gem', '330 Gem', 330, 75000, 0, 1, 1);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(92, 'gem', '1090 Gem', 1090, 86000, 0, 1, 2),
(92, 'gem', '2240 Gem', 2240, 96000, 0, 1, 3),
(92, 'gem', '3880 Gem', 3880, 109000, 0, 1, 4),
(92, 'gem', '8080 Gem', 8080, 122000, 0, 1, 5),
(92, 'pack', 'Gói Tháng', 0, 59000, 0, 1, 0),
(92, 'pack', 'Battle Pass', 0, 70000, 0, 1, 1),
(92, 'pack', 'Premium Pass', 0, 80000, 0, 1, 2),
(92, 'allpack', 'Starter Combo', 0, 91000, 0, 1, 0),
(92, 'allpack', 'Premium Combo', 0, 107000, 0, 1, 1),
(92, 'allpack', 'Whale Pack', 0, 136000, 0, 1, 2),
(92, 'allpack', 'Ultimate Pack', 0, 175000, 0, 1, 3),
(93, 'gem', '60 Ruby', 60, 65000, 0, 1, 0),
(93, 'gem', '330 Ruby', 330, 76000, 0, 1, 1),
(93, 'gem', '1090 Ruby', 1090, 86000, 0, 1, 2),
(93, 'gem', '2240 Ruby', 2240, 97000, 0, 1, 3),
(93, 'gem', '3880 Ruby', 3880, 110000, 0, 1, 4),
(93, 'gem', '8080 Ruby', 8080, 124000, 0, 1, 5),
(93, 'pack', 'Gói Tháng', 0, 60000, 0, 1, 0),
(93, 'pack', 'Battle Pass', 0, 70000, 0, 1, 1),
(93, 'pack', 'Premium Pass', 0, 81000, 0, 1, 2),
(93, 'allpack', 'Starter Combo', 0, 92000, 0, 1, 0),
(93, 'allpack', 'Premium Combo', 0, 108000, 0, 1, 1),
(93, 'allpack', 'Whale Pack', 0, 137000, 0, 1, 2),
(93, 'allpack', 'Ultimate Pack', 0, 177000, 0, 1, 3),
(94, 'gem', '60 Soul', 60, 66000, 0, 1, 0),
(94, 'gem', '330 Soul', 330, 76000, 0, 1, 1),
(94, 'gem', '1090 Soul', 1090, 87000, 0, 1, 2),
(94, 'gem', '2240 Soul', 2240, 98000, 0, 1, 3),
(94, 'gem', '3880 Soul', 3880, 111000, 0, 1, 4),
(94, 'gem', '8080 Soul', 8080, 125000, 0, 1, 5),
(94, 'pack', 'Gói Tháng', 0, 60000, 0, 1, 0),
(94, 'pack', 'Battle Pass', 0, 71000, 0, 1, 1),
(94, 'pack', 'Premium Pass', 0, 82000, 0, 1, 2),
(94, 'allpack', 'Starter Combo', 0, 92000, 0, 1, 0),
(94, 'allpack', 'Premium Combo', 0, 109000, 0, 1, 1),
(94, 'allpack', 'Whale Pack', 0, 138000, 0, 1, 2),
(94, 'allpack', 'Ultimate Pack', 0, 178000, 0, 1, 3),
(95, 'gem', '60 Mana', 60, 66000, 0, 1, 0),
(95, 'gem', '330 Mana', 330, 77000, 0, 1, 1),
(95, 'gem', '1090 Mana', 1090, 88000, 0, 1, 2),
(95, 'gem', '2240 Mana', 2240, 99000, 0, 1, 3),
(95, 'gem', '3880 Mana', 3880, 112000, 0, 1, 4),
(95, 'gem', '8080 Mana', 8080, 126000, 0, 1, 5),
(95, 'pack', 'Gói Tháng', 0, 61000, 0, 1, 0),
(95, 'pack', 'Battle Pass', 0, 72000, 0, 1, 1),
(95, 'pack', 'Premium Pass', 0, 82000, 0, 1, 2),
(95, 'allpack', 'Starter Combo', 0, 93000, 0, 1, 0),
(95, 'allpack', 'Premium Combo', 0, 110000, 0, 1, 1),
(95, 'allpack', 'Whale Pack', 0, 139000, 0, 1, 2),
(95, 'allpack', 'Ultimate Pack', 0, 180000, 0, 1, 3),
(96, 'gem', '60 Rune', 60, 102000, 0, 1, 0),
(96, 'gem', '330 Rune', 330, 132000, 0, 1, 1),
(96, 'gem', '720 Rune', 720, 163000, 0, 1, 2),
(96, 'gem', '1560 Rune', 1560, 194000, 0, 1, 3),
(96, 'gem', '3280 Rune', 3280, 224000, 0, 1, 4),
(96, 'gem', '6480 Rune', 6480, 255000, 0, 1, 5),
(96, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(96, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(96, 'allpack', 'Starter Pack', 0, 153000, 0, 1, 0),
(96, 'allpack', 'Adventurer Pack', 0, 89000, 0, 1, 1),
(96, 'allpack', 'Master Pack', 0, 178000, 0, 1, 2),
(96, 'allpack', 'Ultimate Pack', 0, 255000, 0, 1, 3),
(97, 'gem', '60 Kim cương', 60, 102000, 0, 1, 0),
(97, 'gem', '330 Kim cương', 330, 132000, 0, 1, 1),
(97, 'gem', '720 Kim cương', 720, 163000, 0, 1, 2),
(97, 'gem', '1560 Kim cương', 1560, 194000, 0, 1, 3),
(97, 'gem', '3280 Kim cương', 3280, 224000, 0, 1, 4),
(97, 'gem', '6480 Kim cương', 6480, 255000, 0, 1, 5),
(97, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(97, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(97, 'allpack', 'Starter Pack', 0, 153000, 0, 1, 0),
(97, 'allpack', 'Adventurer Pack', 0, 89000, 0, 1, 1),
(97, 'allpack', 'Master Pack', 0, 178000, 0, 1, 2),
(97, 'allpack', 'Ultimate Pack', 0, 255000, 0, 1, 3),
(98, 'gem', '60 Xu', 60, 25000, 0, 1, 0),
(98, 'gem', '330 Xu', 330, 51000, 0, 1, 1),
(98, 'gem', '720 Xu', 720, 127000, 0, 1, 2),
(98, 'gem', '1560 Xu', 1560, 255000, 0, 1, 3),
(98, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(98, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(98, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(98, 'allpack', 'Adventurer Pack', 0, 76000, 0, 1, 1),
(98, 'allpack', 'Master Pack', 0, 153000, 0, 1, 2),
(98, 'allpack', 'Ultimate Pack', 0, 280000, 0, 1, 3),
(99, 'gem', '60 Vàng', 60, 69000, 0, 1, 0),
(99, 'gem', '330 Vàng', 330, 80000, 0, 1, 1),
(99, 'gem', '1090 Vàng', 1090, 91000, 0, 1, 2),
(99, 'gem', '2240 Vàng', 2240, 102000, 0, 1, 3),
(99, 'gem', '3880 Vàng', 3880, 116000, 0, 1, 4),
(99, 'gem', '8080 Vàng', 8080, 130000, 0, 1, 5),
(99, 'pack', 'Gói Tháng', 0, 63000, 0, 1, 0),
(99, 'pack', 'Battle Pass', 0, 74000, 0, 1, 1),
(99, 'pack', 'Premium Pass', 0, 85000, 0, 1, 2),
(99, 'allpack', 'Starter Combo', 0, 97000, 0, 1, 0),
(99, 'allpack', 'Premium Combo', 0, 113000, 0, 1, 1),
(99, 'allpack', 'Whale Pack', 0, 144000, 0, 1, 2),
(99, 'allpack', 'Ultimate Pack', 0, 186000, 0, 1, 3),
(100, 'gem', '60 Ngọc', 60, 25000, 0, 1, 0),
(100, 'gem', '330 Ngọc', 330, 51000, 0, 1, 1),
(100, 'gem', '720 Ngọc', 720, 127000, 0, 1, 2);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(100, 'gem', '1560 Ngọc', 1560, 255000, 0, 1, 3),
(100, 'gem', '3280 Ngọc', 3280, 382000, 0, 1, 4),
(100, 'gem', '6480 Ngọc', 6480, 510000, 0, 1, 5),
(100, 'gem', '14000 Ngọc', 14000, 765000, 0, 1, 6),
(100, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(100, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(100, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(100, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(100, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(100, 'allpack', 'Adventurer Pack', 0, 325000, 0, 1, 1),
(100, 'allpack', 'Master Pack', 0, 650000, 0, 1, 2),
(100, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(101, 'gem', '60 Đá quý', 60, 51000, 0, 1, 0),
(101, 'gem', '330 Đá quý', 330, 127000, 0, 1, 1),
(101, 'gem', '720 Đá quý', 720, 255000, 0, 1, 2),
(101, 'gem', '1560 Đá quý', 1560, 382000, 0, 1, 3),
(101, 'gem', '3280 Đá quý', 3280, 510000, 0, 1, 4),
(101, 'gem', '6480 Đá quý', 6480, 765000, 0, 1, 5),
(101, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(101, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(101, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(101, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(101, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(101, 'allpack', 'Adventurer Pack', 0, 204000, 0, 1, 1),
(101, 'allpack', 'Master Pack', 0, 408000, 0, 1, 2),
(101, 'allpack', 'Ultimate Pack', 0, 765000, 0, 1, 3),
(102, 'gem', '60 Token', 60, 12000, 0, 1, 0),
(102, 'gem', '330 Token', 330, 25000, 0, 1, 1),
(102, 'gem', '720 Token', 720, 51000, 0, 1, 2),
(102, 'gem', '1560 Token', 1560, 127000, 0, 1, 3),
(102, 'gem', '3280 Token', 3280, 255000, 0, 1, 4),
(102, 'gem', '6480 Token', 6480, 382000, 0, 1, 5),
(102, 'gem', '14000 Token', 14000, 510000, 0, 1, 6),
(102, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(102, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(102, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(102, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(102, 'allpack', 'Starter Pack', 0, 19000, 0, 1, 0),
(102, 'allpack', 'Adventurer Pack', 0, 322000, 0, 1, 1),
(102, 'allpack', 'Master Pack', 0, 644000, 0, 1, 2),
(102, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(103, 'gem', '60 Coin', 60, 51000, 0, 1, 0),
(103, 'gem', '330 Coin', 330, 127000, 0, 1, 1),
(103, 'gem', '720 Coin', 720, 255000, 0, 1, 2),
(103, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(103, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(103, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(103, 'allpack', 'Adventurer Pack', 0, 95000, 0, 1, 1),
(103, 'allpack', 'Master Pack', 0, 191000, 0, 1, 2),
(103, 'allpack', 'Ultimate Pack', 0, 331000, 0, 1, 3),
(104, 'gem', '60 Gem', 60, 51000, 0, 1, 0),
(104, 'gem', '330 Gem', 330, 127000, 0, 1, 1),
(104, 'gem', '720 Gem', 720, 255000, 0, 1, 2),
(104, 'gem', '1560 Gem', 1560, 382000, 0, 1, 3),
(104, 'gem', '3280 Gem', 3280, 510000, 0, 1, 4),
(104, 'gem', '6480 Gem', 6480, 765000, 0, 1, 5),
(104, 'gem', '14000 Gem', 14000, 1275000, 0, 1, 6),
(104, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(104, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(104, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(104, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(104, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(104, 'allpack', 'Adventurer Pack', 0, 650000, 0, 1, 1),
(104, 'allpack', 'Master Pack', 0, 1300000, 0, 1, 2),
(104, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(105, 'gem', '60 Ruby', 60, 51000, 0, 1, 0),
(105, 'gem', '330 Ruby', 330, 127000, 0, 1, 1),
(105, 'gem', '720 Ruby', 720, 255000, 0, 1, 2),
(105, 'gem', '1560 Ruby', 1560, 382000, 0, 1, 3),
(105, 'gem', '3280 Ruby', 3280, 510000, 0, 1, 4),
(105, 'gem', '6480 Ruby', 6480, 765000, 0, 1, 5),
(105, 'gem', '14000 Ruby', 14000, 1275000, 0, 1, 6),
(105, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(105, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(105, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(105, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(105, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(105, 'allpack', 'Adventurer Pack', 0, 650000, 0, 1, 1),
(105, 'allpack', 'Master Pack', 0, 1300000, 0, 1, 2),
(105, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(106, 'gem', '60 Soul', 60, 73000, 0, 1, 0),
(106, 'gem', '330 Soul', 330, 85000, 0, 1, 1),
(106, 'gem', '1090 Soul', 1090, 97000, 0, 1, 2),
(106, 'gem', '2240 Soul', 2240, 108000, 0, 1, 3),
(106, 'gem', '3880 Soul', 3880, 123000, 0, 1, 4),
(106, 'gem', '8080 Soul', 8080, 138000, 0, 1, 5),
(106, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(106, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(106, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(106, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(106, 'allpack', 'Starter Combo', 0, 102000, 0, 1, 0),
(106, 'allpack', 'Premium Combo', 0, 120000, 0, 1, 1),
(106, 'allpack', 'Whale Pack', 0, 153000, 0, 1, 2),
(106, 'allpack', 'Ultimate Pack', 0, 198000, 0, 1, 3),
(107, 'gem', '60 Mana', 60, 25000, 0, 1, 0),
(107, 'gem', '330 Mana', 330, 51000, 0, 1, 1),
(107, 'gem', '720 Mana', 720, 127000, 0, 1, 2),
(107, 'gem', '1560 Mana', 1560, 255000, 0, 1, 3),
(107, 'gem', '3280 Mana', 3280, 382000, 0, 1, 4),
(107, 'gem', '6480 Mana', 6480, 510000, 0, 1, 5);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(107, 'gem', '14000 Mana', 14000, 765000, 0, 1, 6),
(107, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(107, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(107, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(107, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(107, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(107, 'allpack', 'Adventurer Pack', 0, 325000, 0, 1, 1),
(107, 'allpack', 'Master Pack', 0, 650000, 0, 1, 2),
(107, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(108, 'gem', '60 Rune', 60, 25000, 0, 1, 0),
(108, 'gem', '330 Rune', 330, 51000, 0, 1, 1),
(108, 'gem', '720 Rune', 720, 127000, 0, 1, 2),
(108, 'gem', '1560 Rune', 1560, 255000, 0, 1, 3),
(108, 'gem', '3280 Rune', 3280, 382000, 0, 1, 4),
(108, 'gem', '6480 Rune', 6480, 510000, 0, 1, 5),
(108, 'gem', '14000 Rune', 14000, 765000, 0, 1, 6),
(108, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(108, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(108, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(108, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(108, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(108, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1),
(108, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(108, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3),
(109, 'gem', '60 Kim cương', 60, 127000, 0, 1, 0),
(109, 'gem', '330 Kim cương', 330, 163000, 0, 1, 1),
(109, 'gem', '720 Kim cương', 720, 199000, 0, 1, 2),
(109, 'gem', '1560 Kim cương', 1560, 234000, 0, 1, 3),
(109, 'gem', '3280 Kim cương', 3280, 270000, 0, 1, 4),
(109, 'gem', '6480 Kim cương', 6480, 306000, 0, 1, 5),
(109, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(109, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(109, 'allpack', 'Starter Pack', 0, 191000, 0, 1, 0),
(109, 'allpack', 'Adventurer Pack', 0, 108000, 0, 1, 1),
(109, 'allpack', 'Master Pack', 0, 216000, 0, 1, 2),
(109, 'allpack', 'Ultimate Pack', 0, 306000, 0, 1, 3),
(110, 'gem', '60 Xu', 60, 127000, 0, 1, 0),
(110, 'gem', '330 Xu', 330, 137000, 0, 1, 1),
(110, 'gem', '720 Xu', 720, 148000, 0, 1, 2),
(110, 'gem', '1560 Xu', 1560, 158000, 0, 1, 3),
(110, 'gem', '3280 Xu', 3280, 168000, 0, 1, 4),
(110, 'gem', '6480 Xu', 6480, 178000, 0, 1, 5),
(110, 'pack', 'Gói Tháng', 0, 127000, 0, 1, 0),
(110, 'pack', 'Battle Pass', 0, 102000, 0, 1, 1),
(110, 'pack', 'Premium Pass', 0, 153000, 0, 1, 2),
(110, 'allpack', 'Starter Pack', 0, 191000, 0, 1, 0),
(110, 'allpack', 'Adventurer Pack', 0, 76000, 0, 1, 1),
(110, 'allpack', 'Master Pack', 0, 153000, 0, 1, 2),
(110, 'allpack', 'Ultimate Pack', 0, 178000, 0, 1, 3),
(111, 'gem', '60 Vàng', 60, 51000, 0, 1, 0),
(111, 'gem', '330 Vàng', 330, 127000, 0, 1, 1),
(111, 'gem', '720 Vàng', 720, 255000, 0, 1, 2),
(111, 'gem', '1560 Vàng', 1560, 382000, 0, 1, 3),
(111, 'gem', '3280 Vàng', 3280, 510000, 0, 1, 4),
(111, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(111, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(111, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(111, 'allpack', 'Starter Pack', 0, 76000, 0, 1, 0),
(111, 'allpack', 'Adventurer Pack', 0, 140000, 0, 1, 1),
(111, 'allpack', 'Master Pack', 0, 280000, 0, 1, 2),
(111, 'allpack', 'Ultimate Pack', 0, 510000, 0, 1, 3),
(112, 'gem', '60 Ngọc', 60, 76000, 0, 1, 0),
(112, 'gem', '330 Ngọc', 330, 89000, 0, 1, 1),
(112, 'gem', '1090 Ngọc', 1090, 101000, 0, 1, 2),
(112, 'gem', '2240 Ngọc', 2240, 114000, 0, 1, 3),
(112, 'gem', '3880 Ngọc', 3880, 129000, 0, 1, 4),
(112, 'gem', '8080 Ngọc', 8080, 145000, 0, 1, 5),
(112, 'pack', 'Gói Tháng', 0, 70000, 0, 1, 0),
(112, 'pack', 'Battle Pass', 0, 83000, 0, 1, 1),
(112, 'pack', 'Premium Pass', 0, 95000, 0, 1, 2),
(112, 'allpack', 'Starter Combo', 0, 107000, 0, 1, 0),
(112, 'allpack', 'Premium Combo', 0, 126000, 0, 1, 1),
(112, 'allpack', 'Whale Pack', 0, 160000, 0, 1, 2),
(112, 'allpack', 'Ultimate Pack', 0, 207000, 0, 1, 3),
(113, 'gem', '60 Đá quý', 60, 25000, 0, 1, 0),
(113, 'gem', '330 Đá quý', 330, 51000, 0, 1, 1),
(113, 'gem', '720 Đá quý', 720, 127000, 0, 1, 2),
(113, 'gem', '1560 Đá quý', 1560, 255000, 0, 1, 3),
(113, 'gem', '3280 Đá quý', 3280, 382000, 0, 1, 4),
(113, 'gem', '6480 Đá quý', 6480, 510000, 0, 1, 5),
(113, 'gem', '14000 Đá quý', 14000, 765000, 0, 1, 6),
(113, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(113, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(113, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(113, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(113, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(113, 'allpack', 'Adventurer Pack', 0, 325000, 0, 1, 1),
(113, 'allpack', 'Master Pack', 0, 650000, 0, 1, 2),
(113, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(114, 'gem', '60 Token', 60, 25000, 0, 1, 0),
(114, 'gem', '330 Token', 330, 51000, 0, 1, 1),
(114, 'gem', '720 Token', 720, 127000, 0, 1, 2),
(114, 'gem', '1560 Token', 1560, 255000, 0, 1, 3),
(114, 'gem', '3280 Token', 3280, 382000, 0, 1, 4),
(114, 'gem', '6480 Token', 6480, 510000, 0, 1, 5),
(114, 'gem', '14000 Token', 14000, 765000, 0, 1, 6),
(114, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(114, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(114, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(114, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(114, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(114, 'allpack', 'Adventurer Pack', 0, 325000, 0, 1, 1),
(114, 'allpack', 'Master Pack', 0, 650000, 0, 1, 2),
(114, 'allpack', 'Ultimate Pack', 0, 1275000, 0, 1, 3),
(115, 'gem', '60 Coin', 60, 78000, 0, 1, 0),
(115, 'gem', '330 Coin', 330, 91000, 0, 1, 1),
(115, 'gem', '1090 Coin', 1090, 104000, 0, 1, 2),
(115, 'gem', '2240 Coin', 2240, 116000, 0, 1, 3),
(115, 'gem', '3880 Coin', 3880, 132000, 0, 1, 4),
(115, 'gem', '8080 Coin', 8080, 148000, 0, 1, 5),
(115, 'pack', 'Gói Tháng', 0, 72000, 0, 1, 0),
(115, 'pack', 'Battle Pass', 0, 84000, 0, 1, 1),
(115, 'pack', 'Premium Pass', 0, 97000, 0, 1, 2),
(115, 'allpack', 'Starter Combo', 0, 110000, 0, 1, 0),
(115, 'allpack', 'Premium Combo', 0, 129000, 0, 1, 1),
(115, 'allpack', 'Whale Pack', 0, 164000, 0, 1, 2),
(115, 'allpack', 'Ultimate Pack', 0, 212000, 0, 1, 3),
(116, 'gem', '60 Gem', 60, 79000, 0, 1, 0),
(116, 'gem', '330 Gem', 330, 92000, 0, 1, 1),
(116, 'gem', '1090 Gem', 1090, 104000, 0, 1, 2),
(116, 'gem', '2240 Gem', 2240, 117000, 0, 1, 3),
(116, 'gem', '3880 Gem', 3880, 133000, 0, 1, 4),
(116, 'gem', '8080 Gem', 8080, 149000, 0, 1, 5),
(116, 'pack', 'Gói Tháng', 0, 72000, 0, 1, 0),
(116, 'pack', 'Battle Pass', 0, 85000, 0, 1, 1),
(116, 'pack', 'Premium Pass', 0, 98000, 0, 1, 2),
(116, 'allpack', 'Starter Combo', 0, 111000, 0, 1, 0),
(116, 'allpack', 'Premium Combo', 0, 130000, 0, 1, 1),
(116, 'allpack', 'Whale Pack', 0, 165000, 0, 1, 2),
(116, 'allpack', 'Ultimate Pack', 0, 214000, 0, 1, 3),
(117, 'gem', '60 Ruby', 60, 25000, 0, 1, 0),
(117, 'gem', '330 Ruby', 330, 51000, 0, 1, 1),
(117, 'gem', '720 Ruby', 720, 127000, 0, 1, 2),
(117, 'gem', '1560 Ruby', 1560, 255000, 0, 1, 3),
(117, 'gem', '3280 Ruby', 3280, 382000, 0, 1, 4),
(117, 'gem', '6480 Ruby', 6480, 510000, 0, 1, 5),
(117, 'gem', '14000 Ruby', 14000, 765000, 0, 1, 6),
(117, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(117, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(117, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(117, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(117, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(117, 'allpack', 'Adventurer Pack', 0, 580000, 0, 1, 1),
(117, 'allpack', 'Master Pack', 0, 1160000, 0, 1, 2),
(117, 'allpack', 'Ultimate Pack', 0, 2295000, 0, 1, 3),
(118, 'gem', '60 Soul', 60, 80000, 0, 1, 0),
(118, 'gem', '330 Soul', 330, 93000, 0, 1, 1),
(118, 'gem', '1090 Soul', 1090, 106000, 0, 1, 2),
(118, 'gem', '2240 Soul', 2240, 119000, 0, 1, 3),
(118, 'gem', '3880 Soul', 3880, 135000, 0, 1, 4),
(118, 'gem', '8080 Soul', 8080, 152000, 0, 1, 5),
(118, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(118, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(118, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(118, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(118, 'allpack', 'Starter Combo', 0, 112000, 0, 1, 0),
(118, 'allpack', 'Premium Combo', 0, 132000, 0, 1, 1),
(118, 'allpack', 'Whale Pack', 0, 168000, 0, 1, 2),
(118, 'allpack', 'Ultimate Pack', 0, 217000, 0, 1, 3),
(119, 'gem', '60 Mana', 60, 80000, 0, 1, 0),
(119, 'gem', '330 Mana', 330, 94000, 0, 1, 1),
(119, 'gem', '1090 Mana', 1090, 107000, 0, 1, 2),
(119, 'gem', '2240 Mana', 2240, 120000, 0, 1, 3),
(119, 'gem', '3880 Mana', 3880, 136000, 0, 1, 4),
(119, 'gem', '8080 Mana', 8080, 153000, 0, 1, 5),
(119, 'pack', 'Gói Tháng', 0, 74000, 0, 1, 0),
(119, 'pack', 'Battle Pass', 0, 87000, 0, 1, 1),
(119, 'pack', 'Premium Pass', 0, 100000, 0, 1, 2),
(119, 'allpack', 'Starter Combo', 0, 113000, 0, 1, 0),
(119, 'allpack', 'Premium Combo', 0, 133000, 0, 1, 1),
(119, 'allpack', 'Whale Pack', 0, 169000, 0, 1, 2),
(119, 'allpack', 'Ultimate Pack', 0, 218000, 0, 1, 3),
(120, 'gem', '60 Rune', 60, 25000, 0, 1, 0),
(120, 'gem', '330 Rune', 330, 51000, 0, 1, 1),
(120, 'gem', '720 Rune', 720, 127000, 0, 1, 2),
(120, 'gem', '1560 Rune', 1560, 255000, 0, 1, 3),
(120, 'gem', '3280 Rune', 3280, 382000, 0, 1, 4),
(120, 'gem', '6480 Rune', 6480, 510000, 0, 1, 5),
(120, 'gem', '14000 Rune', 14000, 765000, 0, 1, 6),
(120, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(120, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(120, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(120, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(120, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(120, 'allpack', 'Adventurer Pack', 0, 452000, 0, 1, 1),
(120, 'allpack', 'Master Pack', 0, 905000, 0, 1, 2),
(120, 'allpack', 'Ultimate Pack', 0, 1785000, 0, 1, 3),
(121, 'gem', '60 Kim cương', 60, 25000, 0, 1, 0),
(121, 'gem', '330 Kim cương', 330, 51000, 0, 1, 1),
(121, 'gem', '720 Kim cương', 720, 127000, 0, 1, 2),
(121, 'gem', '1560 Kim cương', 1560, 255000, 0, 1, 3),
(121, 'gem', '3280 Kim cương', 3280, 382000, 0, 1, 4),
(121, 'gem', '6480 Kim cương', 6480, 510000, 0, 1, 5),
(121, 'gem', '14000 Kim cương', 14000, 765000, 0, 1, 6),
(121, 'pack', 'Gói Tháng (30d)', 0, 127000, 0, 1, 0),
(121, 'pack', 'Battle Pass', 0, 255000, 0, 1, 1),
(121, 'pack', 'Premium Pass', 0, 510000, 0, 1, 2),
(121, 'pack', 'Growth Pack', 0, 765000, 0, 1, 3),
(121, 'allpack', 'Starter Pack', 0, 38000, 0, 1, 0),
(121, 'allpack', 'Adventurer Pack', 0, 644000, 0, 1, 1);

INSERT INTO `topup_tiers` (`game_id`, `type`, `label`, `amount`, `price`, `cost`, `status`, `sort_order`) VALUES
(121, 'allpack', 'Master Pack', 0, 1287000, 0, 1, 2),
(121, 'allpack', 'Ultimate Pack', 0, 2550000, 0, 1, 3);

-- Total: 121 games, 1702 tiers seeded

-- Admin menu entries for GameTopup
INSERT INTO `menu` (`parent_id`, `name`, `slug`, `icon`, `href`, `status`, `target`, `position`, `create_gettime`, `update_gettime`) VALUES
(0, '🎮 Game Topup', 'game-topup', 'fas fa-gamepad', '', 1, '_self', 20, NOW(), NOW());

SET @topup_menu_id = LAST_INSERT_ID();

INSERT INTO `menu` (`parent_id`, `name`, `slug`, `icon`, `href`, `status`, `target`, `position`, `create_gettime`, `update_gettime`) VALUES
(@topup_menu_id, 'Danh sách Games', 'game-manager', 'fas fa-list', 'admin/game-manager', 1, '_self', 1, NOW(), NOW()),
(@topup_menu_id, 'Đơn nạp game', 'topup-orders', 'fas fa-shopping-cart', 'admin/topup-orders', 1, '_self', 2, NOW(), NOW());

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
