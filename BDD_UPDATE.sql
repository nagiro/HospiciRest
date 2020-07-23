ALTER TABLE `usuaris`
    COMMENT='Nom taula a intranet -> USUARIS_',
	CHANGE COLUMN `UsuariID` `UsuariID` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'IdUsuari' FIRST,
	CHANGE COLUMN `Nivells_idNivells` `Nivells_idNivells` INT(11) NULL COMMENT 'IdNivell' AFTER `UsuariID`,
	CHANGE COLUMN `DNI` `DNI` VARCHAR(12) NULL COMMENT 'Dni' COLLATE 'utf8_general_ci' AFTER `Nivells_idNivells`,
	CHANGE COLUMN `Passwd` `Passwd` VARCHAR(20) NULL COMMENT 'Password' COLLATE 'utf8_general_ci' AFTER `DNI`,
	CHANGE COLUMN `Nom` `Nom` TEXT NULL COMMENT 'Nom' COLLATE 'utf8_general_ci' AFTER `Passwd`,
	CHANGE COLUMN `Cog1` `Cog1` TEXT NULL COMMENT 'Cog1' COLLATE 'utf8_general_ci' AFTER `Nom`,
	CHANGE COLUMN `Cog2` `Cog2` TEXT NULL COMMENT 'Cog2' COLLATE 'utf8_general_ci' AFTER `Cog1`,
	CHANGE COLUMN `Email` `Email` TEXT NULL COMMENT 'Email' COLLATE 'utf8_general_ci' AFTER `Cog2`,
	CHANGE COLUMN `Adreca` `Adreca` TEXT NULL COMMENT 'Adreca' COLLATE 'utf8_general_ci' AFTER `Email`,
	CHANGE COLUMN `CodiPostal` `CodiPostal` INT(11) NULL COMMENT 'CodiPostal' AFTER `Adreca`,
	CHANGE COLUMN `Poblacio` `Poblacio` INT(11) NULL COMMENT 'Poblacio' AFTER `CodiPostal`,
	CHANGE COLUMN `Poblaciotext` `Poblaciotext` TEXT NULL COMMENT 'PoblacioText' COLLATE 'utf8_general_ci' AFTER `Poblacio`;
	CHANGE COLUMN `Telefon` `Telefon` TEXT NULL COMMENT 'Telefon' COLLATE 'utf8_general_ci' AFTER `Poblaciotext`,
	CHANGE COLUMN `Mobil` `Mobil` TEXT NULL COMMENT 'Mobil' COLLATE 'utf8_general_ci' AFTER `Telefon`,
	CHANGE COLUMN `Entitat` `Entitat` TEXT NULL COMMENT 'Entitat' COLLATE 'utf8_general_ci' AFTER `Mobil`,
	CHANGE COLUMN `Habilitat` `Habilitat` TINYINT(4) NULL COMMENT 'Habilitat' AFTER `Entitat`,
	CHANGE COLUMN `Actualitzacio` `Actualitzacio` DATE NULL COMMENT 'Actualitzacio' AFTER `Habilitat`,
	CHANGE COLUMN `site_id` `site_id` TINYINT(4) NULL DEFAULT '1' COMMENT 'SiteId' AFTER `Actualitzacio`,
	CHANGE COLUMN `actiu` `actiu` TINYINT(1) NULL DEFAULT '1' COMMENT 'Actiu' AFTER `site_id`,
	CHANGE COLUMN `facebook_id` `facebook_id` BIGINT(20) NULL COMMENT 'IdFacebook' AFTER `actiu`,
	CHANGE COLUMN `data_naixement` `data_naixement` DATE NULL COMMENT 'DataNaixement' AFTER `facebook_id`;

