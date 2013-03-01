simula=function(dados1,dados2, nsim=1000, teste="bi")
{
  cat("simula uma distribuicao nula, no caso de teste unicaudal o primeiro vetor deve ser o dos dados que seriam maiores.\n As opcoes de teste sao (entre aspas):\n\t uni (normal unicaudal)\n\t bi (normal bicaudal) \n\t t (distribuicao t)\n" )
  dif= mean(dados1)-mean(dados2)
  dif.abs=round(abs(dif),1)
  cat("\n Diferenca absoluta observada entre as medias das variaveis = ", dif.abs, "\n")
  v1=var(dados1)
  v2=var(dados2)
  n1=length(dados1)
  n2=length(dados2)
  s12=sqrt((v1/n1)+(v2/n2))
  tvalor=abs(dif/s12)
  med=mean(c(dados1,dados2))
  des= sd(c(dados1,dados2))
  res=rep(NA,nsim)
  arco=rainbow(nsim)
  #################################################
  ######## uni ou bi caudal simulado pela diferenÃ§a
  #################################################
  if (teste=="bi" | teste=="uni")
  {
    meu.cex=1700/nsim
    plot(runif(50,(dif.abs*-1.5),(dif.abs*1.5)),0:49, type="n", xlim=c((dif*-1.5),(dif*1.5)), ylim=c(0,(0.04*nsim)), xlab=" Diferenca ", ylab="Frequencia", main="Simulacao")
    for (i in 1:nsim)
    {
      res[i]=round(mean(rnorm(n1,mean=med,sd=des))-mean(rnorm(n2,mean=med,sd=des)),1)
      #cat("diferenï¿½a =", res[i],"\n")
      stripchart(res, method="stack", add=T, cex=meu.cex,pch=15, col=arco[i])
    }
    if (teste=="uni")
    {
      res.maior<-res[res<dif.abs]
      legend("top", legend=paste(sum(res>=dif.abs), "valores >= ", round(dif,1)), bty="n", text.col="red3")
      stripchart(res.maior, method="stack", add=T, cex=meu.cex,pch=15, col="dark green")
      abline(v=dif.abs, col="red", lty=2)
    }
    if (teste=="bi")
    {
      res.menor<-res[res<dif.abs & res>(-1*dif.abs) ]
      legend("top", legend=paste(sum(res>=dif.abs |res<= - dif.abs), "valores em modulo >= ", round(dif,1)), bty="n", text.col="red3")
      stripchart(res.menor, method="stack", add=T, cex=meu.cex,pch=15, col="dark green")
      legend ((dif.abs*0.5),(0.038*nsim), legend=paste(sum(res>=dif.abs), "vals. >= ", round(dif,1)), bty="n", text.col="red3")
      legend ((dif.abs*-1.4), (0.038*nsim),legend=paste(sum(res<=(dif.abs*-1)), "vals. <= ", round(dif,1)),bty="n",text.col="red3") 
      abline(v=dif.abs, col="red", lty=2)
      abline(v=(-1*dif.abs), col="red", lty=2)
    }
  }
  ############### TESTE T ###################	
  if (teste=="t")
  {
    meu.cex=1300/nsim
    cat("\t\nValor t observado = ", tvalor, "\n")
    plot(runif(50,(-0.7* dif.abs),(0.7*dif.abs)),runif(50,0,(nsim/20)), type="n", xlim=c((-0.8* dif.abs),(0.8*dif.abs)), ylim=c(0,(nsim/20)), xlab=" valor t ", ylab="Frequencia", main="Simulacao")
    for (i in 1:nsim)
    {
      simula1=rnorm(n1,mean=med,sd=des)
      simula2=rnorm(n2,mean=med,sd=des)
      difs= mean(simula1)-mean(simula2)
      vs1=var(simula1)
      vs2=var(simula2)
      ss12=sqrt((vs1/n1)+(vs2/n2))
      tsimula=difs/ss12
      res[i]=round(tsimula,1)
      stripchart(res, method="stack", add=T, cex=meu.cex,pch=15, col=arco[i])
    }
    tvalor.vf= res<round(tvalor,1) & res>round((-1*tvalor),1)
    ntvalor=sum(tvalor.vf==F)
    res.menor=res[tvalor.vf]
    stripchart(res.menor, method="stack", add=T, cex=meu.cex,pch=15, col="dark green")
    legend((tvalor*0.2),(nsim/22), legend=paste(sum(res>=round(tvalor,1)), "valores >= ", round(tvalor,1)), bty="n", text.col="red3")
    legend((tvalor*-0.9),(nsim/22),legend=paste(sum(res<=round((tvalor*-1),1)), "valores <= -", round(tvalor,1)),bty="n",text.col="red3") 
    abline(v=tvalor, col="red")
    abline(v=(-1*tvalor), col="red")
    cat("\n\tProbabilidade erro I = ", ntvalor/nsim, "\n")
  }
  ############ fim do teste t ###########################
  ret<-list(teste=teste,dados=c(deparse(substitute(dados1)),deparse(substitute(dados2))),diferencas=res)
  return(ret)
  
}
