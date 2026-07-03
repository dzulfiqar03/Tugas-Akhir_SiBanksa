-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 06, 2026 at 07:14 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4
SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_sibanksa_app`
--
-- --------------------------------------------------------
--
-- Table structure for table `banks`
--
CREATE TABLE
    `banks` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `transfer_code` varchar(10) NOT NULL,
        `name` varchar(255) NOT NULL,
        `short_name` varchar(20) DEFAULT NULL,
        `swift_code` varchar(20) DEFAULT NULL,
        `logo` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `banks`
--
INSERT INTO
    `banks` (
        `id`,
        `transfer_code`,
        `name`,
        `short_name`,
        `swift_code`,
        `logo`,
        `created_at`,
        `updated_at`
    )
VALUES
    (
        1,
        '008',
        'Bank Mandiri',
        'MANDIRI',
        'BMRIIDJA',
        'https://logo.clearbit.com/mandiri.co.id',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        2,
        '014',
        'Bank Central Asia',
        'BCA',
        'CENAIDJA',
        'https://logo.clearbit.com/bca.co.id',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        3,
        '009',
        'Bank Negara Indonesia',
        'BNI',
        'BNINIDJA',
        'https://logo.clearbit.com/bni.co.id',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        4,
        '002',
        'Bank Rakyat Indonesia',
        'BRI',
        'BRINIDJA',
        'https://logo.clearbit.com/bri.co.id',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        5,
        '013',
        'Bank Permata',
        'PERMATA',
        'BBBAIDJA',
        'https://logo.clearbit.com/permatabank.com',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        6,
        '022',
        'CIMB Niaga',
        'CIMB',
        'BNIAIDJA',
        'https://logo.clearbit.com/cimbniaga.co.id',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        7,
        '016',
        'Bank BJB',
        'BJB',
        'BJBRIDJA',
        'https://logo.clearbit.com/bankbjb.co.id',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    );

-- --------------------------------------------------------
--
-- Table structure for table `cache`
--
CREATE TABLE
    `cache` (
        `key` varchar(255) NOT NULL,
        `value` mediumtext NOT NULL,
        `expiration` int (11) NOT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `cache_locks`
--
CREATE TABLE
    `cache_locks` (
        `key` varchar(255) NOT NULL,
        `owner` varchar(255) NOT NULL,
        `expiration` int (11) NOT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `document_archivers`
--
CREATE TABLE
    `document_archivers` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `id_jadwal` bigint (20) UNSIGNED DEFAULT NULL,
        `original_filesname` varchar(255) DEFAULT NULL,
        `encrypted_filesname` varchar(255) DEFAULT NULL,
        `name` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `evidence_archivers`
--
CREATE TABLE
    `evidence_archivers` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `original_photoname` varchar(255) DEFAULT NULL,
        `encrypted_photoname` varchar(255) DEFAULT NULL,
        `name` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `failed_jobs`
--
CREATE TABLE
    `failed_jobs` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `uuid` varchar(255) NOT NULL,
        `connection` text NOT NULL,
        `queue` text NOT NULL,
        `payload` longtext NOT NULL,
        `exception` longtext NOT NULL,
        `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `genders`
--
CREATE TABLE
    `genders` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `gender` enum ('Laki-Laki', 'Perempuan', 'None') DEFAULT 'None',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `genders`
--
INSERT INTO
    `genders` (`id`, `gender`, `created_at`, `updated_at`)
VALUES
    (
        1,
        'Laki-Laki',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        2,
        'Perempuan',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        3,
        'None',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    );

-- --------------------------------------------------------
--
-- Table structure for table `jadwal_pelaksanaan`
--
CREATE TABLE
    `jadwal_pelaksanaan` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `tanggal_setoran` date NOT NULL DEFAULT curdate (),
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `janji_setors`
--
CREATE TABLE
    `janji_setors` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `id_jadwal` bigint (20) UNSIGNED NOT NULL,
        `waktu_janji` time NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `jobs`
--
CREATE TABLE
    `jobs` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `queue` varchar(255) NOT NULL,
        `payload` longtext NOT NULL,
        `attempts` tinyint (3) UNSIGNED NOT NULL,
        `reserved_at` int (10) UNSIGNED DEFAULT NULL,
        `available_at` int (10) UNSIGNED NOT NULL,
        `created_at` int (10) UNSIGNED NOT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `job_batches`
