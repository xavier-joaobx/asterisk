create database BdAsterisk;

use BdAsterisk;
CREATE TABLE Nota
(
Data_nota datetime not null primary key,
Caller_id varchar(50)not null,
Atendente varchar(50)not null,
Fila varchar(50)not null,
Nota int not null,
);
