DROP DATABASE EscoteirosCR;

CREATE DATABASE EscoteirosCR;
USE EscoteirosCR;

CREATE TABLE Groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    state CHAR(2) NOT NULL,
    num INT NOT NULL,
    name VARCHAR(127) NOT NULL
);

CREATE TABLE Troups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_group INT NOT NULL,
    name VARCHAR(127) NOT NULL,
    FOREIGN KEY (id_group) REFERENCES Groups(id) ON DELETE CASCADE
);

CREATE TABLE Patrols (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_troup INT NOT NULL,
    status BOOLEAN NOT NULL DEFAULT TRUE,
    name VARCHAR(127) NOT NULL,
    FOREIGN KEY (id_troup) REFERENCES Troups(id) ON DELETE CASCADE
);

CREATE TABLE Users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reg CHAR(8) NOT NULL UNIQUE,
    username VARCHAR(32) NOT NULL,
    password VARCHAR(127) NOT NULL,
    name VARCHAR(255) NOT NULL
    -- por algum motivo não funciona:
    -- CONSTRAINT CK_LEN_username CHECK LEN(username) >= 4,
    -- CONSTRAINT CK_LEN_password CHECK LEN(password) >= 6
);

CREATE TABLE Chefia (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL, -- A chefia também pode ser interpretada como usuário
    -- Informações adicionais necessárias

    FOREIGN KEY (id_user) REFERENCES Users(id) ON DELETE CASCADE
);

-- TODO: Apagar o grupo se ele não tiver mais integrantes (talvez)

-- FIXME: Esse permission level é provisório, não tenho certeza se é ideal isso aí
-- a ideia é ser tipo: 
-- 0 -> integrante
-- 1 -> Chefia (moderador)
-- 2 -> Dono
CREATE TABLE Grupo_Integrantes (
    id_group INT NOT NULL,    
    id_user INT NOT NULL UNIQUE,
    permission_level INT DEFAULT 0, 
    status ENUM("Ativo", "Inativo") DEFAULT "Ativo",
    FOREIGN KEY (id_group) REFERENCES Groups(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES Users(id) ON DELETE CASCADE
);

CREATE TABLE Troup_Integrantes (
    id_troup INT NOT NULL,    
    id_user INT NOT NULL UNIQUE,
    permission_level INT DEFAULT 0, 
    status ENUM("Ativo", "Inativo") DEFAULT "Ativo",
    FOREIGN KEY (id_troup) REFERENCES Troups(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES Users(id) ON DELETE CASCADE
);

-- Revisar essa tag UNIQUE
CREATE TABLE Patrol_Integrantes (
    id_patrol INT NOT NULL,    
    id_user INT NOT NULL UNIQUE,
    permission_level INT DEFAULT 0, 
    status ENUM("Ativo", "Inativo") DEFAULT "Ativo",
    FOREIGN KEY (id_patrol) REFERENCES Patrols(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES Users(id) ON DELETE CASCADE
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
    FOREIGN KEY (id_badge) REFERENCES Badges(id) ON DELETE CASCADE
);

CREATE TRIGGER AutoBadgeInitialLevel
    AFTER INSERT ON Badges
    FOR EACH ROW
    INSERT INTO Badge_Levels (id_badge, title) VALUES (NEW.id, "Initial Level (Placeholder)");

-- Cada etapa para conseguir um distintivo específica
CREATE TABLE Badge_Level_Steps (
    -- TODO: Mudar isso aqui, para algum tipo de id melhor

    id INT PRIMARY KEY AUTO_INCREMENT,
    -- Posição desse passo em uma lista de passos
    order_index INT NOT NULL DEFAULT 0,
    -- Nível do distintivo que requer esse passo
    target_level INT NOT NULL,
    id_badge INT NOT NULL,
    title VARCHAR(127) NOT NULL,
    description VARCHAR(255),
    FOREIGN KEY (id_badge) REFERENCES Badges(id) ON DELETE CASCADE,
    FOREIGN KEY (target_level) REFERENCES Badge_Levels(id) ON DELETE CASCADE
);

CREATE TABLE Badge_Requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_user INT NOT NULL,
    id_badge INT NOT NULL,
    id_in_charge INT, -- Id do chefe que aprovou
    status ENUM("Idle", "Aprovado", "Recusado") DEFAULT "Idle",
    FOREIGN KEY (id_user) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_badge) REFERENCES Badges(id) ON DELETE CASCADE,
    FOREIGN KEY (id_in_charge) REFERENCES Chefia(id) ON DELETE CASCADE
);

-- Distintivos que um usuário tem
CREATE TABLE User_Badges (
    id_user INT NOT NULL,
    id_badge INT NOT NULL,
    id_request INT NOT NULL,
    id_step INT,
    id_level INT DEFAULT 1,

    status ENUM("Paused", "Progressing", "Complete") DEFAULT "Paused",
    -- Calculado pela API em porcentagem -> Passos concluídos / Quantidade de passos
    progress INT NOT NULL DEFAULT 0,

    CONSTRAINT FK_badge_user_id FOREIGN KEY (id_user) REFERENCES Users(id) ON DELETE CASCADE,
    CONSTRAINT FK_user_badge_id FOREIGN KEY (id_badge) REFERENCES Badges(id) ON DELETE CASCADE,
    CONSTRAINT FK_request_id FOREIGN KEY (id_request) REFERENCES Badge_Requests(id) ON DELETE CASCADE,
    CONSTRAINT FK_current_badge_step_id FOREIGN KEY (id_step) REFERENCES Badge_Level_Steps(id) ON DELETE SET NULL,
    CONSTRAINT FK_current_badge_level_id FOREIGN KEY (id_level) REFERENCES Badge_Levels(id) ON DELETE SET NULL
);

CREATE TABLE active_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL, -- Refresh token
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    UNIQUE (token_hash),
    FOREIGN KEY (user_id) REFERENCES Users(id)
);