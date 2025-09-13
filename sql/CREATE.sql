CREATE DATABASE EscoteirosCR;
USE EscoteirosCR;

CREATE TABLE Groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estado CHAR(2) NOT NULL,
    num INT NOT NULL,
    name VARCHAR(127) NOT NULL
);

CREATE TABLE Troups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_group INT NOT NULL,
    name VARCHAR(127) NOT NULL,
    FOREIGN KEY (id_group) REFERENCES Groups(id)    
);

CREATE TABLE Patrols (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_troup INT NOT NULL,
    status BOOLEAN NOT NULL DEFAULT TRUE,
    name VARCHAR(127) NOT NULL,
    FOREIGN KEY (id_troup) REFERENCES Troups(id)
);

CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg CHAR(8) NOT NULL UNIQUE,
    username VARCHAR(32) NOT NULL,
    password VARCHAR(127) NOT NULL,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE Grupo_Integrantes (
    id_group INT NOT NULL,    
    id_user INT NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT "Ativo",
    FOREIGN KEY (id_group) REFERENCES Groups(id),
    FOREIGN KEY (id_user) REFERENCES Users(id)
);

CREATE TABLE Troup_Integrantes (
    id_troup INT NOT NULL,    
    id_user INT NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT "Ativo",
    FOREIGN KEY (id_troup) REFERENCES Troups(id),
    FOREIGN KEY (id_user) REFERENCES Users(id)
);

CREATE TABLE Patrol_Integrantes (
    id_patrol INT NOT NULL,    
    id_user INT NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT "Ativo",
    FOREIGN KEY (id_patrol) REFERENCES Patrols(id),
    FOREIGN KEY (id_user) REFERENCES Users(id)
);
