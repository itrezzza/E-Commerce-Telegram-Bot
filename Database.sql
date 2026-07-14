-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 20, 2024 at 10:30 PM
-- Server version: 8.0.31
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `E-Commerce TelBot`
--

-- --------------------------------------------------------

--
-- Table structure for table `sp_admins`
--

DROP TABLE IF EXISTS `sp_admins`;
CREATE TABLE IF NOT EXISTS `sp_admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sp_admins`
--

INSERT INTO `sp_admins` (`id`, `username`, `password`) VALUES
(3, 'admin', '$2y$10$DW7HUhh3IQb6otFoeDl.dO1zGmgCMsUv.94YazdP4hmPmWuSkDtDy');

-- --------------------------------------------------------

--
-- Table structure for table `sp_cats`
--

DROP TABLE IF EXISTS `sp_cats`;
CREATE TABLE IF NOT EXISTS `sp_cats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sp_files`
--

DROP TABLE IF EXISTS `sp_files`;
CREATE TABLE IF NOT EXISTS `sp_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  `catid` int NOT NULL,
  `fileurl` mediumtext NOT NULL,
  `type` varchar(10) NOT NULL,
  `price` int NOT NULL,
  `status` int NOT NULL,
  `demo` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `views` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sp_options`
--

DROP TABLE IF EXISTS `sp_options`;
CREATE TABLE IF NOT EXISTS `sp_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sp_options`
--

