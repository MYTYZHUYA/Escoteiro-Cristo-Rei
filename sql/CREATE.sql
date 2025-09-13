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


-- Distintivos que o escoteiro pode conseguir
CREATE TABLE Badges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(127),
    description VARCHAR(127)
);

CREATE TABLE Badge_Levels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_badge INT NOT NULL,
    -- Só um 'título' para o distintivo, por exemplo se for um distintivo nível máximo, o título pode ser meste em tal coisa
    title VARCHAR(32),
    level INT NOT NULL DEFAULT 0,
    FOREIGN KEY (id_badge) REFERENCES Badges(id)
);

-- Cada etapa para conseguir um distintivo específica
CREATE TABLE BadgeSteps (
    -- Posição desse passo em uma lista de passos
    order_index INT NOT NULL DEFAULT 0,
    -- Nível do distintivo que requer esse passo
    target_level INT NOT NULL,
    id_badge INT NOT NULL,
    title VARCHAR(127) NOT NULL,
    description VARCHAR(255),
    FOREIGN KEY (id_badge) REFERENCES Badges(id),
    FOREIGN KEY (target_level) REFERENCES Badge_Levels(id)
);

-- Distintivos que um usuário tem
CREATE TABLE User_Badges (
    id_user INT NOT NULL,
    id_badge INT NOT NULL,
    id_step INT NOT NULL,
    id_level INT NOT NULL,

    status ENUM("Paused", "Progressing", "Complete") DEFAULT "Paused",
    -- Calculado pela API em porcentagem -> Passos concluídos / Quantidade de passos
    progress INT NOT NULL DEFAULT 0,

    FOREIGN KEY (id_user) REFERENCES Users(id),
    FOREIGN KEY (id_badge) REFERENCES Badges(id),
    FOREIGN KEY (id_step) REFERENCES BadgeSteps(id),
    FOREIGN KEY (id_level) REFERENCES Badge_Levels(id)
);