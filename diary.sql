/*
 Navicat Premium Data Transfer

 Source Server         : 512
 Source Server Type    : MariaDB
 Source Server Version : 50568
 Source Host           : kylebing.cn:3306
 Source Schema         : diary

 Target Server Type    : MariaDB
 Target Server Version : 50568
 File Encoding         : 65001

 Date: 16/03/2022 11:14:31
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for diaries
-- ----------------------------
DROP TABLE IF EXISTS `diaries`;
CREATE TABLE `diaries`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime(0) NOT NULL COMMENT '日记日期',
  `title` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '标题',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `temperature` int(3) NULL DEFAULT -273 COMMENT '室内温度',
  `temperature_outside` int(3) NULL DEFAULT -273 COMMENT '室外温度',
  `weather` enum('sunny','cloudy','overcast','sprinkle','rain','thunderstorm','fog','snow','tornado','smog','sandstorm') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'sunny' COMMENT '天气',
  `category` enum('life','study','film','game','work','sport','bigevent','week','article') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'life' COMMENT '类别',
  `date_create` datetime(0) NOT NULL COMMENT '创建日期',
  `date_modify` datetime(0) NULL DEFAULT NULL COMMENT '编辑日期',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `is_public` int(1) NOT NULL DEFAULT 0 COMMENT '是否共享',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5344 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_visit_time` datetime(0) NULL DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `register_time` datetime(0) NULL DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`uid`, `email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
