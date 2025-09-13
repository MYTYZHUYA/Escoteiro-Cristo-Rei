CREATE DATABASE EscoteirosCR;
USE EscoteirosCR;

CREATE TABLE Grupos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(127) NOT NULL
);

CREATE TABLE Tropas (
    
);

CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg CHAR(8) NOT NULL,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE Grupo_Integrantes (
    id_grupo INT NOT NULL,    
    id_user INT NOT NULL
);
