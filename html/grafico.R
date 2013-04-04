#!/usr/bin/Rscript --vanilla
# Usa uma conexao "global" con
require(RMySQL)
# Conexao com o banco de dados
con<- dbConnect(MySQL(), user="notaR", password="notaRpw", dbname="notaR")

basedir <- "/var/www/rserve/img/"
startpng <-function(name) png(width=800, height=550, filename=paste(basedir,name,sep=""))

### Por Hora
porDow <- function() {
	startpng("dow.png")
	x <- dbGetQuery(con, "select count(*), date_format(data, '%w') 
	from nota group by date_format(data, '%w')")
	x[,1] <- x[,1]/sum(x[,1])
	par(fg='#FF6666', family='Verdana')
	plot(x[,1]~x[,2], type='l', bty='n', xaxt='n', yaxt='n', xlab='Dia', ylab='% entregas', col='#007788', lwd=3, ylim=c(0,0.35))
	axis(1, at=0:6, labels=c("dom", "seg", "ter", "qua", "qui", "sex", "sab"), lwd=3)
	axis(2, at=c(0,0.1, 0.2, 0.3), lwd=3)
	dev.off()
}

porHora <- function() {
	startpng("porhora.png")
	x <- dbGetQuery(con, "select count(*), date_format(data, '%H') 
	from nota group by date_format(data, '%H')")
	x[,1] <- x[,1]/sum(x[,1])
	par(fg='#FF6666', family='Verdana')
	plot(spline(x[,2],x[,1]), type='l', bty='n', xaxt='n', yaxt='n', xlab='Hora', ylab='% entregas', col='#007788', lwd=3, ylim=c(0,0.15))
	axis(1, at=2*0:11, lwd=3)
	axis(2, at=c(0,0.05, 0.1, 0.15), lwd=3)
	dev.off()
}
porExercicio <- function() {
	turma = 8
	startpng("exercicio.png")
	n_turma <- dbGetQuery(con, paste("select count(distinct id_aluno) from aluno join nota using(id_aluno) where id_turma=",turma))
	x <- dbGetQuery(con, paste("select nome, count(distinct id_aluno) from nota join aluno using(id_aluno)  join exercicio using (id_exercicio) where id_turma=",turma,"group by nome"))
	y <- dbGetQuery(con, paste("select nome, count(distinct id_aluno) from nota join aluno using(id_aluno)  join exercicio using (id_exercicio) where id_turma=",turma," and nota=100 group by nome"))
	x <- merge(x, y, by="nome", all=TRUE)
	x[,2:3] <- x[,2:3] / as.numeric(n_turma)
	Encoding(x[,1]) <- "latin1"
	#extrai o numero do exercicio
	f <- function(s) strsplit(s, " ")[[1]][1]
	x[,1] <- sapply(x[,1], f)
	x[is.na(x)] <- 0
	par(fg='#FF6666', family='Verdana', cex.axis=0.8)
	plot(x[,2], type='l', bty='n', xaxt='n', yaxt='n', xlab='Exercicio', ylab='% incompleto/completo', col='#007788', lwd=3, ylim=c(0,1))
	points(x[,3], type='l', col='#770088', lwd=3)
	axis(1, at=1:dim(x)[1], labels=x[,1], lwd=3)
	axis(2, at=c(0,0.5, 1), lwd=3)
	dev.off()
}

porHora()
porDow()
porExercicio()
