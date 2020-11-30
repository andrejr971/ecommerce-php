CREATE DATABASE store;

USE store;

CREATE TABLE users (
	`id` int UNSIGNED NOT NULL,
	`name` varchar(80) NOT NULL,
	`email` varchar(254) NOT NULL,
    `username` varchar(254) NOT NULL,
	`password` varchar(191) NOT NULL,
    `avatar` varchar(191) DEFAULT NULL,
    `permission` enum('0', '1'),
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
  
CREATE TABLE clients (
	`id` int UNSIGNED NOT NULL,
	`name` varchar(80) NOT NULL,
	`email` varchar(254) NOT NULL,
	`password` varchar(191) NOT NULL,
    `avatar` varchar(191) DEFAULT NULL,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
);

ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_email_unique` (`email`);

ALTER TABLE `clients`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
  
/*
* table brands
*/
CREATE TABLE brands (
	`id` int UNSIGNED NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NULL,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
);

ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `brands`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
  
/**
*table categories
*/
CREATE TABLE categories (
	`id` int UNSIGNED NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NULL,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

/**
*table categories
*/
CREATE TABLE products (
	`id` int UNSIGNED NOT NULL,
    `category_id` int UNSIGNED,
    `brand_id` int UNSIGNED,
    `discount` decimal (2,2) NULL,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
);
 
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_categories_id_foreign` (`category_id`),
  ADD KEY `products_bands_id_foreign` (`brand_id`);

ALTER TABLE `products`
  ADD COLUMN `name` VARCHAR (50) NOT NULL;
  
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `products`
  ADD CONSTRAINT `products_categories_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) 
	ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_bands_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) 
	ON DELETE SET NULL ON UPDATE CASCADE;
    
/*
* table product_variations
*/

CREATE TABLE product_variations (
	`id` int UNSIGNED NOT NULL,
    `name` varchar(255),
    `description` varchar(255),
    `slug` varchar(255),
    `price` decimal(8, 2) NOT NULL DEFAULT 0,
    `product_id` int UNSIGNED,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
);  

ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id_foreign` (`product_id`);

ALTER TABLE `product_variations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_variations`
  MODIFY `description` TEXT DEFAULT NULL;

ALTER TABLE `product_variations`
  ADD CONSTRAINT `product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE;

/*
* table product size
*/
CREATE TABLE product_sizes (
	`id` int UNSIGNED NOT NULL,
    `size` int NOT NULL,
    `quantity` int NOT NULL,
    `product_variation_id` int UNSIGNED,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
); 

ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variation_id_foreign` (`product_variation_id`);

ALTER TABLE `product_sizes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_variation_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE; 

/*
* table coupon
*/
CREATE TABLE coupons (
	`id` int UNSIGNED NOT NULL,
    `title` int UNSIGNED,
    `value` decimal(2,2) NOT NULL,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
); 

ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`);

/*
* table cart
*/
CREATE TABLE cart (
	`id` int UNSIGNED NOT NULL,
    `client_id` int UNSIGNED,
    `coupon_id` int UNSIGNED,
    `status` enum('0', '1') NOT NULL DEFAULT '0',
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
); 

ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id_foreign` (`client_id`),
  ADD KEY `coupon_id_foreign` (`coupon_id`);

ALTER TABLE `cart`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cart`
  ADD CONSTRAINT `client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE; 

/*
* table itens cart
*/
CREATE TABLE cart_items (
	`id` int UNSIGNED NOT NULL,
    `cart_id` int UNSIGNED,
    `product_variation_id` int UNSIGNED,
    `quantity_id` int NOT NULL,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
); 

ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variation_cart_items_id_foreign` (`product_variation_id`),
  ADD KEY `cart_id_foreign` (`cart_id`);

ALTER TABLE `cart_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cart_items`
  ADD CONSTRAINT `product_variation_cart_items_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) 
	ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE; 
    
/*
* table product size
*/
CREATE TABLE product_images (
	`id` int UNSIGNED NOT NULL,
    `path` varchar(191) NOT NULL,
    `product_variation_id` int UNSIGNED,
	`created_at` datetime DEFAULT NULL,
	`updated_at` datetime DEFAULT NULL
); 

ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variation_product_images_id_foreign` (`product_variation_id`);

ALTER TABLE `product_images`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `product_images`
  ADD CONSTRAINT `product_variation_id_product_images_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) 
	ON DELETE CASCADE ON UPDATE CASCADE; 





  
    