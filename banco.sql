create database BdAsterisk;
CREATE USER 'asterisk'@'localhost' IDENTIFIED BY 'asterisk';
GRANT ALL PRIVILEGES ON BdAsterisk . * TO 'asterisk'@'localhost';
use BdAsterisk;
CREATE TABLE Nota
(
Data_nota datetime not null,
Caller_id varchar(50)not null,
Atendente varchar(50)not null,
Fila varchar(50)not null,
Nota int not null,
PRIMARY KEY(Data_nota,Caller_id)
);


