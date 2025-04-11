-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 11, 2025 lúc 06:56 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ban`
--

CREATE TABLE `ban` (
  `ID_Ban` int(11) NOT NULL,
  `SoBang` int(11) DEFAULT NULL,
  `DungTich` int(11) DEFAULT NULL,
  `ID_KhuVuc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ban`
--

INSERT INTO `ban` (`ID_Ban`, `SoBang`, `DungTich`, `ID_KhuVuc`) VALUES
(1, 1, 4, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bangchitietdatban`
--

CREATE TABLE `bangchitietdatban` (
  `ID_ChiTietDatBan` int(11) NOT NULL,
  `ID_Ban` int(11) NOT NULL,
  `ID_ThongTinDatBan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bangchitietdatban`
--

INSERT INTO `bangchitietdatban` (`ID_ChiTietDatBan`, `ID_Ban`, `ID_ThongTinDatBan`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdatmon`
--

CREATE TABLE `chitietdatmon` (
  `ID_MonAn` int(11) NOT NULL,
  `ID_ThongTinDatBan` int(11) NOT NULL,
  `ID_ChiTietDatMon` int(11) DEFAULT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `GhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DonGia` float DEFAULT NULL,
  `ThanhTien` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhgia`
--

CREATE TABLE `danhgia` (
  `ID_DanhGia` int(11) NOT NULL,
  `ID_USER` int(11) NOT NULL,
  `XepHang` int(11) DEFAULT NULL,
  `BinhLuan` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TraLoi` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID_NhaHang` int(11) NOT NULL,
  `NgayTao` datetime DEFAULT NULL,
  `NgayCapNhat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danhgia`
--

INSERT INTO `danhgia` (`ID_DanhGia`, `ID_USER`, `XepHang`, `BinhLuan`, `TraLoi`, `ID_NhaHang`, `NgayTao`, `NgayCapNhat`) VALUES
(1, 1, 5, 'Nhà hàng phục vụ rất tốt, món ăn ngon!', 'cảm ơn quý khách', 1, '2025-03-12 17:30:34', '2025-04-10 18:59:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuvuc`
--

CREATE TABLE `khuvuc` (
  `ID_KhuVuc` int(11) NOT NULL,
  `ID_NhaHang` int(11) NOT NULL,
  `Ten` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Tang` char(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khuvuc`
--

INSERT INTO `khuvuc` (`ID_KhuVuc`, `ID_NhaHang`, `Ten`, `DiaChi`, `Tang`) VALUES
(1, 1, 'Khu vực Thường', 'Tầng 2, khu B', '2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaimonan`
--

CREATE TABLE `loaimonan` (
  `MaLoai` int(11) NOT NULL,
  `TenLoai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Hide` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loaimonan`
--

INSERT INTO `loaimonan` (`MaLoai`, `TenLoai`, `MoTa`, `Hide`) VALUES
(1, 'Pataco', 'Món ăn của mexico', 0),
(2, 'Pizza', 'Pizza sieu ngon', 0),
(3, 'Hamberger', 'Món Ăn Ngon nhiều thịt và phô mai', 0),
(4, 'Khoai Tây Chiên Lắc Phô Mai', 'Khoai Tây Lắc đều', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monan`
--

CREATE TABLE `monan` (
  `ID_MonAn` int(11) NOT NULL,
  `ID_NhaHang` int(11) NOT NULL,
  `MaLoai` int(11) NOT NULL,
  `TenMonAn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Gia` float DEFAULT NULL,
  `TrangThai` int(11) DEFAULT NULL,
  `Anh1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Anh2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Anh3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Anh4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Anh5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime DEFAULT NULL,
  `NgayCapNhap` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `monan`
--

INSERT INTO `monan` (`ID_MonAn`, `ID_NhaHang`, `MaLoai`, `TenMonAn`, `MoTa`, `Gia`, `TrangThai`, `Anh1`, `Anh2`, `Anh3`, `Anh4`, `Anh5`, `NgayTao`, `NgayCapNhap`) VALUES
(1, 1, 2, 'Pizza Pig Cream', 'Pizza Pig Cream ăn là bao no', 159000, 1, 'uploads/monan/monan_1744354462_1.png', NULL, NULL, NULL, NULL, '2025-04-11 06:54:22', '2025-04-11 06:54:22'),
(2, 1, 3, 'Hamberger size to', 'Hamberger cho dân Gymer', 89000, 1, 'uploads/monan/monan_1744354525_1.png', NULL, NULL, NULL, NULL, '2025-04-11 06:55:25', '2025-04-11 06:55:25'),
(3, 1, 4, 'Khoai Tây Truyền Thống', 'Khoai Tây chấm sốt bao mê', 39000, 1, 'uploads/monan/monan_1744354569_1.png', NULL, NULL, NULL, NULL, '2025-04-11 06:56:10', '2025-04-11 06:56:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhahang`
--

CREATE TABLE `nhahang` (
  `ID_NhaHang` int(11) NOT NULL,
  `TenNhaHang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Sdt` char(20) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `MieuTa` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `OpenTime` datetime DEFAULT NULL,
  `CloseTime` datetime DEFAULT NULL,
  `DungTich` int(11) DEFAULT NULL,
  `XepHangTrungBinh` int(11) DEFAULT NULL,
  `Anh1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Anh2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Anh3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime DEFAULT NULL,
  `NgayCapNhap` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhahang`
--

INSERT INTO `nhahang` (`ID_NhaHang`, `TenNhaHang`, `DiaChi`, `Sdt`, `Email`, `MieuTa`, `OpenTime`, `CloseTime`, `DungTich`, `XepHangTrungBinh`, `Anh1`, `Anh2`, `Anh3`, `NgayTao`, `NgayCapNhap`) VALUES
(1, 'Nhà Hàng Gà To', '123 đường  Lê Lợi,', '0123456789', 'nhahanggato@example.com', 'Nhà hàng chuyên các món ăn truyền thống Việt Nam', '2025-03-12 08:00:00', '2025-03-12 22:00:00', 100, 5, NULL, NULL, NULL, '2025-03-12 17:25:59', '2025-03-12 17:30:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `expires_at`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '1f678c7782f6b527c5c68de7c87d94a609c3383ed90dcf0093599d825b5fe108', '[\"*\"]', NULL, NULL, '2025-03-12 09:39:35', '2025-03-12 09:39:35'),
(2, 'App\\Models\\User', 1, 'auth_token', '4764efc84399ae4798ad0437be9d948234bd90c2a050be27120a4f00f5545b41', '[\"*\"]', NULL, '2025-03-12 10:35:06', '2025-03-12 09:39:41', '2025-03-12 10:35:06'),
(3, 'App\\Models\\User', 1, 'auth_token', '2cbb18649ddea912e2c6075b0b0fd8851b4a4b9645d4acd4057d7b86b5069ecf', '[\"*\"]', NULL, NULL, '2025-03-12 10:30:26', '2025-03-12 10:30:26'),
(4, 'App\\Models\\User', 1, 'auth_token', '618367910085955d86c6dd10c412efafa7b505411a4601c69e0396f1b8a3aeef', '[\"*\"]', NULL, NULL, '2025-03-16 07:43:21', '2025-03-16 07:43:21'),
(5, 'App\\Models\\User', 1, 'auth_token', 'bce2a3208f60a2ed197c42bc495964f37583c067f4003ed5af682a8e78fdd6cc', '[\"*\"]', NULL, NULL, '2025-03-31 01:33:26', '2025-03-31 01:33:26'),
(9, 'App\\Models\\User', 1, 'auth_token', '3894d0075ea639c92cb7ebf116c2bc4b123d3bc044210ce87de6d8f32425ae1c', '[\"*\"]', NULL, NULL, '2025-03-31 09:57:40', '2025-03-31 09:57:40'),
(10, 'App\\Models\\User', 2, 'auth_token', '8050043714c037ebbb2dd09a6c7630c4179771e275e8313f02b491efc80304a1', '[\"*\"]', NULL, NULL, '2025-03-31 09:58:35', '2025-03-31 09:58:35'),
(11, 'App\\Models\\User', 2, 'auth_token', '3b3f359b0c43f8f2222f19063c33717f9be3f8898fa8d8711dba9600bd662f71', '[\"*\"]', NULL, NULL, '2025-03-31 09:58:57', '2025-03-31 09:58:57'),
(16, 'App\\Models\\User', 1, 'auth_token', '748526ed2397dc1850e3fcce0ef115d2967934c283c09cf81396294a8ea42786', '[\"*\"]', NULL, '2025-03-31 10:02:52', '2025-03-31 10:02:44', '2025-03-31 10:02:52'),
(27, 'App\\Models\\User', 1, 'auth_token', '94f69d1924af8fbe103e69f4068b988935e8e6cead7595d92950bb73d28a96f1', '[\"*\"]', NULL, NULL, '2025-03-31 11:20:23', '2025-03-31 11:20:23'),
(28, 'App\\Models\\User', 1, 'auth_token', '29a02ea5752cf51f5180147e2e3b86a7d8937ccfbedf368d53f5caa1f51cba54', '[\"*\"]', NULL, NULL, '2025-04-01 00:08:22', '2025-04-01 00:08:22'),
(30, 'App\\Models\\User', 1, 'auth_token', 'f0b46eb96cd0d053bce83f09cd3890794ea9db1d1c1a6e8511002bf8f666d5b7', '[\"*\"]', NULL, NULL, '2025-04-01 00:21:59', '2025-04-01 00:21:59'),
(31, 'App\\Models\\User', 1, 'auth_token', '5adaae6fbee1e35e0bb6d561a25d3b7f8abbdc1b025a5b7f01af88252d53c6e0', '[\"*\"]', NULL, NULL, '2025-04-01 00:34:24', '2025-04-01 00:34:24'),
(32, 'App\\Models\\User', 1, 'auth_token', '05f67d1619557f776893ea005131e3d6855aa31b58e2b2c29b49d94584482d98', '[\"*\"]', NULL, NULL, '2025-04-01 00:39:35', '2025-04-01 00:39:35'),
(33, 'App\\Models\\User', 1, 'auth_token', 'a248f199f81a9425c3ff4396f9e1548eb6f9f569c65429ac6247be904ac76bb9', '[\"*\"]', NULL, NULL, '2025-04-01 00:39:42', '2025-04-01 00:39:42'),
(34, 'App\\Models\\User', 1, 'auth_token', '1a6c6004b7a36871119157e4eb2c77a1e9d4eda0053abe646704571eaf36f480', '[\"*\"]', NULL, NULL, '2025-04-01 00:47:15', '2025-04-01 00:47:15'),
(35, 'App\\Models\\User', 2, 'auth_token', '57e27a22db31a818700c9527004e46428752b44b4ecc18565113f73d5fbd720c', '[\"*\"]', NULL, '2025-04-01 01:04:15', '2025-04-01 00:50:49', '2025-04-01 01:04:15'),
(36, 'App\\Models\\User', 1, 'auth_token', 'f750f32fb07eec01bb923988f14b081e5abc96258077a50b982744148fc41914', '[\"*\"]', NULL, NULL, '2025-04-01 00:51:16', '2025-04-01 00:51:16'),
(37, 'App\\Models\\User', 1, 'auth_token', '666c2909c89cdf1910a9f851d926343e313d711112339892ad5c8d5c564fbbe4', '[\"*\"]', NULL, NULL, '2025-04-01 00:56:50', '2025-04-01 00:56:50'),
(38, 'App\\Models\\User', 1, 'auth_token', '3611e99ea455d14bf5318a138836bddaf3a92ca8a128cb1d45a5b7d7ae241cbd', '[\"*\"]', NULL, NULL, '2025-04-01 01:03:28', '2025-04-01 01:03:28'),
(39, 'App\\Models\\User', 1, 'auth_token', 'cbac8c208c85c7e19c8614673d37786844884f6ef26bfb1385632cfaf776d5fe', '[\"*\"]', NULL, NULL, '2025-04-01 01:04:31', '2025-04-01 01:04:31'),
(40, 'App\\Models\\User', 1, 'auth_token', '0027160293433f147f8f30eaac74fb287c727a3ac22bd4b353a78b26f6ce0249', '[\"*\"]', NULL, NULL, '2025-04-01 01:06:43', '2025-04-01 01:06:43'),
(45, 'App\\Models\\User', 1, 'auth_token', '28f3352b26ce00cf5f59b71d8a92ec384c967257c3e533eeca85ccc7cf2e1d83', '[\"*\"]', NULL, NULL, '2025-04-01 02:15:08', '2025-04-01 02:15:08'),
(46, 'App\\Models\\User', 1, 'auth_token', 'fba07da5a3838748fb1dc914be37c054d5480f7dcca2bc32ece506b76c78d859', '[\"*\"]', NULL, NULL, '2025-04-01 02:16:57', '2025-04-01 02:16:57'),
(48, 'App\\Models\\User', 1, 'auth_token', 'de3c91a20be4d470ca21f8bac4e643b40aa226e018af86ed25fa112867f4efa5', '[\"*\"]', NULL, NULL, '2025-04-01 02:17:17', '2025-04-01 02:17:17'),
(49, 'App\\Models\\User', 1, 'auth_token', '97026bdc08600a7b9d3321242001eadfb82a85b449fe82dfe25e3cc515e8a586', '[\"*\"]', NULL, NULL, '2025-04-01 02:23:16', '2025-04-01 02:23:16'),
(50, 'App\\Models\\User', 1, 'auth_token', 'a24af4fb5d65b397c8a731e0fa044c0d42a6c2eb06adae2fb15177ee16fe7b49', '[\"*\"]', NULL, NULL, '2025-04-01 19:18:36', '2025-04-01 19:18:36'),
(54, 'App\\Models\\User', 1, 'auth_token', 'a190380080c0d9030e3f1544d886fc677825dbb0a791f8a05a5e60c0e7a129d7', '[\"*\"]', NULL, '2025-04-06 22:14:11', '2025-04-05 09:22:30', '2025-04-06 22:14:11'),
(61, 'App\\Models\\User', 2, 'auth_token', '11ef7f6002219bc8c1cf44b71abc67f9355ebf00f01a016d0a832159ca8b4c52', '[\"*\"]', NULL, NULL, '2025-04-08 01:37:24', '2025-04-08 01:37:24'),
(68, 'App\\Models\\User', 1, 'auth_token', 'c0bf66c40a00f61451f76b3d313b8bfc521a5bd0832639d613dee90104d1126f', '[\"*\"]', NULL, NULL, '2025-04-08 12:29:12', '2025-04-08 12:29:12'),
(69, 'App\\Models\\User', 5, 'auth_token', '4de22f884959df181af22d47b0290caf8d02229c36dabae5218938382b0a25b4', '[\"*\"]', NULL, NULL, '2025-04-08 12:35:21', '2025-04-08 12:35:21'),
(70, 'App\\Models\\User', 1, 'auth_token', 'f3847ec516e7b28a05e3adc036005ae78fa8f05065e9598f62f89bcceecc4d94', '[\"*\"]', NULL, NULL, '2025-04-08 12:37:33', '2025-04-08 12:37:33'),
(71, 'App\\Models\\User', 1, 'auth_token', '0a96149e9a3395e7ef9559345a40bf1aaa95011509a693fdbcb6293f9b1297d7', '[\"*\"]', NULL, NULL, '2025-04-08 12:39:45', '2025-04-08 12:39:45'),
(72, 'App\\Models\\User', 1, 'auth_token', '1557ccfdec457494bebb1a8d9507609234b0cef0252037968b2d4650cdc15e74', '[\"*\"]', NULL, '2025-04-09 00:51:54', '2025-04-08 22:06:25', '2025-04-09 00:51:54'),
(73, 'App\\Models\\User', 2, 'auth_token', '359d62fbdf1d0b416b1bcec35cbb09e907d0fcde9089048ec484e7bebb341c0d', '[\"*\"]', NULL, '2025-04-09 00:52:28', '2025-04-09 00:52:10', '2025-04-09 00:52:28'),
(74, 'App\\Models\\User', 2, 'auth_token', '9e30f3a3cc8b33a3d700070723fade3812f01046de55f3f59dc9efb2e7d4dc96', '[\"*\"]', NULL, '2025-04-09 01:21:19', '2025-04-09 00:57:19', '2025-04-09 01:21:19'),
(75, 'App\\Models\\User', 1, 'auth_token', 'dd76967f1ae531a17cd45b70168e9eb5d51e57c7ee0b817578ade188b813bd3f', '[\"*\"]', NULL, NULL, '2025-04-09 01:39:14', '2025-04-09 01:39:14'),
(76, 'App\\Models\\User', 2, 'auth_token', '43f740d80a46033d11a51c9eaf7a79b3e1074003f9797c93f6060a0283c852fb', '[\"*\"]', NULL, '2025-04-09 07:56:34', '2025-04-09 01:42:48', '2025-04-09 07:56:34'),
(77, 'App\\Models\\User', 1, 'auth_token', '4f80cb0ebbc3aa9ceef94fa9e2b5460f1a332d12a3201c196817a64b7c65930a', '[\"*\"]', NULL, NULL, '2025-04-09 06:30:33', '2025-04-09 06:30:33'),
(78, 'App\\Models\\User', 1, 'auth_token', '8a133f38bbf5c311eec74090b5d09a9327961b6531c24f0a0b7b0688420e8237', '[\"*\"]', NULL, '2025-04-09 08:32:00', '2025-04-09 06:30:34', '2025-04-09 08:32:00'),
(79, 'App\\Models\\User', 1, 'auth_token', 'b5f4bb0e2ea755835bc8be0a4b16b8b7f3acadf775b3b2ab56bc295a01749fe3', '[\"*\"]', NULL, NULL, '2025-04-09 18:10:37', '2025-04-09 18:10:37'),
(80, 'App\\Models\\User', 1, 'auth_token', 'c92a362d88f1a36770f3c8ae1571b422895c69c2fa3284df6253f894945a4190', '[\"*\"]', NULL, '2025-04-11 09:00:26', '2025-04-09 18:10:58', '2025-04-11 09:00:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Gff9m7lnEJfsww9r9iJ8PuNrcPVHzIT83UBdC1nT', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV0d0d1F1S1FTT0lrSG1vRW9CS2p3OTR5NHU0Smt0Y2dnQkF6QkdvdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1744140239),
('mjlzAXb7memeVMZ48a6vJMiCbvc17aBwq6KUQT4g', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNmNMcmoxakhIMUYwbXNwR2xpbFRLV0RTZ1BXZTFMV1RST0lVYUoxYyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1743493675),
('sNOpuRL1XRzSFJmxrmwgMUQ61hQFQJMSVh9CZK4T', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMlkwMWtZVG4wWlp4TVMwZGFpdnVZSHhFczgycDF3WWNKR0pyZ1cxeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1744140336);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhtoan`
--

CREATE TABLE `thanhtoan` (
  `ID_ThanhToan` int(11) NOT NULL,
  `ID_ThongTinDatBan` int(11) NOT NULL,
  `SoLuong` int(11) DEFAULT NULL,
  `PhuongThucThanhToan` int(11) DEFAULT NULL,
  `TrangThaiThanhToan` int(11) DEFAULT NULL,
  `NgayThanhToan` datetime DEFAULT NULL,
  `MaGiaoDich` varchar(100) DEFAULT NULL,
  `NgayTao` datetime DEFAULT NULL,
  `NgayCapNhap` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thanhtoan`
--

INSERT INTO `thanhtoan` (`ID_ThanhToan`, `ID_ThongTinDatBan`, `SoLuong`, `PhuongThucThanhToan`, `TrangThaiThanhToan`, `NgayThanhToan`, `MaGiaoDich`, `NgayTao`, `NgayCapNhap`) VALUES
(1, 1, 100000, 1, 1, '2025-03-12 17:27:50', 'TM12345', '2025-03-12 17:27:50', '2025-03-12 17:27:50'),
(2, 2, 173123, 1, 1, '2025-04-10 02:12:12', 'CASH_1744251130', '2025-04-10 02:12:12', '2025-04-10 02:12:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongbao`
--

CREATE TABLE `thongbao` (
  `ID_ThongBao` int(11) NOT NULL,
  `ID_USER` int(11) NOT NULL,
  `Ten` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NoiDung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TheLoai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DieuKienKichHoat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DaDoc` tinyint(1) DEFAULT 0,
  `Hide` tinyint(1) DEFAULT NULL,
  `NgayTao` datetime DEFAULT NULL,
  `NgayCapNhap` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thongbao`
--

INSERT INTO `thongbao` (`ID_ThongBao`, `ID_USER`, `Ten`, `MoTa`, `NoiDung`, `TheLoai`, `DieuKienKichHoat`, `DaDoc`, `Hide`, `NgayTao`, `NgayCapNhap`) VALUES
(1, 1, 'Khuyến mãi đặc biệt', 'Giảm giá 20% cho đơn đặt bàn', 'Nhân dịp khai trương, nhà hàng giảm giá 20% cho tất cả các đơn đặt bàn từ ngày 15-20/3/2025', 'promotion', NULL, 1, 0, '2025-03-12 17:09:27', '2025-03-12 17:31:24'),
(2, 1, 'Xác nhận đặt bàn', 'Đơn đặt bàn của bạn đã được xác nhận', 'Kính gửi Admin admin,\n\nĐơn đặt bàn của bạn tại Nhà Hàng Gà To vào lúc 18:00 15/03/2025 đã được xác nhận.\nSố lượng khách: 4\nCảm ơn bạn đã sử dụng dịch vụ của chúng tôi!', 'booking_confirmation', NULL, 0, 0, '2025-03-12 17:33:15', '2025-03-12 17:33:15'),
(3, 1, 'Đơn đặt bàn mới', 'Có đơn đặt bàn mới cần xác nhận', 'Khách hàng Admin admin đã đặt bàn ngày 17/04/2025 vào lúc 02:12. Vui lòng kiểm tra và xác nhận.', 'new_booking', NULL, 0, 0, '2025-04-07 05:13:17', '2025-04-07 05:13:17'),
(4, 1, 'Đơn đặt bàn mới', 'Có đơn đặt bàn mới cần xác nhận', 'Khách hàng Nguyen Van A đã đặt bàn ngày 18/04/2025 vào lúc 06:30. Vui lòng kiểm tra và xác nhận.', 'new_booking', NULL, 0, 0, '2025-04-07 08:31:16', '2025-04-07 08:31:16'),
(5, 1, 'Đơn đặt bàn mới', 'Có đơn đặt bàn mới cần xác nhận', 'Khách hàng Nguyen Van A đã đặt bàn ngày 01/05/2025 vào lúc 09:53. Vui lòng kiểm tra và xác nhận.', 'new_booking', NULL, 0, 0, '2025-04-09 14:50:39', '2025-04-09 14:50:39'),
(6, 2, 'Xác nhận đặt bàn', 'Đơn đặt bàn của bạn đã được xác nhận', 'Kính gửi Nguyen Van A,\n\nĐơn đặt bàn của bạn tại Nhà Hàng Gà To vào lúc 09:53 01/05/2025 đã được xác nhận.\nSố lượng khách: 4\nCảm ơn bạn đã sử dụng dịch vụ của chúng tôi!', 'booking_confirmation', NULL, 0, 0, '2025-04-10 12:11:30', '2025-04-10 12:11:30'),
(7, 2, 'Bạn là người may mắn nhận được thông báo giảm giá này trong ngày hôm nay', 'vocher 100k', 'vocher 100k', 'promotion', NULL, 0, 0, '2025-04-10 17:52:37', '2025-04-10 17:52:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongtindatban`
--

CREATE TABLE `thongtindatban` (
  `ID_ThongTinDatBan` int(11) NOT NULL,
  `ID_USER` int(11) NOT NULL,
  `ThoiGianDatBan` datetime DEFAULT NULL,
  `SoLuongKhach` int(11) DEFAULT NULL,
  `YeuCau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NgayTao` datetime DEFAULT NULL,
  `NgayCapNhap` datetime DEFAULT NULL,
  `TrangThai` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thongtindatban`
--

INSERT INTO `thongtindatban` (`ID_ThongTinDatBan`, `ID_USER`, `ThoiGianDatBan`, `SoLuongKhach`, `YeuCau`, `NgayTao`, `NgayCapNhap`, `TrangThai`) VALUES
(1, 1, '2025-03-15 18:00:00', 4, 'Vui lòng chuẩn bị bàn gần cửa sổ', '2025-03-12 17:26:54', '2025-03-12 17:33:15', 1),
(2, 1, '2025-04-17 02:12:00', 2, 'sadsad', '2025-04-07 05:13:15', '2025-04-10 01:56:18', 1),
(3, 2, '2025-04-18 06:30:00', 2, 'Sắp xếp thật lãng mạn', '2025-04-07 08:31:14', '2025-04-09 13:01:52', 2),
(4, 2, '2025-05-01 09:53:00', 4, 'Yêu cầu gần cửa sổ', '2025-04-09 14:50:37', '2025-04-10 12:11:30', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `ID_USER` int(11) NOT NULL,
  `TenDangNhap` varchar(100) DEFAULT NULL,
  `MatKhau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `HoVaTen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Sdt` char(10) DEFAULT NULL,
  `Quyen` int(11) DEFAULT NULL,
  `NgayDK` datetime DEFAULT NULL,
  `Anh` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Hide` tinyint(1) DEFAULT NULL,
  `NgayTao` datetime DEFAULT NULL,
  `NgayCapNhap` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`ID_USER`, `TenDangNhap`, `MatKhau`, `HoVaTen`, `Email`, `Sdt`, `Quyen`, `NgayDK`, `Anh`, `Hide`, `NgayTao`, `NgayCapNhap`) VALUES
(1, 'admin', '$2y$12$IF5uzLquO30qHavy8FT/VOe0Qvq0usxr7htXz2XTtPpIGvBCe63Za', 'Admin admin', 'admin@example.com', '0987654321', 1, '2025-03-12 16:39:34', NULL, 0, '2025-03-12 16:39:34', '2025-03-12 16:39:34'),
(2, 'user1', '$2y$12$HP0ZCfk4GfBbQuJkSvyek.P2qKwdob6ICKzi1BqSy9IcM0fdnPYc6', 'Nguyen Van A', 'user1@example.com', '0987654321', 0, '2025-03-31 16:58:35', NULL, 0, '2025-03-31 16:58:35', '2025-03-31 16:58:35'),
(3, 'user2', '$2y$12$vm5uhuI8f3Wmx/yPSIC00.yWvYvBli3FrbDyO8eBUwGCyGGsM5FM6', 'Nguyen Van B', 'user2@gmail.com', '0918321319', 0, '2025-03-31 17:00:55', NULL, 0, '2025-03-31 17:00:55', '2025-03-31 17:00:55');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ban`
--
ALTER TABLE `ban`
  ADD PRIMARY KEY (`ID_Ban`),
  ADD KEY `ID_KhuVuc` (`ID_KhuVuc`);

--
-- Chỉ mục cho bảng `bangchitietdatban`
--
ALTER TABLE `bangchitietdatban`
  ADD PRIMARY KEY (`ID_ChiTietDatBan`,`ID_Ban`,`ID_ThongTinDatBan`),
  ADD KEY `ID_Ban` (`ID_Ban`),
  ADD KEY `ID_ThongTinDatBan` (`ID_ThongTinDatBan`);

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `chitietdatmon`
--
ALTER TABLE `chitietdatmon`
  ADD PRIMARY KEY (`ID_MonAn`,`ID_ThongTinDatBan`),
  ADD KEY `ID_ThongTinDatBan` (`ID_ThongTinDatBan`);

--
-- Chỉ mục cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`ID_DanhGia`),
  ADD KEY `ID_USER` (`ID_USER`),
  ADD KEY `ID_NhaHang` (`ID_NhaHang`);

--
-- Chỉ mục cho bảng `khuvuc`
--
ALTER TABLE `khuvuc`
  ADD PRIMARY KEY (`ID_KhuVuc`),
  ADD KEY `ID_NhaHang` (`ID_NhaHang`);

--
-- Chỉ mục cho bảng `loaimonan`
--
ALTER TABLE `loaimonan`
  ADD PRIMARY KEY (`MaLoai`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `monan`
--
ALTER TABLE `monan`
  ADD PRIMARY KEY (`ID_MonAn`),
  ADD KEY `ID_NhaHang` (`ID_NhaHang`),
  ADD KEY `MaLoai` (`MaLoai`);

--
-- Chỉ mục cho bảng `nhahang`
--
ALTER TABLE `nhahang`
  ADD PRIMARY KEY (`ID_NhaHang`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`ID_ThanhToan`),
  ADD KEY `ID_ThongTinDatBan` (`ID_ThongTinDatBan`);

--
-- Chỉ mục cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD PRIMARY KEY (`ID_ThongBao`),
  ADD KEY `ID_USER` (`ID_USER`);

--
-- Chỉ mục cho bảng `thongtindatban`
--
ALTER TABLE `thongtindatban`
  ADD PRIMARY KEY (`ID_ThongTinDatBan`),
  ADD KEY `ID_USER` (`ID_USER`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_USER`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `ID_USER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `ban`
--
ALTER TABLE `ban`
  ADD CONSTRAINT `ban_ibfk_1` FOREIGN KEY (`ID_KhuVuc`) REFERENCES `khuvuc` (`ID_KhuVuc`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `bangchitietdatban`
--
ALTER TABLE `bangchitietdatban`
  ADD CONSTRAINT `bangchitietdatban_ibfk_1` FOREIGN KEY (`ID_Ban`) REFERENCES `ban` (`ID_Ban`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `bangchitietdatban_ibfk_2` FOREIGN KEY (`ID_ThongTinDatBan`) REFERENCES `thongtindatban` (`ID_ThongTinDatBan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `chitietdatmon`
--
ALTER TABLE `chitietdatmon`
  ADD CONSTRAINT `chitietdatmon_ibfk_1` FOREIGN KEY (`ID_MonAn`) REFERENCES `monan` (`ID_MonAn`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdatmon_ibfk_2` FOREIGN KEY (`ID_ThongTinDatBan`) REFERENCES `thongtindatban` (`ID_ThongTinDatBan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `danhgia`
--
ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgia_ibfk_1` FOREIGN KEY (`ID_USER`) REFERENCES `user` (`ID_USER`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `danhgia_ibfk_2` FOREIGN KEY (`ID_NhaHang`) REFERENCES `nhahang` (`ID_NhaHang`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `khuvuc`
--
ALTER TABLE `khuvuc`
  ADD CONSTRAINT `khuvuc_ibfk_1` FOREIGN KEY (`ID_NhaHang`) REFERENCES `nhahang` (`ID_NhaHang`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `monan`
--
ALTER TABLE `monan`
  ADD CONSTRAINT `monan_ibfk_1` FOREIGN KEY (`ID_NhaHang`) REFERENCES `nhahang` (`ID_NhaHang`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `monan_ibfk_2` FOREIGN KEY (`MaLoai`) REFERENCES `loaimonan` (`MaLoai`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `thanhtoan_ibfk_1` FOREIGN KEY (`ID_ThongTinDatBan`) REFERENCES `thongtindatban` (`ID_ThongTinDatBan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`ID_USER`) REFERENCES `user` (`ID_USER`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `thongtindatban`
--
ALTER TABLE `thongtindatban`
  ADD CONSTRAINT `thongtindatban_ibfk_1` FOREIGN KEY (`ID_USER`) REFERENCES `user` (`ID_USER`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
