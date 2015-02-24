### eq testa se dois objetos sao identicos
### a menos de erros numericos e nomes


### objetos para os exercicios 206
list.dat<-list()
list.dat.names<-list()
list.mle<-list()
list.mle.gaus<-list()
list.func<-list()
list.func.mle<-list()
mle.corr<-list()
func.corr<-list()
mle.fim<-list()
func.fim<-list()
list.loglik<-list()
meuenv<-environment()


select.stuff<-function()
{
dat<-list()
datnam<-list()
funcs<-list()
mles<-list()
funcmle<-list()
mlegaus<-list()
mlecorr<-list()
funccorr<-list()
funcfim<-list()
mlefim<-list()
listalik<-list()
tudo<-as.list(meuenv)
j<-0
w<-0
k<-0
l<-0
q<-0
t<-0
for(i in 1:length(tudo))
  {
   if(sum(grep("60.61",deparse(tudo[[i]])),grep("16.50",deparse(tudo[[i]])),grep("11.74",deparse(tudo[[i]])),grep("11.35",deparse(tudo[[i]])),grep("48.52",deparse(tudo[[i]])),grep("49.95",deparse(tudo[[i]])))>0)
     {
     if(sum(grep("function",deparse(tudo[[i]])))==0)
        {if(sum(grep("list.dat",deparse(names(tudo[i]))))==0) 
          {if(names(tudo[i])!="superesa")
             {
             j<-j+1
             dat[[j]]<-tudo[[i]]
             datnam[[j]]<-names(tudo[i])
             }
          }
        }
      
     }
    if(class(tudo[[i]])=="function")
      {
       k<-k+1
       funcs[[k]]<-tudo[[i]]
       }

    if((class(tudo[[i]])=="mle2")|("mle"==class(tudo[[i]])))
      {
       l<-l+1
       mles[[l]]<-tudo[[i]]
        if(sum(grep("norm",deparse(tudo[[i]]@minuslogl)))>0)
           {
            q<-q+1
            mlegaus[[q]]<-tudo[[i]]
            }
       }

   }

assign("list.dat",dat,envir=meuenv)
assign("list.dat.names",datnam,envir=meuenv)
assign("list.func",funcs,envir=meuenv)
assign("list.mle",mles,envir=meuenv)
assign("list.mle.gaus",mlegaus,envir=meuenv)

if(length(mlegaus)==0){return(0)}

for(i in 1:length(mlegaus))
   {for(j in 1:length(funcs))
       {if(sum(deparse(mlegaus[[i]]@minuslogl)==deparse(funcs[[j]]))==length(deparse(mlegaus[[i]]@minuslogl)))
          {funcmle[[i]]<-funcs[[j]]}
        }
    }
assign("list.func.mle",funcmle,envir=meuenv)


for(i in 1:length(dat))
    {x<-deparse(dat[[i]])
     for (j in 1:length(unique(unlist(superesa$tronco))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(superesa$tronco))[j]),"([,\\)])",sep=""),"\\1mean(superesa$tronco)\\2",x)
         }
     for (j in 1:length(unique(unlist(superesa$ht))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(superesa$ht))[j]),"([,\\)])",sep=""),"\\1mean(superesa$ht)\\2",x)
         }
     for (j in 1:length(unique(unlist(superesa$dap))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(superesa$dap))[j]),"([,\\)])",sep=""),"\\1mean(superesa$dap)\\2",x)
         }
     for (j in 1:length(unique(unlist(superesa$sobra))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(superesa$sobra))[j]),"([,\\)])",sep=""),"\\1mean(superesa$sobra)\\2",x)
         }
     for (j in 1:length(unique(unlist(superesa$folha))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(superesa$folha))[j]),"([,\\)])",sep=""),"\\1mean(superesa$folha)\\2",x)
         }
     for (j in 1:length(unique(unlist(superesa$total))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(superesa$total))[j]),"([,\\)])",sep=""),"\\1mean(superesa$total)\\2",x)
         }
     x<-eval(parse(text=x))
     assign(datnam[[i]],x,envir=meuenv)
     }