ALTER TABLE `cursos`
    COMMENT='CURSOS_',
	CHANGE COLUMN `idCursos` `idCursos` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'IdCurs' FIRST,
	CHANGE COLUMN `TitolCurs` `TitolCurs` TEXT NULL COMMENT 'TitolCurs' COLLATE 'utf8_general_ci' AFTER `idCursos`,
	CHANGE COLUMN `isActiu` `isActiu` TINYINT(1) NULL DEFAULT '1' COMMENT 'IsActiu' AFTER `TitolCurs`,
	CHANGE COLUMN `Places` `Places` INT(11) NULL COMMENT 'Places' AFTER `isActiu`,
	CHANGE COLUMN `Codi` `Codi` TEXT NULL COMMENT 'Codi' COLLATE 'utf8_general_ci' AFTER `Places`,
	CHANGE COLUMN `Descripcio` `Descripcio` TEXT NULL COMMENT 'Descripcio' COLLATE 'utf8_general_ci' AFTER `Codi`,
	CHANGE COLUMN `Preu` `Preu` INT(11) NULL COMMENT 'Preu' AFTER `Descripcio`,
	CHANGE COLUMN `Horaris` `Horaris` TEXT NULL COMMENT 'Horaris' COLLATE 'utf8_general_ci' AFTER `Preu`,
	CHANGE COLUMN `Categoria` `Categoria` TEXT NULL COMMENT 'Categoria' COLLATE 'utf8_general_ci' AFTER `Horaris`,
	CHANGE COLUMN `OrdreSortida` `OrdreSortida` INT(11) NULL COMMENT 'OrdreSortida' AFTER `Categoria`,
	CHANGE COLUMN `DataAparicio` `DataAparicio` DATE NULL COMMENT 'DataAparicio' AFTER `OrdreSortida`,
	CHANGE COLUMN `DataDesaparicio` `DataDesaparicio` DATE NULL COMMENT 'DataDesaparicio' AFTER `DataAparicio`,
	CHANGE COLUMN `DataInMatricula` `DataInMatricula` DATE NULL COMMENT 'DataInMatricula' AFTER `DataDesaparicio`,
	CHANGE COLUMN `DataFiMatricula` `DataFiMatricula` DATE NULL COMMENT 'DataFiMatricula' AFTER `DataInMatricula`,
	CHANGE COLUMN `DataInici` `DataInici` DATE NULL COMMENT 'DataInici' AFTER `DataFiMatricula`,
	CHANGE COLUMN `VisibleWEB` `VisibleWEB` TINYINT(4) NOT NULL DEFAULT '1' COMMENT 'VisibleWeb' AFTER `DataInici`,
	CHANGE COLUMN `site_id` `site_id` TINYINT(4) NULL DEFAULT '1' COMMENT 'SiteId' AFTER `VisibleWEB`,
	CHANGE COLUMN `actiu` `actiu` TINYINT(1) NULL DEFAULT '1' COMMENT 'Actiu' AFTER `site_id`,
	CHANGE COLUMN `cicle_id` `cicle_id` INT(11) NULL COMMENT 'CicleId' AFTER `actiu`,
	CHANGE COLUMN `activitat_id` `activitat_id` INT(11) NULL /* gran consulta SQL (2,7 KiB), recortada a los 2.000 caracteres */

