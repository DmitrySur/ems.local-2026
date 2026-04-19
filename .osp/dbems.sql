-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0:3306
-- Время создания: Апр 02 2026 г., 19:02
-- Версия сервера: 8.0.45
-- Версия PHP: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `dbems`
--

-- --------------------------------------------------------

--
-- Структура таблицы `dispatch_areas`
--

CREATE TABLE `dispatch_areas` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `group_infrastructure_object_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на группу объектов инфраструктуры',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Диспетчерские участки';

-- --------------------------------------------------------

--
-- Структура таблицы `disp_ar_gr_infr_obj_pivot`
--

CREATE TABLE `disp_ar_gr_infr_obj_pivot` (
  `id` bigint UNSIGNED NOT NULL,
  `group_infrastructure_object_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на группу объектов инфраструктуры',
  `dispatch_area_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на диспетчерский участок'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Сводная таблица диспетчерских участков и групп объектов инфраструктуры';

-- --------------------------------------------------------

--
-- Структура таблицы `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Полное наименование',
  `short_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Краткое наименование',
  `has_group_object` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Флаг привязки подразделения к группам объектов инфраструктуры',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `group_infrastructure_object_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на группу объектов инфраструктуры при наличии',
  `report_position` int DEFAULT NULL COMMENT 'Сортировка последовательности для отчетов'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Подразделение';

-- --------------------------------------------------------

--
-- Структура таблицы `drop_voltages`
--

