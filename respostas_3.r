ANSWERS <- rep(FALSE, 5)
if ( exists ("hamsters") ) {
	ANSWERS[1] <- TRUE
	if (class(hamsters) == "data.frame") ANSWERS[2] <- TRUE
	if (identical(dim(hamsters),as.integer(c(18,3)))) ANSWERS[3]  <- TRUE
	attach(hamsters)
	if (exists ("pesos") & sum(pesos) == 13.4) ANSWERS[4] <- TRUE
	detach()
	dieta<-c(rep(c("A", "B", "C"),each=6))
	cor<-c(rep(c(rep(c("claro", "escuro"), each=3)), times=3))
	pesos<-c(0.1, 1.1, 3.7, 1.5, -0.1, 2.0, 5.7, -1.2, -1.5, 0.6, -3.0, -0.3, 3.0, -0.4, 0.6, -0.2, 0.3, 1.5)
	meus.hamsters<-data.frame(dieta, cor, pesos)
	if (identical(meus.hamsters, hamsters)) ANSWERS[5] <- TRUE
}
