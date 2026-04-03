-- ============================================================
-- Honlor Admin Dashboard Schema
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─── auth — User Credentials ──────────────────────────────
DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth` (
  `id`         int          NOT NULL AUTO_INCREMENT,
  `username`   varchar(32)  NOT NULL,
  `password`   varchar(256) NOT NULL,
  `email`      varchar(256) NOT NULL,
  `phone`      varchar(16)  NOT NULL DEFAULT '',
  `active`     tinyint(1)   NOT NULL DEFAULT 1,
  `blocked`    tinyint(1)   NOT NULL DEFAULT 0,
  `sec_email`  varchar(256)          DEFAULT NULL,
  `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email`    (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── profiles — Extended Profile Data ───────────────────────
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id`         int           NOT NULL,
  `bio`        longtext,
  `avatar`     varchar(1024)          DEFAULT NULL,
  `firstname`  varchar(128)  NOT NULL DEFAULT '',
  `lastname`   varchar(128)  NOT NULL DEFAULT '',
  `dob`        date                   DEFAULT NULL,
  `instagram`  varchar(1024)          DEFAULT NULL,
  `twitter`    varchar(1024)          DEFAULT NULL,
  `facebook`   varchar(1024)          DEFAULT NULL,
  `created_at` datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`id`) REFERENCES `auth` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── session — Token Authentication ──────────────────────
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `id`          int          NOT NULL AUTO_INCREMENT,
  `uid`         int          NOT NULL,
  `token`       varchar(64)  NOT NULL,
  `login_time`  datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip`          varchar(45)           DEFAULT NULL,
  `user_agent`  text                  DEFAULT NULL,
  `active`      tinyint(1)   NOT NULL DEFAULT 1,
  `fingerprint` varchar(256)          DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token`),
  KEY `fk_session_auth` (`uid`),
  CONSTRAINT `session_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `auth` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── channels — Workspace Channels ──────────────────────────
DROP TABLE IF EXISTS `channels`;
CREATE TABLE `channels` (
  `id`           int          NOT NULL AUTO_INCREMENT,
  `name`         varchar(128) NOT NULL,
  `type`         varchar(32)  NOT NULL DEFAULT 'public', -- 'public', 'private'
  `member_count` int          NOT NULL DEFAULT 0,
  `created_at`   datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── messages — Chat Logs & Moderation ──────────────────────
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id`         int          NOT NULL AUTO_INCREMENT,
  `channel_id` int          NOT NULL,
  `user_id`    int          NOT NULL,
  `content`    text         NOT NULL,
  `status`     varchar(32)  NOT NULL DEFAULT 'normal', -- 'normal', 'flagged', 'deleted'
  `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_msg_channel` (`channel_id`),
  KEY `fk_msg_user` (`user_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`channel_id`) REFERENCES `channels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_id`)    REFERENCES `auth` (`id`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── ads — Advertising Campaigns ───────────────────────────
DROP TABLE IF EXISTS `ads`;
CREATE TABLE `ads` (
  `id`          int           NOT NULL AUTO_INCREMENT,
  `name`        varchar(256)  NOT NULL,
  `type`        varchar(64)   NOT NULL, -- 'Display', 'Search', 'Social'
  `budget`      decimal(10,2) NOT NULL DEFAULT '0.00',
  `status`      varchar(32)   NOT NULL DEFAULT 'Active', -- 'Active', 'Paused', 'Archived'
  `impressions` int           NOT NULL DEFAULT 0,
  `clicks`      int           NOT NULL DEFAULT 0,
  `start_date`  date          DEFAULT NULL,
  `end_date`    date          DEFAULT NULL,
  `created_at`  datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS = 1;
