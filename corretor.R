# Conexao com o banco de dados funciona no servidor da lage:
#library(RMySQL)
#con <-dbConnect(MySQL(), user="notaR", password="autonota", dbname="notaR")

file.exerc <- "exerc_3_ma.r"
file.test <- "respostas_3.r"

#Corrige simplesmente faz source() em 3 arquivos
corrige <- function (file.prev, file.exerc, file.test) {
		if (!is.null(file.prev)) source(file.prev)
		source(file.exerc)
		source(file.test)
		return(sum(ANSWERS))
}

print(corrige(NULL, file.exerc, file.test))

# Alternativa: parse+eval:
eval(parse(text="y<-rnorm(5)"))
