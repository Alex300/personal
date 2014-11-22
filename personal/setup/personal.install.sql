--
-- Структура таблицы `cot_personal_categories`
--
CREATE TABLE IF NOT EXISTS `cot_personal_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `parent` int(11) DEFAULT 0,
  PRIMARY KEY(`id`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Категории';

--
-- Структура таблицы `cot_personal_staff`
--
CREATE TABLE IF NOT EXISTS `cot_personal_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `order` int(11) DEFAULT '0',
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Уровни в штатном расписании';

--
-- Дамп данных таблицы `cot_personal_staff`
--
INSERT INTO `cot_personal_staff` (`id`, `title`, `order`) VALUES
(1, 'Руководитель высшего звена', 1),
(2, 'Руководитель подразделения', 2),
(3, 'Менеджер проектов / Руководитель отдела', 3),
(4, 'Специалист', 4),
(5, 'Ассистент / Стажер', 5);


--
-- Структура таблицы `cot_personal_education_levels`
--
CREATE TABLE IF NOT EXISTS `cot_personal_education_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `order` int(11) DEFAULT '0',
  PRIMARY KEY(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Уровни образования';

--
-- Дамп данных таблицы `cot_personal_education_levels`
--
INSERT INTO `cot_personal_education_levels` (`id`, `title`, `order`) VALUES
  (1, 'Неполное среднее', 1),
  (2, 'Полное среднее', 2),
  (3, 'Средне-специальное', 3),
  (4, 'Неоконченное высшее', 4),
  (5, 'Высшее', 5),
  (6, 'Кандидат наук', 6),
  (7, 'Доктор наук', 7);


--
-- Структура таблицы `cot_personal_empl_profiles`
--
CREATE TABLE IF NOT EXISTS `cot_personal_empl_profiles` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `locked` TINYINT UNSIGNED DEFAULT 0,
  `title` VARCHAR(255),
  `alias` VARCHAR(255),
  `text` text DEFAULT '',
  `address` VARCHAR(255),
  `pphone` VARCHAR(255),
  `pemail` VARCHAR(255),
  `site` VARCHAR(255),
  `in_main_to` DATETIME DEFAULT '1970-01-01 00:00:00',
  `brand_bg` VARCHAR(255),
  `brand_tpl` VARCHAR(255),
  `brand_css` VARCHAR(255),
  `brand_bg_bot` VARCHAR(255),
  `is_default` TINYINT UNSIGNED DEFAULT 0 COMMENT 'По умолчанию?',
  `type` TINYINT UNSIGNED DEFAULT 0  COMMENT 'Тип профиля. 0 - прямой, 1 - кадровое агентство',
  `anonim` TINYINT UNSIGNED DEFAULT 0  COMMENT 'Анономный профиль?',
  `created` DATETIME,
  `created_by` int UNSIGNED,
  `updated` DATETIME,
  `updated_by`int UNSIGNED,
  PRIMARY KEY(`id`),
  KEY `locked` (`locked`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Профили работодателей';


--
-- Структура таблицы `cot_personal_vacancies`
--
CREATE TABLE IF NOT EXISTS `cot_personal_vacancies`
(
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `profile` int DEFAULT 0,
  `user_id` int UNSIGNED NOT NULL,
  `title` VARCHAR(255) DEFAULT '',
  `alias` VARCHAR(255) DEFAULT '',
  `text` text NOT NULL DEFAULT '',
  `city` int DEFAULT 0,
  `district` VARCHAR(255) DEFAULT '',
  `salary` int DEFAULT 0,
  `vcontact_face`  VARCHAR(255) DEFAULT '',
  `vemail`  VARCHAR(255) DEFAULT '',
  `vphone`  VARCHAR(255) DEFAULT '',
  `skills` text DEFAULT '',
  `experience` smallint UNSIGNED DEFAULT 0,
  `education` smallint UNSIGNED DEFAULT 0,
  `status` TINYINT UNSIGNED DEFAULT 0,
  `active` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Флаг активной вакансии',
  `active_to` DATETIME DEFAULT '1970-01-01 00:00:00',
  `hot` TINYINT UNSIGNED DEFAULT 0,
  `hot_to` DATETIME ,
  `views` int DEFAULT 0,
  `activated` DATETIME ,
  `deactivated` DATETIME COMMENT 'Дата полследнего отключения вакансии',
  `sort` DATETIME DEFAULT '1970-01-01 00:00:00' COMMENT 'Поле для сортировки',
  `created` DATETIME,
  `created_by` int UNSIGNED,
  `updated` DATETIME,
  `updated_by`int UNSIGNED,
  PRIMARY KEY(`id`),
  KEY `active_active_to` (`active`, `active_to`),
  KEY `active` (`active`),
  KEY `active_to` (`active_to`),
  KEY `hot_hot_to` (`hot`, `hot_to`),
  KEY `hot` (`hot`),
  KEY `hot_to` (`hot_to`),
  KEY `city` (`city`),
  KEY `user_id` (`user_id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Вакансии';


