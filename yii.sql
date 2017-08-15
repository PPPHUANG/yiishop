/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1_3306
Source Server Version : 50714
Source Host           : 127.0.0.1:3306
Source Database       : yii

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-08-15 23:42:05
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for shop_address
-- ----------------------------
DROP TABLE IF EXISTS `shop_address`;
CREATE TABLE `shop_address` (
  `addressid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(32) NOT NULL DEFAULT '',
  `lastname` varchar(32) NOT NULL DEFAULT '',
  `company` varchar(100) NOT NULL DEFAULT '',
  `address` text,
  `postcode` char(6) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `telephone` varchar(20) NOT NULL DEFAULT '',
  `userid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`addressid`),
  KEY `shop_address_userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_address
-- ----------------------------
INSERT INTO `shop_address` VALUES ('1', 'huang', 'peng', '', '金明区金明大道', '123456', '123456@qq.com', '12345678901', '7', '0');
INSERT INTO `shop_address` VALUES ('2', 'admin', 'admin', '', 'jinmingheda', '475000', '123@qq.com', '12345678911', '1', '0');
INSERT INTO `shop_address` VALUES ('3', '黄', '鹏', '', '金明区金明大道', '123456', '123456255', '112378855454', '1', '0');

-- ----------------------------
-- Table structure for shop_admin
-- ----------------------------
DROP TABLE IF EXISTS `shop_admin`;
CREATE TABLE `shop_admin` (
  `adminid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `adminuser` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员账号',
  `adminpass` char(32) NOT NULL DEFAULT '' COMMENT '管理员密码',
  `adminemail` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员电子邮箱',
  `logintime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `loginip` bigint(20) NOT NULL DEFAULT '0' COMMENT '登录IP',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`adminid`),
  UNIQUE KEY `shop_admin_adminuser_adminpass` (`adminuser`,`adminpass`),
  UNIQUE KEY `shop_admin_adminuser_adminemail` (`adminuser`,`adminemail`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_admin
-- ----------------------------
INSERT INTO `shop_admin` VALUES ('1', 'admin', '202cb962ac59075b964b07152d234b70', '329649258@qq.com', '1495431107', '2130706433', '1488710601');

-- ----------------------------
-- Table structure for shop_cart
-- ----------------------------
DROP TABLE IF EXISTS `shop_cart`;
CREATE TABLE `shop_cart` (
  `cartid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `productid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `productnum` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `userid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cartid`),
  KEY `shop_cart_productid` (`productid`),
  KEY `shop_cart_userid` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_cart
-- ----------------------------
INSERT INTO `shop_cart` VALUES ('2', '6', '1', '189.00', '1', '1497664781');

-- ----------------------------
-- Table structure for shop_category
-- ----------------------------
DROP TABLE IF EXISTS `shop_category`;
CREATE TABLE `shop_category` (
  `cateid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '',
  `parentid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cateid`),
  KEY `shop_category_parentid` (`parentid`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_category
-- ----------------------------
INSERT INTO `shop_category` VALUES ('8', '服装 & 运动户外', '0', '1489894023');
INSERT INTO `shop_category` VALUES ('9', '电子产品', '0', '1489894034');
INSERT INTO `shop_category` VALUES ('12', '裤子', '8', '1489940266');
INSERT INTO `shop_category` VALUES ('13', '上衣', '8', '1489940304');
INSERT INTO `shop_category` VALUES ('14', '女鞋 & 男鞋 & 箱包', '0', '1489940323');
INSERT INTO `shop_category` VALUES ('15', '男鞋', '14', '1489940344');
INSERT INTO `shop_category` VALUES ('16', '汽车 & 装饰', '0', '1490067010');
INSERT INTO `shop_category` VALUES ('17', '豪华车', '16', '1490067034');
INSERT INTO `shop_category` VALUES ('18', '裙子', '8', '1490085108');
INSERT INTO `shop_category` VALUES ('19', '生鲜水果', '0', '1490085209');
INSERT INTO `shop_category` VALUES ('20', '家具建材', '0', '1490085221');
INSERT INTO `shop_category` VALUES ('21', '医药保健', '0', '1490085236');
INSERT INTO `shop_category` VALUES ('22', '图书音像', '0', '1490085256');
INSERT INTO `shop_category` VALUES ('23', '零食 & 进口食品', '0', '1490085282');
INSERT INTO `shop_category` VALUES ('24', '化妆品 & 个人护理', '0', '1490085319');
INSERT INTO `shop_category` VALUES ('25', '家纺 & 家饰', '0', '1490085363');
INSERT INTO `shop_category` VALUES ('26', '窗帘', '25', '1490783858');

-- ----------------------------
-- Table structure for shop_order
-- ----------------------------
DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE `shop_order` (
  `orderid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `addressid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `expressid` int(10) unsigned NOT NULL DEFAULT '0',
  `expressno` varchar(50) NOT NULL DEFAULT '',
  `tradeno` varchar(100) NOT NULL DEFAULT '',
  `tradeext` text,
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`orderid`),
  KEY `shop_order_userid` (`userid`),
  KEY `shop_order_addressid` (`addressid`),
  KEY `shop_order_expressid` (`expressid`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_order
-- ----------------------------
INSERT INTO `shop_order` VALUES ('70', '1', '3', '189.00', '100', '3', '402545350264', '', null, '1492002555', '2017-05-22 13:31:11');
INSERT INTO `shop_order` VALUES ('71', '7', '0', '0.00', '0', '0', '', '', null, '1493878755', '2017-05-04 14:19:15');
INSERT INTO `shop_order` VALUES ('72', '7', '0', '0.00', '0', '0', '', '', null, '1493879012', '2017-05-04 14:23:32');
INSERT INTO `shop_order` VALUES ('73', '1', '0', '0.00', '220', '0', '1234645464', '', null, '1495431003', '2017-05-22 13:33:13');
INSERT INTO `shop_order` VALUES ('74', '1', '0', '0.00', '0', '0', '', '', null, '1497664642', '2017-06-17 09:57:22');

-- ----------------------------
-- Table structure for shop_order_detail
-- ----------------------------
DROP TABLE IF EXISTS `shop_order_detail`;
CREATE TABLE `shop_order_detail` (
  `detailid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `productid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `productnum` int(10) unsigned NOT NULL DEFAULT '0',
  `orderid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`detailid`),
  KEY `shop_order_detail_productid` (`productid`),
  KEY `shop_order_detail_orderid` (`orderid`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_order_detail
-- ----------------------------
INSERT INTO `shop_order_detail` VALUES ('48', '9', '890000.00', '1', '70', '1492002555');
INSERT INTO `shop_order_detail` VALUES ('49', '5', '189.00', '2', '71', '1493878755');
INSERT INTO `shop_order_detail` VALUES ('50', '6', '189.00', '2', '71', '1493878755');
INSERT INTO `shop_order_detail` VALUES ('51', '5', '189.00', '1', '72', '1493879012');
INSERT INTO `shop_order_detail` VALUES ('52', '5', '189.00', '1', '73', '1495431003');
INSERT INTO `shop_order_detail` VALUES ('53', '6', '189.00', '1', '74', '1497664642');

-- ----------------------------
-- Table structure for shop_product
-- ----------------------------
DROP TABLE IF EXISTS `shop_product`;
CREATE TABLE `shop_product` (
  `productid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cateid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` varchar(200) NOT NULL DEFAULT '',
  `descr` text,
  `num` int(10) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cover` varchar(200) NOT NULL DEFAULT '',
  `pics` text,
  `issale` enum('0','1') NOT NULL DEFAULT '0',
  `ishot` enum('0','1') NOT NULL DEFAULT '0',
  `istui` enum('0','1') NOT NULL DEFAULT '0',
  `saleprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ison` enum('0','1') NOT NULL DEFAULT '1',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`productid`),
  KEY `shop_product_cateid` (`cateid`),
  KEY `shop_product_ison` (`ison`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_product
-- ----------------------------
INSERT INTO `shop_product` VALUES ('5', '13', '印花', '印花印花印花印花印花印花印花印花印花印花印花印花印花印花印花印花印花印花印花印花印花', '78', '199.00', 'http://on1v47dqg.bkt.clouddn.com/58d8df1a6547f', 'http://on1v47dqg.bkt.clouddn.com/58d8df1a65488', '1', '1', '1', '189.00', '1', '0');
INSERT INTO `shop_product` VALUES ('6', '13', 'T恤', 'T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤T恤', '79', '199.00', 'http://on1v47dqg.bkt.clouddn.com/58d8df4360748', 'http://on1v47dqg.bkt.clouddn.com/58d8df436074f', '1', '1', '1', '189.00', '1', '0');
INSERT INTO `shop_product` VALUES ('7', '15', '帆布鞋', '帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋帆布鞋', '95', '199.00', 'http://on1v47dqg.bkt.clouddn.com/58ceb10426011', 'http://on1v47dqg.bkt.clouddn.com/58cfc89ce8528', '1', '1', '1', '189.00', '0', '0');
INSERT INTO `shop_product` VALUES ('8', '13', '短袖', '短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖短袖', '99', '199.00', 'http://on1v47dqg.bkt.clouddn.com/58d08499e2832', 'http://on1v47dqg.bkt.clouddn.com/58d0849a1f759', '1', '1', '1', '99.00', '0', '0');
INSERT INTO `shop_product` VALUES ('9', '17', '路虎揽胜', '路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜路虎揽胜', '93', '890000.00', 'http://on1v47dqg.bkt.clouddn.com/58d09ebb43e87', 'http://on1v47dqg.bkt.clouddn.com/58d09ec59a59f', '1', '1', '1', '890000.00', '1', '0');
INSERT INTO `shop_product` VALUES ('10', '12', '休闲长裤', '休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲长裤休闲', '97', '199.00', 'http://on1v47dqg.bkt.clouddn.com/58d09f251c53d', 'http://on1v47dqg.bkt.clouddn.com/58d09f254a64f', '1', '1', '0', '189.00', '1', '0');
INSERT INTO `shop_product` VALUES ('11', '18', '长裙', '长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙', '96', '299.00', 'http://on1v47dqg.bkt.clouddn.com/58d0e6e147361', 'http://on1v47dqg.bkt.clouddn.com/58d0e6e5130b6', '1', '1', '1', '269.00', '1', '0');
INSERT INTO `shop_product` VALUES ('12', '18', '白色长裙', '白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙白色长裙', '99', '299.00', 'http://on1v47dqg.bkt.clouddn.com/58d0e72fcd766', 'http://on1v47dqg.bkt.clouddn.com/58d0e73326817', '1', '1', '1', '258.00', '1', '0');
INSERT INTO `shop_product` VALUES ('13', '18', '长裙', '长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙长裙', '99', '199.00', 'http://on1v47dqg.bkt.clouddn.com/58d8dfa873ff7', 'http://on1v47dqg.bkt.clouddn.com/58d8dfa8b9777', '1', '1', '1', '189.00', '1', '0');

-- ----------------------------
-- Table structure for shop_profile
-- ----------------------------
DROP TABLE IF EXISTS `shop_profile`;
CREATE TABLE `shop_profile` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `truename` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `age` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '年龄',
  `sex` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '2016-01-01' COMMENT '生日',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `company` varchar(100) NOT NULL DEFAULT '' COMMENT '公司',
  `userid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户的ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_profile_userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_profile
-- ----------------------------

-- ----------------------------
-- Table structure for shop_user
-- ----------------------------
DROP TABLE IF EXISTS `shop_user`;
CREATE TABLE `shop_user` (
  `userid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `username` varchar(32) NOT NULL DEFAULT '',
  `userpass` char(32) NOT NULL DEFAULT '',
  `useremail` varchar(100) NOT NULL DEFAULT '',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `shop_user_username_userpass` (`username`,`userpass`),
  UNIQUE KEY `shop_user_useremail_userpass` (`useremail`,`userpass`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shop_user
-- ----------------------------
INSERT INTO `shop_user` VALUES ('1', 'admin', '202cb962ac59075b964b07152d234b70', 'fa@qq.com', '0');
INSERT INTO `shop_user` VALUES ('5', '58cab481aaf5d', '9bed74085b84583291072160853d95df', '329649258@qq.com', '1489679490');
INSERT INTO `shop_user` VALUES ('6', '58cab51c461a9', 'e9092cfff25c00a791b36142cf9fd4aa', '329649258@qq.com', '1489679645');
INSERT INTO `shop_user` VALUES ('7', '58cab69b00446', '52d39e67b293a332e1419c472d91edb2', '329649258@qq.com', '1489680027');
INSERT INTO `shop_user` VALUES ('8', '590acc9c4e562', 'd22eaff3e481f63a342fd3830a47b75f', '329649258@qq.com', '1493879965');
INSERT INTO `shop_user` VALUES ('9', '590ad3e8013e0', 'cf8673e95bdaeff763fd11c6ef0b66cc', '329649258@qq.com', '1493881832');
INSERT INTO `shop_user` VALUES ('10', '5912809fae13b', '208285a21f2315ade1f94d7cc2d1d8fd', 'huangpengr@gmail.com', '1494384800');
INSERT INTO `shop_user` VALUES ('11', '59142bd27aecf', '93ea002f695730f43b58443ed6ca99b5', '624774509@qq.com', '1494494163');
INSERT INTO `shop_user` VALUES ('12', '59142bff9f7a7', '9b24c5d904c1b31a928abc4bcbcb4ad6', '741432605@qq.com', '1494494207');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user` varchar(20) NOT NULL,
  `passpord` varchar(20) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
