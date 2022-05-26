# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 8.0.27-18)
# Database: shop
# Generation Time: 2022-05-26 13:54:47 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `position` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id`, `name`, `position`)
VALUES
	(1,'Bags & Luggage',90),
	(2,'Bed & Bath',70),
	(4,'Decorative Accents',50),
	(6,'Electronics',80),
	(7,'Eyewear',60),
	(8,'Men\'s Clothing',20),
	(9,'Women\'s Clothing',10),
	(10,'Jewelry',40),
	(12,'Shoes',30);

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `bill_name` varchar(255) NOT NULL DEFAULT '',
  `bill_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `bill_phone` varchar(255) NOT NULL DEFAULT '',
  `bill_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `bill_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `bill_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ship_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ship_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ship_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ship_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ship_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `ship_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `no_items` tinyint unsigned NOT NULL DEFAULT '0',
  `total` double(13,2) NOT NULL DEFAULT '0.00',
  `status` enum('new','processing','complete','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'new',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;

INSERT INTO `order` (`id`, `user_id`, `bill_name`, `bill_email`, `bill_phone`, `bill_address`, `bill_city`, `bill_state`, `ship_name`, `ship_email`, `ship_phone`, `ship_address`, `ship_city`, `ship_state`, `no_items`, `total`, `status`, `created`)
VALUES
	(5,1,'John Doe','john.doe@example.com','1234567890','2601 Mission St.','San Francisco','CA','John Doe','john.doe@example.com','1234567890','2601 Mission St.','San Francisco','CA',3,1760.00,'canceled','2022-05-19 19:30:33'),
	(6,1,'Marion Doe','marion.doe@example.com','2345678901','1900 Mission St.','Chicago','CA','Marion Doe','marion.doe@example.com','2345678901','1900 Mission St.','Chicago','CA',3,510.00,'complete','2022-05-25 10:33:12'),
	(7,1,'John Smith','john.smith@example.com','3456789012','2500 Mission St.','New York','CA','Marion Smith','marion.smith@example.com','4567890123','2602 Mission St.','San Francisco','CA',2,285.00,'processing','2022-05-25 16:01:58'),
	(8,1,'Marion Smith','marion.smith@example.com','4567890123','2602 Mission St.','San Francisco','CA','John Smith','john.smith@example.com','3456789012','2500 Mission St.','New York','CA',1,455.00,'new','2022-05-25 16:10:46');

/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table order_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `price` double(13,2) NOT NULL DEFAULT '0.00',
  `qty` tinyint unsigned NOT NULL DEFAULT '1',
  `total` double(13,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `name`, `price`, `qty`, `total`)
VALUES
	(7,5,14,'Dorian Perforated Oxford',410.00,1,410.00),
	(8,5,20,'Broad St. Flapover Briefcase',570.00,2,1140.00),
	(9,5,29,'Shay Printed Pillow',210.00,1,210.00),
	(10,6,2,'Slim fit Dobby Oxford Shirt',175.00,2,350.00),
	(11,6,34,'Modern Murray Ceramic Vase',135.00,1,135.00),
	(12,6,26,'Bath Minerals and Salt',25.00,1,25.00),
	(13,7,50,'Blue Horizons Bracelets',55.00,2,110.00),
	(14,7,2,'Slim fit Dobby Oxford Shirt',175.00,1,175.00),
	(15,8,3,'Linen Blazer',455.00,1,455.00);

/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `price` double(13,2) DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;

INSERT INTO `product` (`id`, `name`, `price`, `image`, `description`, `category_id`)
VALUES
	(1,'French Cuff Cotton Twill Oxford',190.00,'/m/s/msj000t_1.jpg','Made with wrinkle resistant cotton twill, this French-cuffed luxury dress shirt is perfect for Business Class frequent flyers.',8),
	(2,'Slim fit Dobby Oxford Shirt',175.00,'/m/s/msj003t_1.jpg','A bold hue and understated dobby detail bring refined nuance to this modern dress shirt. ',8),
	(3,'Linen Blazer',455.00,'/m/s/msj012t_1.jpg','In airy lightweight linen, this blazer is classic tailoring with a warm weather twist.',8),
	(4,'The Essential Boot Cut Jean',140.00,'/m/p/mpd006t_1.jpg','The new standard in denim, this jean is the rightful favorite among our designers. Made from lightly distressed denim to achieve that perfectly broken-in feel.',8),
	(5,'NoLIta Cami',150.00,'/w/b/wbk002t.jpg','Cut from tissue-weight silk crepe de chine, this airy style features a ruched neckline with tie and an unfinished hem for a contrastinly rugged feel. Compliment yours with skinny jeans.',9),
	(6,'Delancy Cardigan Sweater',275.00,'/w/b/wbk006t_1.jpg','Refresh your knitwear collection with our silk blend top. Layer over a bold hue for maximum contrast.',9),
	(7,'Racer Back Maxi Dress',280.00,'/w/s/wsd005t_1.jpg','This classic maxi dress drapes beautifully throughout body and sweeps in a light A-line to the floor. Keep a casual chic look by pairing with a jean jacket or go glam with a statement necklace.',9),
	(8,'Sheath',305.00,'/w/s/wsd008t_1.jpg','Our feminine wool-blend frock transitions seamlessly from day to night. We suggest wearing with classic heels and a standout strand necklace.',9),
	(9,'Aviator Sunglasses',295.00,'/a/c/ace000a_1.jpg','A timeless accessory staple, the unmistakable teardrop lenses of our Aviator sunglasses appeal to everyone from suits to rock stars to citizens of the world.',7),
	(10,'Jackie O Round Sunglasses',295.00,'/a/c/ace001_1.jpg','These distinct, feminine frames balance a classic Jackie-O styling with a modern look. ',7),
	(11,'Retro Chic Eyeglasses',295.00,'/a/c/ace002a_1.jpg','Madison Island Retro chic lenses are stylish on both men and women.',7),
	(12,'Barclay d\'Orsay pump, Nude',390.00,'/a/w/aws000a_1.jpg','Step forward with a fresh and neutral hued finish.',12),
	(13,'Ann Ankle Boot',470.00,'/a/w/aws005a_1.jpg','A stylish companion to your LBD or skinny jeans.',12),
	(14,'Dorian Perforated Oxford',410.00,'/a/m/ams000a_1.jpg','Crafted from premium polished leather, unrivaled in design.',12),
	(15,'Wingtip Cognac Oxford',375.00,'/a/m/ams005a_1.jpg','Classic cognac wingtip with a modern silhouette--it only gets better with wear.',12),
	(16,'Suede Loafer, Navy',310.00,'/a/m/ams010a_1.jpg','Make a statement, even when relaxed and casual. The classic loafer design elevates even the most dressed down look.',12),
	(17,'Isla Crossbody Handbag',290.00,'/a/b/abl000_4.jpg','Form follows function with this decidedly chic mini bag. ',1),
	(18,'Florentine Satchel Handbag',625.00,'/a/b/abl001_1.jpg','Carry it all with the spacious and stylishFlorentine Satchel.',1),
	(19,'Flatiron Tablet Sleeve',150.00,'/a/b/abl002b_1.jpg','Protect your tablet with our minimal tablet sleeve.',1),
	(20,'Broad St. Flapover Briefcase',570.00,'/a/b/abl003b_1.jpg','Make an impression at overseas business meetings.',1),
	(21,'Houston Travel Wallet',210.00,'/a/b/abl004a_1.jpg','Just the right size for your passport, tickets and other essentials, this leather wallet is the perfect travel carry all.',1),
	(22,'Roller Suitcase',650.00,'/a/b/abl005a_1.jpg','No more baggage claim mixups! Our Roller in bold cobalt blue is sure to standout in a sea of suitcases.',1),
	(23,'Classic Hardshell Suitcase 21\"',650.00,'/a/b/abl0006a_2.jpg','Some like it classic. This luggage provides ample room for multiday trips.',1),
	(24,'Classic Hardshell Suitcase 29\"',750.00,'/a/b/abl0006a_3.jpg','Some like it classic. This luggage provides ample room for multiday trips.',1),
	(25,'Body Wash with Lemon Flower Extract and Aloe Vera',28.00,'/h/d/hdb000_1.jpg','A rich lather, infused with lemon flower awakens the senses.',2),
	(26,'Bath Minerals and Salt',25.00,'/h/d/hdb001_2.jpg','Just what your body needs after a long day on the road. Soak, relax and reenergize with 100% natural Dead Sea salt crystals and minerals.',2),
	(27,'Shea Enfused Hydrating Body Lotion',28.00,'/h/d/hdb002_1.jpg','Experience the perfect escape with this irresistable blend of milk extract and shea.',2),
	(28,'Titian Raw Silk Pillow',125.00,'/h/d/hdb005_1.jpg','An exquisite home accent, our bazaar inspired raw silk square pillow is a statement in luxury. Interior pillow included.',2),
	(29,'Shay Printed Pillow',210.00,'/h/d/hdb006_1.jpg','A distinctive printed pillow that fills any room with classic appeal.',2),
	(30,'Carnegie Alpaca Throw',275.00,'/h/d/hdb007_1.jpg','A luxuriously soft throw made of long-fiber lambs wool woven into a Chevron twill.',2),
	(31,'Park Row Throw',240.00,'/h/d/hdb008_1.jpg','A rustic wool blend leaves our Park Row Throw feeling lofty and warm. Packs perfectly into carry-ons.',2),
	(32,'Gramercy Throw',275.00,'/h/d/hdb009_1.jpg','Wrap yourself in this incredibly soft and luxurious blanket for all climate comfort. ',2),
	(33,'Herald Glass Vase',110.00,'/h/d/hdd000_1.jpg','The uniquely shaped Herand Glass Vase packs easily and adds instant impact.',4),
	(34,'Modern Murray Ceramic Vase',135.00,'/h/d/hdd001_1.jpg','Modern, edgy, distinct. Choose from two colors.',4),
	(35,'Modern Murray Ceramic Vase',135.00,'/h/d/hdd002_1.jpg','Modern, edgy, distinct. Choose from two colors.',4),
	(36,'Stone Salt and Pepper Shakers',65.00,'/h/d/hdd004_1.jpg','A subtle nod to Old World antiquity.',4),
	(37,'Fragrance Diffuser Reeds',75.00,'/h/d/hdd005_1.jpg','A clean and effective delivery of continuous flameless fragrance to enhance your home.',4),
	(38,'Geometric Candle Holders',90.00,'/h/d/hdd006_1.jpg','A simple and stylish way to add warmth and dimension to any room. Perfect for gifting.',4),
	(39,'Madison LX2200',425.00,'/h/d/hde001t_2.jpg','The compact travel friendly solution for sightseers.',6),
	(40,'Madison RX3400',715.00,'/h/d/hde003a_2.jpg','For budding photo connoisseurs.',6),
	(41,'16GB Memory Card',30.00,'/h/d/hde004__1.jpg','Keeping all your travel memories compact. 16GB.',6),
	(42,'8GB Memory Card',20.00,'/h/d/hde005_.jpg','Keeping all your travel memories compact. 8GB.',6),
	(43,'Large Camera Bag',120.00,'/h/d/hde006t.jpg','Keep your camera safe and secure in our Large Camera case.',6),
	(44,'Madison Earbuds',35.00,'/h/d/hde010_1.jpg','Why not play the Amelie Soundtrack while parading through Parisian rues? Madison earbuds deliver crisp clear sound with minimal distortion.',6),
	(45,'Madison Overear Headphones',125.00,'/h/d/hde011_1.jpg','Escape the sleepless city buzz with robust sound and aggressive noise cancellation.',6),
	(46,'Madison 8GB Digital Media Player',150.00,'/h/d/hde012_3.jpg','Expidite a long flight by getting into the groove with our plug and play mp3 player. Download movies, pictures or up to 3000 songs with the included USB cable.',6),
	(47,'Compact mp3 Player',40.00,'/h/d/hde013__1.jpg','Save space without sacrificing sound quality.',6),
	(48,'Khaki Bowery Chino Pants',140.00,'/m/p/mpd000t_1.jpg','The slim and trim Bowery is a wear-to-work pant you\'ll actually want to wear. A clean style in our crisp, compact cotton twill, it\'s perfectly polished (but also comfortable enough for hanging out after hours).',8),
	(49,'Classic Hardshell Suitcase 19\"',600.00,'/a/b/abl0006a_4.jpg','Some like it classic. This luggage provides ample room for multiday trips.',1),
	(50,'Blue Horizons Bracelets',55.00,'/a/c/acj006_2.jpg','Add a pop of color with these handmade bangles from India.',10),
	(51,'Pearl Stud Earrings',110.00,'/a/c/acj003_2.jpg','Prim and demure, pearl studs are a cross cultural symbol of style and refinement.',10),
	(52,'Swing Time Earrings',75.00,'/a/c/acj004_2.jpg','Artisans from nonprofit Comite Artisanal Haitien in Port-au-Prince fashion these tasteful earrings from shaped horn. Each pair possesses its own unique natural beauty.',10),
	(53,'Silver Desert Necklace',210.00,'/a/c/acj000_2.jpg','Wear your passport by adding an edgy and artistic statement necklace. Ethnic design on hand-hammered and chiseled silver.',10),
	(54,'Swiss Movement Sports Watch',500.00,'/a/c/acj005_2.jpg','A traditional timepiece with edgy detailing.',10);

/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `registered` datetime DEFAULT CURRENT_TIMESTAMP,
  `comment` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `email`, `phone`, `name`, `password`, `admin`, `registered`, `comment`)
VALUES
	(1,'admin@example.com','1234566780','John Admin Doe ','8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',1,'2022-05-01 14:23:08','Initial password: admin'),
	(6,'visitor@example.com','2345667801','Marion Visitor Smith','5f14f9e6d80f802a65269804f2552ef9889f2c7ccec5067214e58a1e48e0b3ff',0,'2022-05-07 13:00:00','Initial password: visitor');

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
