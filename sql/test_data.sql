-- Valores adicionados pelo ChatGpt para testes !!!
-- não usar para nada além de testes !!
INSERT INTO Groups (estado, num, name)
VALUES 
('SP', 123, 'Grupo Escoteiro Anhanguera'),
('RJ', 45, 'Grupo Escoteiro do Sol'),
('MG', 87, 'Grupo Escoteiro das Montanhas');

INSERT INTO Troups (id_group, name)
VALUES
(1, 'Tropa Sênior Falcões'),
(1, 'Tropa Escoteira Lobos'),
(2, 'Tropa Sênior Jaguar'),
(3, 'Tropa Escoteira Águias');

INSERT INTO Patrols (id_troup, name, status)
VALUES
(1, 'Patrulha Lobo Cinzento', TRUE),
(1, 'Patrulha Raposa Vermelha', TRUE),
(2, 'Patrulha Urso Pardo', TRUE),
(3, 'Patrulha Onça Pintada', TRUE),
(4, 'Patrulha Falcão Dourado', TRUE);

INSERT INTO Users (reg, username, password, name)
VALUES
('00000001', 'joao123', 'senha123', 'João Silva'),
('00000002', 'maria456', 'senha456', 'Maria Oliveira'),
('00000003', 'pedro789', 'senha789', 'Pedro Santos'),
('00000004', 'ana321', 'senha321', 'Ana Costa'),
('00000005', 'lucas654', 'senha654', 'Lucas Pereira');

INSERT INTO Chefia (id_user)
VALUES
(1),
(2);

INSERT INTO Grupo_Integrantes (id_group, id_user, status)
VALUES
(1, 1, 'Ativo'),
(1, 2, 'Ativo'),
(1, 3, 'Ativo'),
(2, 4, 'Ativo'),
(3, 5, 'Inativo');

INSERT INTO Troup_Integrantes (id_troup, id_user, status)
VALUES
(1, 1, 'Ativo'),
(1, 3, 'Ativo'),
(2, 2, 'Ativo'),
(3, 4, 'Ativo'),
(4, 5, 'Inativo');

INSERT INTO Patrol_Integrantes (id_patrol, id_user, status)
VALUES
(1, 1, 'Ativo'),
(1, 3, 'Ativo'),
(2, 2, 'Ativo'),
(3, 4, 'Ativo'),
(4, 5, 'Ativo');