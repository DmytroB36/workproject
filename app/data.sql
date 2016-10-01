CREATE TABLE `students` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
  `family_name` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
  `sex` TINYINT(1) DEFAULT NULL,
  `age` TINYINT(4) DEFAULT NULL,
  `group_name` VARCHAR(3) COLLATE utf8_unicode_ci NOT NULL,
  `faculty` VARCHAR(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY USING BTREE (`id`) COMMENT '',
   INDEX `first_name` USING BTREE (`first_name`) COMMENT '',
   INDEX `group_name` USING BTREE (`group_name`) COMMENT '',
   INDEX `faculty` USING BTREE (`faculty`) COMMENT ''
)ENGINE=InnoDB
AUTO_INCREMENT=27 AVG_ROW_LENGTH=1024 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT=''
;
COMMIT;

/* Data for the 'students' table  (Records 1 - 16) */

INSERT INTO `students` (`id`, `first_name`, `family_name`, `sex`, `age`, `group_name`, `faculty`) VALUES
  (10, 'Дмитрий', 'Перов2', 1, 29, 'fe1', 'физикотехнический'),
  (11, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (12, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (13, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (14, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (15, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (16, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (17, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (18, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (19, 'Дмитрий', 'Перов', 1, 25, 'fe1', 'IT'),
  (20, 'Елена', 'Феденко', 0, 65, 'фт', 'физмат'),
  (21, 'Елена', 'Степанова', 0, 0, 'вфы', 'вфыв'),
  (22, 'Елена', 'fdsf', 0, 12, 'ffd', 'dasdasdasd'),
  (23, 'Елена', 'fdsf', 0, 12, 'ffd', 'dasdasdasd'),
  (25, 'Елена', 'fdsf', 0, 12, 'ffd', 'dasdasdasd'),
  (26, 'dbvfiubvy', 'dwbfcigv', 0, 15, 'dck', '12312');