ALTER TABLE `matricules`
	COMMENT='MATRICULES_',
	CHANGE COLUMN `idMatricules` `idMatricules` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'IdMatricules' FIRST,
	CHANGE COLUMN `Usuaris_UsuariID` `Usuaris_UsuariID` INT(11) NULL COMMENT 'UsuariId' AFTER `idMatricules`,
	CHANGE COLUMN `Cursos_idCursos` `Cursos_idCursos` INT(11) NULL COMMENT 'CursId' AFTER `Usuaris_UsuariID`,
	CHANGE COLUMN `Estat` `Estat` SMALLINT(2) NULL COMMENT 'Estat' AFTER `Cursos_idCursos`,
	CHANGE COLUMN `Comentari` `Comentari` TEXT NULL COMMENT 'Comentari' COLLATE 'utf8_general_ci' AFTER `Estat`,
	CHANGE COLUMN `DataInscripcio` `DataInscripcio` DATETIME NULL COMMENT 'DataInscripcio' AFTER `Comentari`,
	CHANGE COLUMN `data_baixa` `data_baixa` DATE NULL COMMENT 'DataBaixa' AFTER `DataInscripcio`,
	CHANGE COLUMN `Pagat` `Pagat` DOUBLE NULL COMMENT 'Pagat' AFTER `data_baixa`,
	CHANGE COLUMN `tReduccio` `tReduccio` SMALLINT(2) NOT NULL COMMENT 'TipusReduccio' AFTER `Pagat`,
	CHANGE COLUMN `tPagament` `tPagament` SMALLINT(2) NOT NULL COMMENT 'TipusPagament' AFTER `tReduccio`,
	CHANGE COLUMN `site_id` `site_id` TINYINT(4) NULL DEFAULT '1' COMMENT 'SiteId' AFTER `tPagament`,
	CHANGE COLUMN `actiu` `actiu` TINYINT(1) NULL DEFAULT '1' COMMENT 'Actiu' AFTER `site_id`,
	CHANGE COLUMN `tpv_operacio` `tpv_operacio` VARCHAR(20) NOT NULL COMMENT 'TpvOperacio' COLLATE 'utf8_general_ci' AFTER `actiu`,
	CHANGE COLUMN `tpv_order` `tpv_order` INT(11) NOT NULL COMMENT 'TpvOrder' AFTER `tpv_operacio`,
	CHANGE COLUMN `idDadesBancaries` `idDadesBancaries` INT(11) NULL COMMENT 'DadesBancariesId' AFTER `tpv_order`,
	CHANGE COLUMN `tutor_dni` `tutor_dni` TEXT NULL COMMENT 'TutorDni' COLLATE 'utf8_general_ci' AFTER `idDadesBancaries`,
	CHANGE COLUMN `tutor_nom` `tutor_nom` TEXT NULL COMMENT 'TutorNom' COLLATE 'utf8_general_ci' AFTER `tutor_dni`,
	CHANGE COLUMN `Data_pagament` `Data_pagament` DATE NULL COMMENT 'DataPagament' AFTER `tutor_nom`,
	CHANGE COLUMN `rebut` `rebut` VARCHAR(40) NULL COMMENT 'Rebut' COL/* gran consulta SQL (2,0 KiB), recortada a los 2.000 caracteres */

ALTER TABLE `cursos`
	ADD COLUMN `cicle_id` INT NULL DEFAULT NULL AFTER `actiu`;

ALTER TABLE `matricules`
	CHANGE COLUMN `tpv_operacio` `tpv_operacio` VARCHAR(20) NULL COMMENT 'TpvOperacio' COLLATE 'utf8_general_ci' AFTER `actiu`,
	CHANGE COLUMN `tpv_order` `tpv_order` INT(11) NULL COMMENT 'TpvOrder' AFTER `tpv_operacio`;

ALTER TABLE `usuaris`
	ADD COLUMN `Genere` CHAR(1) NULL DEFAULT 'A' COMMENT 'Genere' COLLATE 'utf8_general_ci' AFTER `Email`;	


ALTER TABLE `matricules` ADD COLUMN `GrupMatricules` INT(11) NULL DEFAULT NULL COMMENT 'GrupMatricules' AFTER `rebut`;
ALTER TABLE `matricules` CHANGE COLUMN `GrupMatricules` `GrupMatricules` INT(11) NULL DEFAULT NULL COMMENT 'GrupMatricules (Indica quina és la matrícula inicial del grup i totes tenen el mateix codi)' AFTER `rebut`;

ALTER TABLE `activitats`
	ADD COLUMN `ImatgeS` VARCHAR(200) NULL DEFAULT NULL AFTER `Definiciohoraris`,
	ADD COLUMN `ImatgeM` VARCHAR(200) NULL DEFAULT NULL AFTER `ImatgeS`,
	ADD COLUMN `ImatgeL` VARCHAR(200) NULL DEFAULT NULL AFTER `ImatgeM`;

/** PASSAT ***/

ALTER TABLE `cursos`
	ADD COLUMN `Teatre` VARCHAR(10) NULL DEFAULT NULL COMMENT 'Teatre' COLLATE 'utf8_general_ci' AFTER `DadesExtres`;

 TABLE `matricules`
	ADD COLUMN `Fila` SMALLINT NULL DEFAULT NULL COMMENT 'Fila' AFTER `GrupMatricules`,
	ADD COLUMN `Seient` SMALLINT NULL DEFAULT NULL COMMENT 'Seient' AFTER `Fila`;


const TEATRES = AUXDIR . 'Teatres/';