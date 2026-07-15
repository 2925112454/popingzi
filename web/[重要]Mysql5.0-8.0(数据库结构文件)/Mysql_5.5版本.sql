/*
 Navicat MySQL Data Transfer

 Source Server         : root
 Source Server Type    : MySQL
 Source Server Version : 5500
 Source Host           : localhost:3306
 Source Schema         : popingzi

 Target Server Type    : MySQL
 Target Server Version : 5500
 File Encoding         : 65001

 Date: 27/02/2026 17:39:56
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ppz_ads
-- ----------------------------
DROP TABLE IF EXISTS `ppz_ads`;
CREATE TABLE `ppz_ads`  (
  `aid` int(11) NOT NULL DEFAULT 1 COMMENT 'ID，1为横幅广告，2为右下角弹窗广告，3为内容页横幅广告，4为右边栏广告，5为网页左侧悬浮广告，6为网页右侧悬浮广告，7为自定义的JS广告代码',
  `aimg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '广告图片路径',
  `aurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '广告跳转链接',
  `ayes` int(11) NOT NULL DEFAULT 0 COMMENT '广告展示开关，默认0关闭，1开启',
  `aeye` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '广告展现区域，1首页，2列表页，3内容页，4搜索页(可多选)',
  `atime` datetime NULL DEFAULT NULL COMMENT '广告过期时间，过期默认隐藏',
  `avip` int(11) NOT NULL DEFAULT 0 COMMENT '会员免除广告展示开关，0关闭，1开启',
  `ajs` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '自定义js代码内容，ID为7时有效',
  PRIMARY KEY (`aid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_ads
-- ----------------------------
INSERT INTO `ppz_ads` VALUES (1, NULL, NULL, 0, NULL, NULL, 0, NULL);
INSERT INTO `ppz_ads` VALUES (2, NULL, NULL, 0, NULL, NULL, 0, NULL);
INSERT INTO `ppz_ads` VALUES (3, NULL, NULL, 0, NULL, NULL, 0, NULL);
INSERT INTO `ppz_ads` VALUES (4, NULL, NULL, 0, NULL, NULL, 0, NULL);
INSERT INTO `ppz_ads` VALUES (5, NULL, NULL, 0, NULL, NULL, 0, NULL);
INSERT INTO `ppz_ads` VALUES (6, NULL, NULL, 0, NULL, NULL, 0, NULL);
INSERT INTO `ppz_ads` VALUES (7, NULL, NULL, 0, NULL, NULL, 0, NULL);

-- ----------------------------
-- Table structure for ppz_announcement
-- ----------------------------
DROP TABLE IF EXISTS `ppz_announcement`;
CREATE TABLE `ppz_announcement`  (
  `ggid` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告id',
  `ggtext` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '公告标题',
  `ggbigtext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '公告内容',
  `ggrowid` int(11) NULL DEFAULT NULL COMMENT '发布者id',
  `ggtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '公告发布时间',
  `ggimg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '公告封面图',
  `ggtop` int(1) NULL DEFAULT 1 COMMENT '公告置顶，默认1不置顶，2为置顶',
  PRIMARY KEY (`ggid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_announcement
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_cardset
-- ----------------------------
DROP TABLE IF EXISTS `ppz_cardset`;
CREATE TABLE `ppz_cardset`  (
  `setid` int(11) NOT NULL DEFAULT 1 COMMENT 'id',
  `seturlyue` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '月度购买地址',
  `setrmbyue` int(11) NOT NULL DEFAULT 0 COMMENT '月度金额',
  `seturlji` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '季度购买地址',
  `setrmbji` int(11) NOT NULL DEFAULT 0 COMMENT '季度金额',
  `seturlnian` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '年度购买地址',
  `setrmbnian` int(11) NOT NULL DEFAULT 0 COMMENT '年度金额',
  `seturlbai` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '百年购买地址',
  `setrmbbai` int(11) NOT NULL DEFAULT 0 COMMENT '百度金额',
  `seturlshi` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '10积分购买地址',
  `setrmbshi` int(11) NOT NULL DEFAULT 0 COMMENT '10积分金额',
  `seturler` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '20积分购买地址',
  `setrmber` int(11) NOT NULL DEFAULT 0 COMMENT '20积分金额',
  `seturlsan` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '30积分购买地址',
  `setrmbsan` int(11) NOT NULL DEFAULT 0 COMMENT '30积分金额',
  `seturlsi` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '40积分购买地址',
  `setrmbsi` int(11) NOT NULL DEFAULT 0 COMMENT '40积分金额',
  `seturlwu` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '50积分购买地址',
  `setrmbwu` int(11) NOT NULL DEFAULT 0 COMMENT '50积分金额',
  `seturlyi` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '100积分购买地址',
  `setrmbyi` int(11) NOT NULL DEFAULT 0 COMMENT '100积分金额',
  `setrmbqian` int(11) NOT NULL DEFAULT 0 COMMENT '1000积分金额',
  `seturlqian` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '1000积分购买地址',
  PRIMARY KEY (`setid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值卡配置信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_cardset
-- ----------------------------
INSERT INTO `ppz_cardset` VALUES (1, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, NULL, 0, 0, NULL);

-- ----------------------------
-- Table structure for ppz_code
-- ----------------------------
DROP TABLE IF EXISTS `ppz_code`;
CREATE TABLE `ppz_code`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '邀请码ID',
  `invitecode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邀请码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_code
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_codeset
-- ----------------------------
DROP TABLE IF EXISTS `ppz_codeset`;
CREATE TABLE `ppz_codeset`  (
  `setid` int(11) NOT NULL DEFAULT 1,
  `seturl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邀请码获取地址',
  `setrmb` bigint(20) NOT NULL DEFAULT 0 COMMENT '邀请码获取价格',
  `settext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`setid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_codeset
-- ----------------------------
INSERT INTO `ppz_codeset` VALUES (1, NULL, 0, NULL);

-- ----------------------------
-- Table structure for ppz_commentary
-- ----------------------------
DROP TABLE IF EXISTS `ppz_commentary`;
CREATE TABLE `ppz_commentary`  (
  `plid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `plbigtext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '评论内容',
  `plip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '评论IP地址',
  `pltime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '评论时间',
  `plrowid` bigint(20) NULL DEFAULT NULL COMMENT '评论所属文章id',
  `pladmin` bigint(20) NULL DEFAULT 1 COMMENT '评论发布者id',
  `pltop` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '评论点数组',
  PRIMARY KEY (`plid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_commentary
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_diy
-- ----------------------------
DROP TABLE IF EXISTS `ppz_diy`;
CREATE TABLE `ppz_diy`  (
  `diyid` int(11) NOT NULL DEFAULT 1,
  `diyindex` int(11) NOT NULL DEFAULT 1 COMMENT '首页版面',
  `diyday` int(11) NOT NULL DEFAULT 1 COMMENT '白天模式',
  `diynight` int(11) NOT NULL DEFAULT 1 COMMENT '夜晚模式',
  `day` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '自定义白天模式的代码',
  `night` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '自定义夜晚模式的代码',
  `image` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '自定义轮播图JSON',
  `carousel` int(11) NOT NULL DEFAULT 5 COMMENT '轮播图模式：1为加“热门”内容，2为加"精华"内容，3为加"置顶"内容，4为自动最新内容，5为自动最高阅览量内容，6为自定义内容',
  PRIMARY KEY (`diyid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_diy
-- ----------------------------
INSERT INTO `ppz_diy` VALUES (1, 1, 1, 1, NULL, NULL, NULL, 5);

-- ----------------------------
-- Table structure for ppz_email
-- ----------------------------
DROP TABLE IF EXISTS `ppz_email`;
CREATE TABLE `ppz_email`  (
  `id` int(11) NOT NULL DEFAULT 1 COMMENT 'id',
  `smtp` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'SMTP服务器',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发件人邮箱账号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发件人邮箱密码',
  `diy` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义文字内容落款',
  `port` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '端口',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发件人邮箱',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发件人名称',
  `diyhed` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义头部前缀',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_email
-- ----------------------------
INSERT INTO `ppz_email` VALUES (1, 'smtp.qq.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for ppz_fl
-- ----------------------------
DROP TABLE IF EXISTS `ppz_fl`;
CREATE TABLE `ppz_fl`  (
  `flid` int(11) NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `fllinkid` int(11) NULL DEFAULT NULL COMMENT '分类所属列表id',
  `flname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分类名称',
  PRIMARY KEY (`flid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_fl
-- ----------------------------
INSERT INTO `ppz_fl` VALUES (1, 1, '测试分类');

-- ----------------------------
-- Table structure for ppz_folus
-- ----------------------------
DROP TABLE IF EXISTS `ppz_folus`;
CREATE TABLE `ppz_folus`  (
  `usid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '关注id',
  `usuename` bigint(20) NULL DEFAULT NULL COMMENT '被关注者id',
  `usvip` bigint(20) NULL DEFAULT NULL COMMENT '关注者id',
  PRIMARY KEY (`usid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of ppz_folus
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_fuck
-- ----------------------------
DROP TABLE IF EXISTS `ppz_fuck`;
CREATE TABLE `ppz_fuck`  (
  `id` int(11) NOT NULL DEFAULT 1 COMMENT 'ID',
  `fuck` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '违禁词设定，多个词之间用|分割',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_fuck
-- ----------------------------
INSERT INTO `ppz_fuck` VALUES (1, NULL);

-- ----------------------------
-- Table structure for ppz_ggcommentary
-- ----------------------------
DROP TABLE IF EXISTS `ppz_ggcommentary`;
CREATE TABLE `ppz_ggcommentary`  (
  `plid` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `plbigtext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '评论内容',
  `plip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '评论IP地址',
  `pltime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '评论时间',
  `plrowid` int(11) NULL DEFAULT NULL COMMENT '评论所属文章id',
  `pladmin` int(11) NULL DEFAULT 1 COMMENT '评论发布者id',
  `pltop` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '评论点数组',
  PRIMARY KEY (`plid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_ggcommentary
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_ggreply
-- ----------------------------
DROP TABLE IF EXISTS `ppz_ggreply`;
CREATE TABLE `ppz_ggreply`  (
  `repid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `repplid` int(11) NULL DEFAULT NULL COMMENT '评论id',
  `repadmin` int(11) NULL DEFAULT NULL COMMENT '回复者id',
  `reptext` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '回复内容',
  `reptime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '回复时间',
  `repip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '回复ip地址',
  PRIMARY KEY (`repid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_ggreply
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_letter
-- ----------------------------
DROP TABLE IF EXISTS `ppz_letter`;
CREATE TABLE `ppz_letter`  (
  `terid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '私信id',
  `tertext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '私信内容',
  `teradmin` bigint(20) NULL DEFAULT NULL COMMENT '私信发送者',
  `teruser` bigint(20) NULL DEFAULT 0 COMMENT '私信接收人，0为所有人',
  `tertime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '私信发送时间',
  `terip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '私信发送ip地址',
  `teryes` int(11) NULL DEFAULT 0 COMMENT '私信状态，0为未读，1为已读',
  PRIMARY KEY (`terid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_letter
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_link
-- ----------------------------
DROP TABLE IF EXISTS `ppz_link`;
CREATE TABLE `ppz_link`  (
  `linkid` int(11) NOT NULL AUTO_INCREMENT COMMENT '列表id',
  `linkname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '列表名称',
  `linkimg` int(2) NULL DEFAULT 1 COMMENT '列表封面类别，1为竖屏，2为横屏，3为资讯类',
  `linkico` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '列表图标',
  `linkint` int(2) NULL DEFAULT 1 COMMENT '封面单行数量，1为默认4张，2为3张（资讯类无效）',
  PRIMARY KEY (`linkid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_link
-- ----------------------------
INSERT INTO `ppz_link` VALUES (1, '测试列表', 1, 'fa-birthday-cake', 1);

-- ----------------------------
-- Table structure for ppz_log
-- ----------------------------
DROP TABLE IF EXISTS `ppz_log`;
CREATE TABLE `ppz_log`  (
  `logid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '交易记录id',
  `logadmin` bigint(20) NULL DEFAULT NULL COMMENT '交易会员ID',
  `logtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '交易产生的时间',
  `logtype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易产生的类型',
  `logmun` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易订单号',
  `logrmb` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易积分变动',
  `logab` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易积分后的余额',
  `logrowid` bigint(20) NULL DEFAULT NULL COMMENT '交易产生文章ID',
  `logip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易产生的ip地址',
  PRIMARY KEY (`logid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_log
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_newusername
-- ----------------------------
DROP TABLE IF EXISTS `ppz_newusername`;
CREATE TABLE `ppz_newusername`  (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '会员id',
  `uname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `uusername` bigint(20) NULL DEFAULT NULL COMMENT '账号',
  `upass` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `uimg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像',
  `uemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `ucollect` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '收藏',
  `utel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `upersonal` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简介',
  `ugold` bigint(20) NULL DEFAULT 0 COMMENT '积分',
  `usex` int(2) NULL DEFAULT 1 COMMENT '性别',
  `uurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网址',
  `ustatus` int(11) NULL DEFAULT 1 COMMENT '身份，1普通会员，2为管理员，3为副站长，4为站长',
  `utime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `uviptime` timestamp NULL DEFAULT NULL COMMENT '会员时间',
  `uip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'IP地址',
  `utelyes` int(2) NULL DEFAULT 1 COMMENT '手机验证状态，1未验证，2已验证',
  `uemailyes` int(2) NULL DEFAULT 1 COMMENT '邮箱验证状态，1未验证，2已验证',
  `uformtel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '临时储存手机验证码',
  `uformemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '临时储存邮箱验证码',
  `ulogintime` timestamp NULL DEFAULT NULL COMMENT '最近登录时间',
  `uban` int(2) NULL DEFAULT 1 COMMENT '封禁状态',
  `udate` timestamp NULL DEFAULT NULL COMMENT '会员最近签到时间',
  `udateday` int(11) NULL DEFAULT 0 COMMENT '会员连续签到天数',
  `urowyes` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '购买的文件',
  `tpastime` timestamp NULL DEFAULT NULL COMMENT '最近一次发送验证码的时间',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_newusername
-- ----------------------------
INSERT INTO `ppz_newusername` VALUES (1, 'Admin', 12345678, '$2y$10$xIvtU2IDYfiXWlXwlv5YpehvDKSP3ua2CMYRbNoUttUvOfj/eQz2C', NULL, 'admin@popingzi.com', NULL, '18888888888', NULL, 0, 1, NULL, 4, '2026-02-27 17:31:48', NULL, NULL, 1, 1, NULL, NULL, '2026-02-27 17:32:00', 1, NULL, 0, NULL, NULL);

-- ----------------------------
-- Table structure for ppz_regif
-- ----------------------------
DROP TABLE IF EXISTS `ppz_regif`;
CREATE TABLE `ppz_regif`  (
  `id` int(11) NOT NULL DEFAULT 1 COMMENT 'ID',
  `regif` int(11) NULL DEFAULT 1 COMMENT '注册状态：1开启，2关闭',
  `regoff` int(11) NULL DEFAULT 1 COMMENT '注册方式：1开放注册，2邀请码注册',
  `regtext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '注册协议',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_regif
-- ----------------------------
INSERT INTO `ppz_regif` VALUES (1, 1, 1, NULL);

-- ----------------------------
-- Table structure for ppz_reply
-- ----------------------------
DROP TABLE IF EXISTS `ppz_reply`;
CREATE TABLE `ppz_reply`  (
  `repid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `repplid` bigint(20) NULL DEFAULT NULL COMMENT '评论id',
  `repadmin` bigint(20) NULL DEFAULT NULL COMMENT '回复者id',
  `reptext` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '回复内容',
  `reptime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '回复时间',
  `repip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '回复ip地址',
  PRIMARY KEY (`repid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_reply
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_row
-- ----------------------------
DROP TABLE IF EXISTS `ppz_row`;
CREATE TABLE `ppz_row`  (
  `rowid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `rowtexe` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `rowbigtext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `rowtop` int(2) NOT NULL DEFAULT 1 COMMENT '是否置顶，1默认不置顶，2置顶，3,热门，4精华',
  `roweye` int(11) NULL DEFAULT 0 COMMENT '阅览量',
  `rowfl` int(11) NULL DEFAULT 0 COMMENT '所属分类',
  `rowtag` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标签',
  `rowtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发表时间',
  `rowadmin` bigint(20) NULL DEFAULT 1 COMMENT '发表人',
  `rowcp` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '版权方名称',
  `rowcpurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '版权方url地址',
  `rowdw` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '资源下载地址',
  `rowdwgold` int(11) NOT NULL DEFAULT 0 COMMENT '资源下载所需积分',
  `rowdwif` int(11) NULL DEFAULT 1 COMMENT '资源下载权限-->1所有已登录会员，2仅限VIP会员，3仅限网站管理人员',
  `rowvip` int(11) NULL DEFAULT 1 COMMENT '文章访问权限-->1所有人，2登录可见，3充值会员及管理员可见',
  `rowif` int(11) NOT NULL DEFAULT 1 COMMENT '文章类型，1图文，2相册，3视频',
  `rowimg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '封面',
  `videotext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '相册或者视频的说明介绍',
  `rowsc` bigint(20) NULL DEFAULT 0 COMMENT '收藏数',
  `rowyes` int(11) NOT NULL DEFAULT 1 COMMENT '审核状态，1待审核，2未通过，3已撤销，4已通过',
  `videotexttop` int(11) NOT NULL DEFAULT 1 COMMENT '相册或者视频的说明介绍位置，1显示在文章下方，2显示在文章上方',
  `vorimg` int(11) NULL DEFAULT 0 COMMENT '向游客开放的相册图片数量',
  `vorimg_log` int(11) NULL DEFAULT 0 COMMENT '向登录用户开放的相册图片数量',
  PRIMARY KEY (`rowid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_row
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_subcomm
-- ----------------------------
DROP TABLE IF EXISTS `ppz_subcomm`;
CREATE TABLE `ppz_subcomm`  (
  `comm_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '话题评论ID',
  `comm_subid` bigint(20) NULL DEFAULT NULL COMMENT '评论所属话题的ID',
  `comm_type` bigint(20) NULL DEFAULT 0 COMMENT '评论类型，0为父评论，大于0则为对应父评论的回复',
  `comm_yes` int(11) NULL DEFAULT 0 COMMENT '回复是否已读，0为未读，1为已读',
  `comm_admin` bigint(20) NULL DEFAULT NULL COMMENT '评论者ID',
  `comm_text` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '评论内容',
  `comm_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '评论IP地址',
  `comm_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '评论时间',
  `comm_top` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '评论点赞数量',
  PRIMARY KEY (`comm_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_subcomm
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_subject
-- ----------------------------
DROP TABLE IF EXISTS `ppz_subject`;
CREATE TABLE `ppz_subject`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '话题ID',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '话题标题',
  `text` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '话题内容',
  `admin` bigint(20) NULL DEFAULT NULL COMMENT '发表人ID',
  `type` int(11) NULL DEFAULT NULL COMMENT '话题所属标签',
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发表时间',
  `top` int(11) NULL DEFAULT 1 COMMENT '文章标注(1普通，2精选，3置顶)',
  `eyes` bigint(20) NULL DEFAULT 0 COMMENT '话题阅览量',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '发表IP地址',
  `yes` int(11) NULL DEFAULT 1 COMMENT '状态，1待审核，2违规，3通过',
  `no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '违规说明，状态2时有效',
  `quote` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '引用文章或话题的ID，多个ID用英文逗号进行分割，话题ID单独用{}包裹，如{1}',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_subject
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_subset
-- ----------------------------
DROP TABLE IF EXISTS `ppz_subset`;
CREATE TABLE `ppz_subset`  (
  `set_id` int(11) NOT NULL DEFAULT 1 COMMENT '话题配置ID',
  `set_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '话题标题',
  `set_off` int(11) NULL DEFAULT 0 COMMENT '话题开关,0：关闭，1：开启',
  `set_tag` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '话题子标签名称',
  `set_mun` int(11) NULL DEFAULT 0 COMMENT '单个会员每天最多可发表的篇数，0为不限制',
  `set_maxrep` int(11) NULL DEFAULT 2 COMMENT '评论的最大回复层级，0为不限制。',
  PRIMARY KEY (`set_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_subset
-- ----------------------------
INSERT INTO `ppz_subset` VALUES (1, NULL, 1, NULL, 0, 2);

-- ----------------------------
-- Table structure for ppz_subtype
-- ----------------------------
DROP TABLE IF EXISTS `ppz_subtype`;
CREATE TABLE `ppz_subtype`  (
  `sub_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '话题标签ID',
  `sub_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '话题标签名称',
  PRIMARY KEY (`sub_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_subtype
-- ----------------------------
INSERT INTO `ppz_subtype` VALUES (1, '测试标签');

-- ----------------------------
-- Table structure for ppz_tel
-- ----------------------------
DROP TABLE IF EXISTS `ppz_tel`;
CREATE TABLE `ppz_tel`  (
  `id` int(11) NOT NULL DEFAULT 1 COMMENT 'id',
  `apiname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '短信宝账号',
  `apikey` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '短信宝KEY',
  `apidiy` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义头部信息',
  `apibody` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '自定义落款',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_tel
-- ----------------------------
INSERT INTO `ppz_tel` VALUES (1, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for ppz_upfile
-- ----------------------------
DROP TABLE IF EXISTS `ppz_upfile`;
CREATE TABLE `ppz_upfile`  (
  `id` int(11) NOT NULL DEFAULT 1 COMMENT 'id',
  `upif` int(11) NULL DEFAULT 0 COMMENT '投稿开关0关，1开',
  `upmime` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '允许上传的文件MIME类型，用逗号分割',
  `upsize` int(11) NULL DEFAULT NULL COMMENT '允许用户上传的文件大小，整数，单位KB',
  `upfcsize` int(11) NULL DEFAULT 0 COMMENT '投稿分成0-100',
  `upvipsize` int(11) NULL DEFAULT 0 COMMENT 'VIP所享折扣0-100',
  `upifimg` int(11) NULL DEFAULT 0 COMMENT '是否允许会员投稿时上传附件,1为允许，0为不允许',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_upfile
-- ----------------------------
INSERT INTO `ppz_upfile` VALUES (1, 0, '', 0, 0, 0, 0);

-- ----------------------------
-- Table structure for ppz_vtime
-- ----------------------------
DROP TABLE IF EXISTS `ppz_vtime`;
CREATE TABLE `ppz_vtime`  (
  `vid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `vbin` int(11) NOT NULL DEFAULT 1 COMMENT '充值卡类型，1月度会员，2季度会员，3年度会员，4百年会员，5积分充值',
  `vgold` int(11) NOT NULL DEFAULT 1 COMMENT '积分充值数量，1为10,2为20，3为30,4为40，5为50,6为100',
  `vvar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '充值卡号',
  PRIMARY KEY (`vid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_vtime
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_web
-- ----------------------------
DROP TABLE IF EXISTS `ppz_web`;
CREATE TABLE `ppz_web`  (
  `webid` int(11) NOT NULL DEFAULT 1 COMMENT 'id',
  `webtext` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站标题',
  `webpass` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站关键词',
  `webvar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站简介',
  `webfooter` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站版权说明',
  `webqqurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ',
  `webwburl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微博',
  `webqqqurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'QQ群链接',
  `webemail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `webby` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '网站标语',
  `webip` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ip黑名单',
  `weblogo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '导航栏LOGO',
  `webbutlogo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '导航栏底部LOGO',
  `webnewnet` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '底部新媒体账号图片地址',
  `webmaxsize` int(20) NULL DEFAULT 0 COMMENT '网站服务器分配的最大储存空间，单位GB',
  `toplogourl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '/' COMMENT '导航栏LOGO跳转链接',
  `webjifen` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '签到时获得的积分奖励范围，如1-10',
  PRIMARY KEY (`webid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_web
-- ----------------------------
INSERT INTO `ppz_web` VALUES (1, '我的网站', '关键词', '简单高效，为梦而生！<br>2026 我故自我在', '<p>© 破瓶子<a>POPINGZI.COM</a><p>', '', '', '', '', '为梦想而生', NULL, '/images/logo.svg', '/images/logo2.svg', '/images/logo/ewm.png', 0, '/', '0');

-- ----------------------------
-- Table structure for ppz_work
-- ----------------------------
DROP TABLE IF EXISTS `ppz_work`;
CREATE TABLE `ppz_work`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '工单ID',
  `wktext` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工单标题，用于简单阐述问题',
  `wkword` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '工单详细问题说明',
  `wkimg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工单附件',
  `wkyes` int(11) NOT NULL DEFAULT 1 COMMENT '工单状态，1为待处理，2为已处理',
  `wkfl` int(11) NULL DEFAULT NULL COMMENT '工单分类，对应工单分类的数据表',
  `wktime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '工单时间',
  `wkadmin` bigint(20) NULL DEFAULT NULL COMMENT '提交工单的会员id',
  `wkhf` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '管理员回复内容',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci PACK_KEYS = 0 ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_work
-- ----------------------------

-- ----------------------------
-- Table structure for ppz_workfl
-- ----------------------------
DROP TABLE IF EXISTS `ppz_workfl`;
CREATE TABLE `ppz_workfl`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '工单分类id',
  `wkname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '工单分类名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ppz_workfl
-- ----------------------------
INSERT INTO `ppz_workfl` VALUES (1, '问题反馈');
INSERT INTO `ppz_workfl` VALUES (2, '投诉建议');
INSERT INTO `ppz_workfl` VALUES (3, '咨询问答');

-- ----------------------------
-- Function structure for REGEXP_REPLACE (适配MySQL5.5)
-- ----------------------------
DROP FUNCTION IF EXISTS `REGEXP_REPLACE`;
delimiter ;;
CREATE FUNCTION `REGEXP_REPLACE`(input TEXT, pattern TEXT, replace_text TEXT)
 RETURNS text CHARSET utf8
BEGIN
    DECLARE temp TEXT DEFAULT '';
    DECLARE len INT DEFAULT 0;
    DECLARE i INT DEFAULT 1;
    
    SET temp = input;
    SET len = CHAR_LENGTH(temp);
    
    -- MySQL5.5 简化版正则替换实现
    WHILE i <= len DO
        IF temp REGEXP pattern THEN
            SET temp = REPLACE(temp, SUBSTRING(temp, REGEXP_INSTR(temp, pattern), LENGTH(pattern)), replace_text);
        END IF;
        SET i = i + 1;
    END WHILE;
    
    RETURN temp;
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;