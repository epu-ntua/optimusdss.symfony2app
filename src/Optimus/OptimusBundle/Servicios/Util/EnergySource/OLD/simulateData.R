simulateData <- function(data){
  
  startingcapacity<-0 #Apo pou ksekinhsa (as ksekinhsoume me 10)
  Pcapacity=50  #estw 50max kai 4 min
  Ncapacity=4
  data2 <- data 
  
  #NA afta pou thelw na ftiaksw egw
  data2$Storage <- 0;    data2$Grid <- 0
  data2$Capacity <-0 ; data2$Capacity[1]=startingcapacity
  data2$Grid[1]=data2$Load[1]-data2$PV[1]+data2$CHP[1]
  
  for (i in 2:length(data2$Load)){
    # Storage - otan travaw apo mpataria
    capacity= data2$Capacity[i-1] #capacity th stigmh i
    renewable <- data2$PV[i]-data2$CHP[i] #res th stigmh i
    
    if ((renewable>data2$Load[i])&(capacity<Pcapacity)) { #Megalhterh paragwgh - Adeia mpataria
      if (  (renewable-data2$Load[i]) >= (Pcapacity-capacity)  ){ #mporw na thn gemisw full
        data2$Storage[i] <- Pcapacity-capacity
        data2$Capacity[i] <- Pcapacity
        data2$Grid[i] <- (-1)*( renewable-data2$Load[i]-(Pcapacity-capacity) )
      }else{                                                     # Th gemizw oso mporw
        data2$Storage[i] <- renewable-data2$Load[i]
        data2$Capacity[i] <- capacity+renewable-data2$Load[i]
        data2$Grid[i] <- 0
      }
    }
    
    if ((renewable>data2$Load[i])&(capacity==Pcapacity)){  #Megalhterh paragwgh - Gemati mpataria
      data2$Storage[i] <- 0
      data2$Capacity[i] <- Pcapacity
      data2$Grid[i] <- (-1)*(renewable-data2$Load[i])
    }
    
    if (renewable<=data2$Load[i]) { #Mikroterh paragwgh
      if ( capacity==Ncapacity  ){ #adeia mpataria
        data2$Storage[i] <- 0
        data2$Capacity[i] <- Ncapacity
        data2$Grid[i] <- data2$Load[i]-renewable
      }else{  
        if (capacity>=(data2$Load[i]-renewable)){ #H mpataria kalhptei
          data2$Storage[i] <- (-1)*(data2$Load[i]-renewable)
          data2$Capacity[i] <- capacity-(data2$Load[i]-renewable)
          data2$Grid[i] <- 0
        }else{                          # H mpataria den kalyptei
          data2$Storage[i] <- (-1)*(capacity-Ncapacity)
          data2$Capacity[i] <- Ncapacity
          data2$Grid[i] <- data2$Load[i]-renewable-(capacity-Ncapacity)
        }
      }
      
    }
    
  }
  return(data2)
}
