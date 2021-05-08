/*
 Navicat Premium Data Transfer

 Source Server         : kylebing.cn
 Source Server Type    : MariaDB
 Source Server Version : 50568
 Source Host           : kylebing.cn:3306
 Source Schema         : diary

 Target Server Type    : MariaDB
 Target Server Version : 50568
 File Encoding         : 65001

 Date: 08/05/2021 09:13:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for diaries
-- ----------------------------
DROP TABLE IF EXISTS `diaries`;
CREATE TABLE `diaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL COMMENT '日记日期',
  `title` text NOT NULL COMMENT '标题',
  `content` longtext COMMENT '内容',
  `temperature` int(3) DEFAULT '-273' COMMENT '室内温度',
  `temperature_outside` int(3) DEFAULT '-273' COMMENT '室外温度',
  `weather` enum('sunny','cloudy','overcast','sprinkle','rain','thunderstorm','fog','snow','tornado','smog','sandstorm') NOT NULL DEFAULT 'sunny' COMMENT '天气',
  `category` enum('life','study','film','game','work','sport','bigevent','week','article') NOT NULL DEFAULT 'life' COMMENT '类别',
  `date_create` datetime NOT NULL COMMENT '创建日期',
  `date_modify` datetime DEFAULT NULL COMMENT '编辑日期',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `is_public` int(1) NOT NULL DEFAULT '0' COMMENT '是否共享',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1805 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `last_visit_time` datetime DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `register_time` datetime DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
