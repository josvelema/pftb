CREATE DATABASE IF NOT EXISTS `phpnewsletter` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phpnewsletter`;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `role` enum('Admin','Member') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `accounts` (`id`, `email`, `password`, `display_name`, `role`) VALUES
(1, 'admin@example.com', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'Admin', 'Admin');

CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Paused','Completed','Cancelled') NOT NULL,
  `newsletter_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `campaign_clicks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `campaign_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `status` enum('Queued','Completed','Cancelled','Failed') NOT NULL,
  `fail_text` varchar(255) NOT NULL DEFAULT '',
  `update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `campaign_opens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `campaign_unsubscribes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `newsletters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `last_scheduled` datetime DEFAULT NULL,
  `submit_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `newsletters` (`id`, `title`, `content`, `last_scheduled`, `submit_date`) VALUES
(1, 'General Example', '<!DOCTYPE html>\r\n<html>\r\n	<head>\r\n		<meta charset=\"utf-8\">\r\n		<meta name=\"viewport\" content=\"width=device-width,minimum-scale=1\">\r\n		<title>Newsletter Title</title>\r\n	</head>\r\n	<body style=\"background-color:#eaebee;font-family:-apple-system, BlinkMacSystemFont, \'segoe ui\', roboto, oxygen, ubuntu, cantarell, \'fira sans\', \'droid sans\', \'helvetica neue\', Arial, sans-serif;box-sizing:border-box;font-size:16px;padding:30px;\">\r\n		<!-- TEMPLATE WRAPPER -->\r\n        <div style=\"background-color:#fff;box-sizing:border-box;font-size:16px;max-width:600px;margin: 30px auto;box-shadow:0 0 4px 0 rgba(0,0,0,0.03);padding-bottom:30px;\">\r\n			<!-- HEADER -->\r\n            <div style=\"display:flex;align-items: center;padding:40px;box-sizing:border-box;color:#636468;background-color:#f9fafc;margin:0;\">\r\n                <!-- TITLE -->\r\n                <h1 style=\"padding:0;font-size:22px;margin:0;flex:1;width:100%;\">Newsletter Title</h1>\r\n                <!-- DATE -->\r\n                <div style=\"font-size:16px;color:#909196;text-align:right;width:100%;\">January, 2022</div>\r\n            </div>\r\n            <!-- SECTION HEADING -->\r\n			<h2 style=\"padding:20px 40px;margin:0;color:#6e6f74;box-sizing:border-box;font-size:20px;\">Section 1</h2>\r\n            <!-- SECTION PARAGRAPH -->\r\n            <p style=\"margin:10px 40px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ornare lectus sed lorem placerat condimentum. Nam aliquam viverra libero sed malesuada. Nunc in ligula est. Curabitur eu mattis purus, quis semper lacus. Cras rutrum pellentesque purus et scelerisque. Aliquam feugiat vehicula nulla, sit amet mollis mauris gravida vitae. Sed sit amet erat ac nulla feugiat viverra pretium nec felis.</p>\r\n            <!-- SECTION PARAGRAPH -->\r\n            <p style=\"margin:10px 40px;\">Nunc maximus tincidunt magna, eget placerat felis bibendum ut. Curabitur mollis neque eget vestibulum vulputate. Proin fermentum eros arcu, vel efficitur ipsum efficitur id. Nunc fringilla, nulla et faucibus pulvinar, arcu neque bibendum felis, eu sodales elit dolor at lacus. Nullam eget feugiat mauris. Morbi sed nunc nibh. Quisque sit amet justo elit.</p>\r\n            <!-- SECTION HEADING -->\r\n            <h2 style=\"padding:20px 40px;margin:0;color:#6e6f74;box-sizing:border-box;font-size:20px;\">Section 2</h2>\r\n            <!-- PARAGRAPH & IMAGE -->\r\n            <div style=\"display:flex;\">\r\n                <!-- SECTION PARAGRAPH -->\r\n                <p style=\"margin:10px 40px;\">Praesent at sapien pretium, placerat magna sed, ornare sapien. Proin pharetra, libero sit amet pharetra congue, libero diam venenatis lacus, id rhoncus eros nulla non velit. Mauris in vehicula tortor, mattis interdum risus. Sed molestie, enim sit amet dignissim volutpat, neque risus facilisis massa, non molestie ex ipsum nec leo. Nam volutpat eros in mollis suscipit.</p>\r\n                <!-- SECTION IMAGE -->\r\n                <img src=\"https://via.placeholder.com/300x280\" width=\"300\" height=\"280\" alt=\"\">\r\n            </div>\r\n            <!-- READ MORE LINK -->\r\n            <a href=\"%click_link%http://example.com\" style=\"display:inline-block;background-color:#2b7fc4;border-radius:4px;padding:12px 15px;text-decoration:none;color:#fff;font-weight:500;font-size:14px;margin:20px 40px;\">Read More</a>\r\n\r\n			%open_tracking_code%\r\n		</div>\r\n        <!-- UNSUBSCRIBE LINK -->\r\n        <p style=\"font-size:14px;text-align:center;color:#636468;margin:30px 0;\">If you no longer like to hear from us, please <a href=\"%unsubscribe_link%\" style=\"font-size:14px;color:#636468;\">click here to unsubscribe</a>.</p>\r\n	</body>\r\n</html>', NULL, '2022-07-21 22:20:16'),
(2, 'Product Example', '<!DOCTYPE html>\r\n<html>\r\n	<head>\r\n		<meta charset=\"utf-8\">\r\n		<meta name=\"viewport\" content=\"width=device-width,minimum-scale=1\">\r\n		<title>Newsletter Title</title>\r\n	</head>\r\n	<body style=\"background-color:#eaebee;font-family:-apple-system, BlinkMacSystemFont, \'segoe ui\', roboto, oxygen, ubuntu, cantarell, \'fira sans\', \'droid sans\', \'helvetica neue\', Arial, sans-serif;box-sizing:border-box;font-size:16px;padding:30px;\">\r\n		<!-- TEMPLATE WRAPPER -->\r\n        <div style=\"background-color:#fff;box-sizing:border-box;font-size:16px;max-width:600px;margin: 30px auto;box-shadow:0 0 4px 0 rgba(0,0,0,0.03);padding-bottom:30px;\">\r\n			<!-- HEADER -->\r\n            <div style=\"display:flex;align-items: center;padding:40px;box-sizing:border-box;color:#636468;background-color:#f9fafc;margin:0;\">\r\n                <!-- TITLE -->\r\n                <h1 style=\"padding:0;font-size:22px;margin:0;flex:1;width:100%;\">Newsletter Title</h1>\r\n                <!-- DATE -->\r\n                <div style=\"font-size:16px;color:#909196;text-align:right;width:100%;\">January, 2022</div>\r\n            </div>\r\n            <!-- PARAGRAPH & IMAGE -->\r\n            <div style=\"display:flex;margin:30px 40px;\">\r\n                <!-- SECTION IMAGE -->\r\n                <img src=\"https://via.placeholder.com/250x250\" width=\"250\" height=\"250\" alt=\"\">\r\n                <!-- SECTION PARAGRAPH -->\r\n                <div style=\"margin-right:15px;padding:0 40px;\">\r\n                    <!-- PRODUCT NAME -->\r\n                    <h2 style=\"margin:0;color:#6e6f74;box-sizing:border-box;font-size:20px;padding-bottom:20px;\">Product Name</h2>\r\n                    <!-- PRODUCT PRICE -->\r\n                    <span style=\"font-size:20px;color:#909197;padding-right:5px;\">$14.99</span>\r\n                    <!-- PRODUCT RRP PRICE -->\r\n                    <span style=\"font-size:20px;color:#b34343;text-decoration:line-through;\">$19.99</span>\r\n                </div>\r\n            </div>\r\n            <!-- SHOP NOW LINK -->\r\n            <a href=\"%click_link%http://example.com\" style=\"display:inline-block;background-color:#2b7fc4;border-radius:4px;padding:12px 15px;text-decoration:none;color:#fff;font-weight:500;font-size:14px;margin:20px 40px;\">Shop Now</a>\r\n\r\n			%open_tracking_code%\r\n		</div>\r\n        <!-- UNSUBSCRIBE LINK -->\r\n        <p style=\"font-size:14px;text-align:center;color:#636468;margin:30px 0;\">If you no longer like to hear from us, please <a href=\"%unsubscribe_link%\" style=\"font-size:14px;color:#636468;\">click here to unsubscribe</a>.</p>\r\n	</body>\r\n</html>', NULL, '2022-08-09 13:59:15'),
(3, 'Products Example', '<!DOCTYPE html>\r\n<html>\r\n	<head>\r\n		<meta charset=\"utf-8\">\r\n		<meta name=\"viewport\" content=\"width=device-width,minimum-scale=1\">\r\n		<title>Newsletter Title</title>\r\n	</head>\r\n	<body style=\"background-color:#eaebee;font-family:-apple-system, BlinkMacSystemFont, \'segoe ui\', roboto, oxygen, ubuntu, cantarell, \'fira sans\', \'droid sans\', \'helvetica neue\', Arial, sans-serif;box-sizing:border-box;font-size:16px;padding:30px;\">\r\n		<!-- TEMPLATE WRAPPER -->\r\n        <div style=\"background-color:#fff;box-sizing:border-box;font-size:16px;max-width:600px;margin: 30px auto;box-shadow:0 0 4px 0 rgba(0,0,0,0.03);padding-bottom:30px;\">\r\n			<!-- HEADER -->\r\n            <div style=\"display:flex;align-items: center;padding:40px;box-sizing:border-box;color:#636468;background-color:#f9fafc;margin:0;\">\r\n                <!-- TITLE -->\r\n                <h1 style=\"padding:0;font-size:22px;margin:0;flex:1;width:100%;\">Newsletter Title</h1>\r\n                <!-- DATE -->\r\n                <div style=\"font-size:16px;color:#909196;text-align:right;width:100%;\">January, 2022</div>\r\n            </div>\r\n            <!-- PRODUCTS LIST -->\r\n            <div style=\"display:flex;flex-wrap:wrap;margin:0 30px;\">\r\n                <!-- PRODUCT 1 -->\r\n                <div style=\"margin:30px 15px;\">\r\n                    <!-- SECTION IMAGE -->\r\n                    <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                    <!-- PRODUCT NAME -->\r\n                    <h2 style=\"margin:0;color:#6e6f74;box-sizing:border-box;font-size:16px;padding:5px 0;\">Product 1</h2>\r\n                    <!-- PRODUCT PRICE -->\r\n                    <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$14.99</span>\r\n                    <!-- PRODUCT RRP PRICE -->\r\n                    <span style=\"font-size:16px;color:#b34343;text-decoration:line-through;\">$19.99</span>\r\n                </div>\r\n                <!-- PRODUCT 2 -->\r\n                <div style=\"margin:30px 15px;\">\r\n                    <!-- SECTION IMAGE -->\r\n                    <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                    <!-- PRODUCT NAME -->\r\n                    <h2 style=\"margin:0;color:#6e6f74;box-sizing:border-box;font-size:16px;padding:5px 0;\">Product 2</h2>\r\n                    <!-- PRODUCT PRICE -->\r\n                    <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$7.99</span>\r\n                    <!-- PRODUCT RRP PRICE -->\r\n                    <span style=\"font-size:16px;color:#b34343;text-decoration:line-through;\">$12.99</span>\r\n                </div>\r\n                <!-- PRODUCT 3 -->\r\n                <div style=\"margin:30px 15px;\">\r\n                    <!-- SECTION IMAGE -->\r\n                    <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                    <!-- PRODUCT NAME -->\r\n                    <h2 style=\"margin:0;color:#6e6f74;box-sizing:border-box;font-size:16px;padding:5px 0;\">Product 3</h2>\r\n                    <!-- PRODUCT PRICE -->\r\n                    <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$39.99</span>\r\n                    <!-- PRODUCT RRP PRICE -->\r\n                    <span style=\"font-size:16px;color:#b34343;text-decoration:line-through;\">$59.99</span>\r\n                </div>\r\n                <!-- PRODUCT 4 -->\r\n                <div style=\"margin:30px 15px;\">\r\n                    <!-- SECTION IMAGE -->\r\n                    <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                    <!-- PRODUCT NAME -->\r\n                    <h2 style=\"margin:0;color:#6e6f74;box-sizing:border-box;font-size:16px;padding:5px 0;\">Product 4</h2>\r\n                    <!-- PRODUCT PRICE -->\r\n                    <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$44.99</span>\r\n                    <!-- PRODUCT RRP PRICE -->\r\n                    <span style=\"font-size:16px;color:#b34343;text-decoration:line-through;\">$49.99</span>\r\n                </div>\r\n                <!-- PRODUCT 5 -->\r\n                <div style=\"margin:30px 15px;\">\r\n                    <!-- SECTION IMAGE -->\r\n                    <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                    <!-- PRODUCT NAME -->\r\n                    <h2 style=\"margin:0;color:#6e6f74;box-sizing:border-box;font-size:16px;padding:5px 0;\">Product 5</h2>\r\n                    <!-- PRODUCT PRICE -->\r\n                    <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$67.99</span>\r\n                    <!-- PRODUCT RRP PRICE -->\r\n                    <span style=\"font-size:16px;color:#b34343;text-decoration:line-through;\">$79.99</span>\r\n                </div>\r\n                <!-- PRODUCT 6 -->\r\n                <div style=\"margin:30px 15px;\">\r\n                    <!-- SECTION IMAGE -->\r\n                    <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                    <!-- PRODUCT NAME -->\r\n                    <h2 style=\"margin:0;color:#6e6f74;box-sizing:border-box;font-size:16px;padding:5px 0;\">Product 6</h2>\r\n                    <!-- PRODUCT PRICE -->\r\n                    <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$39.99</span>\r\n                    <!-- PRODUCT RRP PRICE -->\r\n                    <span style=\"font-size:16px;color:#b34343;text-decoration:line-through;\">$59.99</span>\r\n                </div>\r\n            </div>\r\n            <!-- SHOP NOW LINK -->\r\n            <a href=\"%click_link%http://example.com\" style=\"display:inline-block;background-color:#2b7fc4;border-radius:4px;padding:12px 15px;text-decoration:none;color:#fff;font-weight:500;font-size:14px;margin:20px 40px;\">Shop Now</a>\r\n\r\n			%open_tracking_code%\r\n		</div>\r\n        <!-- UNSUBSCRIBE LINK -->\r\n        <p style=\"font-size:14px;text-align:center;color:#636468;margin:30px 0;\">If you no longer like to hear from us, please <a href=\"%unsubscribe_link%\" style=\"font-size:14px;color:#636468;\">click here to unsubscribe</a>.</p>\r\n	</body>\r\n</html>', NULL, '2022-08-09 19:24:00');

CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `date_subbed` datetime NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `status` enum('Subscribed','Unsubscribed') NOT NULL DEFAULT 'Subscribed',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;