for(i in 1:length(mlegaus))
    {coefs<-coef(mlegaus[[i]])
     names(coefs)<-NULL
     res1<-eval(parse(text=paste("nova.func(funcmle[[i]],\"c(30,10,100,50)\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")")))
     res2<-sum(eval(parse(text=paste("nova.func(funcmle[[i]],\"c(10)\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")"))),eval(parse(text=paste("nova.func(funcmle[[i]],\"c(100)\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")"))),eval(parse(text=paste("nova.func(funcmle[[i]],\"c(30,50)\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")"))))
     if(round(res1)==round(res2))
        {y<-function(x)
            {
            plc<--eval(parse(text=paste("nova.func(funcmle[[i]],\"",x,"\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")")))
            exp(plc)
            }
            if(round(integrate(Vectorize(y),-1000,1000)[[1]],3)==1)
               {
                t<-t+1
                mlecorr[[t]]<-mlegaus[[i]]
                funccorr[[t]]<-funcmle[[i]]
                }
           }
    }
assign("mle.corr",mlecorr,envir=meuenv)
assign("func.corr",funccorr,envir=meuenv)

for(i in 1:length(dat))
    {x<-deparse(dat[[i]])
     for (j in 1:length(unique(unlist(superesa$tronco))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(superesa$tronco))[j]),"([,\\)])",sep=""),"\\1100\\2",x)
         }
     x<-eval(parse(text=x))
     assign(datnam[[i]],x,envir=meuenv)
     }

res1<-list()
res2<-list()
res3<-list()
for(i in 1:length(mlecorr))
    {coefs<-coef(mlecorr[[i]])
     names(coefs)<-NULL
     res1[[i]]<-eval(parse(text=paste("nova.func(funccorr[[i]],\"c(30,10,100,50)\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")")))
     res3[[i]]<-eval(parse(text=paste("nova.func(funccorr[[i]],\"superesa$tronco\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")")))
    }




for(i in 1:length(dat))
    {
     assign(datnam[[i]],dat[[i]],envir=meuenv)
     }

for(i in 1:length(mlecorr))
    {coefs<-coef(mlecorr[[i]])
     names(coefs)<-NULL
     res2[[i]]<-eval(parse(text=paste("nova.func(funccorr[[i]],\"c(30,10,100,50)\")(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")")))
     if(round(res1[[i]])==round(res2[[i]])) 
      {if(round(res3[[i]])==round(-logLik(mlecorr[[i]])))
        {w<-w+1
         mlefim[[w]]<-mlecorr[[i]]
         funcfim[[w]]<-funccorr[[i]]
         listalik[[w]]<-logLik(mlecorr[[i]])[[1]]
        }
      }

    }
assign("mle.fim",mlefim,envir=meuenv)
assign("func.fim",funcfim,envir=meuenv)
assign("list.loglik",listalik,envir=meuenv)


}




nova.func<-function(funcao,substituto)
{
x<-deparse(funcao)


y<-x
while(sum(grep("norm[[:space:]]*\\([^\\)]*\\(",x))>0)
{
s<-x
m <- gregexpr("\\([^\\)\\(]*\\)", x)
for(j in 1:length(s))
{
    s[j]<-sub("\\([^\\)\\(]*\\)",paste(rep(1,max(attributes(m[[j]])[[1]][1],0)),collapse=""),s[j])
}
x<-s
}

m<-regexpr("norm[[:space:]]*\\([^\\)]*)",x)
substring1<-substring(y[which(m>0)],1,m[which(m>0)]+attributes(m)[[1]][which(m>0)]-1)
substring2<-substring(y[which(m>0)],m[which(m>0)]+attributes(m)[[1]][which(m>0)],nchar(x[which(m>0)]))
y[which(m>0)]<-paste(substring1,"[1:length(",substituto,")]",substring2,sep="")

x<-y

y<-x
while(sum(grep("norm[[:space:]]*\\([^\\)]*\\(",x))>0)
{
s<-x
m <- gregexpr("\\([^\\)\\(]*\\)", x)
for(j in 1:length(s))
{
    s[j]<-sub("\\([^\\)\\(]*\\)",paste(rep(1,max(attributes(m[[j]])[[1]][1],0)),collapse=""),s[j])
}
x<-s
}


if(sum(grep("norm[[:space:]]*\\([^\\)]*x[[:space:]]*=",x))>0)
  {
   m<-regexpr("(norm[[:space:]]*\\([^\\)]*x[[:space:]]*=)",x)
   m2<-regexpr("(norm[[:space:]]*\\([^\\)]*x[[:space:]]*=)([^,\\)]*)([,\\)])",x)
   substring1<-substring(y[which(m>0)],1,m[which(m>0)]+attributes(m)[[1]][which(m>0)]-1)
   substring2<-substring(y[which(m>0)],m2[which(m2>0)]+attributes(m2)[[1]][which(m2>0)]-1,nchar(x[which(m>0)]))
   y[which(m>0)]<-paste(substring1,substituto,substring2,sep="")
  }
else
  {if(sum(grep("norm[[:space:]]*\\([^,=]*,",x))>0)
     {
      m<-regexpr("norm[[:space:]]*\\(",x)
      m2<-regexpr("norm[[:space:]]*\\([^,]*,",x)
      substring1<-substring(y[which(m>0)],1,m[which(m>0)]+attributes(m)[[1]][which(m>0)]-1)
      substring2<-substring(y[which(m>0)],m2[which(m2>0)]+attributes(m2)[[1]][which(m2>0)]-1,nchar(x[which(m>0)]))
      y[which(m>0)]<-paste(substring1,substituto,substring2,sep="")
     }
    else
      {
      m<-regexpr("norm[[:space:]]*\\(([^,=]*=[^,=]*,)*",x)
      m2<-regexpr("norm[[:space:]]*\\(([^,=]*=[^,=]*,)*[^,=\\)]*[,\\)]",x)
      substring1<-substring(y[which(m>0)],1,m[which(m>0)]+attributes(m)[[1]][which(m>0)]-1)
      substring2<-substring(y[which(m>0)],m2[which(m2>0)]+attributes(m2)[[1]][which(m2>0)]-1,nchar(x[which(m>0)]))
      y[which(m>0)]<-paste(substring1,substituto,substring2,sep="")
     }
   }

eval(parse(text=y))

}


select.stuff2<-function(mle.fim,data){
listvar<-list()
k<-0
for(i in 1:length(mle.fim))
{

var1<-0
var2<-0


x<-deparse(mle.fim[[i]]@minuslogl)


y<-x
while(sum(grep("norm[[:space:]]*\\([^\\)]*\\(",x))>0)
{
s<-x
m <- gregexpr("\\([^\\)\\(]*\\)", x)
for(j in 1:length(s))
{
    s[j]<-sub("\\([^\\)\\(]*\\)",paste(rep(1,max(attributes(m[[j]])[[1]][1],0)),collapse=""),s[j])
}
x<-s
}

if(sum(grep("norm[[:space:]]*\\([^\\)]*sd(log)*[[:space:]]*=",x))>0)
  {
   m<-regexpr("(norm[[:space:]]*\\([^\\)]*sd(log)*[[:space:]]*=)",x)
   m2<-regexpr("(norm[[:space:]]*\\([^\\)]*sd(log)*[[:space:]]*=)([^,\\)]*)([,\\)])",x)
   substring1<-substring(y[which(m>0)],m[which(m>0)]+attributes(m)[[1]][which(m>0)],m2[which(m2>0)]+attributes(m2)[[1]][which(m2>0)]-2)
  }
if(sum(grep("norm[[:space:]]*\\([^\\)]*sd(log)*[[:space:]]*=",x))<=0)
  {
  n<-sum(c(grepl("norm[[:space:]]*\\([^\\)]*mean(log)*[[:space:]]*=",x),grepl("norm[[:space:]]*\\([^\\)]*x[[:space:]]*=",x)))
  m<-regexpr(paste(collapse="",sep="","norm[[:space:]]*\\(([^,=]*=[^,]*,)*",paste(collapse="",sep="",rep("([^,=]*=[^,]*,)*[^,=]*,",2-n))),x)
  m2<-regexpr(paste(collapse="",sep="","norm[[:space:]]*\\(([^,=]*=[^,]*,)*",paste(collapse="",sep="",rep("([^,=]*=[^,]*,)*[^,=]*,",2-n)),"[^,\\)]*[,\\)]"),x)
  substring1<-substring(y[which(m>0)],m[which(m>0)]+attributes(m)[[1]][which(m>0)],m2[which(m2>0)]+attributes(m2)[[1]][which(m2>0)]-2)
  }

if(sum(grep("\\{",y))>0)
{
y[length(y)]<-substring1
y[length(y)+1]<-"}"
y<-eval(parse(text=y))
}
else
{y[3]<-y[2]
y[2]<-"{"
y[4]<-substring1
y[5]<-"}"
y<-eval(parse(text=y))
}
for(z in 1:length(list.dat))
    {x<-deparse(list.dat[[z]])
     for (j in 1:length(unique(unlist(data))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(data))[j]),"([,\\)])",sep=""),"\\1mean(data)*0.9\\2",x)
         }
     x<-eval(parse(text=x))
     assign(list.dat.names[[z]],x,envir=meuenv)
     }

coefs<-coef(mle.fim[[i]])
names(coefs)<-NULL
var1[i]<-eval(parse(text=paste("y(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")[1]",collapse="")))  


for(z in 1:length(list.dat))
    {x<-deparse(list.dat[[z]])
     for (j in 1:length(unique(unlist(data))))
         {
          x<-gsub(paste("([[:space:]\\(])",deparse(unique(unlist(data))[j]),"([,\\)])",sep=""),"\\1mean(data)\\2",x)
         }
     x<-eval(parse(text=x))
     assign(list.dat.names[[z]],x,envir=meuenv)
     }

var2[i]<-eval(parse(text=paste("y(",gsub("[c\\)\\(]","",paste(deparse(coefs),collapse="")),")[1]",collapse="")))  

for(z in 1:length(list.dat))
    {
     assign(list.dat.names[[z]],list.dat[[z]],envir=meuenv)
     }

    if((round(var2[i],2)==round(var1[i],2)))
       {}
    else
       {k<-k+1
       listvar[[k]]<-mle.fim[[i]]
       }

}
listvar
}

