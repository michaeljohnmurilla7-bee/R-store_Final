-- ============================================================
--  rstore - Sales Inventory System
--  Database: MySQL / MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS rstore
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE rstore;

-- ------------------------------------------------------------
-- 1. users (custom version) - FIXED data type
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,  -- Changed from int(11) to INT UNSIGNED
  `uuid` char(36) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT 'user',
  `status` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 2. suppliers
-- ------------------------------------------------------------
CREATE TABLE suppliers (
  id              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name            VARCHAR(150)  NOT NULL,
  contact_person  VARCHAR(100)  NULL,
  phone           VARCHAR(30)   NULL,
  email           VARCHAR(150)  NULL,
  address         TEXT          NULL,
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. categories
-- ------------------------------------------------------------
CREATE TABLE categories (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100)  NOT NULL,
  description TEXT          NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. products
-- ------------------------------------------------------------
CREATE TABLE products (
  id             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  category_id    INT UNSIGNED     NOT NULL,
  supplier_id    INT UNSIGNED     NOT NULL,
  name           VARCHAR(200)     NOT NULL,
  sku            VARCHAR(100)     NOT NULL UNIQUE,
  description    TEXT             NULL,
  cost_price     DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  price          DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  stock_qty      INT              NOT NULL DEFAULT 0,
  reorder_level  INT              NOT NULL DEFAULT 0,
  is_active      TINYINT(1)       NOT NULL DEFAULT 1,
  created_at     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories (id),
  CONSTRAINT fk_products_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. customers
-- ------------------------------------------------------------
CREATE TABLE customers (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name        VARCHAR(150)  NOT NULL,
  phone       VARCHAR(30)   NULL,
  email       VARCHAR(150)  NULL,
  address     TEXT          NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. sales
-- ------------------------------------------------------------
CREATE TABLE sales (
  id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  user_id       INT UNSIGNED     NOT NULL,  -- Now matches users table
  customer_id   INT UNSIGNED     NULL,
  sale_date     DATE             NOT NULL,
  total_amount  DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  discount      DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  amount_paid   DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  status        ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'completed',
  notes         TEXT             NULL,
  created_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_sales_user     FOREIGN KEY (user_id)     REFERENCES users (id),
  CONSTRAINT fk_sales_customer FOREIGN KEY (customer_id) REFERENCES customers (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 7. sale_items
-- ------------------------------------------------------------
CREATE TABLE sale_items (
  id          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  sale_id     INT UNSIGNED   NOT NULL,
  product_id  INT UNSIGNED   NOT NULL,
  quantity    INT            NOT NULL DEFAULT 1,
  unit_price  DECIMAL(12,2)  NOT NULL,
  subtotal    DECIMAL(12,2)  NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_saleitems_sale    FOREIGN KEY (sale_id)    REFERENCES sales    (id) ON DELETE CASCADE,
  CONSTRAINT fk_saleitems_product FOREIGN KEY (product_id) REFERENCES products (id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 8. stock_adjustments
-- ------------------------------------------------------------
CREATE TABLE stock_adjustments (
  id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED  NOT NULL,
  user_id     INT UNSIGNED  NOT NULL,  -- Now matches users table
  type        ENUM('in','out') NOT NULL,
  quantity    INT           NOT NULL,
  reason      TEXT          NULL,
  created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_stockadj_product FOREIGN KEY (product_id) REFERENCES products (id),
  CONSTRAINT fk_stockadj_user    FOREIGN KEY (user_id)    REFERENCES users (id)
) ENGINE=InnoDB;

-- ============================================================
--  TRIGGERS
--  Automatically update stock_qty after sales and adjustments
-- ============================================================

DELIMITER $$

-- Decrease stock when a sale item is inserted
CREATE TRIGGER trg_sale_item_insert
AFTER INSERT ON sale_items
FOR EACH ROW
BEGIN
  UPDATE products
  SET    stock_qty = stock_qty - NEW.quantity
  WHERE  id = NEW.product_id;
END$$

-- Restore stock if a sale item is deleted (e.g. sale cancelled)
CREATE TRIGGER trg_sale_item_delete
AFTER DELETE ON sale_items
FOR EACH ROW
BEGIN
  UPDATE products
  SET    stock_qty = stock_qty + OLD.quantity
  WHERE  id = OLD.product_id;
END$$

-- Apply stock adjustment (in or out)
CREATE TRIGGER trg_stock_adjustment_insert
AFTER INSERT ON stock_adjustments
FOR EACH ROW
BEGIN
  IF NEW.type = 'in' THEN
    UPDATE products SET stock_qty = stock_qty + NEW.quantity WHERE id = NEW.product_id;
  ELSE
    UPDATE products SET stock_qty = stock_qty - NEW.quantity WHERE id = NEW.product_id;
  END IF;
END$$

DELIMITER ;

-- ============================================================
--  SEED DATA — default admin account
--  Password: your_password_here
-- ============================================================

INSERT INTO `users` (`id`, `uuid`, `email`, `password`, `role`, `status`, `name`, `phone`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'admin@rstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'Active', 'Administrator', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);

INSERT INTO categories (name, description) VALUES
('General', 'Default product category');
