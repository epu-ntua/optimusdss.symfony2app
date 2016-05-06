runningpeaks <- function(data,prices){
  
  #data<-simulated_data_OUT
  
  #Pou eimai panw kai katw apo to peak tou load
  data$canremove<-0
  for (i in 1:length(data$OH)){
    if( data$OH[i]==1 ){ 
      if (data$Load[i]-wherepeak>=0){
        data$canremove[i]=abs(data$Load[i]-wherepeak)
      }
    }
  }
  
  #poses meres exw?
  energyControl<-data.frame(matrix(NA,ncol=2,nrow=20)) ; colnames(energyControl)<-c("Day","CanRemove")
  data$Year <- as.numeric(substring(data$datetime, first=1, last=4))
  data$Month <- as.numeric(substring(data$datetime, first=6, last=7))
  data$Day <- as.numeric(substring(data$datetime, first=9, last=10))
  data$Hour <- as.numeric(substring(data$datetime, first=12, last=13))
  howmanydays<-1 ; energyControl$Day[1]<-data$Day[1]; k=1
  for (i in 1:(length(data$Day)-1)){
    if (data$Day[i]!=data$Day[i+1]) {
      howmanydays=howmanydays+1
      energyControl$Day[k+1]<-data$Day[i+1]
      k=k+1
    }
  }
  energyControl<-energyControl[1: howmanydays,]
  
  #Isozygio kathe hmera se pleonasma
  for (i in 1:howmanydays){
    temp<-data[data$Day==energyControl$Day[i],]
    energyControl$CanRemove[i]<-sum(temp$canremove)
  }
  
  #Twra tha meirasw to fortio se epipedo meras (meiwnw shsthmatika to peak tou grid)
  new_data<-NULL
  for (n in 1:howmanydays){
    
    temp<-data[data$Day==energyControl$Day[n],] ; finish=1
    
    ypoloipo<-energyControl$CanRemove[n]
    minmeras=min(temp$Load)
    
    difference<-prices[prices$Month==mean(temp$Month),]$Difference ; symferei=0
    if (difference>=0){ symferei=1} #to F1 einai pio akrivo
    temp$symferei<-0
    if (symferei==1){
      for (ii in 1:length(temp$Hour)){
        if ((temp$Hour[ii]==7)|(temp$Hour[ii]==19)|(temp$Hour[ii]==20)|(temp$Hour[ii]==21)|(temp$Hour[ii]==22)){
          temp$symferei[ii]=1
        }
      }
    }else{
      for (ii in 1:length(temp$Hour)){
        if ((temp$Hour[ii]!=7)|(temp$Hour[ii]!=19)|(temp$Hour[ii]!=20)|(temp$Hour[ii]!=21)|(temp$Hour[ii]!=22)){
          temp$symferei[ii]=1
        }
      }
    }
    
    if ((ypoloipo>0)&(length(temp$Load)>1)) {finish=0}
    reps<-0
    
    while ((finish==0)&(ypoloipo>=0)){
      
      peaktemp<-max(data[(data$Month=temp$Month[1])&(data$Year=temp$Year[1]),]$Grid)
      peakdata<-max(dataRoll[(dataRoll$Month=temp$Month[1])&(dataRoll$Year=temp$Year[1]),]$Grid)
      peakmonth<-max(peaktemp,peakdata,na.rm = TRUE)*0.7
      meiwsh<-0.05
      
      
      ypoepeksergasia<-temp[(temp$OH==1)&(temp$Load>=minmeras),] ; xwrisepeksergasia<-temp[(temp$OH==0)|(temp$Load<minmeras),]
      ypoepeksergasia$cost<-NA
      for (ki in 1:length(ypoepeksergasia$Grid)){
        if (ypoepeksergasia$symferei[ki]==1){
          ypoepeksergasia$cost[ki]<-ypoepeksergasia$Grid[ki]
        }else{
          ypoepeksergasia$cost[ki]<-ypoepeksergasia$Grid[ki]*(1+abs(difference))
        }
      }
      ypoepeksergasia$Advantage<-1 #to akrivo xwris peak
      for (k in 1:length(ypoepeksergasia$cost)){
        if (ypoepeksergasia$Grid[k]+meiwsh+10>=peakmonth){
          ypoepeksergasia$Advantage[k]=0 #to peak
        }else if (ypoepeksergasia$symferei[k]==1){
          ypoepeksergasia$Advantage[k]=2 #to ftino
        }
      }
      lngth<-length(ypoepeksergasia$Grid)
      
      #######################   Senaria    ########################
      if ( (length(ypoepeksergasia[ypoepeksergasia$Advantage==1,]$Grid)==lngth) | (length(ypoepeksergasia[ypoepeksergasia$Advantage==2,]$Grid)==lngth) ){
        #AN OLA 2 H OLA 1 DEN KANW TIPOTA
        finish=1
      }else if (length(ypoepeksergasia[ypoepeksergasia$Advantage==0,]$Grid)==lngth) {
        #AN OLA PEAK TA MOIRAZW
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if (ypoepeksergasia$Grid[i]>max){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if (ypoepeksergasia$Grid[i]<min){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }else if ((length(ypoepeksergasia[ypoepeksergasia$Advantage==2,]$Grid)>0)&(length(ypoepeksergasia[ypoepeksergasia$Advantage==0,]$Grid)>0)){
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==0)&(ypoepeksergasia$Grid[i]>max)){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==2)&(ypoepeksergasia$Grid[i]<min)){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }else if ((length(ypoepeksergasia[ypoepeksergasia$Advantage==2,]$Grid)>0)&(length(ypoepeksergasia[ypoepeksergasia$Advantage==1,]$Grid)>0)){
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==1)&(ypoepeksergasia$Grid[i]>max)){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==2)&(ypoepeksergasia$Grid[i]<min)){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }else {
        max=0 ; theshmax<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]==0)&(ypoepeksergasia$Grid[i]>max)){
            max<-ypoepeksergasia$Grid[i] ;        theshmax<-i
          }
        }
        min=100*exp(100) ; theshmin<-NA
        for (i in 1:length(ypoepeksergasia$Advantage)){
          if ((ypoepeksergasia$Advantage[i]!=0)&(ypoepeksergasia$Grid[i]<min)){
            min<-ypoepeksergasia$Grid[i];       theshmin<-i
          }
        }
        ypoepeksergasia$Grid[theshmin]=min+meiwsh
        ypoepeksergasia$Grid[theshmax]=max-meiwsh
        ypoloipo<-ypoloipo-meiwsh
        ypoepeksergasia$cost=ypoepeksergasia$Advantage<-NULL
        temp<-rbind(xwrisepeksergasia,ypoepeksergasia)
        temp <- temp[order(temp$Hour),]
        temp$Load <- temp$PV-temp$CHP-temp$Storage+temp$Grid
        reps<-reps+1
      }
      
    }#END OF CHANGES
    
    #     minlim=min(temp$Load,data[data$Day==energyControl$Day[n],]$Load)
    #     maxlim=max(temp$Load,data[data$Day==energyControl$Day[n],]$Load)
    #     ylimm=c(minlim,maxlim)
    #     plot(temp$Load,type="l",col="red",ylim=ylimm)
    #     lines(data[data$Day==energyControl$Day[n],]$Load,type="l")
    
    new_data[length(new_data)+1] = list(temp)
    
  }#end of day
  
  dataN<-new_data[[length(new_data)]]
  for (i in 1:(length(new_data)-1)){
    dataN<-rbind(dataN,new_data[[i]])
  }
  dataN <- dataN[order(dataN$Year, dataN$Month, dataN$Day, dataN$Hour),]
  
  dataN$canadd=dataN$canremove<-NULL
  
  #   minlim=min(data$Grid,dataN$Grid)
  #   maxlim=max(data$Grid,dataN$Grid)
  #   ylimm=c(minlim,maxlim)
  #   plot(data$Grid,type="l",ylim=ylimm)
  #   lines(dataN$Grid,type="l",col="red")
  
  return (dataN)
}