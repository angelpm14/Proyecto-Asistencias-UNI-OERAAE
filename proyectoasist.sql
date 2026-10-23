CREATE DATABASE IF NOT EXISTS proyectoasist;

use proyectoasist; 

DROP TABLE IF EXISTS ROL;
CREATE TABLE ROL (
  id_rol int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(100) DEFAULT NULL,
  PRIMARY KEY (id_rol)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO ROL VALUES ('1', 'ADMIN');
INSERT INTO ROL VALUES ('2', 'PRACTICANTE');
INSERT INTO ROL VALUES ('3', 'TRABAJOR');

select * from ROL

DROP TABLE IF EXISTS AREAS;
CREATE TABLE AREAS (
  id_area int(11) NOT NULL AUTO_INCREMENT,
  nombre varchar(100) DEFAULT NULL,
  PRIMARY KEY (id_area)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO AREAS VALUES ('1', 'Programacion');
INSERT INTO AREAS VALUES ('2', 'Mesa de partes');
INSERT INTO AREAS VALUES ('3', 'Impresion');

select * from areas

DROP TABLE IF EXISTS TURNO;
CREATE TABLE TURNO (
  id_turno INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) DEFAULT NULL,
  hora_inicio TIME,
  hora_fin TIME,
  PRIMARY KEY (id_turno)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO TURNO VALUES ('1', 'TURNO MAÑANA', '08:00:00', '13:00:00');
INSERT INTO TURNO VALUES ('2', 'TURNO TARDE', '13:00:00', '17:00:00');
INSERT INTO TURNO VALUES ('3', 'FULL', '08:00:00', '17:00:00');

#delete from turno where id_turno=13
select * from TURNO

DROP TABLE IF EXISTS USUARIOS;
create table USUARIOS(
    id_usu int(11) not null auto_increment,
    nombre varchar(50) not null,
    apellido varchar(50) not null,
    dni char(8) not null,
    id_rol int(11) not null,
    email varchar(100) not null,
    telefono varchar(20) not null,
    direccion varchar(100) not null,
    sexo varchar(20) not null,
    id_area int(11) not null,
    id_turno int(11) not null,
    usuario varchar(50) not null,
    contra varchar(20) not null,
    PRIMARY KEY (id_usu),
	FOREIGN KEY (id_rol) REFERENCES ROL (id_rol),
	FOREIGN KEY (id_area) REFERENCES AREAS (id_area),    
	FOREIGN KEY (id_turno) REFERENCES TURNO (id_turno)
);

INSERT INTO USUARIOS (id_usu,nombre,apellido,dni,id_rol,email,telefono,direccion,sexo,id_area,id_turno,usuario,contra) VALUES
('1','maxwell','Lopez','745896123',1,'max@gmail.com','987654321','los olivos','M',1,1,'admin','123');

select * from USUARIOS;

#update USUARIOS set nueva_contrasena="jose1", confirmar_contrasena="jose1" where id_usu;

DROP TABLE IF EXISTS MIEMBROS;
CREATE TABLE MIEMBROS(
	id_miembro int(11) not null auto_increment,
    nombre varchar(50) not null,
    apellido varchar(50) not null,
    dni char(8) not null,
    id_rol int(11) not null,
    id_area int(11) not null,
    email varchar(100) not null,
    telefono varchar(20) not null,
    direccion varchar(100) not null,
    especialidad varchar(120) not null,
    id_turno int(11) not null,
    hors_acum float not null default 0,
    hors_neces float not null default 0,
    PRIMARY KEY (id_miembro),
	FOREIGN KEY (id_rol) REFERENCES ROL (id_rol),
    FOREIGN KEY (id_area) REFERENCES AREAS (id_area),
    FOREIGN KEY (id_turno) REFERENCES TURNO (id_turno)
);

INSERT INTO MIEMBROS VALUES ('1', 'Gerson','Presentacion','54129856','2','1','gerson@gmail.com','96587412','addsvasda','Diseñador grafico','2', '0','1,815');
/*
UPDATE MIEMBROS SET
               nombre = 'Maxwell', apellido = 'lopez', dni = '74589612', id_rol = 1, id_area = 1,
               email = 'maxwell@gmail.com', telefono = '000000000', direccion = 'lima', especialidad = 'Desarrollo de software',
               id_turno = 3, hors_acum = 50, hors_neces = 100
               WHERE id_miembro = 2
UPDATE MIEMBROS SET
               nombre = 'angel', apellido = 'kkkkkkkk', dni = '74589654', id_rol = 2, id_area = 1,
               email = 'angel@gmail.com', telefono = '6541484864', direccion = 'lima', especialidad = 'Computacion e informatica',
               id_turno = 3, hors_acum = 24.5, hors_neces = 24
               WHERE id_miembro = 3
*/
select * from MIEMBROS

DROP TABLE IF EXISTS REGIST_ASIS;
CREATE TABLE REGIST_ASIS (
  id_regist int(11) NOT NULL AUTO_INCREMENT,
  id_miembro int(11) NOT NULL,
  entrada datetime DEFAULT NULL,
  salida datetime DEFAULT NULL,
  dni char(8) not null,
  justifc varchar(500) default null,
  PRIMARY KEY(id_regist),
  FOREIGN KEY (id_miembro) REFERENCES MIEMBROS (id_miembro)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
ALTER TABLE REGIST_ASIS ADD COLUMN fecha DATE DEFAULT NULL AFTER id_miembro;
SET SQL_SAFE_UPDATES = 0;
UPDATE REGIST_ASIS SET fecha = DATE(entrada) WHERE entrada IS NOT NULL;
UPDATE REGIST_ASIS SET fecha = CURDATE() WHERE entrada IS NULL AND fecha IS NULL;
ALTER TABLE REGIST_ASIS ADD UNIQUE KEY uq_miembro_fecha (id_miembro, fecha);
-- ----------------------------
-- Records of asistencia
-- ----------------------------
INSERT INTO REGIST_ASIS VALUES ('3', '1', '2022-03-31 00:17:34', '2022-03-31 00:17:41','12458756','');
#INSERT INTO REGIST_ASIS VALUES ('2', '4', '2022-03-31 00:22:53', '2022-03-31 00:23:04','36251478');
#INSERT INTO REGIST_ASIS VALUES ('3', '11', '2022-03-31 10:36:58', '2022-03-31 10:37:37','2154847');
#INSERT INTO REGIST_ASIS VALUES ('4', '6', '2022-08-06 20:59:07', '','12654765');

DELETE FROM REGIST_ASIS WHERE fecha IS NULL AND entrada IS NULL AND salida IS NULL;

#UPDATE MIEMBROS SET hors_acum = 290 WHERE dni = '72424598';

SELECT * FROM REGIST_ASIS