INSERT INTO `sp_options` (`id`, `name`, `description`, `value`) VALUES
(1, 'home', 'دکمه منوی اصلی', 'منوی اصلی 🏠'),
(2, 'shop', 'دکمه فروشگاه', '🎯 ورود به فروشگاه'),
(3, 'vip_member', 'دکمه عضویت ویژه', '🌟 عضویت ویژه'),
(4, 'my_transactions', 'دکمه تراکنش ها', '🔖 تراکنش های من'),
(5, 'send_ticket', 'دکمه تیکت', '📨 ارسال تیکت / درخواست محصول'),
(6, 'phone_verefication', 'دکمه ارسال شماره', '☎️ ارسال شماره همراه'),
(7, 'welcome_msg', 'پیام خوش آمدگویی', 'به فروشگاه وردپرس ایکس خوش آمدید 🎉\r\n\r\n🛍 ربات وردپرس ایکس جهت دریافت محصولات کانال وردپرس ایکس و همچنین پشتیبانی کاربران ایجاد شده است.\r\n\r\nاز منوی پایین انتخاب کنید 👇🏻'),
(8, 'requst_phone_msg', 'پیام درخواست تایید شماره', '⚠️ برای استفاده از ربات ابتدا باید شماره همراه خود را تایید کنید. ⚠️\r\n\r\n📌 نکات:\r\n1- از ارسال شماره دیگران خودداری فرمایید.\r\n2- از شماره مجازی  برای تایید شماره خود استفاده نکنید.(تنها کاربران ایرانی قادر به استفاده از ربات هستند)\r\n\r\n❕ چرا باید شماره خود را ارسال کنم؟\r\nبا توجه به متصل بودن ربات به درگاه پرداخت اینترنتی، به منظور جلوگیری از هرگونه سوء استفاده احتمالی از کارت های بانکی (فیشینگ)، کاربر باید قبل از استفاده از ربات شماره خود را تایید کند تا با اطلاعات پرداخت کننده تطبیق داده شود.'),
(9, 'phone_verified', 'پیام تایید شدن شماره', 'شماره موبایل شما با موفقیت تایید شد ✅'),
(10, 'phone_cheating', 'پیام زمانی که شماره اشتباه شیر شود', '❌  لطفا از اشتراک شماره ی دیگران خودداری کنید ❌'),
(11, 'wrong_format', 'پیام شماره غیر ایران', '❌ شماره ارسالی صحیح نمیباشد و یا متعلق به ایران نمیباشد.\r\n🇮🇷 فقط شماره کاربران ایرانی مورد قبول میباشد\r\n\r\n.Only Iranian numbers are currently supported '),
(12, 'cats_msg', 'پیام دسته بندی ها', '🎉 به فروشگاه وردپرس ایکس خوش آمدید\r\nدر این بخش می توانید دسته بندی مورد نظر خود را انتخاب کنید و محصولات هر دسته بندی را مشاهده کنید.\r\n\r\n  لطفا یکی از دسته بندی های زیر را انتخاب کنید👇'),
(13, 'empty_cat', 'پیام دسته بندی خالی', '❗️ محصولی در این دسته بندی وجود ندارد!'),
(14, 'products_list_waiting', 'پیام انتظار دریافت لیست محصولات', 'در حال دریافت محصولات...'),
(15, 'choose_product', 'پیام انتخاب محصول', '  لطفا یکی از محصولات زیر را انتخاب کنید👇'),
(16, 'product_info_waiting', 'پیام انتظار دریافت اطلاعات محصول', 'در حال دریافت اطلاعات محصول ...'),
(17, 'free_msg', 'پیام دریافت رایگان فایل', '🔓 این محصول رایگان است و می توانید آنرا دانلود کنید.'),
(18, 'vip_msg', 'پیام پیشنهاد خرید اشتراک', '🎖 نکته : با خرید اشتراک ویژه می توانید همه محصولات ربات را بصورت رایگان دریافت کنید.'),
(19, 'dl_btn', 'دکمه دانلود فایل', '📥 دریافت فایل محصول'),
(20, 'pay_btn', 'دکمه پرداخت', '💳 پرداخت آنلاین'),
(21, 'demo_btn', 'دکمه پیشنمایش', '🔗 پیشنمایش محصول '),
(22, 'vip_remaining', 'پیام روز های باقیمانده اشتراک --- (متغیر ها = [vip_days])', '🌟 اشتراک ویژه فعال دارید.\r\n❕ برای اطلاع از جزئیات حساب خود، دکمه حساب کاربری را انتخاب کنید.\r\n⏳ تعداد روز های باقیمانده از اشتراک : [vip_days] روز\r\n'),
(23, 'allowed_vip_msg', 'پیام اشتراک ویژه / اجازه ی دانلود', '🌟 اشتراک ویژه دارید و میتوانید این محصول را دریافت کنید.'),
(24, 'sending_file', 'پیام ارسال فایل', 'در حال ارسال فایل ...'),
(25, 'vip_plans', 'پیام پلن های اشتراک ویژه', '🌟  با خرید اشتراک ویژه، می توانید تمام محصولات را به صورت رایگان دریافت کنید.\r\n💳 پرداخت به صورت اتوماتیک انجام شده و بلافاصله پس از پرداخت، پلن مورد نظر روی حساب شما فعال خواهد شد.\r\n\r\n  لطفا یکی از پلن های زیر را انتخاب کنید 👇'),
(26, 'orders_msg', 'متن سفارش ها\r\n( متغیر ها =\r\n[product_name]،\r\n[product_link]،\r\n[order_price]،\r\n[order_transcode]،\r\n[order_date] )', '📦 نام محصول: <a href=\"[product_link]\">[product_name]</a>\r\n💵 قیمت : [order_price] تومان\r\n📝 شماره تراکنش: [order_transcode]\r\n📅 تاریخ تراکنش: [order_date]'),
(27, 'already_purchased_product', 'پیام محصول خریداری شده', '🛒 شما این محصول را از قبل خریداری کرده اید و میتوانید آنرا دانلود کنید'),
(28, 'empty_transactions', 'پیام خالی بودن سفارشات', 'تاکنون هیچ تراکنش ثبت شده ای ندارید 📑'),
(29, 'product_info', 'توضیحات محصول\r\n(متغیر ها =[name]،[desc]،[price]،[footer_msg])', '🔻نام محصول:\r\n[name]\r\n\r\n📃توضیحات :\r\n[desc]\r\n\r\n==================\r\n💰 قیمت : [price]\r\n==================\r\n[footer_msg]'),
(30, 'ticket_msg', 'پیام قبل از ارسال تیکت', 'لطفا پیام خود را بنویسید و ارسال کنید. ✍️'),
(31, 'ticket_sent', 'پیام تیکت ارسال شد', 'پیام شما با موفقیت ارسال شد ✅ \r\nدر صورت نیاز به پاسخگویی حداکثر ظرف 24 ساعت، تیکت شما پاسخ داده خواهد شد 📩'),
(32, 'new_ticket', 'پیام تیکت جدید (برای ادمین)', '📩 تیکت جدیدی از سوی کاربران ارسال شده است.\r\n🛟 داشبورد مدیریتی خود را بررسی کنید'),
(33, 'search_products', 'دکمه ی جستجوی محصولات', '🔍 جستجوی محصولات'),
(34, 'no_search_result', 'پیام پیدا نشدن محصول در جستجو', '❌ محصولی یافت نشد❌\r\nسعی کنید کلید واژه ی جستجوی خود را تغییر دهید'),
(35, 'search_description', 'توضیحات زیر نتایج جستجو', '🔸 برای دریافت اطلاعات محصول مورد نظر، روی محصول کلیک کرده و ربات را استارت کنید.'),
(36, 'empty_cats', 'پیام خالی بودن دسته بندی ها', 'هیج دسته بندی وجود ندارد. نسبت به اضافه کردن دسته بندی اقدام کنید'),
(37, 'account_info', 'اطلاعات حساب کاربری', '🛖 حساب کاربری شما در ربات وردپرس ایکس :\r\n👤 نام = [name]\r\n🔢 شناسه عددی = [userid]\r\n☎️ شماره تلفن = [phone]\r\n💎 پلن اشتراک = [vip_plan]\r\n💲 مجموع تراکنش ها = [total_orders] تومان\r\n\r\n⚠️ در صورت نیاز به پشتیبانی، از طریق دکمه تیکت با ما در تماس باشید.'),
(38, 'vip_purchased_successfully', 'پیام خرید موفق آمیز اشتراک ویژه', '✅ عضویت VIP شما با موفقیت فعال شد .\r\n📝 شماره تراکنش : [refid]\r\n💎 نام پلن : [plan_name]\r\n💵 مبلغ تراکنش : [plan_price] تومان'),
(39, 'product_purchased_successfully', 'پیام خرید موفق محصول', '✅تراکنش شما با موفقیت انجام شد\r\n📝 شماره تراکنش : [refid]\r\n📦 نام محصول : [name]\r\n💵 مبلغ تراکنش : [price] تومان'),
(40, 'no_popular_product', 'پیام خالی بودن محصولت محبوب', 'محصول محبوبی وجود ندارد ☹️'),
(41, 'phone_not_verified', 'متن عدم تایید شماره همراه(بخش حساب کاربری)', 'شماره موبایل تایید نشده ☹️'),
(42, 'no_vip_plan', 'عدم وجود اشتراک (بخش حساب کاربری)', 'اشتراک فعال ندارید ☹️'),
(43, 'search_text', 'پیام جستجوی محصول', 'نام محصول مورد نظر خود را وارد کنید ✍️'),
(44, 'cat_list_waiting', 'پیام انتظار دریافت دسته بندی ها', 'درحال دریافت دسته بندی ها ...'),
(45, 'back_to_cats', 'دکمه دسته بندی ها', '▶️ بازگشت به دسته بندی ها'),
(46, 'popular_products', 'دکمه محصولات محبوب', '❤️ محبوب ترین محصولات '),
(47, 'popular_products_text', 'متن پیام محصولات محبوب', 'محبوب ترین محصولات به شرح زیر می باشند\r\n  لطفا یکی از محصولات زیر را انتخاب کنید👇'),
(48, 'ad', 'تبلیغات (زیر فایل)', '🚀 کانال وردپرس ایکس '),
(49, 'ad_link', 'لینک تبلیغات ', 'https://t.me/wordpressx'),
(50, 'account', 'دکمه حساب کاربری', '👤  حساب کاربری'),
(51, 'main_menu_msg', 'پیام بازگشت به منوی اصلی', 'به منوی اصلی خوش آمدید 🙂\r\nیکی از دکمه های زیر را انتخاب کنید 👇🏻'),
(52, 'latest_products', 'دکمه محصولات جدید', '🏆 جدیدترین محصولات'),
(53, 'latest_products_text', 'متن پیام محصولات جدید', '🏆 جدید ترین محصولات به شرح زیر می باشند\r\n  لطفا یکی از محصولات زیر را انتخاب کنید👇'),
(54, 'no_latest_product', 'پیام خالی بودن محصولات جدید', 'محصول جدیدی وجود ندارد ☹️'),
(55, 'exit', 'دکمه ی بستن پیام', '❌ بستن ');

