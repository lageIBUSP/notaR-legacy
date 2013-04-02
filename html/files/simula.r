simula=function(dados1,dados2, nsim=1000, teste="bi")
{
  return ( list(teste=teste,dados=c(deparse(substitute(dados1)),deparse(substitute(dados2))),diferencas=NULL) );
}
