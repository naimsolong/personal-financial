-- create databases
CREATE DATABASE IF NOT EXISTS `personal_financial`;
CREATE DATABASE IF NOT EXISTS `personal_financial_test`;

-- grant user rights
GRANT ALL ON *.* TO 'dbuser'@'%';