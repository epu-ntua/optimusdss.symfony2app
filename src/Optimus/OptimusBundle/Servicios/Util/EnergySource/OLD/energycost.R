energycost <- function(data,Energyprices){
  t1=7; t2=8; t3=19; t4=23
  data$weekend <-as.numeric(isWeekend(data$datetime)) 
  data$asdate<-as.timeDate(substring(data$datetime, first=1, last=10))
  data$typeofday<-dayOfWeek(data$asdate)
  data$Energy_Cost<-NA; data$Charge<-NA ; data$Type<-NA
  
  for (sm in 1:length(Energyprices[,1])){
    
    pricebuy1=Energyprices[sm,3]/1000 ; pricebuy2=Energyprices[sm,4]/1000 ; pricebuy3=Energyprices[sm,5]/1000 ; pricesell=0.07
    
    ######################
    for (i in 1:length(data$Load)){
      
      if ( (data$Month[i]==Energyprices[sm,2])&((data$Year[i]==Energyprices[sm,1])) ){
        
        if (data$Grid[i]>=0){
          if (data$typeofday[i]=="Sun"){
            data$Energy_Cost[i] <- pricebuy3*data$Grid[i]*(-1)
            data$Charge[i]<-pricebuy3
            data$Type[i]<-"F3"
          }else if (data$typeofday[i]=="Sat"){
            if ((data$Hour[i]>=7)&(data$Hour[i]<23)){
              data$Energy_Cost[i] <- pricebuy2*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy2
              data$Type[i]<-"F2"
            }else{
              data$Energy_Cost[i] <- pricebuy3*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy3
              data$Type[i]<-"F3"
            }
          }else{
            if ((data$Hour[i]>=8)&(data$Hour[i]<19)){
              data$Energy_Cost[i] <- pricebuy1*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy1
              data$Type[i]<-"F1"
            }else if (  (data$Hour[i]==7)|((data$Hour[i]>=19)&(data$Hour[i]<23))){
              data$Energy_Cost[i] <- pricebuy2*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy2
              data$Type[i]<-"F2"
            }else{
              data$Energy_Cost[i] <- pricebuy3*data$Grid[i]*(-1)
              data$Charge[i]<-pricebuy3
              data$Type[i]<-"F3"
            }
          }
        }else{
          data$Energy_Cost[i] <- pricesell*abs(data$Grid[i])
          data$Charge[i]<-pricesell
          data$Type[i]<-"Sell"
        }
      }
      
      
    }
    
  }
  
  data$weekend <- NULL ; data$asdate <- NULL
  data$typeofday <- NULL 
  
  return(data)
}