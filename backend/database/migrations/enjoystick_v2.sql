CREATE TABLE IF NOT EXISTS `users` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`name` varchar(100) NOT NULL,
	`email` varchar(255) NOT NULL UNIQUE,
	`email_verified_at` timestamp,
	`password` varchar(255) NOT NULL,
	`role` enum('cliente', 'admin') NOT NULL DEFAULT 'cliente',
	`timestamps` timestamp NOT NULL COMMENT 'created_at e updated_at',
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
	`email` varchar(255) NOT NULL UNIQUE,
	`token` varchar(255) NOT NULL,
	`created_at` timestamp,
	PRIMARY KEY (`email`)
);

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`tokenable_type` varchar(255) NOT NULL COMMENT 'morphs: model dono do token',
	`tokenable_id` int NOT NULL COMMENT 'morphs: id do model dono do token',
	`name` text NOT NULL,
	`token` varchar(64) NOT NULL UNIQUE,
	`abilities` text,
	`last_used_at` timestamp,
	`expires_at` timestamp COMMENT 'index',
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `categories` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`name` varchar(100) NOT NULL UNIQUE,
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `products` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`category_id` int NOT NULL,
	`name` varchar(255) NOT NULL,
	`description` text,
	`price` decimal(10, 2) NOT NULL COMMENT 'unsigned',
	`image` varchar(255),
	`stock` int NOT NULL DEFAULT 0 COMMENT 'unsigned',
	`active` tinyint NOT NULL DEFAULT 1 COMMENT 'exclusao logica: 1 ativo, 0 inativo',
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `carts` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`user_id` int UNIQUE COMMENT 'nulo = carrinho de visitante; unique = 1 carrinho por usuario',
	`token` varchar(64) UNIQUE COMMENT 'token opaco do visitante; nulo apos fusao/login',
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `cart_items` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`cart_id` int NOT NULL,
	`product_id` int NOT NULL,
	`quantity` int NOT NULL DEFAULT 1 COMMENT 'unsigned, minimo 1',
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `addresses` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`user_id` int NOT NULL,
	`recipient_name` varchar(100) NOT NULL,
	`postal_code` varchar(20) NOT NULL,
	`street` varchar(150) NOT NULL,
	`number` varchar(20) NOT NULL,
	`complement` varchar(100),
	`neighborhood` varchar(100) NOT NULL,
	`city` varchar(100) NOT NULL,
	`state` varchar(100) NOT NULL,
	`country` varchar(100) NOT NULL DEFAULT 'Brasil',
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `orders` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`user_id` int NOT NULL,
	`status` enum('aguardando_pagamento', 'pago', 'enviado', 'entregue', 'cancelado') NOT NULL DEFAULT 'aguardando_pagamento',
	`subtotal` decimal(10, 2) NOT NULL COMMENT 'soma dos itens',
	`shipping_cost` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'frete fixo ou regra documentada',
	`total` decimal(10, 2) NOT NULL COMMENT 'subtotal + frete, calculado no servidor',
	`shipping_recipient_name` varchar(100) NOT NULL COMMENT 'snapshot do endereco no momento do pedido',
	`shipping_postal_code` varchar(20) NOT NULL,
	`shipping_street` varchar(150) NOT NULL,
	`shipping_number` varchar(20) NOT NULL,
	`shipping_complement` varchar(100),
	`shipping_neighborhood` varchar(100) NOT NULL,
	`shipping_city` varchar(100) NOT NULL,
	`shipping_state` varchar(100) NOT NULL,
	`shipping_country` varchar(100) NOT NULL,
	`paid_at` datetime COMMENT 'data/hora do pagamento simulado',
	`sent_at` datetime,
	`arrived_at` datetime,
	`cancelled_at` datetime,
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `order_items` (
	`id` int AUTO_INCREMENT NOT NULL UNIQUE,
	`order_id` int NOT NULL,
	`product_id` int NOT NULL,
	`product_name` varchar(255) NOT NULL COMMENT 'nome congelado no momento da compra',
	`unit_price` decimal(10, 2) NOT NULL COMMENT 'preco unitario congelado no momento da compra',
	`quantity` int NOT NULL COMMENT 'unsigned',
	`timestamps` timestamp NOT NULL,
	PRIMARY KEY (`id`)
);

ALTER TABLE `products` ADD CONSTRAINT `products_fk1` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT;
ALTER TABLE `carts` ADD CONSTRAINT `carts_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `cart_items` ADD CONSTRAINT `cart_items_fk1` FOREIGN KEY (`cart_id`) REFERENCES `carts`(`id`) ON DELETE CASCADE;
ALTER TABLE `cart_items` ADD CONSTRAINT `cart_items_fk2` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE;
ALTER TABLE `addresses` ADD CONSTRAINT `addresses_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `orders` ADD CONSTRAINT `orders_fk1` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT;
ALTER TABLE `order_items` ADD CONSTRAINT `order_items_fk1` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE;
ALTER TABLE `order_items` ADD CONSTRAINT `order_items_fk2` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT;

CREATE UNIQUE INDEX `uq_cart_items_cart_product` ON `cart_items` (`cart_id`, `product_id`);
CREATE INDEX `idx_products_category_id` ON `products` (`category_id`);
CREATE INDEX `idx_products_active` ON `products` (`active`);
CREATE INDEX `idx_orders_status` ON `orders` (`status`);
CREATE INDEX `idx_personal_access_tokens_tokenable` ON `personal_access_tokens` (`tokenable_type`, `tokenable_id`);
CREATE INDEX `idx_personal_access_tokens_expires_at` ON `personal_access_tokens` (`expires_at`);
