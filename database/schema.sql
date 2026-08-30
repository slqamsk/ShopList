-- =============================================
-- Схема базы данных для ShopList
-- =============================================

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Таблица домохозяйств
CREATE TABLE IF NOT EXISTS `households` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `created_by` INT(11) NOT NULL COMMENT 'администратор',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Участники домохозяйства
CREATE TABLE IF NOT EXISTS `household_members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `household_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `role` ENUM('admin', 'member') NOT NULL DEFAULT 'member',
  `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `household_user` (`household_id`, `user_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Приглашения в домохозяйство
CREATE TABLE IF NOT EXISTS `invitations` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `household_id` INT(11) NOT NULL,
  `inviter_id` INT(11) NOT NULL,
  `invitee_email` VARCHAR(100) DEFAULT NULL,
  `code` VARCHAR(64) NOT NULL UNIQUE,
  `status` ENUM('pending', 'accepted', 'expired') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`code`),   -- ← имя изменено
  KEY `household_id` (`household_id`),
  KEY `inviter_id` (`inviter_id`),
  FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`inviter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Продукты (привязаны к домохозяйству)
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `household_id` INT(11) NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `household_id` (`household_id`),
  FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Список покупок (привязан к домохозяйству)
CREATE TABLE IF NOT EXISTS `shopping_list` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `household_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` VARCHAR(20) DEFAULT NULL,
  `status` ENUM('pending', 'bought', 'not_available') NOT NULL DEFAULT 'pending',
  `added_by` INT(11) NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `household_id` (`household_id`),
  KEY `product_id` (`product_id`),
  KEY `added_by` (`added_by`),
  FOREIGN KEY (`household_id`) REFERENCES `households` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
