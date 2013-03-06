## ## ## Corretor automatico

# Usa uma conexao "global" con
connect <- function () {
		require(RMySQL)
		# Conexao com o banco de dados
		try(dbDisconnect(con), silent=TRUE)
		con<- dbConnect(MySQL(), user="notaR", password="notaRpw", dbname="notaR")
		return (con);
}
con <- connect()

# corretoR recebe: 
# texto 
# E devolve um um vector logico com o resultado dos testes
# Caso o codigo tenha erros de sintaxe, retorna NULL
corretoR <- function (id.exerc, texto) {
		# Definicoes iniciais
		corrEnv <- new.env()
		testes <- dbGetQuery(con,
							 paste("SELECT condicao FROM teste
								   WHERE id_exercicio=", id.exerc,
								   " ORDER BY ordem ASC", sep=""));
		precondi <- dbGetQuery(con, 
							   paste("SELECT precondicoes FROM exercicio 
									 WHERE id_exercicio=", id.exerc, sep=""));

		# Executa as precondicoes
		if(sum(dim(precondi)) > 0) eval(parse(text=precondi), envir=corrEnv);

		# Executa o texto da resposta
		# try pega erros de sintaxe
		getError <- try(eval(parse(text=texto), envir=corrEnv));
		if (class(getError) == "try-error") return (NULL);

		# Executa os testes cadastrados, sequencialmente
		notaMax <-dim(testes)[1]
		notas <- rep(FALSE, notaMax)
		for (i in 1:notaMax) {
				# A avaliacao pode retornar TRUE, FALSE ou erro
				# No momento, erro esta sendo tratado como FALSE
				# Edit fev 2013: 
				# O [1] no final tem a funcao de evitar condicoes com comprimento 0.
				# Agora essas condicoes se tornam [1] NA, que serao transformados em FALSE abaixo
				notas[i] <- try(eval(parse(text=testes[i,1]), envir=corrEnv))[1] == TRUE;
		}
		notas[is.na(notas)] <- FALSE
		return(notas);
}

# Gera um output formatado em HTML a respeito de um exercicio corrigido
relatorioNota <- function (id.exerc, nota, texto) {
		# Definicoes iniciais
		dica <- dbGetQuery (con,
							paste("SELECT dica FROM teste
								  WHERE id_exercicio = ", id.exerc,
								  " ORDER BY ordem ASC ", sep=""));
		peso <- dbGetQuery (con,
							paste("SELECT peso FROM teste
								  WHERE id_exercicio = ", id.exerc,
								  " ORDER BY ordem ASC ", sep=""));
		notaMax <-dim(dica)[1]
		Rel <- "";
		if (! is.null(nota) && sum(nota) != notaMax) { 
			Rel <- paste(Rel, "<font color='#8c2618'>ATEN&Ccedil;&Atilde;O!</font><br>")
			# Envia a primeira mensagem de erro
			primeiro.erro <- min(which(!nota))
			Rel <- paste(Rel, "<br>", dica[primeiro.erro,1], 
					 "<br>Corrija essa condi&ccedil;&atilde;o para continuar a corre&ccedil;&atilde;o.",	sep="");
		}
		if (is.null(nota)) {
				Rel <- paste(Rel, "<p>Cuidado! Seu exerc&iacute;cio n&atilde;o executou. Ser&aacute; que ele cont&eacute;m algum
							  erro de sintaxe? Veja dicas no linque de \"ajuda\" para corrigir os problemas.</p>", sep="");
		} else { Rel <- paste(Rel, "<p>Seu aproveitamento: <b>", round(100*weighted.mean(nota, t(peso))),"%</b>.</p>", sep=""); }
		Rel <- paste(Rel, "<p>Sua resposta:<br>", paste(texto, collapse="<br>"),"</p>", sep="");
		return(Rel)
}

# gravarNota recebe
# nome.aluno (pode ser NULL, no caso de ouvintes)
# numero.aula, numero.exercicio
# nota: logical vector contendo o resultado da correcao
# texto: resposta dada pelo aluno
# Valor de retorno: char, especificando mensagem de sucesso ou erro 
# na insercao da nota
gravarNota <- function (nome.aluno, id.exerc, texto, nota = corretoR(id.exerc, texto)) {
		# Definicoes iniciais
		id.aluno <- dbGetQuery(con, 
							   paste("SELECT id_aluno FROM aluno 
									 WHERE nome_aluno ='", nome.aluno,"'", sep=""));

		if (sum(dim(id.aluno)) == 0) return ("<p><font color='#8c2618'>Voc&ecirc; n&atilde;o est&aacute; logado.</font> A nota n&atilde;o foi gravada.</p>")

		prazo <- dbGetQuery(con,
			paste("SELECT prazo FROM prazo
			JOIN turma USING (id_turma) JOIN aluno USING (id_turma)
			WHERE id_exercicio=", id.exerc, " AND id_aluno=", id.aluno));
		Date <- format(Sys.time(), "%F %R");

		# Condicoes para gravar a nota
		if (sum(dim(prazo)) > 0) if (Date > prazo) return ("<p><font color='#8c2618'>O prazo para entrega j&aacute; expirou!</font> A nota n&atilde;o foi gravada.</p>")
		if (is.null(nota)) return (NULL);

		peso <- dbGetQuery (con,
							paste("SELECT peso FROM teste
								  WHERE id_exercicio = ", id.exerc,
								  " ORDER BY ordem ASC ", sep=""));

		# Escapa os single quotes do texto
		texto <- gsub("'", '"', texto)
		res <- dbSendQuery(con,paste("INSERT INTO nota (id_aluno, id_exercicio, data, nota, texto) 
									 VALUES (",id.aluno,",",id.exerc,",'",Date,"',",round(100*weighted.mean(nota, t(peso))), ",'",paste(texto, collapse="\n"),"')",sep=""))
		melhorNota <- dbGetQuery(con,
								 paste("SELECT max(nota) FROM nota
									   WHERE id_aluno = ",id.aluno, " AND id_exercicio=",
									   id.exerc, sep=""));
		Rel <- paste("<p>Nota cadastrada! Sua melhor nota nesse exerc&iacute;cio &eacute; <b>", melhorNota, 
					  "%</b>.", sep="")
		if (sum(dim(prazo)) > 0) Rel <- paste (Rel, "<br>O prazo para enviar novas tentativas &eacute; ", prazo, ".", sep="");
		return (paste(Rel, "</p>"));
}

# Recebe o exercicio, corrige, grava a nota e gera um output formatado em HTML
notaR <- function (nome.aluno, id.exerc, arquivo) {
		texto <- readLines(arquivo, encoding="utf8");
		nota <- corretoR (id.exerc, texto);
		# Tenta de novo com charset latin1:
		if (is.null(nota)) {
			texto <- readLines(arquivo, encoding="latin1");
			nota <- corretoR (id.exerc, texto);
		}
		# Grava a nota no banco:
		notaGravada <- gravarNota(nome.aluno, id.exerc, texto, nota)
		# Gera o relatorio de notas:
		Rel <- relatorioNota(id.exerc, nota, texto);
		return(paste(Rel, notaGravada,sep=""))
}
# Exemplos: 
# notaR('chalom', 2,"xpto.R")