-- --------------------------------------------------------

--
-- Table structure for table `sp_orders`
--

DROP TABLE IF EXISTS `sp_orders`;
CREATE TABLE IF NOT EXISTS `sp_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `productid` int NOT NULL,
  `userid` bigint NOT NULL,
  `price` int NOT NULL,
  `transcode` varchar(255) NOT NULL,
  `status` int NOT NULL,
  `type` varchar(5) NOT NULL,
  `date` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sp_tickets`
--

DROP TABLE IF EXISTS `sp_tickets`;
CREATE TABLE IF NOT EXISTS `sp_tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `msg` text NOT NULL,
  `date` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sp_users`
--

DROP TABLE IF EXISTS `sp_users`;
CREATE TABLE IF NOT EXISTS `sp_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userid` bigint NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `vip_date` varchar(30) NOT NULL,
  `vip_plan` varchar(50) NOT NULL,
  `vip_refid` int NOT NULL,
  `verified` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sp_vip_plans`
--

DROP TABLE IF EXISTS `sp_vip_plans`;
CREATE TABLE IF NOT EXISTS `sp_vip_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `price` int NOT NULL,
  `days` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sp_vip_plans`
--

INSERT INTO `sp_vip_plans` (`id`, `name`, `price`, `days`) VALUES
(1, 'اشتراک 10 روزه', 299000, 10),
(2, 'اشتراک 30 روزه', 429000, 30),
(3, 'اشتراک 90 روزه', 800000, 90);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
