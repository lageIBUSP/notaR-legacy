DELETE FROM prazo;
DELETE FROM nota;
DELETE FROM teste;
DELETE FROM exercicio;
DELETE FROM aluno;
DELETE FROM turma;

INSERT INTO turma (nome_turma) VALUES ('Alunos 2013');
SET @turma := (SELECT id_turma FROM turma WHERE nome_turma='Alunos 2013');

INSERT INTO aluno (nome_aluno, id_turma) VALUES ('chalom', @turma);

-- O prazo para a aula 1 jah terminou!

INSERT INTO exercicio (numero_aula, numero_exercicio, precondicoes)
VALUES (1, 1, '
dieta<-c(rep(c("A", "B", "C"),each=6));
cor<-c(rep(c(rep(c("claro", "escuro"), each=3)), times=3));
pesos<-c(0.1, 1.1, 3.7, 1.5, -0.1, 2.0, 5.7, -1.2, -1.5, 0.6, -3.0, -0.3, 3.0, -0.4, 0.6, -0.2, 0.3, 1.5);
meus.hamsters<-data.frame(dieta, cor, pesos);
');

SET @exerc := (SELECT id_exercicio FROM exercicio WHERE numero_aula=1
		AND numero_exercicio=1);
INSERT INTO prazo (id_exercicio, id_turma, prazo) VALUES
	(@exerc, @turma, '2001-01-01 00:00:00');
INSERT INTO teste (id_exercicio, condicao, ordem) VALUES 
(@exerc, 'exists("hamsters")', 1);

-- O prazo para a aula 3 ainda eh valido

INSERT INTO exercicio (numero_aula, numero_exercicio, precondicoes)
VALUES (3, 2, '
dieta<-c(rep(c("A", "B", "C"),each=6));
cor<-c(rep(c(rep(c("claro", "escuro"), each=3)), times=3));
pesos<-c(0.1, 1.1, 3.7, 1.5, -0.1, 2.0, 5.7, -1.2, -1.5, 0.6, -3.0, -0.3, 3.0, -0.4, 0.6, -0.2, 0.3, 1.5);
meus.hamsters<-data.frame(dieta, cor, pesos);
');

SET @exerc := (SELECT id_exercicio FROM exercicio WHERE numero_aula=3 
		AND numero_exercicio=2);

INSERT INTO prazo (id_exercicio, id_turma, prazo) VALUES
	(@exerc, @turma, '2012-12-12 00:00:00');
INSERT INTO teste (id_exercicio, condicao, ordem, dica) VALUES 
(@exerc, 'exists("hamsters")', 1, 
		'N&atilde;o existe nenhum objeto chamado "hamsters"');
INSERT INTO teste (id_exercicio, condicao, ordem, dica) VALUES 
(@exerc, 'class(hamsters)=="data.frame"', 2, 
		'Hamsters n&atilde;o &eacute; um data frame');
INSERT INTO teste (id_exercicio, condicao, ordem, dica) VALUES 
(@exerc, 'identical(dim(hamsters), as.integer(c(18,3)))', 3,
		'As dimens&otilde;es do objeto est&atilde;o erradas');
INSERT INTO teste (id_exercicio, condicao, ordem, dica) VALUES 
(@exerc, '"pesos" %in% names(hamsters)', 4,
		'O nome das colunas est&aacute; errado');
INSERT INTO teste (id_exercicio, condicao, ordem, dica) VALUES 
(@exerc, 'sum(hamsters$pesos) == 13.4', 5,
		'Os pesos dos hamsters est&atilde;o errados');
INSERT INTO teste (id_exercicio, condicao, ordem, dica) VALUES 
(@exerc, 'identical(hamsters, meus.hamsters)', 6,
		'O data frame gerado n&atilde;o est&aacute; igual ao esperado');

COMMIT;
