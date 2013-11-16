DROP TABLE IF EXISTS nota;
DROP TABLE IF EXISTS aluno;
DROP TABLE IF EXISTS prazo;
DROP TABLE IF EXISTS turma;
DROP TABLE IF EXISTS teste;
DROP TABLE IF EXISTS proibido;
DROP TABLE IF EXISTS exercicio;

CREATE TABLE turma (
	id_turma INT(10) PRIMARY KEY AUTO_INCREMENT,
	nome_turma VARCHAR(200) NOT NULL
) ENGINE=INNODB;

INSERT INTO turma(id_turma, nome_turma) VALUES (1, "Admin");

CREATE TABLE aluno (
	id_aluno INT(10) PRIMARY KEY AUTO_INCREMENT,
	nome_aluno VARCHAR(200) NOT NULL,
	UNIQUE KEY nome_aluno (nome_aluno),
	senha VARCHAR(40) NOT NULL,
	id_turma INT(10) NOT NULL,
	admin tinyint(4) DEFAULT '0',
	INDEX (id_turma),
	FOREIGN KEY (id_turma) REFERENCES turma (id_turma)
) ENGINE=INNODB;

INSERT INTO aluno (id_aluno, nome_aluno, senha, id_turma, admin) VALUES (1, "admin", SHA1("segredo"), 1, 1); 

CREATE TABLE exercicio (
	id_exercicio INT(10) PRIMARY KEY AUTO_INCREMENT,
	precondicoes VARCHAR(2000),
	nome VARCHAR(200) NOT NULL,
	html VARCHAR(4000) NOT NULL
) ENGINE=INNODB;

INSERT INTO exercicio (id_exercicio, precondicoes, nome, html) VALUES (1, "", "1.1 Exemplo", "Exemplo de exerc&iacute;cio. Crie um objeto chamado 'x', contendo um vetor numerico de tamanho 5.<br>Dica: o comando 'x <- 1:5' &eacute; uma resposta correta.");

CREATE TABLE prazo (
	id_exercicio INT(10) NOT NULL,
	id_turma INT(10) NOT NULL,
	prazo Datetime,
	INDEX (id_exercicio), INDEX(id_turma),
	FOREIGN KEY (id_exercicio) REFERENCES exercicio (id_exercicio),
	FOREIGN KEY (id_turma) REFERENCES turma (id_turma),
	PRIMARY KEY (id_exercicio, id_turma)
) ENGINE=INNODB;

CREATE TABLE teste (
	id_teste INT(10) PRIMARY KEY AUTO_INCREMENT,
	id_exercicio INT(10) NOT NULL,
	condicao VARCHAR(4000) NOT NULL,
	ordem INT(4),
	peso  INT(10) DEFAULT 1,
	dica VARCHAR(200),
	INDEX(id_exercicio),
	FOREIGN KEY (id_exercicio) REFERENCES exercicio (id_exercicio)
) ENGINE=INNODB;

insert into teste (id_exercicio, condicao, ordem, dica) VALUES 
(1, 'exists(\"x\")', 1, 'Crie um objeto chamado x'), 
(1, 'class(x) == \"numeric\" | class(x) == \"integer\"', 2, 'x deve ser da classe numeric ou integer'), 
(1, 'length(x)==5', 3, 'x deve ter exatamente 5 elementos');

CREATE TABLE nota (
	id_nota INT(10) PRIMARY KEY AUTO_INCREMENT,
	id_aluno INT(10),
	id_exercicio INT(10) NOT NULL,
	data Datetime NOT NULL,
	nota INT(3) NOT NULL,
	texto VARCHAR(8000) NOT NULL,
	INDEX (id_exercicio), INDEX (id_aluno),
	FOREIGN KEY (id_exercicio) REFERENCES exercicio (id_exercicio),
	FOREIGN KEY (id_aluno) REFERENCES aluno (id_aluno)
) ENGINE=INNODB;

CREATE TABLE proibido (
	id_proibido int(10) PRIMARY KEY AUTO_INCREMENT,
	palavra VARCHAR(200),
	id_exercicio INT(10),
	INDEX (id_exercicio), 
	FOREIGN KEY (id_exercicio) REFERENCES exercicio (id_exercicio)
) ENGINE=INNODB;

INSERT INTO proibido (palavra) VALUES ('system'),('save.image'),('file.copy'),('set.seed'),('library'),
('MySQL'),('dbApply'),('dbBuild'),('dbCall'),('dbConnect'),('dbEscape'),('dbSendQuery'),
('mysql'),('unlink'),('setwd'),('shell'),('.C'),('dyn.load'),('library.dynam'),('.External'),
('.Fortran'),('.Call'),('.Platform'),('<<-'),('set.global'),('reg.finalizer'),('on.exit'),
('assign'),('deparse'),('new.env'),('.Last'),('parse'),('eval');