--
-- Структура таблицы `cot_personal_vacancies_employment`
--
CREATE TABLE IF NOT EXISTS `cot_personal_vacancies_employment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `vacancy_id` bigint(20) NOT NULL,
  `empl_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vacancy_id` (`vacancy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Занятость';


--
-- Структура таблицы `cot_personal_vacancies_schedule`
--
CREATE TABLE IF NOT EXISTS `cot_personal_vacancies_schedule` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `vacancy_id` bigint(20) NOT NULL,
  `sche_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vacancy_id` (`vacancy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='График работы';

--
-- Структура таблицы `cot_personal_vacancies_link_cot_personal_categories`
--
CREATE TABLE IF NOT EXISTS `cot_personal_vacancies_link_cot_personal_categories` (
  `xref_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cot_personal_vacancies_id` int(11) DEFAULT NULL,
  `cot_personal_categories_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY(`xref_id`),
  KEY `cot_personal_vacancies_id` (`cot_personal_vacancies_id`),
  KEY `cot_personal_categories_id` (`cot_personal_categories_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Структура таблицы `cot_personal_vacancies_link_cot_personal_staff`
--
CREATE TABLE IF NOT EXISTS `cot_personal_vacancies_link_cot_personal_staff` (
  `xref_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cot_personal_vacancies_id` int(11) DEFAULT NULL,
  `cot_personal_staff_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY(`xref_id`),
  KEY `cot_personal_vacancies_id` (`cot_personal_vacancies_id`),
  KEY `cot_personal_staff_id` (`cot_personal_staff_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Структура таблицы `cot_personal_resumes`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `title` VARCHAR(255) DEFAULT '',
  `alias` VARCHAR(255) DEFAULT '',
  `text` text DEFAULT '' COMMENT 'Дополнительная информация',
  `city` int DEFAULT 0,
  `city_name` VARCHAR(255) DEFAULT '',
  `district` VARCHAR(255) DEFAULT '',
  `salary` int DEFAULT 0,
  `remail`  VARCHAR(255) DEFAULT '',
  `rphone`  VARCHAR(255) DEFAULT '',
  `other_contacts` text DEFAULT '' ,
  `skills` text DEFAULT '',
  `education_level` smallint UNSIGNED DEFAULT 0,
  `experience` smallint UNSIGNED DEFAULT 0,
  `status` TINYINT UNSIGNED DEFAULT 0,
  `active` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Флаг активного резюме',
  `hot` TINYINT UNSIGNED DEFAULT 0,
  `hot_to` DATETIME ,
  `views` int DEFAULT 0,
  `activated` DATETIME ,
  `deactivated` DATETIME COMMENT 'Дата полследнего отключения резюме',
  `note` text DEFAULT '' COMMENT 'Примечания модератора',
  `deny_unregister` TINYINT UNSIGNED DEFAULT 0 COMMENT 'Запретить к просмотру для незарегов',
  `sort` DATETIME DEFAULT '1970-01-01 00:00:00' COMMENT 'Поле для сортировки',
  `created` DATETIME,
  `created_by` int UNSIGNED,
  `updated` DATETIME,
  `updated_by`int UNSIGNED,
  PRIMARY KEY(`id`),
  KEY `active` (`active`),
  KEY `hot_hot_to` (`hot`, `hot_to`),
  KEY `hot` (`hot`),
  KEY `hot_to` (`hot_to`),
  KEY `city` (`city`),
  KEY `user_id` (`user_id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Резюме';

--
-- Структура таблицы `cot_personal_resumes_lang_levels`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_lang_levels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resume_id` bigint(20) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `level_id` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `resume_id` (`resume_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Уровни владения иностранными языками';


--
-- Структура таблицы `cot_personal_resumes_education`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_education` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resume_id` bigint(20) NOT NULL COMMENT 'id резюме',
  `level_id` smallint(6) DEFAULT '0' COMMENT 'уровень образования',
  `title` varchar(255) DEFAULT '' COMMENT 'название учебного заведения',
  `faculty` varchar(255)  DEFAULT '' COMMENT 'факультет',
  `specialty` varchar(255) DEFAULT '' COMMENT 'специальность',
  `year` smallint(6) DEFAULT '0' COMMENT 'год окончания',
  PRIMARY KEY (`id`),
  KEY `resume_id` (`resume_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Резюме - образование';


--
-- Структура таблицы `cot_personal_resumes_recommendations`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_recommendations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resume_id` bigint(20) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '' COMMENT 'Имя',
  `position` varchar(255) DEFAULT '' COMMENT 'Должность',
  `organization` varchar(255) DEFAULT '' COMMENT 'Организация',
  `phone` varchar(255) DEFAULT '' COMMENT 'Телефон',
  PRIMARY KEY (`id`),
  KEY `resume_id` (`resume_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Рекомендации' ;


--
-- Структура таблицы `cot_personal_resumes_experience`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_experience` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `resume_id` bigint(20) unsigned NOT NULL,
  `organization` varchar(255) DEFAULT '' COMMENT 'Организация',
  `city` int(11) DEFAULT '0' COMMENT 'Город',
  `website` varchar(255) DEFAULT '' COMMENT 'Сайт компании',
  `position` varchar(255) DEFAULT '' COMMENT 'Должность',
  `begin` date DEFAULT NULL COMMENT 'Начало работы',
  `end` date DEFAULT NULL COMMENT 'Окончание',
  `for_now` tinyint(4) DEFAULT '0' COMMENT 'По настоящее время',
  `achievements` text COMMENT 'Обязанности, функции, достижения',
  PRIMARY KEY (`id`),
  KEY `resume_id` (`resume_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Опыт работы' ;


--
-- Структура таблицы `cot_personal_resumes_employment`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_employment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resume_id` bigint(20) NOT NULL,
  `empl_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resume_id` (`resume_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Занятость';


--
-- Структура таблицы `cot_personal_resumes_schedule`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_schedule` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resume_id` bigint(20) NOT NULL,
  `sche_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resume_id` (`resume_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='График работы';


CREATE TABLE IF NOT EXISTS `cot_personal_languages` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) DEFAULT '',
  `sort` int UNSIGNED DEFAULT 0,
  PRIMARY KEY(`id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Языки';

--
-- Дамп данных таблицы `cot_personal_languages`
--
INSERT INTO `cot_personal_languages` (`id`, `title`, `sort`) VALUES
  (1, 'Русский', 1),
  (2, 'English', 2),
  (3, 'German', 3),
  (4, 'Spanish', 4),
  (5, 'French', 5),
  (6, 'Italian', 6),
  (7, 'Japanese', 7),
  (8, 'Korean', 8),
  (9, 'Lithuanian', 9),
  (10, 'Polish', 10),
  (11, 'Portuguese', 11),
  (12, 'Traditional Chinese', 12),
  (13, 'Ukrainian', 13),
  (14, 'Chinese Simplified', 14),
  (15, 'Bulgarian', 15),
  (16, 'Dutch', 16),
  (17, 'Latvian', 17),
  (18, 'Arabic', 18),
  (19, 'Hebrew', 19),
  (20, 'Greek', 20),
  (21, 'Romanian', 21),
  (22, 'Czech', 22),
  (23, 'Finnish', 23),
  (24, 'Swedish', 24),
  (25, 'Norwegian', 25),
  (26, 'Danish', 26),
  (27, 'Serbian', 27),
  (28, 'Slovak', 28),
  (29, 'Slovenian', 29),
  (30, 'Croatian', 30),
  (31, 'Turkish', 31),
  (32, 'Hindi', 32);


--
-- Структура таблицы `cot_personal_resumes_link_cot_city`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_link_cot_city` (
  `xref_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cot_personal_resumes_id` int(11) DEFAULT NULL,
  `cot_city_city_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`xref_id`),
  KEY `personal_resumes_id` (`cot_personal_resumes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Структура таблицы `cot_personal_resumes_link_cot_personal_categories`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_link_cot_personal_categories` (
  `xref_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cot_personal_resumes_id` int(11) DEFAULT NULL,
  `cot_personal_categories_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`xref_id`),
  KEY `personal_resumes_id` (`cot_personal_resumes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Структура таблицы `cot_personal_resumes_link_cot_personal_staff`
--
CREATE TABLE IF NOT EXISTS `cot_personal_resumes_link_cot_personal_staff` (
  `xref_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cot_personal_resumes_id` int(11) DEFAULT NULL,
  `cot_personal_staff_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`xref_id`),
  KEY `personal_resumes_id` (`cot_personal_resumes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