--
CREATE TABLE
    `job_batches` (
        `id` varchar(255) NOT NULL,
        `name` varchar(255) NOT NULL,
        `total_jobs` int (11) NOT NULL,
        `pending_jobs` int (11) NOT NULL,
        `failed_jobs` int (11) NOT NULL,
        `failed_job_ids` longtext NOT NULL,
        `options` mediumtext DEFAULT NULL,
        `cancelled_at` int (11) DEFAULT NULL,
        `created_at` int (11) NOT NULL,
        `finished_at` int (11) DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `kepengurusans`
--
CREATE TABLE
    `kepengurusans` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `userName` varchar(255) NOT NULL,
        `fullName` varchar(255) NOT NULL,
        `address` varchar(255) NOT NULL,
        `telephone_number` varchar(255) NOT NULL,
        `id_gender` bigint (20) UNSIGNED NOT NULL,
        `divisi` enum (
            'Ketua',
            'Sekretaris',
            'Bendahara',
            'Pemilah',
            'Penimbang'
        ) NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `migrations`
--
CREATE TABLE
    `migrations` (
        `id` int (10) UNSIGNED NOT NULL,
        `migration` varchar(255) NOT NULL,
        `batch` int (11) NOT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--
INSERT INTO
    `migrations` (`id`, `migration`, `batch`)
VALUES
    (1, '0001_01_01_000000_create_users_table', 1),
    (2, '0001_01_01_000001_create_cache_table', 1),
    (3, '0001_01_01_000002_create_jobs_table', 1),
    (4, '2025_11_15_125607_create_roles_table', 1),
    (
        5,
        '2025_11_15_130345_create_rt_perumahan_table',
        1
    ),
    (6, '2025_11_15_130830_create_genders_table', 1),
    (
        7,
        '2025_11_15_131015_create_user_details_table',
        1
    ),
    (8, '2025_11_15_132935_create_banks_table', 1),
    (9, '2025_11_15_142144_create_sampahs_table', 1),
    (
        10,
        '2025_11_15_142204_create_pencatatan_setorans_table',
        1
    ),
    (
        11,
        '2025_11_15_150015_create_pencatatan_setoran_items_table',
        1
    ),
    (
        12,
        '2025_11_15_150325_create_user_transactions_table',
        1
    ),
    (
        13,
        '2025_11_15_155120_create_document_archivers_table',
        1
    ),
    (
        14,
        '2025_11_15_155134_create_evidence_archivers_table',
        1
    ),
    (
        15,
        '2025_11_15_155532_create_kepengurusans_table',
        1
    ),
    (
        16,
        '2026_01_14_030645_create_jadwal_pelaksanaans_table',
        1
    ),
    (17, '2026_01_14_030708_add_jadwal_pencatatan', 1),
    (
        18,
        '2026_01_14_035254_add_foreign_jadwal.user_detail',
        1
    ),
    (
        19,
        '2026_01_14_051200_add_unique_to_tanggal_setoran_in_jadwal_pelaksaanaan',
        1
    ),
    (
        20,
        '2026_01_19_081252_create_notifications_table',
        1
    ),
    (
        21,
        '2026_01_19_092225_modify_notifications_table_for_uuid',
        1
    ),
    (22, '2026_01_19_144631_create_user_bank_table', 1),
    (
        23,
        '2026_01_19_145739_get_rid_and_add_from_table_user_transactions',
        1
    ),
    (24, '2026_01_24_090954_add_column_in_sampah', 1),
    (25, '2026_01_31_163136_create_user_logs_table', 1),
    (26, '2026_02_01_145156_add_original_name_', 1),
    (
        27,
        '2026_02_01_234209_add_jadwal_pencatatan_on_evidance_table',
        1
    ),
    (
        28,
        '2026_02_02_000430__get_rid_and_add_from_table_evidence_archiver',
        1
    ),
    (
        29,
        '2026_02_02_123557__get_rid_and_add_from_document_archiver',
        1
    ),
    (
        30,
        '2026_02_03_091300__get_rid_and_add_from_user_details',
        1
    ),
    (
        31,
        '2026_02_03_171559_create_user_chats_table',
        1
    ),
    (
        32,
        '2026_02_03_202201_add_column_id_sender_on_user_chat_table',
        1
    ),
    (
        33,
        '2026_02_04_143920_add_column_read_on_user_chat_table',
        1
    ),
    (34, '2026_02_06_001656_create_user_bots_table', 1),
    (
        35,
        '2026_02_08_164608_create_geolocations_table',
        1
    ),
    (
        36,
        '2026_02_08_171141_create_open_street_views_table',
        1
    ),
    (
        37,
        '2026_02_18_085236_create_user_queues_table',
        1
    ),
    (
        38,
        '2026_02_26_230459_create_janji_setors_table',
        1
    ),
    (
        39,
        '2026_04_14_045051_add_via_pencairan_on_user_details_tables',
        1
    ),
    (
        40,
        '2026_04_18_164349_remove_foreign_id_userbank_on_user_transaction_tables',
        1
    ),
    (
        41,
        '2026_04_26_155017_create_push_subscriptions_table',
        1
    );

-- --------------------------------------------------------
--
-- Table structure for table `notifications`
--
CREATE TABLE
    `notifications` (
        `id` char(36) NOT NULL,
        `type` varchar(255) NOT NULL,
        `notifiable_type` varchar(255) NOT NULL,
        `notifiable_id` char(36) NOT NULL,
        `data` text NOT NULL,
        `read_at` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `open_street_views`
--
CREATE TABLE
    `open_street_views` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_geoLoc` bigint (20) UNSIGNED NOT NULL,
        `display_name` varchar(255) NOT NULL,
        `latitude` decimal(8, 2) DEFAULT NULL,
        `longitude` decimal(8, 2) DEFAULT NULL,
        `type` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `password_reset_tokens`
--
CREATE TABLE
    `password_reset_tokens` (
        `email` varchar(255) NOT NULL,
        `token` varchar(255) NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `pencatatan_setoran`
--
CREATE TABLE
    `pencatatan_setoran` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_jadwal` bigint (20) UNSIGNED DEFAULT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `total_setoran` int (11) NOT NULL DEFAULT 0,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `pencatatan_setoran_items`
--
CREATE TABLE
    `pencatatan_setoran_items` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `pencatatan_setoran_id` bigint (20) UNSIGNED NOT NULL,
        `sampah_id` bigint (20) UNSIGNED NOT NULL,
        `jumlah` decimal(10, 2) NOT NULL DEFAULT 0.00,
        `harga_satuan` int (11) NOT NULL,
        `subtotal` int (11) NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `push_subscriptions`
--
CREATE TABLE
    `push_subscriptions` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `user_id` char(36) NOT NULL,
        `endpoint` text NOT NULL,
        `public_key` text NOT NULL,
        `auth_token` text NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `roles`
--
CREATE TABLE
    `roles` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `role` enum ('Developer', 'Ketua RW', 'Bank Sampah', 'Warga') NOT NULL DEFAULT 'Warga',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--
INSERT INTO
    `roles` (`id`, `role`, `created_at`, `updated_at`)
VALUES
    (
        1,
        'Ketua RW',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        2,
        'Bank Sampah',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        3,
        'Warga',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        4,
        'Developer',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    );

-- --------------------------------------------------------
--
-- Table structure for table `rt_perumahan`
--
CREATE TABLE
    `rt_perumahan` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `RT` enum ('1', '2', '3', '4', '5', '6', '7', '8') NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `rt_perumahan`
--
INSERT INTO
    `rt_perumahan` (`id`, `RT`, `created_at`, `updated_at`)
VALUES
    (
        1,
        '1',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        2,
        '2',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        3,
        '3',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        4,
        '4',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        5,
        '5',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        6,
        '6',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        7,
        '7',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        8,
        '8',
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    );

-- --------------------------------------------------------
--
-- Table structure for table `sampah`
--
CREATE TABLE
    `sampah` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `nama_sampah` varchar(255) NOT NULL,
        `harga` bigint (20) NOT NULL,
        `saldo` bigint (20) DEFAULT NULL,
        `satuan` varchar(255) NOT NULL,
        `kategori` enum ('Daur Ulang', 'Non Daur Ulang') NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `sessions`
--
CREATE TABLE
    `sessions` (
        `id` varchar(255) NOT NULL,
        `user_id` bigint (20) UNSIGNED DEFAULT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` text DEFAULT NULL,
        `payload` longtext NOT NULL,
        `last_activity` int (11) NOT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `users`
--
CREATE TABLE
    `users` (
        `id` char(36) NOT NULL,
        `email` varchar(255) NOT NULL,
        `email_verified_at` timestamp NULL DEFAULT NULL,
        `password` varchar(255) NOT NULL,
        `remember_token` varchar(100) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--
INSERT INTO
    `users` (
        `id`,
        `email`,
        `email_verified_at`,
        `password`,
        `remember_token`,
        `created_at`,
        `updated_at`
    )
VALUES
    (
        '0',
        'muhammaddzulfiqar03@gmail.com',
        '2026-05-06 14:54:15',
        '$2y$12$BNf4MZLtpwS1/74S4VYCJObVH81n7TYPcBwpOidBW1G.l7HZIP0Ce',
        NULL,
        '2026-05-06 14:54:15',
        '2026-05-06 14:54:15'
    ),
    (
        '1b581b41-985a-400e-9ee6-0a0264839802',
        'banksampahbasmi@gmail.com',
        '2026-05-06 14:54:16',
        '$2y$12$bSvQ8NrOxbX.OPFTNR2hkeJCOCrEY3OGp4iqieYFy0E3hZjkUrny6',
        NULL,
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        '5e277a30-c357-4578-9742-90c77a16a438',
        'banksampah05@gmail.com',
        '2026-05-06 14:54:16',
        '$2y$12$Qhv0LeSlpBeP7VGL5wz2JeHZib0N11ov8xTHg9zrn7EzxZjlO5SyO',
        NULL,
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        '86c6e071-d37a-4bdc-804b-bfb814052db5',
        'banksampah01@gmail.com',
        '2026-05-06 14:54:15',
        '$2y$12$ZrFdLSp98n6NLLWiAQsEW.7EEuNfR/kM2j/kiIpEwEZDYrk1fluE.',
        NULL,
        '2026-05-06 14:54:15',
        '2026-05-06 14:54:15'
    ),
    (
        '992fced0-ccdb-4841-817e-78ed79ca5da6',
        'banksampah06@gmail.com',
        '2026-05-06 14:54:16',
        '$2y$12$WbNC8CKmr8KC11BFUnacF.Jw6br42PSguBYKUHTee8.kw2jPbbHcy',
        NULL,
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        'aa71a3bf-5118-43b7-8fb9-a52724f701ed',
        'banksampahmekarjaya@gmail.com',
        '2026-05-06 14:54:15',
        '$2y$12$1hAIsP0puiYtBhdVWpW6WOXxqfJ4iXay7PwVn4Qc1AdQ5N5R3cX1e',
        NULL,
        '2026-05-06 14:54:15',
        '2026-05-06 14:54:15'
    ),
    (
        'b84e5147-1d77-4874-bb26-cfd4efb09931',
        'banksampah08@gmail.com',
        '2026-05-06 14:54:16',
        '$2y$12$MalpazhQOTTO6o7YLQ9GWOAR2InYwVDOEYzpNq1x9zGs7omZwTWhO',
        NULL,
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        'c307781c-0a6b-440c-ab1a-e6fc997f23d5',
        'ketuarw@gmail.com',
        '2026-05-06 14:54:15',
        '$2y$12$LYp7xhv/.51Y3k.Gt24mweWy70Nmfl5d0aIfrlReQX.yp9vT2WFH.',
        NULL,
        '2026-05-06 14:54:15',
        '2026-05-06 14:54:15'
    ),
    (
        'd52248a0-a74c-4f35-9157-8fdac2aa6c82',
        'banksampah04@gmail.com',
        '2026-05-06 14:54:16',
        '$2y$12$WhHnIXNlXvVywe6s3MRwTuH0eBBsrfvLC9UXvyVH3AvvzMRg25o4a',
        NULL,
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    ),
    (
        'e56fa14e-3642-46c2-8303-72e7068bec20',
        'banksampahmelatiputih@gmail.com',
        '2026-05-06 14:54:15',
        '$2y$12$6z7q1sieINWbXrGSISr/VuNouohYyEYAGJgLCx.pGOQugMpeQn07q',
        NULL,
        '2026-05-06 14:54:16',
        '2026-05-06 14:54:16'
    );

-- --------------------------------------------------------
--
-- Table structure for table `user_bank`
--
CREATE TABLE
    `user_bank` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `id_bank` bigint (20) UNSIGNED NOT NULL,
        `nomor_rekening` varchar(255) NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `user_bots`
--
CREATE TABLE
    `user_bots` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `chat` varchar(255) DEFAULT NULL,
        `bot_response` text DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `user_chats`
--
CREATE TABLE
    `user_chats` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `sender_id` char(36) NOT NULL,
        `message` varchar(255) DEFAULT NULL,
        `time` time NOT NULL,
        `read_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `is_read` tinyint (1) NOT NULL DEFAULT 1,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `user_details`
--
CREATE TABLE
    `user_details` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_user` char(36) NOT NULL,
        `userName` varchar(255) NOT NULL,
        `fullName` varchar(255) NOT NULL,
        `id_rt` bigint (20) UNSIGNED NOT NULL,
        `address` varchar(255) DEFAULT NULL,
        `telephone_number` varchar(255) NOT NULL,
        `id_gender` bigint (20) UNSIGNED NOT NULL,
        `id_roles` bigint (20) UNSIGNED NOT NULL,
        `status` enum (
            'Pending',
            'Pengajuan Verifikasi',
            'Ditolak',
            'Disetujui'
        ) NOT NULL,
        `status_transaction` varchar(255) NOT NULL,
        `pencairan_via` enum ('Tunai', 'Non-Tunai') NOT NULL DEFAULT 'Non-Tunai',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `user_details`
--
INSERT INTO
    `user_details` (
        `id`,
        `id_user`,
        `userName`,
        `fullName`,
        `id_rt`,
        `address`,
        `telephone_number`,
        `id_gender`,
        `id_roles`,
        `status`,
        `status_transaction`,
        `pencairan_via`,
        `created_at`,
        `updated_at`
    )
VALUES
    (
        1,
        '0',
        'muhammaddzulfiqar03',
        'Muhammad Dzulfiqar',
        7,
        'Gresik',
        '081216299698',
        1,
        4,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        2,
        'c307781c-0a6b-440c-ab1a-e6fc997f23d5',
        'ketuarw',
        'Ketua RW',
        6,
        'Gresik',
        '081252218959',
        2,
        1,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        3,
        '1b581b41-985a-400e-9ee6-0a0264839802',
        'banksampahbasmi',
        'Bank Sampah Basmi',
        7,
        'Gresik',
        '082242747389',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        4,
        '5e277a30-c357-4578-9742-90c77a16a438',
        'banksampah05',
        'Petugas Bank Sampah RT 05',
        5,
        'Gresik',
        '081252218959',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        5,
        '86c6e071-d37a-4bdc-804b-bfb814052db5',
        'banksampah01',
        'Petugas Bank Sampah RT 01',
        1,
        'Gresik',
        '081252218959',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        6,
        '992fced0-ccdb-4841-817e-78ed79ca5da6',
        'banksampah06',
        'Petugas Bank Sampah RT 06',
        6,
        'Gresik',
        '081252218959',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        7,
        'aa71a3bf-5118-43b7-8fb9-a52724f701ed',
        'banksampahmekarjaya',
        'Bank Sampah Mekar Jaya',
        2,
        'Gresik',
        '0987898789878',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        8,
        'b84e5147-1d77-4874-bb26-cfd4efb09931',
        'banksampah08',
        'Petugas Bank Sampah RT 08',
        8,
        'Gresik',
        '081252218959',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        9,
        'd52248a0-a74c-4f35-9157-8fdac2aa6c82',
        'banksampah04',
        'Petugas Bank Sampah RT 04',
        4,
        'Gresik',
        '081252218959',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Non-Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    ),
    (
        10,
        'e56fa14e-3642-46c2-8303-72e7068bec20',
        'banksampahmelatiputih',
        'Bank Sampah Melati Putih',
        3,
        'Gresik',
        '081252435804',
        3,
        2,
        'Disetujui',
        'Disetujui',
        'Tunai',
        '2026-05-06 14:54:32',
        '2026-05-06 14:54:32'
    );

-- --------------------------------------------------------
--
-- Table structure for table `user_geolocations`
--
CREATE TABLE
    `user_geolocations` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `amenity` varchar(255) NOT NULL,
        `house_number` varchar(255) DEFAULT NULL,
        `city` varchar(255) DEFAULT NULL,
        `county` varchar(255) DEFAULT NULL,
        `state` varchar(255) DEFAULT NULL,
        `country` varchar(255) NOT NULL DEFAULT 'Indonesia',
        `postal_code` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `user_logs`
--
CREATE TABLE
    `user_logs` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `action` varchar(255) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `device_agent` varchar(255) NOT NULL,
        `device` varchar(255) NOT NULL,
        `platform` varchar(255) NOT NULL,
        `type_platform` varchar(255) NOT NULL,
        `time_logs` time NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `user_logs`
--
INSERT INTO
    `user_logs` (
        `id`,
        `id_userdetail`,
        `action`,
        `ip_address`,
        `device_agent`,
        `device`,
        `platform`,
        `type_platform`,
        `time_logs`,
        `created_at`,
        `updated_at`
    )
VALUES
    (
        1,
        3,
        'LOGIN',
        '127.0.0.1',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
        'Macintosh',
        'OS X',
        'Desktop',
        '00:12:18',
        '2026-06-06 17:12:18',
        '2026-06-06 17:12:18'
    ),
    (
        2,
        3,
        'LOGOUT',
        '127.0.0.1',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
        'Macintosh',
        'OS X',
        'Desktop',
        '00:12:51',
        '2026-06-06 17:12:51',
        '2026-06-06 17:12:51'
    ),
    (
        3,
        2,
        'LOGIN',
        '127.0.0.1',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
        'Macintosh',
        'OS X',
        'Desktop',
        '00:14:13',
        '2026-06-06 17:14:13',
        '2026-06-06 17:14:13'
    ),
    (
        4,
        2,
        'LOGOUT',
        '127.0.0.1',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
        'Macintosh',
        'OS X',
        'Desktop',
        '00:14:29',
        '2026-06-06 17:14:29',
        '2026-06-06 17:14:29'
    );

-- --------------------------------------------------------
--
-- Table structure for table `user_queues`
--
CREATE TABLE
    `user_queues` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `id_jadwal` bigint (20) UNSIGNED NOT NULL,
        `queue_number` varchar(255) NOT NULL,
        `status` enum ('waiting', 'processing', 'finished', 'skipped') NOT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `user_transactions`
--
CREATE TABLE
    `user_transactions` (
        `id` bigint (20) UNSIGNED NOT NULL,
        `id_userdetail` bigint (20) UNSIGNED NOT NULL,
        `pencatatan_setoran_id` bigint (20) UNSIGNED NOT NULL,
        `bukti_pembayaran` varchar(255) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--
--
-- Indexes for table `banks`
--
ALTER TABLE `banks` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `banks_transfer_code_unique` (`transfer_code`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache` ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks` ADD PRIMARY KEY (`key`);

--
-- Indexes for table `document_archivers`
--
ALTER TABLE `document_archivers` ADD PRIMARY KEY (`id`),
ADD KEY `document_archivers_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `document_archivers_id_jadwal_foreign` (`id_jadwal`);

--
-- Indexes for table `evidence_archivers`
--
ALTER TABLE `evidence_archivers` ADD PRIMARY KEY (`id`),
ADD KEY `evidence_archivers_id_userdetail_foreign` (`id_userdetail`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `genders`
--
ALTER TABLE `genders` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal_pelaksanaan`
--
ALTER TABLE `jadwal_pelaksanaan` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `jadwal_pelaksanaan_tanggal_setoran_unique` (`tanggal_setoran`),
ADD KEY `jadwal_pelaksanaan_id_userdetail_foreign` (`id_userdetail`);

--
-- Indexes for table `janji_setors`
--
ALTER TABLE `janji_setors` ADD PRIMARY KEY (`id`),
ADD KEY `janji_setors_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `janji_setors_id_jadwal_foreign` (`id_jadwal`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs` ADD PRIMARY KEY (`id`),
ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kepengurusans`
--
ALTER TABLE `kepengurusans` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `kepengurusans_username_unique` (`userName`),
ADD KEY `kepengurusans_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `kepengurusans_id_gender_foreign` (`id_gender`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications` ADD PRIMARY KEY (`id`),
ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`);

--
-- Indexes for table `open_street_views`
--
ALTER TABLE `open_street_views` ADD PRIMARY KEY (`id`),
ADD KEY `open_street_views_id_geoloc_foreign` (`id_geoLoc`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens` ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pencatatan_setoran`
--
ALTER TABLE `pencatatan_setoran` ADD PRIMARY KEY (`id`),
ADD KEY `pencatatan_setoran_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `pencatatan_setoran_id_jadwal_foreign` (`id_jadwal`);

--
-- Indexes for table `pencatatan_setoran_items`
--
ALTER TABLE `pencatatan_setoran_items` ADD PRIMARY KEY (`id`),
ADD KEY `pencatatan_setoran_items_pencatatan_setoran_id_foreign` (`pencatatan_setoran_id`),
ADD KEY `pencatatan_setoran_items_sampah_id_foreign` (`sampah_id`);

--
-- Indexes for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions` ADD PRIMARY KEY (`id`),
ADD KEY `push_subscriptions_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rt_perumahan`
--
ALTER TABLE `rt_perumahan` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `rt_perumahan_rt_unique` (`RT`);

--
-- Indexes for table `sampah`
--
ALTER TABLE `sampah` ADD PRIMARY KEY (`id`),
ADD KEY `sampah_id_userdetail_foreign` (`id_userdetail`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions` ADD PRIMARY KEY (`id`),
ADD KEY `sessions_user_id_index` (`user_id`),
ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users` ADD UNIQUE KEY `users_id_unique` (`id`),
ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_bank`
--
ALTER TABLE `user_bank` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `user_bank_nomor_rekening_unique` (`nomor_rekening`),
ADD KEY `user_bank_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `user_bank_id_bank_foreign` (`id_bank`);

--
-- Indexes for table `user_bots`
--
ALTER TABLE `user_bots` ADD PRIMARY KEY (`id`),
ADD KEY `user_bots_id_userdetail_foreign` (`id_userdetail`);

--
-- Indexes for table `user_chats`
--
ALTER TABLE `user_chats` ADD PRIMARY KEY (`id`),
ADD KEY `user_chats_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `user_chats_sender_id_foreign` (`sender_id`);

--
-- Indexes for table `user_details`
--
ALTER TABLE `user_details` ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `user_details_username_unique` (`userName`),
ADD KEY `user_details_id_user_foreign` (`id_user`),
ADD KEY `user_details_id_rt_foreign` (`id_rt`),
ADD KEY `user_details_id_gender_foreign` (`id_gender`),
ADD KEY `user_details_id_roles_foreign` (`id_roles`);

--
-- Indexes for table `user_geolocations`
--
ALTER TABLE `user_geolocations` ADD PRIMARY KEY (`id`),
ADD KEY `user_geolocations_id_userdetail_foreign` (`id_userdetail`);

--
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs` ADD PRIMARY KEY (`id`),
ADD KEY `user_logs_id_userdetail_foreign` (`id_userdetail`);

--
-- Indexes for table `user_queues`
--
ALTER TABLE `user_queues` ADD PRIMARY KEY (`id`),
ADD KEY `user_queues_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `user_queues_id_jadwal_foreign` (`id_jadwal`);

--
-- Indexes for table `user_transactions`
--
ALTER TABLE `user_transactions` ADD PRIMARY KEY (`id`),
ADD KEY `user_transactions_id_userdetail_foreign` (`id_userdetail`),
ADD KEY `user_transactions_pencatatan_setoran_id_foreign` (`pencatatan_setoran_id`);

--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 8;

--
-- AUTO_INCREMENT for table `document_archivers`
--
ALTER TABLE `document_archivers` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evidence_archivers`
--
ALTER TABLE `evidence_archivers` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genders`
--
ALTER TABLE `genders` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT for table `jadwal_pelaksanaan`
--
ALTER TABLE `jadwal_pelaksanaan` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `janji_setors`
--
ALTER TABLE `janji_setors` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kepengurusans`
--
ALTER TABLE `kepengurusans` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations` MODIFY `id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 42;

--
-- AUTO_INCREMENT for table `open_street_views`
--
ALTER TABLE `open_street_views` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pencatatan_setoran`
--
ALTER TABLE `pencatatan_setoran` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pencatatan_setoran_items`
--
ALTER TABLE `pencatatan_setoran_items` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

--
-- AUTO_INCREMENT for table `rt_perumahan`
--
ALTER TABLE `rt_perumahan` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 9;

--
-- AUTO_INCREMENT for table `sampah`
--
ALTER TABLE `sampah` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_bank`
--
ALTER TABLE `user_bank` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_bots`
--
ALTER TABLE `user_bots` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_chats`
--
ALTER TABLE `user_chats` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_details`
--
ALTER TABLE `user_details` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 11;

--
-- AUTO_INCREMENT for table `user_geolocations`
--
ALTER TABLE `user_geolocations` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

--
-- AUTO_INCREMENT for table `user_queues`
--
ALTER TABLE `user_queues` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_transactions`
--
ALTER TABLE `user_transactions` MODIFY `id` bigint (20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--
--
-- Constraints for table `document_archivers`
--
ALTER TABLE `document_archivers` ADD CONSTRAINT `document_archivers_id_jadwal_foreign` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_pelaksanaan` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `document_archivers_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evidence_archivers`
--
ALTER TABLE `evidence_archivers` ADD CONSTRAINT `evidence_archivers_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_pelaksanaan`
--
ALTER TABLE `jadwal_pelaksanaan` ADD CONSTRAINT `jadwal_pelaksanaan_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `janji_setors`
--
ALTER TABLE `janji_setors` ADD CONSTRAINT `janji_setors_id_jadwal_foreign` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_pelaksanaan` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `janji_setors_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kepengurusans`
--
ALTER TABLE `kepengurusans` ADD CONSTRAINT `kepengurusans_id_gender_foreign` FOREIGN KEY (`id_gender`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `kepengurusans_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `open_street_views`
--
ALTER TABLE `open_street_views` ADD CONSTRAINT `open_street_views_id_geoloc_foreign` FOREIGN KEY (`id_geoLoc`) REFERENCES `user_geolocations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pencatatan_setoran`
--
ALTER TABLE `pencatatan_setoran` ADD CONSTRAINT `pencatatan_setoran_id_jadwal_foreign` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_pelaksanaan` (`id`),
ADD CONSTRAINT `pencatatan_setoran_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pencatatan_setoran_items`
--
ALTER TABLE `pencatatan_setoran_items` ADD CONSTRAINT `pencatatan_setoran_items_pencatatan_setoran_id_foreign` FOREIGN KEY (`pencatatan_setoran_id`) REFERENCES `pencatatan_setoran` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `pencatatan_setoran_items_sampah_id_foreign` FOREIGN KEY (`sampah_id`) REFERENCES `sampah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions` ADD CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sampah`
--
ALTER TABLE `sampah` ADD CONSTRAINT `sampah_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_bank`
--
ALTER TABLE `user_bank` ADD CONSTRAINT `user_bank_id_bank_foreign` FOREIGN KEY (`id_bank`) REFERENCES `banks` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `user_bank_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_bots`
--
ALTER TABLE `user_bots` ADD CONSTRAINT `user_bots_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_chats`
--
ALTER TABLE `user_chats` ADD CONSTRAINT `user_chats_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `user_chats_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_details`
--
ALTER TABLE `user_details` ADD CONSTRAINT `user_details_id_gender_foreign` FOREIGN KEY (`id_gender`) REFERENCES `genders` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `user_details_id_roles_foreign` FOREIGN KEY (`id_roles`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `user_details_id_rt_foreign` FOREIGN KEY (`id_rt`) REFERENCES `rt_perumahan` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `user_details_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_geolocations`
--
ALTER TABLE `user_geolocations` ADD CONSTRAINT `user_geolocations_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_logs`
--
ALTER TABLE `user_logs` ADD CONSTRAINT `user_logs_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_queues`
--
ALTER TABLE `user_queues` ADD CONSTRAINT `user_queues_id_jadwal_foreign` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_pelaksanaan` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `user_queues_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_transactions`
--
ALTER TABLE `user_transactions` ADD CONSTRAINT `user_transactions_id_userdetail_foreign` FOREIGN KEY (`id_userdetail`) REFERENCES `user_details` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `user_transactions_pencatatan_setoran_id_foreign` FOREIGN KEY (`pencatatan_setoran_id`) REFERENCES `pencatatan_setoran` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;