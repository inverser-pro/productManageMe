-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 13 2021 г., 21:37
-- Версия сервера: 5.6.34
-- Версия PHP: 5.3.10-1ubuntu3.26

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `test_name`
--

-- --------------------------------------------------------

--
-- Структура таблицы `flr_goods`
--

CREATE TABLE IF NOT EXISTS `flr_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  `priceopt` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `comment` mediumtext NOT NULL,
  `datecreate` datetime NOT NULL,
  `who` int(11) NOT NULL,
  `state` int(11) NOT NULL COMMENT '1-on,0-off,2-del',
  `din` date NOT NULL COMMENT 'приход',
  `suppliers` mediumtext NOT NULL,
  `valid` date NOT NULL COMMENT 'годен до',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=302 ;

--
-- Дамп данных таблицы `flr_goods`
--

INSERT INTO `flr_goods` (`id`, `name`, `price`, `priceopt`, `quantity`, `comment`, `datecreate`, `who`, `state`, `din`, `suppliers`, `valid`) VALUES
(58, 'Juvederm Voluma (2x1ml)', 8250, 0, 0, '0', '2020-02-24 09:57:21', 854, 1, '2021-02-13', '', '2021-05-13'),
(59, 'Surgiderm 30xp (2x0.8ml)', 5500, 0, 0, '0', '2020-02-24 09:57:21', 854, 1, '0000-00-00', '', '0000-00-00'),
(60, 'Surgiderm 24xp (2x0.8ml)', 5350, 0, 0, '0', '2020-02-24 09:57:21', 854, 1, '0000-00-00', '', '0000-00-00'),
(62, 'Surgiderm 30 (2x0.8)', 5600, 0, 0, '0', '2020-02-24 09:57:21', 854, 1, '0000-00-00', '', '0000-00-00');

-- --------------------------------------------------------

--
-- Структура таблицы `flr_sales`
--

CREATE TABLE IF NOT EXISTS `flr_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` int(11) NOT NULL DEFAULT '1' COMMENT '0-off,1-on,2-del',
  `pid` int(11) NOT NULL COMMENT 'product id',
  `date` date NOT NULL,
  `uid` int(11) NOT NULL COMMENT 'user id',
  `quantity` int(11) NOT NULL COMMENT 'количество продаж',
  `dateadd` datetime NOT NULL,
  `text` text NOT NULL COMMENT 'Описание',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `flr_sales`
--

INSERT INTO `flr_sales` (`id`, `state`, `pid`, `date`, `uid`, `quantity`, `dateadd`, `text`) VALUES
(1, 1, 21, '2020-03-31', 854, 1, '2020-03-09 22:51:12', '0'),
(2, 1, 21, '2020-03-09', 854, 2, '2020-03-09 22:54:00', 'ннн'),
(3, 1, 21, '2020-03-09', 854, 1, '2020-03-09 22:55:37', 'sdasdasd'),
(4, 1, 21, '2020-03-09', 854, 1, '2020-03-09 22:56:27', 'dfgdfgdfg'),
(5, 1, 24, '2020-03-09', 854, 123, '2020-03-09 22:58:29', 'уцкцукцу'),
(6, 1, 26, '2020-03-10', 854, 1, '2020-03-09 23:03:31', 'цукцукцук'),
(7, 1, 26, '2020-03-10', 854, 123, '2020-03-09 23:05:54', 'dfdfdddddddddddddddddddd'),
(8, 1, 26, '2020-03-25', 854, 666, '2020-03-10 12:11:34', 'asdas123123123'),
(9, 1, 21, '2020-03-10', 854, 22, '2020-03-10 01:41:58', '-22'),
(10, 1, 21, '2020-03-10', 854, 201, '2020-03-10 01:44:33', '-201 должно быть минус'),
(11, 1, 21, '2020-03-10', 854, 201, '2020-03-10 01:46:56', '-201 должно стать 0'),
(12, 1, 21, '2020-03-10', 854, 401, '2020-03-10 01:49:43', '-401'),
(13, 1, 21, '2020-04-19', 854, 1, '2020-04-19 09:30:45', 'wer');

-- --------------------------------------------------------

--
-- Структура таблицы `flr_suppliers`
--

CREATE TABLE IF NOT EXISTS `flr_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `date` datetime NOT NULL,
  `comment` mediumtext NOT NULL,
  `state` int(11) NOT NULL COMMENT '0-off,1-on,2-del',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `flr_suppliers`
--

INSERT INTO `flr_suppliers` (`id`, `name`, `date`, `comment`, `state`) VALUES
(1, 'RF', '2020-02-29 01:02:03', 'RF', 1),
(2, 'V-p', '2020-01-29 01:02:03', 'V-p', 1),
(3, 'FO', '2020-02-29 01:02:03', 'FO', 1),
(4, 'GC', '2020-02-29 20:41:59', 'GC', 1),
(5, 'L', '2020-02-29 20:42:12', 'L', 1),
(6, 'qweqwwqeqweghgfhfg+', '2020-02-29 20:43:14', 'xcvxcvxcvxcv', 2),
(7, 'N', '2020-03-06 00:23:05', '', 1),
(8, 'U', '2020-03-06 00:23:28', '', 1),
(9, 'O', '2020-03-06 00:24:22', '', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `flr_users`
--

CREATE TABLE IF NOT EXISTS `flr_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `dateCreate` datetime NOT NULL,
  `role` int(1) NOT NULL,
  `comment` text NOT NULL,
  `password` text NOT NULL,
  `state` int(11) NOT NULL COMMENT '1-on,0-delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=859 ;

--
-- Дамп данных таблицы `flr_users`
--

INSERT INTO `flr_users` (`id`, `name`, `email`, `dateCreate`, `role`, `comment`, `password`, `state`) VALUES
(854, 'Admin', 'admin@admin.com', '2020-02-21 09:45:23', 1, 'Не удаляйте эту учетную запись...', '$2y$12$htAEW787AfBrw68wingdkezjPomIicEmU7SU8zoiWaYCqdMJhxr.6', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
