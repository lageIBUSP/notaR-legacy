
file.exerc <- "exerc_3_ma.r"

file.test <- "respostas_3.r"

corrige <- function (file.exerc, file.test) {
		source(file.exerc)
		source(file.test)
		return(sum(ANSWERS))
}

print(corrige(file.exerc, file.test))