CREATE TABLE `drop_voltages` (
  `id` bigint UNSIGNED NOT NULL,
  `datetime_drop` datetime NOT NULL COMMENT 'Дата и время посадки',
  `group_infrastructure_object_id` bigint UNSIGNED NOT NULL,
  `detail_location` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Уточнение местоположения',
  `detail_drop` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Уточнение по посадке',
  `status_drop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Статус устранения инцидента',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Основная таблица с посадками';

-- --------------------------------------------------------

--
-- Структура таблицы `drop_voltage_devices`
--

CREATE TABLE `drop_voltage_devices` (
  `id` bigint UNSIGNED NOT NULL,
  `drop_voltage_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Тип устройства',
  `name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Наименование',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Статус',
  `comment` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Отключенные устройства по посадке';

-- --------------------------------------------------------

--
-- Структура таблицы `drop_voltage_event_chronicles`
--

CREATE TABLE `drop_voltage_event_chronicles` (
  `id` bigint UNSIGNED NOT NULL,
  `datetime_event` datetime NOT NULL COMMENT 'Дата и время события',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Описание события',
  `drop_voltage_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на посадку',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='События хроники посадки';

-- --------------------------------------------------------

--
-- Структура таблицы `event_chronicles`
--

CREATE TABLE `event_chronicles` (
  `id` bigint UNSIGNED NOT NULL,
  `datetime_event` datetime NOT NULL COMMENT 'Дата и время события',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Описание события',
  `is_show_in_reports` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT 'Флаг отражения события в сводке',
  `incident_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на инцидент',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='События хроники инцидентов';

-- --------------------------------------------------------

--
-- Структура таблицы `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `group_infrastructure_objects`
--

CREATE TABLE `group_infrastructure_objects` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование группы объектов инфраструктуры',
  `short_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Краткое наименование группы объектов инфраструктуры',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Группы объектов инфраструктуры';

-- --------------------------------------------------------

--
-- Структура таблицы `incidents`
--

CREATE TABLE `incidents` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `datetime_incident` datetime NOT NULL COMMENT 'Дата и время инцидента',
  `object_infrastructure_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на объект инфраструктуры',
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Местоположение',
  `detail_location` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Уточнение местоположения',
  `reported_by` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Сообщил',
  `division_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на подразделение',
  `incident_type_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на тип инцидента',
  `itu_specie_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на вид инцидента',
  `itu_characteristic_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на характеристику ИТУ',
  `itu_directory_object_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на характеристику ИТУ',
  `itu_fault_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на неисправность',
  `itu_element_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на элемент',
  `itu_reason_breakdown_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на элемент',
  `detail_object_incident` varchar(450) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Уточнение по объекту инцидента',
  `detail_incident` varchar(450) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Уточнение по инциденту/неисправности',
  `incident_classification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Тип инцидента (ННР/ТО и т.д.)',
  `number_nnr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Номер ННР при наличии',
  `appropriate_measures` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Принятые меры',
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Примечание',
  `status_resolution` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Статус устранения инцидента',
  `status_incident` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'opened' COMMENT 'Статус карточки инцидента',
  `dispatch_area_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на курирующий участок ДП',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `repair_date` datetime DEFAULT NULL COMMENT 'Дата вывода из ремонта',
  `is_in_report` tinyint(1) DEFAULT '1' COMMENT 'Флаг отражения инцидента в сводке',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Дата мягкого удаления (корзина)',
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `drop_voltage_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на посадку - причину инцидента'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Основная таблица с инцидентами';

-- --------------------------------------------------------

--
-- Структура таблицы `incident_employee_information`
--

CREATE TABLE `incident_employee_information` (
  `id` bigint UNSIGNED NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Должность работника',
  `fio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ФИО работника',
  `information_time` time NOT NULL COMMENT 'Время оповещения',
  `incident_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на инцидент',
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Оповещение работников об инциденте';

-- --------------------------------------------------------

--
-- Структура таблицы `incident_employee_referrals`
--

CREATE TABLE `incident_employee_referrals` (
  `id` bigint UNSIGNED NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Должность работника',
  `fio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ФИО работника',
  `direction_time` time NOT NULL COMMENT 'Время направления',
  `arrival_time` time DEFAULT NULL COMMENT 'Время прибытия',
  `incident_type_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на инцидент',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Информация о направлении работников';

-- --------------------------------------------------------

--
-- Структура таблицы `incident_types`
--

CREATE TABLE `incident_types` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование типа инцидента',
  `has_characteristic` tinyint NOT NULL DEFAULT '0' COMMENT 'Флаг, что данный тип имеет характеристики',
  `has_elements` tinyint NOT NULL DEFAULT '0' COMMENT 'Флаг, что данный тип имеет элементы',
  `has_faults` tinyint NOT NULL DEFAULT '0' COMMENT 'Флаг, что данный тип имеет неисправности',
  `has_directory_objects` tinyint NOT NULL DEFAULT '0' COMMENT 'Флаг, что данный тип имеет справочник ИТУ',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `has_species` tinyint NOT NULL DEFAULT '0' COMMENT 'Флаг, что данный тип имеет виды ИТУ',
  `reported_by_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Перечень значений для поля "сообщил"',
  `report_position` int DEFAULT NULL COMMENT 'Сортировка последовательности для отчетов'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `inf_objs_inspections_pivot`
--

CREATE TABLE `inf_objs_inspections_pivot` (
  `id` bigint UNSIGNED NOT NULL,
  `object_infrastructure_id` bigint UNSIGNED NOT NULL,
  `inspection_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Сводная таблица проверок и местонахождений';

-- --------------------------------------------------------

--
-- Структура таблицы `inspections`
--

CREATE TABLE `inspections` (
  `id` bigint UNSIGNED NOT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Должность проверяющего',
  `fio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ФИО проверяющего',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Тип проверки',
  `date_start` date NOT NULL COMMENT 'Дата начала проверки',
  `division_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на подразделение проверяющего',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `updated_by` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED DEFAULT NULL,
  `inspector_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на проверяющего',
  `start_time` time DEFAULT NULL COMMENT 'Дата начала проверки',
  `end_time` time DEFAULT NULL COMMENT 'Дата окончания проверки',
  `subdivisions` json DEFAULT NULL COMMENT 'Проверяемые участки'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица учета проверок (ночные, день. без. и т.д.';

-- --------------------------------------------------------

--
-- Структура таблицы `inspectors`
--

CREATE TABLE `inspectors` (
  `id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `itu_characteristics`
--

CREATE TABLE `itu_characteristics` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `incident_type_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на тип инцидента',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Характеристики ИТУ';

-- --------------------------------------------------------

--
-- Структура таблицы `itu_directory_objects`
--

CREATE TABLE `itu_directory_objects` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `incident_type_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на тип инцидента',
  `itu_specie_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на вид ИДУ',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Справочник ИТУ';

-- --------------------------------------------------------

--
-- Структура таблицы `itu_elements`
--

CREATE TABLE `itu_elements` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `incident_type_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на тип инцидента',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Элементы ИТУ';

-- --------------------------------------------------------

--
-- Структура таблицы `itu_faults`
--

CREATE TABLE `itu_faults` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `incident_type_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на тип инцидента',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Неисправности ИТУ';

-- --------------------------------------------------------

--
-- Структура таблицы `itu_reason_breakdowns`
--

CREATE TABLE `itu_reason_breakdowns` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `incident_type_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на тип инцидента',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Причины неисправностей ИТУ';

-- --------------------------------------------------------

--
-- Структура таблицы `itu_species`
--

CREATE TABLE `itu_species` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `incident_type_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на тип инцидента',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `has_directory_objects` tinyint NOT NULL DEFAULT '0' COMMENT 'Флаг, что данный вид имеет значения в справочнике'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Виды ИТУ';

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `object_infrastructures`
--

CREATE TABLE `object_infrastructures` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(800) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Наименование объекта',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Тип объекта',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `group_infrastructure_object_id` bigint UNSIGNED NOT NULL COMMENT 'Ссылка на группу обьекта инфраструктуры',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Объекты инфраструктуры';

-- --------------------------------------------------------

--
-- Структура таблицы `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Описание права'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `description` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Описание роли на русском языке',
  `guard` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `login` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Логин пользователя',
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Должность пользователя на предприятии',
  `dispatch_area_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на диспетчерский участок'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user_dispatch_areas_pivot`
--

CREATE TABLE `user_dispatch_areas_pivot` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на пользователя',
  `dispatch_area_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Ссылка на диспетчерский участок'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Сводная таблица диспетчерских участков и пользователей';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `dispatch_areas`
--
ALTER TABLE `dispatch_areas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispatch_areas_group_infrastructure_object_id_foreign` (`group_infrastructure_object_id`);

--
-- Индексы таблицы `disp_ar_gr_infr_obj_pivot`
--
ALTER TABLE `disp_ar_gr_infr_obj_pivot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disp_ar_gr_infr_obj_pivot_group_infrastructure_object_id_foreign` (`group_infrastructure_object_id`),
  ADD KEY `disp_ar_gr_infr_obj_pivot_dispatch_area_id_foreign` (`dispatch_area_id`);

--
-- Индексы таблицы `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `divisions_group_infrastructure_object_id_foreign` (`group_infrastructure_object_id`);

--
-- Индексы таблицы `drop_voltages`
--
ALTER TABLE `drop_voltages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drop_voltages_group_infrastructure_object_id_foreign` (`group_infrastructure_object_id`);

--
-- Индексы таблицы `drop_voltage_devices`
--
ALTER TABLE `drop_voltage_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drop_voltage_devices_drop_voltage_id_foreign` (`drop_voltage_id`);

--
-- Индексы таблицы `drop_voltage_event_chronicles`
--
ALTER TABLE `drop_voltage_event_chronicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drop_voltage_event_chronicles_drop_voltage_id_foreign` (`drop_voltage_id`);

--
-- Индексы таблицы `event_chronicles`
--
ALTER TABLE `event_chronicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_chronicles_incident_id_foreign` (`incident_id`);

--
-- Индексы таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Индексы таблицы `group_infrastructure_objects`
--
ALTER TABLE `group_infrastructure_objects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incidents_object_infrastructure_id_foreign` (`object_infrastructure_id`),
  ADD KEY `incidents_division_id_foreign` (`division_id`),
  ADD KEY `incidents_incident_type_id_foreign` (`incident_type_id`),
  ADD KEY `incidents_itu_specie_id_foreign` (`itu_specie_id`),
  ADD KEY `incidents_itu_characteristic_id_foreign` (`itu_characteristic_id`),
  ADD KEY `incidents_itu_directory_object_id_foreign` (`itu_directory_object_id`),
  ADD KEY `incidents_itu_fault_id_foreign` (`itu_fault_id`),
  ADD KEY `incidents_itu_element_id_foreign` (`itu_element_id`),
  ADD KEY `incidents_itu_reason_breakdown_id_foreign` (`itu_reason_breakdown_id`),
  ADD KEY `incidents_dispatch_area_id_foreign` (`dispatch_area_id`),
  ADD KEY `incidents_drop_voltage_id_foreign` (`drop_voltage_id`);

--
-- Индексы таблицы `incident_employee_information`
--
ALTER TABLE `incident_employee_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_employee_information_incident_id_foreign` (`incident_id`);

--
-- Индексы таблицы `incident_employee_referrals`
--
ALTER TABLE `incident_employee_referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_employee_referrals_incident_type_id_foreign` (`incident_type_id`);

--
-- Индексы таблицы `incident_types`
--
ALTER TABLE `incident_types`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `inf_objs_inspections_pivot`
--
ALTER TABLE `inf_objs_inspections_pivot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inf_objs_inspections_pivot_object_infrastructure_id_foreign` (`object_infrastructure_id`),
  ADD KEY `inf_objs_inspections_pivot_inspection_id_foreign` (`inspection_id`);

--
-- Индексы таблицы `inspections`
--
ALTER TABLE `inspections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspections_division_id_foreign` (`division_id`),
  ADD KEY `inspections_inspector_id_foreign` (`inspector_id`);

--
-- Индексы таблицы `inspectors`
--
ALTER TABLE `inspectors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `itu_characteristics`
--
ALTER TABLE `itu_characteristics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itu_characteristics_incident_type_id_foreign` (`incident_type_id`);

--
-- Индексы таблицы `itu_directory_objects`
--
ALTER TABLE `itu_directory_objects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itu_directory_objects_incident_type_id_foreign` (`incident_type_id`),
  ADD KEY `itu_directory_objects_itu_specie_id_foreign` (`itu_specie_id`);

--
-- Индексы таблицы `itu_elements`
--
ALTER TABLE `itu_elements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itu_elements_incident_type_id_foreign` (`incident_type_id`);

--
-- Индексы таблицы `itu_faults`
--
ALTER TABLE `itu_faults`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itu_faults_incident_type_id_foreign` (`incident_type_id`);

--
-- Индексы таблицы `itu_reason_breakdowns`
--
ALTER TABLE `itu_reason_breakdowns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itu_reason_breakdowns_incident_type_id_foreign` (`incident_type_id`);

--
-- Индексы таблицы `itu_species`
--
ALTER TABLE `itu_species`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itu_species_incident_type_id_foreign` (`incident_type_id`);

--
-- Индексы таблицы `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Индексы таблицы `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Индексы таблицы `object_infrastructures`
--
ALTER TABLE `object_infrastructures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `object_infrastructures_group_infrastructure_object_id_foreign` (`group_infrastructure_object_id`);

--
-- Индексы таблицы `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Индексы таблицы `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Индексы таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Индексы таблицы `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_dispatch_area_id_foreign` (`dispatch_area_id`);

--
-- Индексы таблицы `user_dispatch_areas_pivot`
--
ALTER TABLE `user_dispatch_areas_pivot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_dispatch_areas_pivot_user_id_foreign` (`user_id`),
  ADD KEY `user_dispatch_areas_pivot_dispatch_area_id_foreign` (`dispatch_area_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `dispatch_areas`
--
ALTER TABLE `dispatch_areas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `disp_ar_gr_infr_obj_pivot`
--
ALTER TABLE `disp_ar_gr_infr_obj_pivot`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `drop_voltages`
--
ALTER TABLE `drop_voltages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `drop_voltage_devices`
--
ALTER TABLE `drop_voltage_devices`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `drop_voltage_event_chronicles`
--
ALTER TABLE `drop_voltage_event_chronicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `event_chronicles`
--
ALTER TABLE `event_chronicles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `group_infrastructure_objects`
--
ALTER TABLE `group_infrastructure_objects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `incident_employee_information`
--
ALTER TABLE `incident_employee_information`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `incident_employee_referrals`
--
ALTER TABLE `incident_employee_referrals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `incident_types`
--
ALTER TABLE `incident_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `inf_objs_inspections_pivot`
--
ALTER TABLE `inf_objs_inspections_pivot`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `inspections`
--
ALTER TABLE `inspections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `inspectors`
--
ALTER TABLE `inspectors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `itu_characteristics`
--
ALTER TABLE `itu_characteristics`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `itu_directory_objects`
--
ALTER TABLE `itu_directory_objects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `itu_elements`
--
ALTER TABLE `itu_elements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `itu_faults`
--
ALTER TABLE `itu_faults`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `itu_reason_breakdowns`
--
ALTER TABLE `itu_reason_breakdowns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `itu_species`
--
ALTER TABLE `itu_species`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `object_infrastructures`
--
ALTER TABLE `object_infrastructures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_dispatch_areas_pivot`
--
ALTER TABLE `user_dispatch_areas_pivot`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `dispatch_areas`
--
ALTER TABLE `dispatch_areas`
  ADD CONSTRAINT `dispatch_areas_group_infrastructure_object_id_foreign` FOREIGN KEY (`group_infrastructure_object_id`) REFERENCES `group_infrastructure_objects` (`id`);

--
-- Ограничения внешнего ключа таблицы `disp_ar_gr_infr_obj_pivot`
--
ALTER TABLE `disp_ar_gr_infr_obj_pivot`
  ADD CONSTRAINT `disp_ar_gr_infr_obj_pivot_dispatch_area_id_foreign` FOREIGN KEY (`dispatch_area_id`) REFERENCES `dispatch_areas` (`id`),
  ADD CONSTRAINT `disp_ar_gr_infr_obj_pivot_group_infrastructure_object_id_foreign` FOREIGN KEY (`group_infrastructure_object_id`) REFERENCES `group_infrastructure_objects` (`id`);

--
-- Ограничения внешнего ключа таблицы `divisions`
--
ALTER TABLE `divisions`
  ADD CONSTRAINT `divisions_group_infrastructure_object_id_foreign` FOREIGN KEY (`group_infrastructure_object_id`) REFERENCES `group_infrastructure_objects` (`id`);

--
-- Ограничения внешнего ключа таблицы `drop_voltages`
--
ALTER TABLE `drop_voltages`
  ADD CONSTRAINT `drop_voltages_group_infrastructure_object_id_foreign` FOREIGN KEY (`group_infrastructure_object_id`) REFERENCES `group_infrastructure_objects` (`id`);

--
-- Ограничения внешнего ключа таблицы `drop_voltage_devices`
--
ALTER TABLE `drop_voltage_devices`
  ADD CONSTRAINT `drop_voltage_devices_drop_voltage_id_foreign` FOREIGN KEY (`drop_voltage_id`) REFERENCES `drop_voltages` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `drop_voltage_event_chronicles`
--
ALTER TABLE `drop_voltage_event_chronicles`
  ADD CONSTRAINT `drop_voltage_event_chronicles_drop_voltage_id_foreign` FOREIGN KEY (`drop_voltage_id`) REFERENCES `drop_voltages` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `event_chronicles`
--
ALTER TABLE `event_chronicles`
  ADD CONSTRAINT `event_chronicles_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_dispatch_area_id_foreign` FOREIGN KEY (`dispatch_area_id`) REFERENCES `dispatch_areas` (`id`),
  ADD CONSTRAINT `incidents_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `incidents_drop_voltage_id_foreign` FOREIGN KEY (`drop_voltage_id`) REFERENCES `drop_voltages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`),
  ADD CONSTRAINT `incidents_itu_characteristic_id_foreign` FOREIGN KEY (`itu_characteristic_id`) REFERENCES `itu_characteristics` (`id`),
  ADD CONSTRAINT `incidents_itu_directory_object_id_foreign` FOREIGN KEY (`itu_directory_object_id`) REFERENCES `itu_directory_objects` (`id`),
  ADD CONSTRAINT `incidents_itu_element_id_foreign` FOREIGN KEY (`itu_element_id`) REFERENCES `itu_elements` (`id`),
  ADD CONSTRAINT `incidents_itu_fault_id_foreign` FOREIGN KEY (`itu_fault_id`) REFERENCES `itu_faults` (`id`),
  ADD CONSTRAINT `incidents_itu_reason_breakdown_id_foreign` FOREIGN KEY (`itu_reason_breakdown_id`) REFERENCES `itu_reason_breakdowns` (`id`),
  ADD CONSTRAINT `incidents_itu_specie_id_foreign` FOREIGN KEY (`itu_specie_id`) REFERENCES `itu_species` (`id`),
  ADD CONSTRAINT `incidents_object_infrastructure_id_foreign` FOREIGN KEY (`object_infrastructure_id`) REFERENCES `object_infrastructures` (`id`);

--
-- Ограничения внешнего ключа таблицы `incident_employee_information`
--
ALTER TABLE `incident_employee_information`
  ADD CONSTRAINT `incident_employee_information_incident_id_foreign` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `incident_employee_referrals`
--
ALTER TABLE `incident_employee_referrals`
  ADD CONSTRAINT `incident_employee_referrals_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `inf_objs_inspections_pivot`
--
ALTER TABLE `inf_objs_inspections_pivot`
  ADD CONSTRAINT `inf_objs_inspections_pivot_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `inspections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inf_objs_inspections_pivot_object_infrastructure_id_foreign` FOREIGN KEY (`object_infrastructure_id`) REFERENCES `object_infrastructures` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `inspections`
--
ALTER TABLE `inspections`
  ADD CONSTRAINT `inspections_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `inspections_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `inspectors` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `itu_characteristics`
--
ALTER TABLE `itu_characteristics`
  ADD CONSTRAINT `itu_characteristics_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `itu_directory_objects`
--
ALTER TABLE `itu_directory_objects`
  ADD CONSTRAINT `itu_directory_objects_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itu_directory_objects_itu_specie_id_foreign` FOREIGN KEY (`itu_specie_id`) REFERENCES `itu_species` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `itu_elements`
--
ALTER TABLE `itu_elements`
  ADD CONSTRAINT `itu_elements_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `itu_faults`
--
ALTER TABLE `itu_faults`
  ADD CONSTRAINT `itu_faults_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `itu_reason_breakdowns`
--
ALTER TABLE `itu_reason_breakdowns`
  ADD CONSTRAINT `itu_reason_breakdowns_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `itu_species`
--
ALTER TABLE `itu_species`
  ADD CONSTRAINT `itu_species_incident_type_id_foreign` FOREIGN KEY (`incident_type_id`) REFERENCES `incident_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `object_infrastructures`
--
ALTER TABLE `object_infrastructures`
  ADD CONSTRAINT `object_infrastructures_group_infrastructure_object_id_foreign` FOREIGN KEY (`group_infrastructure_object_id`) REFERENCES `group_infrastructure_objects` (`id`);

--
-- Ограничения внешнего ключа таблицы `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_dispatch_area_id_foreign` FOREIGN KEY (`dispatch_area_id`) REFERENCES `dispatch_areas` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `user_dispatch_areas_pivot`
--
ALTER TABLE `user_dispatch_areas_pivot`
  ADD CONSTRAINT `user_dispatch_areas_pivot_dispatch_area_id_foreign` FOREIGN KEY (`dispatch_area_id`) REFERENCES `dispatch_areas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_dispatch_areas_pivot_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
