-- phpMyAdmin SQL Dump
-- version 2.11.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 20, 2008 at 01:09 PM
-- Server version: 5.0.45
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `newsledger`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `active` tinyint(1) NOT NULL,
  `db` varchar(255) collate utf8_bin NOT NULL,
  `user` varchar(255) collate utf8_bin NOT NULL,
  `pw` varchar(255) collate utf8_bin NOT NULL,
  `home` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `clients`
--


-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `key` varchar(255) collate utf8_bin NOT NULL,
  `active` tinyint(1) NOT NULL,
  `desc` varchar(255) collate utf8_bin NOT NULL,
  `type` varchar(255) collate utf8_bin NOT NULL,
  `global` enum('N','Y') collate utf8_bin NOT NULL,
  `required` enum('N','Y') collate utf8_bin NOT NULL,
  `readOnly` enum('N','Y') collate utf8_bin NOT NULL,
  `enum` varchar(2048) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`key`, `active`, `desc`, `type`, `global`, `required`, `readOnly`, `enum`) VALUES
('adjustment-add-popup-height', 1, 'Height of Add Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('adjustment-add-popup-width', 1, 'Width of Add Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('adjustment-edit-popup-height', 1, 'Height of Edit Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('adjustment-edit-popup-width', 1, 'Width of Edit Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('adjustment-view-popup-height', 1, 'Height of View Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('adjustment-view-popup-width', 1, 'Width of View Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('bill-add-popup-height', 1, 'Height of Add Bill Popup', 'INTEGER', 'N', 'N', 'N', ''),
('bill-add-popup-width', 1, 'Width of Add Bill Popup', 'INTEGER', 'N', 'N', 'N', ''),
('bill-edit-popup-height', 1, 'Height of Edit Bill Popup', 'INTEGER', 'N', 'N', 'N', ''),
('bill-edit-popup-width', 1, 'Width of Edit Bill Popup', 'INTEGER', 'N', 'N', 'N', ''),
('bill-view-popup-height', 1, 'Height of View Bill Popup', 'INTEGER', 'N', 'N', 'N', ''),
('bill-view-popup-width', 1, 'Width of View Bill Popup', 'INTEGER', 'N', 'N', 'N', ''),
('billing-minimum', 1, 'Amount a customer bill must be, in order to be included in mail merge download by default.', 'FLOAT', 'Y', 'N', 'N', ''),
('billing-note', 1, 'Global billing note', 'STRING', 'Y', 'N', 'N', ''),
('billing-period', 1, 'Current billing period', 'IID', 'Y', 'Y', 'N', ''),
('billing-status', 1, 'Current status of customer billing', 'ENUM', 'Y', 'N', 'N', 'return array(''0'' => ''Scheduled'',\r\n''1'' => ''Running'',\r\n''2'' => ''Generated, not combined'',\r\n''3'' => ''Combined'',\r\n''4'' => ''Complete'');'),
('client-address-1', 1, 'Street address on printed bills', 'STRING', 'Y', 'Y', 'N', ''),
('client-address-2', 1, 'City, State, Zip on printed bills', 'STRING', 'Y', 'Y', 'N', ''),
('client-name', 1, 'Name on printed bills', 'STRING', 'Y', 'Y', 'N', ''),
('client-telephone', 1, 'Telephone on printed bills', 'TELEPHONE', 'Y', 'N', 'N', ''),
('complaint-add-popup-height', 1, 'Height of Add Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('complaint-add-popup-width', 1, 'Width of Add Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('complaint-edit-popup-height', 1, 'Height of Edit Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('complaint-edit-popup-width', 1, 'Width of Edit Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('complaint-view-popup-height', 1, 'Height of View Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('complaint-view-popup-width', 1, 'Width of View Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('configuration-add-popup-height', 1, 'Height of Add Configuration Popup', 'INTEGER', 'N', 'N', 'N', ''),
('configuration-add-popup-width', 1, 'Width of Add Configuration Popup', 'INTEGER', 'N', 'N', 'N', ''),
('configuration-edit-popup-height', 1, 'Height of Edit Configuration Popup', 'INTEGER', 'N', 'N', 'N', ''),
('configuration-edit-popup-width', 1, 'Width of Edit Configuration Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-adjustment-add-popup-height', 1, 'Width of Add Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-adjustment-add-popup-width', 1, 'Height of Add Adjustment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-billing-type', 0, 'To credit customers set amounts for each paper, set to \\\\''old\\\\''.  To auto calculate credits based on rates and days in period, set to \\\\''auto\\\\''.', 'STRING', 'Y', 'Y', 'N', ''),
('customer-complaint-add-popup-height', 1, 'Width of Add Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-complaint-add-popup-width', 1, 'Height of Add Complaint Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-edit-popup-height', 1, 'Height of Edit Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-edit-popup-width', 1, 'Width of Edit Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-rates-add-popup-height', 1, 'Height of Add Customer Rate Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-rates-add-popup-width', 1, 'Width of Add Customer Rate Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-rates-edit-popup-height', 1, 'Height of Edit Customer Rate Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-rates-edit-popup-width', 1, 'Width of Edit Customer Rate Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-rates-view-popup-height', 1, 'Height of View Customer Rate Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-rates-view-popup-width', 1, 'Width of View Customer Rate Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-service-add-popup-height', 1, 'Width of Add Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-service-add-popup-width', 1, 'Height of Add Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-type-add-popup-height', 1, 'Width of Add Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-type-add-popup-width', 1, 'Height of Add Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-view-popup-height', 1, 'Height of View Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customer-view-popup-width', 1, 'Width of View Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customers-combined-add-popup-height', 1, 'Height of Add Combined Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customers-combined-add-popup-width', 1, 'Width of Add Combined Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customers-combined-edit-popup-height', 1, 'Height of Edit Combined Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customers-combined-edit-popup-width', 1, 'Width of Edit Combined Customer Popup', 'INTEGER', 'N', 'N', 'N', ''),
('customers-daily-only-cost', 1, 'Rate client pays for daily only papers for 1 period (home delivery rate)', 'MONEY', 'Y', 'Y', 'N', ''),
('customers-daily-single-cost', 1, 'Rate client pays for single daily paper (wholesale rate)', 'MONEY', 'Y', 'Y', 'N', ''),
('customers-sunday-only-cost', 1, 'Rate client pays for sunday only papers for 1 period (home delivery rate)', 'MONEY', 'Y', 'Y', 'N', ''),
('customers-sunday-single-cost', 1, 'Rate client pays for single sunday paper (wholesale rate)', 'MONEY', 'Y', 'Y', 'N', ''),
('default-table-background-even', 0, 'Background color fo even rows of tables.', 'COLOR', 'N', 'N', 'N', ''),
('default-table-background-odd', 0, 'Background color for odd rows of tables.', 'COLOR', 'N', 'N', 'N', ''),
('default-table-border', 0, 'Whether to put a border on tables by default.', 'BOOLEAN', 'N', 'N', 'N', ''),
('default-table-border-color', 0, 'Color to use as border color by default.', 'COLOR', 'N', 'N', 'N', ''),
('default-title', 1, 'Identifying title on all pages', 'STRING', 'Y', 'N', 'N', ''),
('flag-stop-billing-minimum', 1, 'Flag Stop customers need to owe at least this amount before a bill is printed', 'FLOAT', 'Y', 'N', 'N', ''),
('flag-stop-daily-rate', 1, 'Amount to charge a Flag Stop customer for a single daily paper', 'MONEY', 'Y', 'N', 'N', ''),
('flag-stop-sunday-rate', 1, 'Amount to charge a Flag Stop customer for a single sunday paper', 'MONEY', 'Y', 'N', 'N', ''),
('flag-stop-type', 1, 'Flag Stop Type ID', 'TID', 'Y', 'N', 'N', ''),
('flagstop-service-add-popup-height', 1, 'Width of Add Start/Stop Popup', 'INTEGER', 'N', 'N', 'N', ''),
('flagstop-service-add-popup-width', 1, 'Height of Add Start/Stop Popup', 'INTEGER', 'N', 'N', 'N', ''),
('group-add-popup-height', 1, 'Height of Add Group Popup', 'INTEGER', 'N', 'N', 'N', ''),
('group-add-popup-width', 1, 'Width of Add Group Popup', 'INTEGER', 'N', 'N', 'N', ''),
('group-edit-popup-height', 1, 'Height of Edit Group Popup', 'INTEGER', 'N', 'N', 'N', ''),
('group-edit-popup-width', 1, 'Width of Edit Group Popup', 'INTEGER', 'N', 'N', 'N', ''),
('payment-add-popup-height', 1, 'Height of Add Payment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('payment-add-popup-width', 1, 'Width of Add Payment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('payment-edit-popup-height', 1, 'Height of Edit Payment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('payment-edit-popup-width', 1, 'Width of Edit Payment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('payment-view-popup-height', 1, 'Height of View Payment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('payment-view-popup-width', 1, 'Width of View Payment Popup', 'INTEGER', 'N', 'N', 'N', ''),
('period-add-popup-height', 1, 'Height of Add Period Popup', 'INTEGER', 'N', 'N', 'N', ''),
('period-add-popup-width', 1, 'Width of Add Period Popup', 'INTEGER', 'N', 'N', 'N', ''),
('period-edit-popup-height', 1, 'Height of Edit Period Popup', 'INTEGER', 'N', 'N', 'N', ''),
('period-edit-popup-width', 1, 'Width of Edit Period Popup', 'INTEGER', 'N', 'N', 'N', ''),
('period-view-popup-height', 1, 'Height of View Period Popup', 'INTEGER', 'N', 'N', 'N', ''),
('period-view-popup-width', 1, 'Width of View Period Popup', 'INTEGER', 'N', 'N', 'N', ''),
('profile-add-popup-height', 1, 'Height of Add Profile Popup', 'INTEGER', 'N', 'N', 'N', ''),
('profile-add-popup-width', 1, 'Width of Add Profile Popup', 'INTEGER', 'N', 'N', 'N', ''),
('profile-edit-popup-height', 1, 'Height of Edit Profile Popup', 'INTEGER', 'N', 'N', 'N', ''),
('profile-edit-popup-width', 1, 'Width of Edit Profile Popup', 'INTEGER', 'N', 'N', 'N', ''),
('route-add-popup-height', 1, 'Height of Add Route Popup', 'INTEGER', 'N', 'N', 'N', ''),
('route-add-popup-width', 1, 'Width of Add Route Popup', 'INTEGER', 'N', 'N', 'N', ''),
('route-edit-popup-height', 1, 'Height of Edit Route Popup', 'INTEGER', 'N', 'N', 'N', ''),
('route-edit-popup-width', 1, 'Width of Edit Route Popup', 'INTEGER', 'N', 'N', 'N', ''),
('service-add-popup-height', 1, 'Height of Add Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('service-add-popup-width', 1, 'Width of Add Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('service-edit-popup-height', 1, 'Height of Edit Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('service-edit-popup-width', 1, 'Width of Edit Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('service-view-popup-height', 1, 'Height of View Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('service-view-popup-width', 1, 'Width of View Stop/Start Popup', 'INTEGER', 'N', 'N', 'N', ''),
('servicetype-add-popup-height', 1, 'Height of Add Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('servicetype-add-popup-width', 1, 'Width of Add Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('servicetype-edit-popup-height', 1, 'Height of Edit Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('servicetype-edit-popup-width', 1, 'Width of Edit Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('servicetype-view-popup-height', 1, 'Height of View Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('servicetype-view-popup-width', 1, 'Width of View Type Change Popup', 'INTEGER', 'N', 'N', 'N', ''),
('telephone-types', 0, 'List of types displaued in the drop down list next to telephone numbers.  Used to specify the type of the telephone number.', 'LIST', 'Y', 'Y', 'N', 'return array(''Main'',\\r\\n''Alternate'',\\r\\n''Mobile'',\\r\\n''Evening'',\\r\\n''Day'',\\r\\n''Office'',\\r\\n''Message'',\\r\\n''Pager'',\\r\\n''Business'',\\r\\n''Mobile (Office)'',\\r\\n''Mobile (Business)'',\\r\\n''Mobile (Day)'',\\r\\n''Mobile (Evening)'');'),
('user-add-popup-height', 1, 'Height of Add User Popup', 'INTEGER', 'N', 'N', 'N', ''),
('user-add-popup-width', 1, 'Width of Add User Popup', 'INTEGER', 'N', 'N', 'N', ''),
('user-edit-popup-height', 1, 'Height of Edit User Popup', 'INTEGER', 'N', 'N', 'N', ''),
('user-edit-popup-width', 1, 'Width of Edit User Popup', 'INTEGER', 'N', 'N', 'N', '');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE IF NOT EXISTS `features` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `page` varchar(20) collate utf8_bin NOT NULL,
  `feature` varchar(40) collate utf8_bin NOT NULL,
  `desc` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `page`, `feature`, `desc`) VALUES
(1, '030511', 'bill-balance', 'Balance after last time customer was billed.  This is used as the starting balance for the billing of this customer.'),
(2, '030511', 'bill-stopped', 'Delivered status after last time customer was billed.  This is used as the starting delivery status the next time this customer is billed.'),
(3, '030511', 'bill-type', 'Delivery type after last time customer was billed.  This is used as the starting delivery type the next time this customer is billed.'),
(4, '030404', 'period-edit', 'Allowed to edit period'),
(5, '030404', 'period-view', 'Allowed to view period'),
(6, '030404', 'customer-edit', 'Allowed to edit customer'),
(7, '030404', 'customer-view', 'Allowed to view customer'),
(8, '030202', 'customer-edit', 'Allowed to edit customer'),
(9, '030202', 'customer-view', 'Allowed to view customer'),
(10, '030203', 'period-edit', 'Allowed to edit period'),
(11, '030203', 'period-view', 'Allowed to view period');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `constant` varchar(255) collate utf8_bin NOT NULL,
  `id` varchar(20) collate utf8_bin NOT NULL,
  `title` varchar(255) collate utf8_bin NOT NULL,
  `parent` varchar(20) collate utf8_bin NOT NULL,
  `code` char(1) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`parent`,`id`),
  KEY `code_parent_title` (`code`,`parent`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`constant`, `id`, `title`, `parent`, `code`) VALUES
('ADMIN', '01', 'Administration', '', 'A'),
('CUSTOMERS', '03', 'Customers', '', 'C'),
('HOME', '04', 'Home', '', 'M'),
('ADJUSTMENT', '06', 'Adjustment', '', 'D'),
('BILL', '07', 'Bill', '', 'L'),
('SERVICE', '08', 'Service', '', 'V'),
('PAYMENT', '09', 'Payment', '', 'P'),
('SERVICETYPE', '10', 'ServiceType', '', 'W'),
('SECURITY', '11', 'Security', '', 'T'),
('CONFIGURATION', '12', 'Configuration', '', 'I'),
('PERIOD', '13', 'Period', '', 'O'),
('ROUTE', '15', 'Route', '', 'U'),
('GROUP', '17', 'Group', '', 'J'),
('USER', '18', 'User', '', 'S'),
('PROFILE', '19', 'Profile', '', 'K'),
('STORES', '20', 'Stores', '', 'N'),
('COMPLAINTS', '21', 'Complaints', '', 'Q'),
('ROUTES', '22', 'Routes', '', 'E'),
('AUDIT', '01', 'Audit', '01', ''),
('BILLING', '02', 'Billing', '01', ''),
('CONFIG', '03', 'Configuration', '01', ''),
('FORMS', '04', 'Forms', '01', ''),
('GROUPS', '05', 'Groups', '01', 'G'),
('PERIODS', '06', 'Periods', '01', 'P'),
('SECURITY', '08', 'Security', '01', ''),
('USERS', '09', 'Users', '01', 'U'),
('ADD', '01', 'Add', '0105', ''),
('EDIT', '02', 'Edit', '0105', ''),
('ADD', '01', 'Add', '0106', ''),
('EDIT', '02', 'Edit', '0106', ''),
('ADD', '01', 'Add', '0109', ''),
('EDIT', '02', 'Edit', '0109', ''),
('ADDNEW', '01', 'New', '03', ''),
('ADMIN', '02', 'Administration', '03', 'D'),
('BILLING', '04', 'Billing', '03', 'B'),
('EDIT', '05', 'Edit', '03', 'E'),
('FLAGSTOPS', '06', 'Flag Stops', '03', ''),
('LOOKUP', '07', 'Search', '03', ''),
('PAYMENTS', '08', 'Payments', '03', 'P'),
('REPORTS', '09', 'Reports', '03', 'R'),
('SEQUENCING', '10', 'Sequencing', '03', ''),
('VIEW', '11', 'View', '03', 'V'),
('WINDOW', '12', 'Popup', '03', 'W'),
('BILLING', '01', 'Billing', '0302', ''),
('COMBINED', '02', 'Combined', '0302', 'C'),
('RATES', '03', 'Rates', '0302', 'R'),
('TYPES', '04', 'Types', '0302', 'T'),
('ADD', '01', 'Add', '030202', ''),
('EDIT', '02', 'Edit', '030202', ''),
('ADD', '01', 'Add', '030203', ''),
('EDIT', '02', 'Edit', '030203', ''),
('VIEW', '03', 'View', '030203', ''),
('ADD', '01', 'Add', '030204', ''),
('EDIT', '02', 'Edit', '030204', ''),
('VIEW', '03', 'View', '030204', ''),
('ADD', '01', 'Add', '0303', ''),
('EDIT', '02', 'Edit', '0303', ''),
('POPUP', '03', 'Popup', '0303', ''),
('BILL', '02', 'Bill', '0304', 'B'),
('DOWNLOAD', '03', 'Download', '0304', ''),
('LOG', '04', 'Log', '0304', ''),
('REPAIR', '05', 'Repair', '0304', ''),
('ADD', '01', 'Add', '030401', ''),
('EDIT', '02', 'Edit', '030401', ''),
('STEP1', '01', 'Step 1', '030402', ''),
('STEP2', '02', 'Step 2', '030402', ''),
('STEP3', '03', 'Step 3', '030402', ''),
('STEP4', '04', 'Step 4', '030402', ''),
('STEP5', '05', 'Step 5', '030402', ''),
('STEP6', '06', 'Step 6', '030402', ''),
('ADDRESSES', '01', 'Addresses', '0305', ''),
('ADJUSTMENTS', '02', 'Adjustments', '0305', ''),
('BILLS', '03', 'Bills', '0305', ''),
('BILLLOG', '04', 'Bill Log', '0305', ''),
('COMPLAINTS', '05', 'Complaints', '0305', ''),
('MAP', '06', 'Map', '0305', ''),
('NOTES', '07', 'Notes', '0305', ''),
('PAYMENTS', '08', 'Payments', '0305', ''),
('SERVICE', '09', 'Service', '0305', ''),
('SERVICETYPES', '10', 'ServiceType', '0305', ''),
('SUMMARY', '11', 'Summary', '0305', ''),
('BILLING', '12', 'Billing', '0305', ''),
('ADD', '01', 'Add', '0308', ''),
('LOOKUP', '03', 'Search', '0308', ''),
('ADD', '01', 'Add', '030802', ''),
('EDIT', '02', 'Edit', '030802', ''),
('AHEAD', '01', 'Ahead', '0309', ''),
('BEHIND', '02', 'Behind', '0309', ''),
('CUSTOMER', '03', 'Customer', '0309', ''),
('ORDERS', '04', 'Orders', '0309', ''),
('STOPPED', '05', 'Stopped', '0309', ''),
('ADDRESSES', '01', 'Addresses', '0311', ''),
('ADJUSTMENTS', '02', 'Adjustments', '0311', ''),
('BILLS', '03', 'Bills', '0311', ''),
('BILLLOG', '04', 'Bill Log', '0311', ''),
('COMPLAINTS', '05', 'Complaints', '0311', ''),
('MAP', '06', 'Map', '0311', ''),
('NOTES', '07', 'Notes', '0311', ''),
('PAYMENTS', '08', 'Payments', '0311', ''),
('SERVICE', '09', 'Service', '0311', ''),
('SERVICETYPES', '10', 'ServiceType', '0311', ''),
('SUMMARY', '11', 'Summary', '0311', ''),
('BILLING', '12', 'Billing', '0311', ''),
('ADJUSTMENT', '01', 'Adjustment', '0312', ''),
('CHANGETYPE', '02', 'ChangeType', '0312', ''),
('COMPLAINT', '03', 'Complaint', '0312', ''),
('STOPSTART', '04', 'StopStart', '0312', ''),
('STARTSTOP', '05', 'StartStop', '0312', ''),
('HOME', '01', 'Home', '04', ''),
('NEWS', '02', 'News', '04', 'N'),
('CURRENT', '01', 'Current', '0402', ''),
('OLD', '02', 'Old', '0402', ''),
('ADD', '01', 'Add', '06', ''),
('EDIT', '02', 'Edit', '06', ''),
('VIEW', '03', 'View', '06', ''),
('ADD', '01', 'Add', '07', ''),
('EDIT', '02', 'Edit', '07', ''),
('VIEW', '03', 'View', '07', ''),
('ADD', '01', 'Add', '08', ''),
('EDIT', '02', 'Edit', '08', ''),
('VIEW', '03', 'View', '08', ''),
('ADD', '01', 'Add', '09', ''),
('EDIT', '02', 'Edit', '09', ''),
('VIEW', '03', 'View', '09', ''),
('ADD', '01', 'Add', '10', ''),
('EDIT', '02', 'Edit', '10', ''),
('VIEW', '03', 'View', '10', ''),
('ADD', '01', 'Add', '11', ''),
('EDIT', '02', 'Edit', '11', ''),
('ADD', '01', 'Add', '12', ''),
('EDIT', '02', 'Edit', '12', ''),
('ADD', '01', 'Add', '13', ''),
('EDIT', '02', 'Edit', '13', ''),
('ADD', '01', 'Add', '15', ''),
('EDIT', '02', 'Edit', '15', ''),
('ADD', '01', 'Add', '17', ''),
('EDIT', '02', 'Edit', '17', ''),
('ADD', '01', 'Add', '18', ''),
('EDIT', '02', 'Edit', '18', ''),
('ADD', '01', 'Add', '19', ''),
('EDIT', '02', 'Edit', '19', ''),
('ACCOUNTS', '01', 'Accounts', '20', 'C'),
('BILLING', '03', 'Billing', '20', 'B'),
('ADDNEW', '04', 'New', '20', ''),
('PAYMENTS', '05', 'Payments', '20', 'P'),
('REPORTS', '07', 'Reports', '20', 'R'),
('RETURNS', '08', 'Returns', '20', ''),
('ADMIN', '09', 'Administration', '20', 'A'),
('EDIT', '10', 'Edit', '20', 'E'),
('VIEW', '11', 'View', '20', 'V'),
('EDIT', '01', 'Edit', '2001', ''),
('VIEW', '02', 'View', '2001', ''),
('BILL', '01', 'Bill', '2003', ''),
('LOG', '02', 'Log', '2003', ''),
('LOOKUP', '02', 'Search', '2005', ''),
('ADDNEW', '03', 'New', '2005', ''),
('RETURNS', '01', 'Returns', '2007', ''),
('RATES', '01', 'Rates', '2009', 'R'),
('ADD', '01', 'Add', '200901', ''),
('EDIT', '02', 'Edit', '200901', ''),
('ADDRESSES', '01', 'Addresses', '2010', ''),
('BILLS', '02', 'Bills', '2010', ''),
('BILLLOG', '03', 'Bill Log', '2010', ''),
('NOTES', '04', 'Notes', '2010', ''),
('PAYMENTS', '05', 'Payments', '2010', ''),
('RETURNS', '06', 'Returns', '2010', ''),
('SUMMARY', '07', 'Summary', '2010', ''),
('RATES', '08', 'Rates', '2010', ''),
('ADDRESSES', '01', 'Addresses', '2011', ''),
('BILLS', '02', 'Bills', '2011', ''),
('BILLLOG', '03', 'Bill Log', '2011', ''),
('NOTES', '04', 'Notes', '2011', ''),
('PAYMENTS', '05', 'Payments', '2011', ''),
('RETURNS', '06', 'Returns', '2011', ''),
('SUMMARY', '07', 'Summary', '2011', ''),
('RATES', '08', 'Rates', '2011', ''),
('ADD', '01', 'Add', '21', ''),
('EDIT', '02', 'Edit', '21', ''),
('VIEW', '03', 'View', '21', ''),
('REPORTS', '01', 'Reports', '22', 'R'),
('SEQUENCING', '02', 'Sequencing', '22', ''),
('ADMIN', '03', 'Administration', '22', 'A'),
('CHANGES', '01', 'Changes', '2201', 'C'),
('DRAW', '02', 'Draw', '2201', ''),
('ROUTE', '03', 'Route', '2201', ''),
('STATUS', '04', 'Status', '2201', ''),
('TIPS', '05', 'Tips', '2201', ''),
('HISTORY', '01', 'History', '220101', ''),
('NOTES', '02', 'Notes', '220101', ''),
('REPORT', '03', 'Report', '220101', ''),
('ADD', '01', 'Add', '2203', ''),
('EDIT', '02', 'Edit', '2203', '');
