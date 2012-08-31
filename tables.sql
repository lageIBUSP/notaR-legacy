DROP TABLE IF EXISTS nota;
DROP TABLE IF EXISTS aluno;
DROP TABLE IF EXISTS teste;
DROP TABLE IF EXISTS exercicio;

CREATE TABLE aluno (
	id_aluno INT(10) PRIMARY KEY AUTO_INCREMENT,
	nome_aluno VARCHAR(200) NOT NULL
) ENGINE=INNODB;

CREATE TABLE exercicio (
	id_exercicio INT(10) PRIMARY KEY AUTO_INCREMENT,
	numero_aula INT(4) NOT NULL,
	numero_exercicio INT(4) NOT NULL,
	prazo Datetime,
	precondicoes VARCHAR(200),
	UNIQUE (numero_aula, numero_exercicio)
) ENGINE=INNODB;

CREATE TABLE teste (
	id_teste INT(10) PRIMARY KEY AUTO_INCREMENT,
	id_exercicio INT(10) NOT NULL,
	condicao VARCHAR(200) NOT NULL,
	INDEX(id_exercicio),
	FOREIGN KEY (id_exercicio) REFERENCES exercicio (id_exercicio)
) ENGINE=INNODB;

CREATE TABLE nota (
	id_nota INT(10) PRIMARY KEY AUTO_INCREMENT,
	id_aluno INT(10) NOT NULL,
	id_exercicio INT(10) NOT NULL,
	data Datetime NOT NULL,
	nota NUMERIC(2,1) NOT NULL,
	texto VARCHAR(2000) NOT NULL,
	INDEX (id_exercicio), INDEX (id_aluno),
	FOREIGN KEY (id_exercicio) REFERENCES exercicio (id_exercicio),
	FOREIGN KEY (id_aluno) REFERENCES aluno (id_aluno)
) ENGINE=INNODB;
