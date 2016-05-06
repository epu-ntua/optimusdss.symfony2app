Kostologish <- function(data1,data2,Enprices){
  
  for (sen in 1:2){
    
    if (sen==1){
      datatemp<-data1
    }else{
      datatemp<-data2
    }
    
    CostCreated<-energycost(datatemp,Enprices)
    test<-CostCreated
    
    howmanymonths<-1 ; test$MY<-test$Year+test$Month/100
    for (i in 1:(length(test$Day)-1)){
      if (test$MY[i]!=test$MY[i+1]) { howmanymonths=howmanymonths+1 }
    }
    
    CompareCosts<-data.frame(matrix(NA,nrow=howmanymonths,ncol=13))
    colnames(CompareCosts)<-c("Year","Month","Peak","PV","CHP","Load","Grid","Storage","F1","F2","F3","TotalCost","FinalCost")
    
    k=1 ; CompareCosts$Year[k]<-test$Year[1] ; CompareCosts$Month[k]<-test$Month[1]
    for (i in 1:(length(test$Day)-1)){
      if (test$MY[i]!=test$MY[i+1]) {
        CompareCosts$Year[k+1]<-test$Year[i+1]
        CompareCosts$Month[k+1]<-test$Month[i+1]
        k=k+1
      }
    }
    
    for (i in 1:length(CompareCosts$Year)){
      sumtemp<-test[(test$Year==CompareCosts$Year[i])&(test$Month==CompareCosts$Month[i]),]
      CompareCosts$PV[i]<-sum(sumtemp$PV) ;    CompareCosts$CHP[i]<-sum(sumtemp$CHP)
      CompareCosts$Load[i]<-sum(sumtemp$Load);     CompareCosts$Grid[i]<-sum(sumtemp$Grid)
      CompareCosts$Storage[i]<-sum(sumtemp$Storage)
      CompareCosts$F1[i]<-sum(sumtemp[sumtemp$Type=="F1",]$Energy_Cost)
      CompareCosts$F2[i]<-sum(sumtemp[sumtemp$Type=="F2",]$Energy_Cost)
      CompareCosts$F3[i]<-sum(sumtemp[sumtemp$Type=="F3",]$Energy_Cost)
      CompareCosts$TotalCost[i]<-sum(CompareCosts$F1[i],CompareCosts$F2[i],CompareCosts$F3[i])
      CompareCosts$Peak[i]<-max(sumtemp$Grid)
      CompareCosts$FinalCost[i]=round((((0.00988004+0.091172)*CompareCosts$Grid[i])+(0.0151*0.0466174797*CompareCosts$Grid[i])+
                                         (2.743633*CompareCosts$Peak[i])+91.647984+(1.04*CompareCosts$TotalCost[i]))*1.22,2)
    }
    
    CompareCostsCreated<-CompareCosts
    
    if (sen==1){
      costsenario1=sum(CompareCostsCreated$FinalCost) 
    }else{
      costsenario2=sum(CompareCostsCreated$FinalCost) 
    }
    
    CompareCosts=CompareCostsCreated<-NULL
    
    
  }
  
  toreturn<-c(costsenario1,costsenario2)
  return(toreturn)
  
}