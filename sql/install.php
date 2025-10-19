<?php
$sql = [];

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "customer_phone` (
    `id_customer_phone` int(11) NOT NULL AUTO_INCREMENT,
    `id_customer` int(11) NOT NULL,
    `phone` varchar(32) NOT NULL,
    `verified` tinyint(1) NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_customer_phone`),
    KEY `id_customer` (`id_customer`),
    KEY `phone` (`phone`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "mobilelogin_verification` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `phone` varchar(32) NOT NULL,
    `code` varchar(12) NOT NULL,
    `id_customer` int(11) NULL,
    `verified` tinyint(1) NOT NULL DEFAULT '0',
    `expires_at` datetime NOT NULL,
    `created_at` datetime NOT NULL,
    `verified_at` datetime NULL,
    PRIMARY KEY (`id`),
    KEY `phone_code` (`phone`, `code`),
    KEY `expires_at` (`expires_at`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "mobilelogin_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `value` text NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
