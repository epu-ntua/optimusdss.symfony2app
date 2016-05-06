heuristics <- function(dataout,dataoutOR,datain,plott=FALSE){
  
  #   dataout=FinalSuggestions;datain=DataReal ; dataoutOR=dataOUT 
  
  
  ########################## ########################## ########################## 
  ################### Start of stage 1 - Peak shaving ############################ 
  ########################## ########################## ##########################
  
  Pcapacity=80  #estw 141 max kai 30 min
  Ncapacity=30
  startingcapacity<-Ncapacity #Apo pou ksekinhsa (as ksekinhsoume me to elaxisto)
  data2 <- dataout 
  data2$Year <- as.numeric(substring(data2$datetime, first=1, last=4))
  data2$Month <- as.numeric(substring(data2$datetime, first=6, last=7))
  data2$Day <- as.numeric(substring(data2$datetime, first=9, last=10))
  data2$Hour <- as.numeric(substring(data2$datetime, first=12, last=13))
  #data2$RES<-data2$PV-data2$CHP ; data2$PV=data2$CHP<-NULL
  data2$RESoriginal<-dataout$RES
  
  #Charging zones
  data2$ChargingZone<-energycost(dataoutOR,Enprices)$Type
  for (i in 1:length(data2$datetime)){
    if(data2$ChargingZone[i]=="F3"){
      data2$CostH[i]<-0
    }else if ((data2$ChargingZone[i]=="F2")&(prices[prices$Month==data2$Month[i],]$Difference>0)){
      data2$CostH[i]<-1
    }else if ((data2$ChargingZone[i]=="F2")&(prices[prices$Month==data2$Month[i],]$Difference<0)){
      data2$CostH[i]<-2
    }else if ((data2$ChargingZone[i]=="F1")&(prices[prices$Month==data2$Month[i],]$Difference>0)){
      data2$CostH[i]<-2
    }else if ((data2$ChargingZone[i]=="F1")&(prices[prices$Month==data2$Month[i],]$Difference<0)){
      data2$CostH[i]<-1
    }
  }
  
  #Not interested in weather data
  data2$TemperatureC=data2$Humidity<-NULL
  data2$PressurehPa=data2$WindDirectionDegrees<-NULL
  data2$SolarRadiationWatts.m.2=data2$DewpointC=data2$WindSpeedKMH<-NULL
  
  #NA afta pou thelw na ftiaksw egw
  data2$Storage <- 0;    data2$Grid <- 0
  data2$Capacity <-0 ; data2$Capacity[1]=startingcapacity
  data2$DayID<-paste(data2$Day,data2$Month,data2$Year)
  data2$MonthID<-paste(data2$Month,data2$Year)
  #Analytical presentation of Energy Flows from/to PV and Battery
  data2$MaxOfMonth<-0
  data2$FromGridtoDemand<-0
  data2$FromREStoStorage<-0
  data2$FromREStoGrid<-0
  data2$FromREStoDemand<-0
  data2$FromStoragetoDemand<-0
  
  #Max of historical months
  datain$MonthID<-paste(datain$Month,datain$Year)
  nummonths<-length(unique(datain$MonthID)); monthsIDs<-unique(datain$MonthID)
  PeakOfMonths<-matrix(NA,ncol=2,nrow=nummonths)
  for (i in 1:nummonths){
    PeakOfMonths[i,1]<-monthsIDs[i] ; PeakOfMonths[i,2]<-max(datain[datain$MonthID==monthsIDs[i],]$Grid)
  }
  
  #Max of selected months in data2
  for (j in 1:length(data2$datetime)){
    for (i in 1:nummonths){
      if (PeakOfMonths[i,1]==data2$MonthID[j]){
        data2$MaxOfMonth[j]<-as.numeric(PeakOfMonths[i,2])
      }
    }
    if (is.na(data2$MaxOfMonth[j])==TRUE){
      data2$MaxOfMonth[j]<-as.numeric(PeakOfMonths[nummonths,2])
    }
  }
  
  #FLow from PV to Demand
  data2$ForMax<-0
  for (j in 1:length(data2$datetime)){
    if (data2$Load[j]>data2$MaxOfMonth[j]){ #xreiazomai peak shaving
      difference<-data2$Load[j]-data2$MaxOfMonth[j]
      if (data2$RES[j]>0){ #exw paragwgh
        if (data2$RES[j]>difference){
          data2$FromREStoDemand[j]<-difference
          data2$RES[j]<-data2$RES[j]-difference
        }else{
          data2$FromREStoDemand[j]<-data2$RES[j]
          data2$RES[j]<-0
          data2$ForMax[j]<-difference-data2$FromREStoDemand[j]
        }
      }else{
        data2$ForMax[j]<-difference  #Poso mou leipei gia na mhn exw peak
      }
    }
  }
  
  
  #This is for peak shaving!!!!!!!!!!!
  
  
  #Tables of changes - charge & discharge
  numdays<-length(unique(data2$DayID))
  PV_table<-data.frame(matrix(0,ncol=27,nrow=numdays))
  colnames(PV_table)<-c("DayId","MonthID","MaxM","H0","H1","H2","H3","H4","H5","H6","H7","H8","H9","H10",
                        "H11","H12","H13","H14","H15","H16","H17","H18","H19","H20","H21","H22","H23")
  PV_table$DayId<-unique(data2$DayID)
  for (i in 1:numdays){   PV_table$MonthID[i]<-data2[data2$DayID==unique(data2$DayID)[i],]$MonthID[1] }
  
  for (i in 1:nummonths){
    for (j in 1:numdays){
      if (monthsIDs[i]==PV_table$MonthID[j]){ PV_table$MaxM[j]=PeakOfMonths[i,2]  }
    }
  }
  for (j in 1:numdays){
    if (is.na(PV_table$MaxM[j])==TRUE){ PV_table$MaxM[j]=PeakOfMonths[nummonths,2] }
  }
  NeedCharge_table=Charge=Capacity<-PV_table
  
  #Calculate changes to be made per day and available RES production
  for (i in 1:numdays){
    for (j in 0:23){
      if (length(data2[(data2$DayID==unique(data2$DayID)[i])&(data2$Hour==j),]$ForMax)>0){
        NeedCharge_table[i,j+4]<-data2[(data2$DayID==unique(data2$DayID)[i])&(data2$Hour==j),]$ForMax
        PV_table[i,j+4]<-data2[(data2$DayID==unique(data2$DayID)[i])&(data2$Hour==j),]$RES
      }else{
        NeedCharge_table[i,j+4]<-0
        PV_table[i,j+4]<-0
      }
    }
  }
  
  for (i in 1:length(NeedCharge_table[,1])){
    for (j in 1:length(NeedCharge_table[1,])){
      if (NeedCharge_table[i,j]<=0){ NeedCharge_table[i,j]=0 } #Afinei mono ta provlhmatika
      if (NeedCharge_table[i,j]>0){ PV_table[i,j]=0 } #Ekserei oso PV exei faei hdh
      if (PV_table[i,j]<0){ PV_table[i,j]=0 } #Ekserei kai oso einai arnhtiko
    }
  }
  
  
  for (i in 1:length(NeedCharge_table[,1])){
    for (j in length(NeedCharge_table[1,]):4){
      Capacity[i,j]=Ncapacity
    }
  }
  
  ############Day 1#####################
  NeedCharge_table$DayId=NeedCharge_table$MaxM=NeedCharge_table$MonthID<-NULL
  PV_table$DayId=PV_table$MaxM=PV_table$MonthID<-NULL
  Capacity$DayId=Capacity$MaxM=Capacity$MonthID<-NULL
  Charge$DayId=Charge$MaxM=Charge$MonthID<-NULL
  
  #Capacity einai to ti tha exw available sto telos ths wras
  #Thetiko charge einai oti fortizw - arnitiko oti ksefwrtizw
  cancharge<-Pcapacity-Ncapacity  
  for (i in 1:length(NeedCharge_table[,1])){
    
    if (sum(NeedCharge_table[i,1:length(NeedCharge_table[1,])])>0){
      
      for (j in length(NeedCharge_table[1,]):1){
        
        meaningless<-FALSE
        for (check in j:24){  #is there a max infront
          if (Capacity[i,check]==Pcapacity){
            maxid<-check 
            break
          }else{
            maxid<-4000
          }
        }
        for (check in j:24){ #where is the peak?
          if (NeedCharge_table[i,check]>0){
            peakid<-check 
            break
          }else{
            peakid<-0.0000001
          }
        }
        if (peakid>maxid){meaningless=TRUE} 
        
        
        if ((sum(NeedCharge_table[i,j:length(NeedCharge_table[1,])])>0)&(meaningless==FALSE)){ 
          #xreiazetai na xrhsimopihsw mpataria meta apo afto
          if (PV_table[i,j]>0){ #exw diathesimh paragwgh thn prohgoumenh wra
            howmuchineed<-sum(NeedCharge_table[i,j:length(NeedCharge_table[1,])])
            
            if (PV_table[i,j]>=howmuchineed){
              applied<-howmuchineed  #Ti tha mporousa na traviksw apo afta pou exw
            }else{
              applied<-PV_table[i,j]
            }
            
            cancharge<-Pcapacity-Capacity[i,j+1]
            if (applied<=cancharge){
              Charge[i,j]<-applied
              PV_table[i,j]<-PV_table[i,j]-Charge[i,j]
              Capacity[i,j]<-Capacity[i,j]+Charge[i,j]
            }else{
              Charge[i,j]<-cancharge
              PV_table[i,j]<-PV_table[i,j]-Charge[i,j]
              Capacity[i,j]<-Capacity[i,j]+Charge[i,j]
            }
            for (n in (j+1):length(NeedCharge_table[1,])){ Capacity[i,n]<-Capacity[i,n-1]+Charge[i,n] }
            
            for (k in (j+1):length(NeedCharge_table[1,])){ 
              if (NeedCharge_table[i,k]>0){
                if ((Capacity[i,k]-Ncapacity)>NeedCharge_table[i,k]){
                  Charge[i,k]<-(-1)*NeedCharge_table[i,k]+Charge[i,k]
                  Capacity[i,k]<-Capacity[i,k]-NeedCharge_table[i,k]
                  NeedCharge_table[i,k]<-0
                  for (n in (k+1):length(NeedCharge_table[1,])){ Capacity[i,n]<-Capacity[i,n-1]+Charge[i,n] }
                }else if (Capacity[i,k]>Ncapacity){
                  Charge[i,k]<-(-1)*(Capacity[i,k]-Ncapacity)+Charge[i,k]
                  Capacity[i,k]<-Ncapacity
                  NeedCharge_table[i,k]<-NeedCharge_table[i,k]+Charge[i,k]
                  for (n in (k+1):length(NeedCharge_table[1,])){ Capacity[i,n]<-Capacity[i,n-1]+Charge[i,n] }
                }
              }
            }
            
          }
          
          
        }
        
        
      }
      
    }
    
  }
  
  
  ####Back to data2
  Charge$Day=Capacity$Day=PV_table$Day<-unique(data2$DayID)
  for (nd in 1:length(unique(data2$DayID))){
    search<-unique(data2$DayID)[nd]
    for (i in 1:length(data2$datetime)){
      if (data2$DayID[i]==search){
        for (j in 0:23){
          if (j==data2$Hour[i]){
            data2$Capacity[i]=Capacity[nd,j+1]
            if (Charge[nd,j+1]>0){
              data2$FromREStoStorage[i]=Charge[nd,j+1]
            }else if (Charge[nd,j+1]<0){
              data2$FromStoragetoDemand[i]=(-1)*Charge[nd,j+1]
            }
            data2$RES[i]=PV_table[nd,j+1]
          }
        }
      }
    }
  }
  data2$ForMax=data2$DayID=data2$MaxOfMonth=data2$MonthID<-NULL  
  Capacity=NeedCharge_table=PV_table=Charge<-NULL ; data2$Keepasitis<-0
  for (i in 1:length(data2$Year)){
    if (data2$Capacity[i]>30){
      data2$Keepasitis[i]<-1
    }
  }
  data2$LoadOr<-data2$Load
  data2$Load<-data2$LoadOr-data2$FromGridtoDemand-data2$FromREStoDemand-data2$FromStoragetoDemand
  #Stage1 - Peak shaving
  #plot(data2$Load,type="l")
  #lines(data2$Grid,type="l",col="red")
  ########################## ########################## ########################## 
  ################### End of stage 1 - Peak shaving ############################ 
  ########################## ########################## ##########################
  
  
  ########################## ########################## ########################## 
  ################### Start of stage 2 - Charging zones ############################ 
  ########################## ########################## ##########################
  
  new_data2<-NULL ; DaysIDs<-unique(data2$Day)
  for (nod in 1:numdays){
    
    whichday<-DaysIDs[nod]
    temp<-data2[data2$Day==whichday,]
    temp$LoadOr<-temp$Load-temp$FromStoragetoDemand-temp$FromREStoDemand-temp$FromGridtoDemand
    if (length(temp$Grid)==1){
      new_data2<-rbind(new_data2,temp)
      for (kkk in 2:length(new_data2$Capacity)){
        if (new_data2$Capacity[kkk]<new_data2$Capacity[kkk-1]){
          new_data2$FromStoragetoDemand[kkk]<-new_data2$Capacity[kkk-1]-new_data2$Capacity[kkk]
        }
      }
    }else{
      
      for (i in 1:(length(temp$Hour)-1)){
        
        price<-temp$CostH[i]
        if (temp$Keepasitis[i]==1){
          atleastkeep<-temp$Capacity[i]
        }else{
          atleastkeep<-30
        }
        
        if (max(temp$CostH[(i+1):length(temp$Year)],na.rm=TRUE)<=price){  #is the most expensive charging zone
          
          if (temp$RES[i]>=temp$Load[i]){ #PV Production greater or equal with the Energy Demand
            temp$FromREStoDemand[i]<-temp$RES[i]-temp$Load[i]+temp$FromREStoDemand[i]
            temp$RES[i]<-temp$RES[i]-temp$Load[i]
            if (temp$RES[i]>0){ #there is a surplus of RES?
              if (temp$Capacity[i]==Pcapacity){  #maximum capacity
                temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
                temp$RES[i]<-0
              }else{  #can charge
                xwraeisempataria<-Pcapacity-temp$Capacity[i]
                if (temp$RES[i]>xwraeisempataria){ #more than can charge
                  temp$FromREStoStorage[i]<-xwraeisempataria+temp$FromREStoStorage[i]
                  temp$FromREStoGrid[i]<-temp$RES[i]-xwraeisempataria+temp$FromREStoGrid[i]
                  temp$RES[i]<-0
                  temp$Capacity[i]<-Pcapacity
                }else{ #less than can charge
                  temp$FromREStoStorage[i]<-temp$RES[i]+temp$FromREStoStorage[i]
                  temp$RES[i]<-0
                  temp$Capacity[i]<-temp$Capacity[i]+temp$FromREStoStorage[i]
                }
              }
            }
            temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
          }else{ #PV Production less than the Energy Demand
            temp$FromREStoDemand[i]<-temp$RES[i]+temp$FromREStoDemand[i]
            temp$RES[i]<-0
            temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
            if (temp$Capacity[i]>atleastkeep){
              howmuchineed<-temp$Load[i]
              diathesimoC<-temp$Capacity[i]-atleastkeep
              if (diathesimoC>howmuchineed){
                temp$FromStoragetoDemand[i]<-howmuchineed+temp$FromStoragetoDemand[i]
                temp$Capacity[i]<-temp$Capacity[i]-howmuchineed
              }
              temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
            }
          }#the rest of the charging zones
          for (n in (i+1):length(temp$Year)){ 
            temp$Capacity[n]<-temp$Capacity[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
          }
          
        }else if (max(temp$CostH[(i+1):length(temp$Year)],na.rm = TRUE)>price){
          
          if (temp$Capacity[i]==Pcapacity){
            if (temp$RES[i]>temp$Load[i]){
              temp$FromREStoDemand[i]<-temp$Load[i]+temp$FromREStoDemand[i]
              temp$RES[i]<-temp$RES[i]-temp$Load[i]
              temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
              temp$RES[i]<-0
              temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
            }
          }else{
            if (temp$RES[i]>0){
              
              #meletaw poso tha valw xwris na to parakanw
              mexriposo<-Pcapacity-temp$Capacity[i]
              if (temp$RES[i]>=mexriposo){
                vazw<-mexriposo+temp$FromREStoStorage[i]
                newcap<-temp$Capacity[i]+mexriposo
              }else{
                vazw<-temp$RES[i]+temp$FromREStoStorage[i]
                newcap<-temp$Capacity[i]+temp$RES[i]
              }
              cap<-temp$Capacity; cap[i]<-newcap
              for (n in (i+1):length(temp$Year)){ 
                cap[n]<-cap[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
              }
              if (max(temp$Capacity[(i+1):length(temp$Year)]+vazw)>Pcapacity){
                mexriposo<-0
              }
              
              if (temp$RES[i]>=mexriposo){
                temp$FromREStoStorage[i]<-mexriposo+temp$FromREStoStorage[i]
                temp$Capacity[i]<-temp$Capacity[i]+mexriposo
                temp$RES[i]<-temp$RES[i]-mexriposo
                for (n in (i+1):length(temp$Year)){ 
                  temp$Capacity[n]<-temp$Capacity[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
                }
              }else{
                temp$FromREStoStorage[i]<-temp$RES[i]+temp$FromREStoStorage[i]
                temp$Capacity[i]<-temp$Capacity[i]+temp$RES[i]
                temp$RES[i]<-0
                for (n in (i+1):length(temp$Year)){ 
                  temp$Capacity[n]<-temp$Capacity[n-1]-temp$FromStoragetoDemand[n]+temp$FromREStoStorage[n] 
                }
              }
              if (temp$RES[i]>=temp$Load[i]){
                temp$FromREStoDemand[i]<-temp$Load[i]+temp$FromREStoDemand[i]
                temp$RES[i]<-temp$RES[i]-temp$Load[i]
                temp$Load[i]<-0
              }else{
                temp$FromREStoDemand[i]<-temp$RES[i]+temp$FromREStoDemand[i]
                temp$RES[i]<-0
                temp$Load[i]<-temp$LoadOr[i]-temp$FromGridtoDemand[i]-temp$FromREStoDemand[i]-temp$FromStoragetoDemand[i]
              }
              if (temp$RES[i]>0){
                temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
                temp$RES[i]<-0
              }
            }
            
          }
        }
        
      }
      
      for (i in 1:length(temp$Hour)){
        if (temp$RES[i]>0){
          if (temp$Load[i]>0){
            if (temp$RES[i]>temp$Load[i]){
              temp$FromREStoDemand[i]<-temp$FromREStoDemand[i]+temp$Load
              temp$RES[i]<-temp$RES[i]-temp$Load
              temp$Load[i]<-0
              temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
              temp$RES[i]<-0
            }else{
              temp$FromREStoDemand[i]<-temp$FromREStoDemand[i]+temp$RES[i]
              temp$Load[i]<-temp$Load[i]-temp$RES[i]
              temp$RES[i]<-0
            }
          }else{
            temp$FromREStoGrid[i]<-temp$RES[i]+temp$FromREStoGrid[i]
            temp$RES[i]<-0
          }
        }
      }
      
      new_data2<-rbind(new_data2,temp)
      for (kkk in 2:length(new_data2$Capacity)){
        if (new_data2$Capacity[kkk]<new_data2$Capacity[kkk-1]){
          new_data2$FromStoragetoDemand[kkk]<-new_data2$Capacity[kkk-1]-new_data2$Capacity[kkk]
        }
      }
    }
  }
  
  new_data2$Keepasitis=new_data2$CostH=new_data2$Capacity=new_data2$ChargingZone<-NULL
  new_data2$FromGridtoDemand<-new_data2$Load
  new_data2$RES<-new_data2$RESoriginal  ; new_data2$RESoriginal<-NULL
  new_data2$Storage<-new_data2$FromREStoStorage-new_data2$FromStoragetoDemand
  new_data2$Load<-new_data2$LoadOr ; new_data2$dataLoadOr<-NULL
  new_data2$Grid<-new_data2$Load-new_data2$FromStoragetoDemand-new_data2$FromREStoDemand
  
  if (plott==TRUE){
    MAX<-max(c(new_data2$Load,new_data2$Grid,new_data2$RES,new_data2$Storage))+20  
    MIN<-min(c(new_data2$Load,new_data2$Grid,new_data2$RES,new_data2$Storage))-20  
    plot(new_data2$Load,type="l",ylim=c(MIN,MAX))
    lines(new_data2$Grid,type="l",col="red")
    lines(new_data2$RES,type="l",col="green")
    lines(new_data2$Storage,type="l",col="blue")
  }
  
  ########################## ########################## ########################## 
  ################### End of stage 2 - Charging zones ############################ 
  ########################## ########################## ##########################
  data2<-new_data2
  data2$FromGridtoDemand=data2$FromREStoStorage=data2$FromREStoGrid<-NULL
  data2$FromREStoDemand=data2$FromStoragetoDemand=data2$LoadOr<-NULL
  
  
  return(data2)
}
