library(forecast)
library(MASS)
library(timeDate)

fittingLoad <- function(data,weatherOut){
  
  library(MASS)
  
  leverage_points <- function(regfit.final, data_in){
    
    d1 <- cooks.distance(regfit.final)
    r <- stdres(regfit.final)
    a <- cbind(data_in, d1, r)
    zz=a[d1 > 4/length(data_in[,1]), ]  #leverage
    list.of.leverage=as.numeric(row.names(zz))
    if (length(list.of.leverage)>=1){
      for (i in 1:length(list.of.leverage)){
        k<-list.of.leverage[length(list.of.leverage)+1-i]
        data_in<-data_in[rownames(data_in)!=k,]
      }
    }
    return(data_in)
  }
  
  data$weekend <- isWeekend(data$date)
  for (i in 1:length(data$weekend)){
    if (data$weekend[i]==TRUE){ data$weekend[i]<-1 }
  }
  
  if( (data$Month[i]==1)&(data$Day[i]==1) ){ data$weekend[i]=0 }
  if( (data$Month[i]==1)&(data$Day[i]==6)){ data$weekend[i]=0 }
  if( (data$Month[i]==4)&(data$Day[i]==25)){ data$weekend[i]=0 }
  if( (data$Month[i]==5)&(data$Day[i]==1)){ data$weekend[i]=0 }
  if( (data$Month[i]==6)&(data$Day[i]==2)){ data$weekend[i]=0 }
  if( (data$Month[i]==8)&(data$Day[i]==15)){ data$weekend[i]=0 }
  if( (data$Month[i]==11)&(data$Day[i]==1)){ data$weekend[i]=0 }
  if( (data$Month[i]==12)&(data$Day[i]==8)){ data$weekend[i]=0 }
  if( (data$Month[i]==12)&(data$Day[i]==25)){ data$weekend[i]=0 }
  if( (data$Month[i]==12)&(data$Day[i]==26)){ data$weekend[i]=0 }
  if( (data$Month[i]==12)&(data$Day[i]==31)){ data$weekend[i]=0 }
  if( (data$Year[i]==2014)&(data$Month[i]==4)&(data$Day[i]==3)){ data$weekend[i]=0 }
  if( (data$Year[i]==2014)&(data$Month[i]==4)&(data$Day[i]==5)){ data$weekend[i]=0 }
  if( (data$Year[i]==2014)&(data$Month[i]==4)&(data$Day[i]==6)){ data$weekend[i]=0 }
  if( (data$Year[i]==2015)&(data$Month[i]==4)&(data$Day[i]==20)){ data$weekend[i]=0 }
  if( (data$Year[i]==2015)&(data$Month[i]==4)&(data$Day[i]==21)){ data$weekend[i]=0 }
  if( (data$Year[i]==2015)&(data$Month[i]==4)&(data$Day[i]==25)){ data$weekend[i]=0 }
  
  data$Oldness <- c(1:length(data[,1]))
  data$ID <- c(1:length(data[,1]))
  data_p=data
  
  ######################  Get rid of NAs (Missing Values)   #############################
  data <- data[complete.cases(data),]
  
  data$Year <- NULL ;data$Day <- NULL;data$Grid <- NULL
  data$Storage <- NULL;data$C65B <- NULL;data$C65 <- NULL
  data$WindDirectionDegrees <- NULL;data$WindSpeedKMH <- NULL;data$PressurehPa <- NULL
  data$Date <- NULL; data$PV <- NULL
  
  weather <- data
  weather$Temp2 <- weather$TemperatureC^2
  weather$Solar2 <-weather$SolarRadiationWatts.m.2^2
  ########################## Forecasting will be made per hour - Select & Create appropriate data-variables #############################################
  
  ######################################         Dataset per hour                   ####################################
  hour1 =weather[ which(weather$Hour==1),  -2];hour2 =weather[ which(weather$Hour==2),  -2];hour3 =weather[ which(weather$Hour==3),  -2]
  hour4 =weather[ which(weather$Hour==4),  -2];hour5 =weather[ which(weather$Hour==5),  -2];hour6 =weather[ which(weather$Hour==6),  -2]
  hour7 =weather[ which(weather$Hour==7),  -2];hour8 =weather[ which(weather$Hour==8),  -2];hour9 =weather[ which(weather$Hour==9),  -2]
  hour10=weather[ which(weather$Hour==10), -2];hour11=weather[ which(weather$Hour==11), -2];hour12=weather[ which(weather$Hour==12), -2]
  hour13=weather[ which(weather$Hour==13), -2];hour14=weather[ which(weather$Hour==14), -2];hour15=weather[ which(weather$Hour==15), -2]
  hour16=weather[ which(weather$Hour==16), -2];hour17=weather[ which(weather$Hour==17), -2];hour18=weather[ which(weather$Hour==18), -2]
  hour19=weather[ which(weather$Hour==19), -2];hour20=weather[ which(weather$Hour==20), -2];hour21=weather[ which(weather$Hour==21), -2]
  hour22=weather[ which(weather$Hour==22), -2];hour23=weather[ which(weather$Hour==23), -2];hour24=weather[ which(weather$Hour==0), -2]
  ####################################################################################################################################################
  
  ####################################################################################################################################################
  #This is the first estimation of the models
  regfit.final1  =lm(Load ~ Month+TemperatureC+Humidity+DewpointC+weekend+Oldness  ,data=hour1);  regfit.final2  =lm(Load ~ Month+TemperatureC+Oldness  ,data=hour2) 
  regfit.final3  =lm(Load ~ Month+weekend+Oldness  ,data=hour3);   regfit.final4  =lm(Load ~ Month+TemperatureC+Oldness  ,data=hour4) 
  regfit.final5  =lm(Load ~ TemperatureC+DewpointC+weekend  ,data=hour5);   regfit.final6  =lm(Load ~ Month+weekend+Oldness  ,data=hour6) 
  regfit.final7  =lm(Load ~ Month+DewpointC+weekend  ,data=hour7);   regfit.final8  =lm(Load ~ Month+TemperatureC+DewpointC+weekend ,data=hour8)
  regfit.final9  =lm(Load ~ TemperatureC+DewpointC+weekend+Oldness ,data=hour9);   regfit.final10 =lm(Load ~ Month+TemperatureC+SolarRadiationWatts.m.2+DewpointC+weekend+Solar2 ,data=hour10)
  regfit.final11 =lm(Load ~ Month+SolarRadiationWatts.m.2+DewpointC+weekend+Solar2 ,data=hour11);    regfit.final12 =lm(Load ~ Month+TemperatureC+DewpointC+weekend ,data=hour12)
  regfit.final13 =lm(Load ~ Month+TemperatureC+DewpointC+weekend  ,data=hour13);   regfit.final14 =lm(Load~ Month+TemperatureC+DewpointC+weekend ,data=hour14)
  regfit.final15 =lm(Load ~ TemperatureC+DewpointC+weekend ,data=hour15);   regfit.final16 =lm(Load ~ weekend+SolarRadiationWatts.m.2 ,data=hour16)
  regfit.final17 =lm(Load ~ Month+weekend+SolarRadiationWatts.m.2 ,data=hour17);  regfit.final18 =lm(Load ~ Month+TemperatureC+weekend+SolarRadiationWatts.m.2+Temp2 ,data=hour18)
  regfit.final19 =lm(Load ~ Month+TemperatureC+Temp2+weekend ,data=hour19);   regfit.final20 =lm(Load ~ Month+TemperatureC+Temp2+weekend ,data=hour20)
  regfit.final21 =lm(Load ~ Month+TemperatureC+Temp2+weekend ,data=hour21);  regfit.final22 =lm(Load ~ TemperatureC+weekend+Month+Temp2 ,data=hour22)
  regfit.final23 =lm(Load ~ TemperatureC+weekend+Month+Temp2+Oldness ,data=hour23);  regfit.final24 =lm(Load ~ Month+TemperatureC+weekend+Oldness ,data=hour24)
  
  ###############################################################################################################################################
  
  ################   Exclude leverage points and recalculate the models ####################
  hour1n <-leverage_points(regfit.final1,hour1);hour2n <-leverage_points(regfit.final2,hour2)
  hour3n <-leverage_points(regfit.final3,hour3);hour4n <-leverage_points(regfit.final4,hour4)
  hour5n <-leverage_points(regfit.final5,hour5);hour6n <-leverage_points(regfit.final6,hour6)
  hour7n <-leverage_points(regfit.final7,hour7);hour8n <-leverage_points(regfit.final8,hour8)
  hour9n <-leverage_points(regfit.final9,hour9);hour10n<-leverage_points(regfit.final10,hour10)
  hour11n<-leverage_points(regfit.final11,hour11);hour12n<-leverage_points(regfit.final12,hour12)
  hour13n<-leverage_points(regfit.final13,hour13);hour14n<-leverage_points(regfit.final14,hour14)
  hour15n<-leverage_points(regfit.final15,hour15);hour16n<-leverage_points(regfit.final16,hour16)
  hour17n<-leverage_points(regfit.final17,hour17);hour18n<-leverage_points(regfit.final18,hour18)
  hour19n<-leverage_points(regfit.final19,hour19);hour20n<-leverage_points(regfit.final20,hour20)
  hour21n<-leverage_points(regfit.final21,hour21);hour22n<-leverage_points(regfit.final22,hour22)
  hour23n<-leverage_points(regfit.final23,hour23);hour24n<-leverage_points(regfit.final24,hour24)
  
  regfit.final1  =lm(Load ~ Month+TemperatureC+Humidity+DewpointC+weekend+Oldness  ,data=hour1n);regfit.final2  =lm(Load ~ Month+TemperatureC+Oldness  ,data=hour2n) 
  regfit.final3  =lm(Load ~ Month+weekend+Oldness  ,data=hour3n);   regfit.final4  =lm(Load ~ Month+TemperatureC+Oldness  ,data=hour4n) 
  regfit.final5  =lm(Load ~ TemperatureC+DewpointC+weekend  ,data=hour5n);  regfit.final6  =lm(Load ~ Month+weekend+Oldness  ,data=hour6n) 
  regfit.final7  =lm(Load ~ Month+DewpointC+weekend  ,data=hour7n);   regfit.final8  =lm(Load ~ Month+TemperatureC+DewpointC+weekend ,data=hour8n)
  regfit.final9  =lm(Load ~ TemperatureC+DewpointC+weekend+Oldness ,data=hour9n);   regfit.final10 =lm(Load ~ Month+TemperatureC+SolarRadiationWatts.m.2+DewpointC+weekend+Solar2 ,data=hour10n)
  regfit.final11 =lm(Load ~ Month+SolarRadiationWatts.m.2+DewpointC+weekend+Solar2 ,data=hour11n);   regfit.final12 =lm(Load ~ Month+TemperatureC+DewpointC+weekend ,data=hour12n)
  regfit.final13 =lm(Load ~ Month+TemperatureC+DewpointC+weekend  ,data=hour13n);  regfit.final14 =lm(Load~ Month+TemperatureC+DewpointC+weekend ,data=hour14n)
  regfit.final15 =lm(Load ~ TemperatureC+DewpointC+weekend ,data=hour15n);   regfit.final16 =lm(Load ~ weekend+SolarRadiationWatts.m.2 ,data=hour16n)
  regfit.final17 =lm(Load ~ Month+weekend+SolarRadiationWatts.m.2 ,data=hour17n);  regfit.final18 =lm(Load ~ Month+TemperatureC+weekend+SolarRadiationWatts.m.2+Temp2 ,data=hour18n)
  regfit.final19 =lm(Load ~ Month+TemperatureC+Temp2+weekend ,data=hour19n);   regfit.final20 =lm(Load ~ Month+TemperatureC+Temp2+weekend ,data=hour20n)
  regfit.final21 =lm(Load ~ Month+TemperatureC+Temp2+weekend ,data=hour21n);  regfit.final22 =lm(Load ~ TemperatureC+weekend+Month+Temp2 ,data=hour22n)
  regfit.final23 =lm(Load ~ TemperatureC+weekend+Month+Temp2+Oldness ,data=hour23n);  regfit.final24 =lm(Load ~ Month+TemperatureC+weekend+Oldness ,data=hour24n)
  
  ################   In-sample forecasts ####################
  predictions1=as.data.frame(predict(regfit.final1, hour1,interval="prediction",level = 0.80) )
  predictions2=as.data.frame(predict(regfit.final2, hour2,interval="prediction",level = 0.80) )
  predictions3=as.data.frame(predict(regfit.final3, hour3,interval="prediction",level = 0.80) )
  predictions4=as.data.frame(predict(regfit.final4, hour4,interval="prediction",level = 0.80) )
  predictions5=as.data.frame(predict(regfit.final5, hour5,interval="prediction",level = 0.80) )
  predictions6=as.data.frame(predict(regfit.final6, hour6,interval="prediction",level = 0.80) )
  predictions7=as.data.frame(predict(regfit.final7, hour7,interval="prediction",level = 0.80) )
  predictions8=as.data.frame(predict(regfit.final8, hour8,interval="prediction",level = 0.80) )
  predictions9=as.data.frame(predict(regfit.final9, hour9,interval="prediction",level = 0.80) )
  predictions10=as.data.frame(predict(regfit.final10, hour10,interval="prediction",level = 0.80) ) 
  predictions11=as.data.frame(predict(regfit.final11, hour11,interval="prediction",level = 0.80) )
  predictions12=as.data.frame(predict(regfit.final12, hour12,interval="prediction",level = 0.80) )
  predictions13=as.data.frame(predict(regfit.final13, hour13,interval="prediction",level = 0.80) )
  predictions14=as.data.frame(predict(regfit.final14, hour14,interval="prediction",level = 0.80) )
  predictions15=as.data.frame(predict(regfit.final15, hour15,interval="prediction",level = 0.80) )
  predictions16=as.data.frame(predict(regfit.final16, hour16,interval="prediction",level = 0.80) )
  predictions17=as.data.frame(predict(regfit.final17, hour17,interval="prediction",level = 0.80) )
  predictions18=as.data.frame(predict(regfit.final18, hour18,interval="prediction",level = 0.80) )
  predictions19=as.data.frame(predict(regfit.final19, hour19,interval="prediction",level = 0.80) )
  predictions20=as.data.frame(predict(regfit.final20, hour20,interval="prediction",level = 0.80) )
  predictions21=as.data.frame(predict(regfit.final21, hour21,interval="prediction",level = 0.80) )
  predictions22=as.data.frame(predict(regfit.final22, hour22,interval="prediction",level = 0.80) )
  predictions23=as.data.frame(predict(regfit.final23, hour23,interval="prediction",level = 0.80) )
  predictions24=as.data.frame(predict(regfit.final24, hour24,interval="prediction",level = 0.80) )
  
  merged.predictions=rbind(predictions1,predictions2,predictions3,predictions4,predictions5,predictions6,
                           predictions7,predictions8,predictions9,predictions10,predictions11,predictions12,
                           predictions13,predictions14,predictions15,predictions16,predictions17,predictions18,
                           predictions19,predictions20,predictions21,predictions22,predictions23,predictions24)
  merged.predictions$ID=row.names(merged.predictions)
  data_p$ID=row.names(data_p)
  final.datatable=merge(merged.predictions,data_p,by="ID",all.y=TRUE)
  final.datatable <- final.datatable[ order(final.datatable$Oldness), ]
  
  ####  Clear NAs ##############
  if (is.na(final.datatable$fit[1])==TRUE){ final.datatable$fit[1]=final.datatable$upr[1]=final.datatable$lwr[1]=0 }
  for (i in 2:length(final.datatable[,1])){
    if (is.na(final.datatable$fit[i])==TRUE){
      final.datatable$fit[i]=final.datatable$fit[i-1]
    }
    if (is.na(final.datatable$lwr[i])==TRUE){
      final.datatable$lwr[i]=final.datatable$lwr[i-1]
    }
    if (is.na(final.datatable$upr[i])==TRUE){
      final.datatable$upr[i]=final.datatable$upr[i-1]
    }
  }
  ##############################################################
  
  #from=1000
  #to=2000
  #plot(final.datatable$Load[from:to],type="l",main="Electricity Consumption",ylab="kWh",xlab="Observations")
  #lines(final.datatable$fit[from:to],type="l",col="red")
  
  
  #################  Here ends the fitting of the models  ####################################
  
  #The "output" matrix includes the produced forecasts  & prediction intervals as well as the forecasted weather data that were given for the upcomming week 
  
  data<-weatherOut
  row.names(weatherOut)<-c(1:length(weatherOut$TemperatureC))
  data$Year <- as.numeric(substring(data$datetime, first=1, last=4))
  data$Month <- as.numeric(substring(data$datetime, first=6, last=7))
  data$Hour <- as.numeric(substring(data$datetime, first=12, last=13))
  data$Oldness <- c(1:length(data[,1]))
  data$ID <- c(1:length(data[,1]))
  data$weekend <- isWeekend(data$date)
  for (i in 1:length(data$weekend)){
    if (data$weekend[i]==TRUE){ data$weekend[i]<-1 }
  }
  
  data_p=data
  data$radiation<-NULL
  data$datetime<-NULL
  ######################  Get rid of NAs (Missing Values)   #############################
  lines=1    
  while (lines<=length(data[,1])){
    data[lines,]=as.numeric(data[lines,])
    if (is.na(sum(data[lines,]))==TRUE){
      data=data[-lines,]
    }else{
      lines=lines+1
    }
  }
  weather_out=data
  weather_out$Temp2 <- weather_out$TemperatureC^2
  weather_out$Solar2 <-weather_out$SolarRadiationWatts.m.2^2
  ######################################         Dataset per hour                   ####################################
  hour1OUT =weather_out[ which(weather_out$Hour==1),  ];hour2OUT =weather_out[ which(weather_out$Hour==2),  ]
  hour3OUT =weather_out[ which(weather_out$Hour==3),  ];hour4OUT =weather_out[ which(weather_out$Hour==4),  ]
  hour5OUT =weather_out[ which(weather_out$Hour==5),  ];hour6OUT =weather_out[ which(weather_out$Hour==6),  ]
  hour7OUT =weather_out[ which(weather_out$Hour==7),  ];hour8OUT =weather_out[ which(weather_out$Hour==8),  ];hour9OUT =weather_out[ which(weather_out$Hour==9),  ];
  hour10OUT=weather_out[ which(weather_out$Hour==10), ];hour11OUT=weather_out[ which(weather_out$Hour==11), ];hour12OUT=weather_out[ which(weather_out$Hour==12), ];
  hour13OUT=weather_out[ which(weather_out$Hour==13), ];hour14OUT=weather_out[ which(weather_out$Hour==14), ];hour15OUT=weather_out[ which(weather_out$Hour==15), ];
  hour16OUT=weather_out[ which(weather_out$Hour==16), ];hour17OUT=weather_out[ which(weather_out$Hour==17), ];hour18OUT=weather_out[ which(weather_out$Hour==18), ];
  hour19OUT=weather_out[ which(weather_out$Hour==19), ];hour20OUT=weather_out[ which(weather_out$Hour==20), ];hour21OUT=weather_out[ which(weather_out$Hour==21), ];
  hour22OUT=weather_out[ which(weather_out$Hour==22), ];hour23OUT=weather_out[ which(weather_out$Hour==23), ];hour24OUT=weather_out[ which(weather_out$Hour==0), ]
  ####################################################################################################################################################
  ################   Out-sample forecasts ####################
  predictions1=as.data.frame(predict(regfit.final1, hour1OUT,interval="prediction",level = 0.90) )
  predictions2=as.data.frame(predict(regfit.final2, hour2OUT,interval="prediction",level = 0.90) )
  predictions3=as.data.frame(predict(regfit.final3, hour3OUT,interval="prediction",level = 0.90) )
  predictions4=as.data.frame(predict(regfit.final4, hour4OUT,interval="prediction",level = 0.90) )
  predictions5=as.data.frame(predict(regfit.final5, hour5OUT,interval="prediction",level = 0.90) )
  predictions6=as.data.frame(predict(regfit.final6, hour6OUT,interval="prediction",level = 0.90) )
  predictions7=as.data.frame(predict(regfit.final7, hour7OUT,interval="prediction",level = 0.90) )
  predictions8=as.data.frame(predict(regfit.final8, hour8OUT,interval="prediction",level = 0.90) )
  predictions9=as.data.frame(predict(regfit.final9, hour9OUT,interval="prediction",level = 0.90) )
  predictions10=as.data.frame(predict(regfit.final10, hour10OUT,interval="prediction",level = 0.90) ) 
  predictions11=as.data.frame(predict(regfit.final11, hour11OUT,interval="prediction",level = 0.90) )
  predictions12=as.data.frame(predict(regfit.final12, hour12OUT,interval="prediction",level = 0.90) )
  predictions13=as.data.frame(predict(regfit.final13, hour13OUT,interval="prediction",level = 0.90) )
  predictions14=as.data.frame(predict(regfit.final14, hour14OUT,interval="prediction",level = 0.90) )
  predictions15=as.data.frame(predict(regfit.final15, hour15OUT,interval="prediction",level = 0.90) )
  predictions16=as.data.frame(predict(regfit.final16, hour16OUT,interval="prediction",level = 0.90) )
  predictions17=as.data.frame(predict(regfit.final17, hour17OUT,interval="prediction",level = 0.90) )
  predictions18=as.data.frame(predict(regfit.final18, hour18OUT,interval="prediction",level = 0.90) )
  predictions19=as.data.frame(predict(regfit.final19, hour19OUT,interval="prediction",level = 0.90) )
  predictions20=as.data.frame(predict(regfit.final20, hour20OUT,interval="prediction",level = 0.90) )
  predictions21=as.data.frame(predict(regfit.final21, hour21OUT,interval="prediction",level = 0.90) )
  predictions22=as.data.frame(predict(regfit.final22, hour22OUT,interval="prediction",level = 0.90) )
  predictions23=as.data.frame(predict(regfit.final23, hour23OUT,interval="prediction",level = 0.90) )
  predictions24=as.data.frame(predict(regfit.final24, hour24OUT,interval="prediction",level = 0.90) )
  
  
  merged.predictionsOUT=rbind(predictions1,predictions2,predictions3,predictions4,predictions5,predictions6,
                              predictions7,predictions8,predictions9,predictions10,predictions11,
                              predictions12,predictions13,predictions14,predictions15,predictions16,
                              predictions17,predictions18,predictions19,predictions20,predictions21,predictions22,predictions23,predictions24)
  merged.predictionsOUT$ID=row.names(merged.predictionsOUT)
  output=merge(merged.predictionsOUT,data_p,by="ID",all.y=TRUE)
  output <- output[ order(output$Oldness), ]
  ####  Hours form 1-6 and 23 has always zero production ##############
  if (is.na(output$fit[1])==TRUE){ output$fit[1]=output$upr[1]=output$lwr[1]=0 }
  
  for (i in 2:length(output[,1])){
    if (is.na(output$fit[i])==TRUE){
      output$fit[i]=output$fit[i-1]
    }
    if (is.na(output$lwr[i])==TRUE){
      output$lwr[i]=output$lwr[i-1]
    }
    if (is.na(output$upr[i])==TRUE){
      output$upr[i]=output$upr[i-1]
    }
  }
  
  ####  Negative predictions are meaningless ##############
  persentile<-95
  for (i in 1:length(output[,1])){
    if (output$fit[i]<persentile){
      output$fit[i]=persentile
    }
    if (output$lwr[i]<persentile){
      output$lwr[i]=persentile
    }
    if (output$upr[i]<persentile){
      output$upr[i]=persentile
    }
  }
  ###############################################################################################
  
  final.datatable$ID<-NULL;        final.datatable$lwr <-NULL ;        
  final.datatable$upr<-NULL;       final.datatable$Year<-NULL ;                   final.datatable$Month   <-NULL               
  final.datatable$Day<-NULL ;      final.datatable$Hour<-NULL ;                  final.datatable$Date    <-NULL               
  final.datatable$Grid<-NULL ;     final.datatable$PV  <-NULL ;                 final.datatable$Storage      <-NULL          
  final.datatable$C65B<-NULL  ;    final.datatable$C65 <-NULL ;                final.datatable$TemperatureC     <-NULL      
  final.datatable$Humidity<-NULL;         final.datatable$PressurehPa <-NULL ;           final.datatable$WindDirectionDegrees <-NULL  
  final.datatable$SolarRadiationWatts.m.2<-NULL; final.datatable$DewpointC<-NULL ;              final.datatable$WindSpeedKMH <-NULL          
  final.datatable$Load<-NULL;         final.datatable$Oldness <-NULL; final.datatable$weekend <-NULL
  
  
  output$lwr<-NULL;   output$upr<-NULL; output$TemperatureC<-NULL; output$Humidity<-NULL; output$PressurehPa<-NULL            
  output$WindDirectionDegrees<-NULL; output$SolarRadiationWatts.m.2<-NULL; output$DewpointC<-NULL              
  output$WindSpeedKMH<-NULL ; output$ID<-NULL; output$weekend<-NULL      
  output$Year<-NULL; output$Month<-NULL; output$Hour<-NULL; output$Oldness<-NULL
  final.datatable$PVBegin<-NULL
  
  output_f <- list(final.datatable,output)
  
  return(output_f)
}
fittingPV <- function(data,weatherOut){
  
  library(MASS)
  
  leverage_points <- function(regfit.final, data_in){
    
    d1 <- cooks.distance(regfit.final)
    r <- stdres(regfit.final)
    a <- cbind(data_in, d1, r)
    zz=a[d1 > 4/length(data_in[,1]), ]  #leverage
    list.of.leverage=as.numeric(row.names(zz))
    if (length(list.of.leverage)>=1){
      for (i in 1:length(list.of.leverage)){
        k<-list.of.leverage[length(list.of.leverage)+1-i]
        data_in<-data_in[rownames(data_in)!=k,]
      }
    }
    return(data_in)
  }
  
  data$Oldness <- c(1:length(data[,1]))
  data$ID <- c(1:length(data[,1]))
  data_p=data
  
  ######################  Get rid of NAs (Missing Values)   #############################
  data <- data[complete.cases(data),]
  
  data$Year <- NULL ;data$Day <- NULL;data$Grid <- NULL
  data$Storage <- NULL;data$C65B <- NULL;data$C65 <- NULL
  data$WindDirectionDegrees <- NULL;data$WindSpeedKMH <- NULL;data$PressurehPa <- NULL
  data$Date <- NULL; data$Load <- NULL
  
  weather <- data
  weather$Solar2 <- weather$SolarRadiationWatts.m.2^2
  weather$Solar3 <- weather$SolarRadiationWatts.m.2^3
  ########################## Forecasting will be made per hour - Select & Create appropriate data-variables #############################################
  
  ######################################         Dataset per hour                   ####################################
  hour7 =weather[ which(weather$Hour==7),  -2];hour8 =weather[ which(weather$Hour==8),  -2];hour9 =weather[ which(weather$Hour==9),  -2];
  hour10=weather[ which(weather$Hour==10), -2];hour11=weather[ which(weather$Hour==11), -2];hour12=weather[ which(weather$Hour==12), -2];
  hour13=weather[ which(weather$Hour==13), -2];hour14=weather[ which(weather$Hour==14), -2];hour15=weather[ which(weather$Hour==15), -2];
  hour16=weather[ which(weather$Hour==16), -2];hour17=weather[ which(weather$Hour==17), -2];hour18=weather[ which(weather$Hour==18), -2];
  ####################################################################################################################################################
  
  ####################################################################################################################################################
  #This is the first estimation of the models
  regfit.final7  =lm(PV~ Oldness  ,data=hour7);  regfit.final8  =lm(PV~ Humidity+SolarRadiationWatts.m.2+Solar2+Solar3 ,data=hour8)
  regfit.final9  =lm(PV~ SolarRadiationWatts.m.2+Solar2+Solar3+Humidity+Oldness ,data=hour9);  regfit.final10 =lm(PV~ 0+SolarRadiationWatts.m.2++Solar2+Solar3+Oldness ,data=hour10)
  regfit.final11 =lm(PV~ SolarRadiationWatts.m.2+Oldness+Humidity ,data=hour11);  regfit.final12 =lm(PV~ 0+SolarRadiationWatts.m.2+Solar2+Solar3 ,data=hour12)
  regfit.final13 =lm(PV~ 0+SolarRadiationWatts.m.2  ,data=hour13);  regfit.final14 =lm(PV~ 0+SolarRadiationWatts.m.2+Solar2 ,data=hour14)
  regfit.final15 =lm(PV~ 0+Month+Humidity+SolarRadiationWatts.m.2+Solar2+DewpointC+Oldness ,data=hour15);  regfit.final16 =lm(PV~ Month+SolarRadiationWatts.m.2+Solar2+DewpointC+Oldness ,data=hour16)
  regfit.final17 =lm(PV~ Month+SolarRadiationWatts.m.2+Solar2+DewpointC+Oldness ,data=hour17);  regfit.final18 =lm(PV~ Humidity+SolarRadiationWatts.m.2+Solar2+Solar3+Oldness ,data=hour18)
  ###############################################################################################################################################
  
  ################   Exclude leverage points and recalculate the models ####################
  hour7n <-leverage_points(regfit.final7,hour7);hour8n <-leverage_points(regfit.final8,hour8);
  hour9n <-leverage_points(regfit.final9,hour9);hour10n<-leverage_points(regfit.final10,hour10);
  hour11n<-leverage_points(regfit.final11,hour11);hour12n<-leverage_points(regfit.final12,hour12);
  hour13n<-leverage_points(regfit.final13,hour13);hour14n<-leverage_points(regfit.final14,hour14);
  hour15n<-leverage_points(regfit.final15,hour15);hour16n<-leverage_points(regfit.final16,hour16);
  hour17n<-leverage_points(regfit.final17,hour17);hour18n<-leverage_points(regfit.final18,hour18);
  
  regfit.final7  =lm(PV~ Oldness  ,data=hour7n);  regfit.final8  =lm(PV~ Humidity+SolarRadiationWatts.m.2+Solar2+Solar3 ,data=hour8n)
  regfit.final9  =lm(PV~ SolarRadiationWatts.m.2+Solar2+Solar3+Humidity+Oldness ,data=hour9n);  regfit.final10 =lm(PV~ 0+SolarRadiationWatts.m.2++Solar2+Solar3+Oldness ,data=hour10n)
  regfit.final11 =lm(PV~ SolarRadiationWatts.m.2+Oldness+Humidity ,data=hour11n);  regfit.final12 =lm(PV~ 0+SolarRadiationWatts.m.2+Solar2+Solar3 ,data=hour12n)
  regfit.final13 =lm(PV~ 0+SolarRadiationWatts.m.2  ,data=hour13n);  regfit.final14 =lm(PV~ 0+SolarRadiationWatts.m.2+Solar2 ,data=hour14n)
  regfit.final15 =lm(PV~ 0+Month+Humidity+SolarRadiationWatts.m.2+Solar2+DewpointC+Oldness ,data=hour15n);  regfit.final16 =lm(PV~ Month+SolarRadiationWatts.m.2+Solar2+DewpointC+Oldness ,data=hour16n)
  regfit.final17 =lm(PV~ Month+SolarRadiationWatts.m.2+Solar2+DewpointC+Oldness ,data=hour17n);  regfit.final18 =lm(PV~ Humidity+SolarRadiationWatts.m.2+Solar2+Solar3+Oldness ,data=hour18n)
  
  ################   In-sample forecasts ####################
  predictions7=as.data.frame(predict(regfit.final7, hour7,interval="prediction",level = 0.80) )
  predictions8=as.data.frame(predict(regfit.final8, hour8,interval="prediction",level = 0.80) )
  predictions9=as.data.frame(predict(regfit.final9, hour9,interval="prediction",level = 0.80) )
  predictions10=as.data.frame(predict(regfit.final10, hour10,interval="prediction",level = 0.80) ) 
  predictions11=as.data.frame(predict(regfit.final11, hour11,interval="prediction",level = 0.80) )
  predictions12=as.data.frame(predict(regfit.final12, hour12,interval="prediction",level = 0.80) )
  predictions13=as.data.frame(predict(regfit.final13, hour13,interval="prediction",level = 0.80) )
  predictions14=as.data.frame(predict(regfit.final14, hour14,interval="prediction",level = 0.80) )
  predictions15=as.data.frame(predict(regfit.final15, hour15,interval="prediction",level = 0.80) )
  predictions16=as.data.frame(predict(regfit.final16, hour16,interval="prediction",level = 0.80) )
  predictions17=as.data.frame(predict(regfit.final17, hour17,interval="prediction",level = 0.80) )
  predictions18=as.data.frame(predict(regfit.final18, hour18,interval="prediction",level = 0.80) )
  
  merged.predictions=rbind(predictions7,predictions8,predictions9,predictions10,predictions11,
                           predictions12,predictions13,predictions14,predictions15,predictions16,
                           predictions17,predictions18)
  
  merged.predictions$ID=row.names(merged.predictions)
  data_p$ID=row.names(data_p)
  final.datatable=merge(merged.predictions,data_p,by="ID",all.y=TRUE)
  final.datatable <- final.datatable[ order(final.datatable$Oldness), ]
  ####  Hours form 1-6 and 22-24 has always zero production ##############
  for (i in 1:length(final.datatable[,1])){
    if (is.na(final.datatable$fit[i])==TRUE){
      final.datatable$fit[i]=-0.022
    }
    if (is.na(final.datatable$lwr[i])==TRUE){
      final.datatable$lwr[i]=-0.023
    }
    if (is.na(final.datatable$upr[i])==TRUE){
      final.datatable$upr[i]=-0.021
    }
  }
  ##############################################################
  
  ####  Negative predictions are meaningless ##############
  for (i in 1:length(final.datatable[,1])){
    if (final.datatable$fit[i]<0){
      final.datatable$fit[i]=-0.022
    }
    if (final.datatable$lwr[i]<0){
      final.datatable$lwr[i]=-0.023
    }
    if (final.datatable$upr[i]<0){
      final.datatable$upr[i]=-0.021
    }
  }
  
  #from=500
  #to=1000
  #plot(final.datatable$PV[from:to],type="l",main="PV production",ylab="kWh",xlab="Observations")
  #lines(final.datatable$fit[from:to],type="l",col="red")
  
  #################  Here ends the fitting of the models  ####################################
  
  #The "output" matrix includes the produced forecasts  & prediction intervals as well as the forecasted weather data that were given for the upcomming week 
  
  data<-weatherOut
  row.names(weatherOut)<-c(1:length(weatherOut$TemperatureC))
  data$Year <- as.numeric(substring(data$datetime, first=1, last=4))
  data$Month <- as.numeric(substring(data$datetime, first=6, last=7))
  data$Hour <- as.numeric(substring(data$datetime, first=12, last=13))
  data$Oldness <- c(1:length(data[,1]))
  data$ID <- c(1:length(data[,1]))
  data_p=data
  data$radiation<-NULL
  data$datetime<-NULL
  ######################  Get rid of NAs (Missing Values)   #############################
  lines=1    
  while (lines<=length(data[,1])){
    data[lines,]=as.numeric(data[lines,])
    if (is.na(sum(data[lines,]))==TRUE){
      data=data[-lines,]
    }else{
      lines=lines+1
    }
  }
  weather_out=data
  weather_out$Solar2 <- weather_out$SolarRadiationWatts.m.2^2
  weather_out$Solar3 <- weather_out$SolarRadiationWatts.m.2^3
  ######################################         Dataset per hour                   ####################################
  hour7OUT =weather_out[ which(weather_out$Hour==7),  ];hour8OUT =weather_out[ which(weather_out$Hour==8),  ];hour9OUT =weather_out[ which(weather_out$Hour==9),  ];
  hour10OUT=weather_out[ which(weather_out$Hour==10), ];hour11OUT=weather_out[ which(weather_out$Hour==11), ];hour12OUT=weather_out[ which(weather_out$Hour==12), ];
  hour13OUT=weather_out[ which(weather_out$Hour==13), ];hour14OUT=weather_out[ which(weather_out$Hour==14), ];hour15OUT=weather_out[ which(weather_out$Hour==15), ];
  hour16OUT=weather_out[ which(weather_out$Hour==16), ];hour17OUT=weather_out[ which(weather_out$Hour==17), ];hour18OUT=weather_out[ which(weather_out$Hour==18), ];
  ####################################################################################################################################################
  ################   Out-sample forecasts ####################
  predictions7=as.data.frame(predict(regfit.final7, hour7OUT,interval="prediction",level = 0.90) )
  predictions8=as.data.frame(predict(regfit.final8, hour8OUT,interval="prediction",level = 0.90) )
  predictions9=as.data.frame(predict(regfit.final9, hour9OUT,interval="prediction",level = 0.90) )
  predictions10=as.data.frame(predict(regfit.final10, hour10OUT,interval="prediction",level = 0.90) ) 
  predictions11=as.data.frame(predict(regfit.final11, hour11OUT,interval="prediction",level = 0.90) )
  predictions12=as.data.frame(predict(regfit.final12, hour12OUT,interval="prediction",level = 0.90) )
  predictions13=as.data.frame(predict(regfit.final13, hour13OUT,interval="prediction",level = 0.90) )
  predictions14=as.data.frame(predict(regfit.final14, hour14OUT,interval="prediction",level = 0.90) )
  predictions15=as.data.frame(predict(regfit.final15, hour15OUT,interval="prediction",level = 0.90) )
  predictions16=as.data.frame(predict(regfit.final16, hour16OUT,interval="prediction",level = 0.90) )
  predictions17=as.data.frame(predict(regfit.final17, hour17OUT,interval="prediction",level = 0.90) )
  predictions18=as.data.frame(predict(regfit.final18, hour18OUT,interval="prediction",level = 0.90) )
  
  merged.predictionsOUT=rbind(predictions7,predictions8,predictions9,predictions10,predictions11,
                              predictions12,predictions13,predictions14,predictions15,predictions16,
                              predictions17,predictions18)
  merged.predictionsOUT$ID=row.names(merged.predictionsOUT)
  output=merge(merged.predictionsOUT,data_p,by="ID",all.y=TRUE)
  output <- output[ order(output$Oldness), ]
  ####  Hours form 1-6 and 23 has always zero production ##############
  for (i in 1:length(output[,1])){
    if (is.na(output$fit[i])==TRUE){
      output$fit[i]=-0.022
    }
    if (is.na(output$lwr[i])==TRUE){
      output$lwr[i]=-0.023
    }
    if (is.na(output$upr[i])==TRUE){
      output$upr[i]=-0.021
    }
  }
  ####  Negative predictions are meaningless ##############
  for (i in 1:length(output[,1])){
    if (output$fit[i]<0){
      output$fit[i]=-0.022
    }
    if (output$lwr[i]<0){
      output$lwr[i]=-0.023
    }
    if (output$upr[i]<0){
      output$upr[i]=-0.021
    }
  }
  ###############################################################################################
  
  ####  Negative predictions are meaningless ##############
  for (i in 1:length(output[,1])){
    if (output$fit[i]>64){
      output$fit[i]=64
      output$lwr[i]=70
      output$upr[i]=57
    }
  }
  ###############################################################################################
  
  final.datatable$ID<-NULL;        final.datatable$lwr<-NULL ;                   
  final.datatable$upr<-NULL;       final.datatable$Year<-NULL ;                   final.datatable$Month   <-NULL               
  final.datatable$Day<-NULL ;      final.datatable$Hour<-NULL  ;                  final.datatable$Date    <-NULL               
  final.datatable$Grid<-NULL ;     final.datatable$PV <-NULL    ;                 final.datatable$Storage      <-NULL          
  final.datatable$C65B<-NULL  ;    final.datatable$C65<-NULL     ;                final.datatable$TemperatureC     <-NULL      
  final.datatable$Humidity<-NULL;         final.datatable$PressurehPa <-NULL ;           final.datatable$WindDirectionDegrees <-NULL  
  final.datatable$SolarRadiationWatts.m.2<-NULL; final.datatable$DewpointC<-NULL ;              final.datatable$WindSpeedKMH <-NULL          
  final.datatable$Load<-NULL;         final.datatable$Oldness <-NULL
  
  
  output$lwr<-NULL;   output$upr<-NULL;                    
  output$TemperatureC<-NULL;      output$Humidity<-NULL;    output$PressurehPa<-NULL            
  output$WindDirectionDegrees<-NULL;output$SolarRadiationWatts.m.2<-NULL; output$DewpointC<-NULL              
  output$WindSpeedKMH<-NULL ; output$ID<-NULL         
  output$Year<-NULL; output$Month<-NULL; output$Hour<-NULL; output$Oldness<-NULL
  
  
  output_f <- list(final.datatable,output)
  
  return(output_f)
}
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

loadshift=FALSE
OHstart=7 ; OHend=20
dataORIGINAL=dataNEW<-NULL
forweeks<-1
cut=17
# 80% 85% 90% 95%
# 17  18  19  20

#################  Get the weather data files  ################################
weather=read.csv("C:/Users/vangelis spil/Google Drive/OPTIMUS/Data and Scripts/Savona Campus Action Plan - DSS version - v2/clean_weather_dataSavona.csv",stringsAsFactors = FALSE,sep = ",")
weather$Year <- as.numeric(substring(weather$datetime, first=1, last=4))
weather$Month <- as.numeric(substring(weather$datetime, first=6, last=7))
weather$Day <- as.numeric(substring(weather$datetime, first=9, last=10))
weather$Hour <- as.numeric(substring(weather$datetime, first=12, last=13))
#########  This is the data.frame for fitting the model and forecasting   ################
energy_data=read.csv("C:/Users/vangelis spil/Google Drive/OPTIMUS/Data and Scripts/Savona Campus Action Plan - DSS version - v2/Energy Data.csv",stringsAsFactors = FALSE,sep = ";")
energy_data$PV <- energy_data$PV*(-1)
data <- merge(energy_data,weather,by=c("Year","Month","Day","Hour"))
data$Load <- data$PV-data$C65B-data$C65-data$Storage+data$Grid
dataALL<-data ; dataALL <- dataALL[order(dataALL$Year, dataALL$Month, dataALL$Day, dataALL$Hour),]
dataALL$CHP<-dataALL$C65+dataALL$C65B ; dataALL$C65=dataALL$C65B<-NULL
##############  Kai afto einai to sygrisimo - simulate  #############################################
DataReal<-simulateData(dataALL)  # Apo edw kai pera yparxei mono afto
dataALL=data=energy_data=weather<-NULL
DataReal$Date<-NULL
#Afto exw otan ksekinaw kai tha to ftiaksw stadiaka gemizontas me tis epiloges mou
dataRoll<-DataReal ; TotalSuggestions<-c() 
#################  Get the data for the rolling origin chosen  ################################
data<-dataRoll
weatherOut<-read.csv("C:/Users/vangelis spil/Google Drive/OPTIMUS/Data and Scripts/Savona Campus Action Plan - DSS version - v2/clean_weather_dataSavona_Out.csv",stringsAsFactors = FALSE,sep = ",")
weatherOut$Year=weatherOut$Month=weatherOut$Hour=weatherOut$weatherOut$Grid=weatherOut$C65B<-NULL  
weatherOut$PV=weatherOut$Storage=weatherOut$C65=weatherOut$Grid<-NULL
################### Forecast PV, CHP and Load #########################################
forecastPV<-fittingPV(data,weatherOut)[[2]] #Forecast PV production
forecastLoad<-fittingLoad(data,weatherOut)[[2]] #Forecast Energy Consumption
modelC<-lm(CHP ~ Year+Month+Hour+Humidity+PressurehPa+SolarRadiationWatts.m.2+DewpointC+WindSpeedKMH,data=data)
weatherOut$Year <- as.numeric(substring(weatherOut$datetime, first=1, last=4))
weatherOut$Month <- as.numeric(substring(weatherOut$datetime, first=6, last=7))
weatherOut$Hour <- as.numeric(substring(weatherOut$datetime, first=12, last=13))
forecastC=as.data.frame(predict(modelC, weatherOut,interval="prediction",level = 0.80))$fit
#################################################################################################

###############################    Energy prices  - forecasts #################################################
Energyprices<-read.csv("C:/Users/vangelis spil/Google Drive/OPTIMUS/Data and Scripts/Savona Campus Action Plan - DSS version - v2/Prices - Savona.csv",stringsAsFactors = FALSE,sep = ",")
outmonths<-c(weatherOut$Month[1],weatherOut$Month[168]) ; outyears<-c(weatherOut$Year[1],weatherOut$Year[168])
if (outmonths[1]==outmonths[2]){ outmonths<-c(weatherOut$Month[1]) }
if (outyears[1]==outyears[2]){ outyears<-c(weatherOut$Year[1]) }
forigin<-min(outyears)+outmonths[which.min(outyears)]/100 ; Energyprices$forigin<-0
for (sfo in 1:length(Energyprices$Year)) { Energyprices$forigin[sfo]<-Energyprices$Year[sfo]+Energyprices$Month[sfo]/100 }
Energyprices<-Energyprices[Energyprices$forigin<forigin,] ; Energyprices$forigin<-NULL
horizon<-length(outmonths) ; prices<-matrix(NA, nrow=horizon, ncol=3)
F1p<-ts(Energyprices$F1,frequency=12,start=c(Energyprices$Year[1],Energyprices$Month[1]))
F2p<-ts(Energyprices$F2,frequency=12,start=c(Energyprices$Year[1],Energyprices$Month[1]))
F3p<-ts(Energyprices$F3,frequency=12,start=c(Energyprices$Year[1],Energyprices$Month[1]))
prices[,3]<-forecast(auto.arima(F1p-F2p), h=horizon)$mean ; prices[,1]<-outyears; prices[,2]<-outmonths
prices<-data.frame(prices); colnames(prices)<-c("Year","Month","Difference")
lengthEnergyprices<-length(Energyprices$Year)+length(prices$Year)
Enprices<-read.csv("C:/Users/vangelis spil/Google Drive/OPTIMUS/Data and Scripts/Savona Campus Action Plan - DSS version - v2/Prices - Savona.csv",stringsAsFactors = FALSE,sep = ",")
Enprices<-Enprices[1:lengthEnergyprices,]
for (checkpr in 1:length(prices$Year)){
  Enprices[length(Energyprices$Year)+checkpr,4]<-Enprices[length(Energyprices$Year)+checkpr,3]-prices[checkpr,3]
}
####################################################################################################

###############  This is what happens to the in and outsample data    ########################
dataOUT<-weatherOut ; dataOUT$Load<-forecastLoad$fit ;   dataOUT$PV<-forecastPV$fit ;   dataOUT$CHP <- forecastC 
dataOUT$Day <- as.numeric(substring(weatherOut$datetime, first=9, last=10)) ; dataOUT$Grid=dataOUT$Storage=dataOUT$Capacity<-0
dataInOut<-rbind(dataRoll,dataOUT) ; dataInOut <- dataInOut[order(dataInOut$datetime),]
dataSimulated<-simulateData(dataInOut)

simulated_data_IN<-dataSimulated[1:length(dataRoll$Load),]
simulated_data_OUT<-dataSimulated[(length(dataRoll$Load)+1):(length(dataRoll$Load)+168),]
####################################################################################################

#################################################  Optimize ##############################################
################ Here i find which is the peak load according to the cut chosen  #####################
simulated_data_IN$weekend <-as.numeric(isWeekend(simulated_data_IN$datetime)) ;simulated_data_IN$OH<-0
for (i in 1:length(simulated_data_IN$OH)){
  if( (simulated_data_IN$Hour[i]>=OHstart)&(simulated_data_IN$Hour[i]<=OHend)&(simulated_data_IN$weekend[i]==0) ){ simulated_data_IN$OH[i]=1 }
  
  if( (simulated_data_IN$Month[i]==1)&(simulated_data_IN$Day[i]==1) ){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==1)&(simulated_data_IN$Day[i]==6)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==5)&(simulated_data_IN$Day[i]==1)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==6)&(simulated_data_IN$Day[i]==2)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==8)&(simulated_data_IN$Day[i]==15)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==11)&(simulated_data_IN$Day[i]==1)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==8)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==26)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Month[i]==12)&(simulated_data_IN$Day[i]==31)){ simulated_data_IN$OH[i]=0 }
  
  if( (simulated_data_IN$Year[i]==2014)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==3)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2014)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==5)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2014)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==6)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2015)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==20)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2015)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==21)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2015)&(simulated_data_IN$Month[i]==4)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2016)&(simulated_data_IN$Month[i]==3)&(simulated_data_IN$Day[i]==25)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2016)&(simulated_data_IN$Month[i]==3)&(simulated_data_IN$Day[i]==27)){ simulated_data_IN$OH[i]=0 }
  if( (simulated_data_IN$Year[i]==2016)&(simulated_data_IN$Month[i]==3)&(simulated_data_IN$Day[i]==28)){ simulated_data_IN$OH[i]=0 }
}
wherepeak<-as.numeric(quantile(simulated_data_IN$Load, probs = seq(0, 1, 0.05))[cut]) # 80% to peak sta working hours
##################################################################################################
simulated_data_OUT$weekend <-as.numeric(isWeekend(simulated_data_OUT$datetime)); simulated_data_OUT$OH<-0
simulated_data_OUT$Day<-as.numeric(substring(simulated_data_OUT$datetime, first=9, last=10))
for (i in 1:length(simulated_data_OUT$OH)){
  if( (simulated_data_OUT$Hour[i]>=OHstart)&(simulated_data_OUT$Hour[i]<=OHend)&(simulated_data_OUT$weekend[i]==0) ){ simulated_data_OUT$OH[i]=1 }
  
  if( (simulated_data_OUT$Month[i]==1)&(simulated_data_OUT$Day[i]==1) ){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==1)&(simulated_data_OUT$Day[i]==6)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==5)&(simulated_data_OUT$Day[i]==1)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==6)&(simulated_data_OUT$Day[i]==2)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==8)&(simulated_data_OUT$Day[i]==15)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==11)&(simulated_data_OUT$Day[i]==1)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==8)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==26)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Month[i]==12)&(simulated_data_OUT$Day[i]==31)){ simulated_data_OUT$OH[i]=0 }
  
  if( (simulated_data_OUT$Year[i]==2014)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==3)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2014)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==5)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2014)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==6)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2015)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==20)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2015)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==21)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2015)&(simulated_data_OUT$Month[i]==4)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2016)&(simulated_data_OUT$Month[i]==3)&(simulated_data_OUT$Day[i]==25)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2016)&(simulated_data_OUT$Month[i]==3)&(simulated_data_OUT$Day[i]==27)){ simulated_data_OUT$OH[i]=0 }
  if( (simulated_data_OUT$Year[i]==2016)&(simulated_data_OUT$Month[i]==3)&(simulated_data_OUT$Day[i]==28)){ simulated_data_OUT$OH[i]=0 }
}

if (loadshift==TRUE){
  optimizedOut<-runningpeaks(simulated_data_OUT,prices) #senario 1:to optimized Load vasei tou ti eprepe na kanw (forecast) peak
}else{
  optimizedOut<-simulated_data_OUT
}

optimizedOut <- optimizedOut[order(optimizedOut$datetime),]
SuggestionsHmerwn <- simulated_data_OUT$Load- optimizedOut$Load
TotalSuggestions<-c(TotalSuggestions,SuggestionsHmerwn)

FinalSuggestions<-optimizedOut
FinalSuggestions$FinalSuggestions<-TotalSuggestions
FinalSuggestions$RES<-FinalSuggestions$PV-FinalSuggestions$CHP

FinalSuggestions$Storage=FinalSuggestions$TemperatureC=FinalSuggestions$Humidity<-NULL
FinalSuggestions$PressurehPa=FinalSuggestions$WindDirectionDegrees=FinalSuggestions$SolarRadiationWatts.m.2<-NULL
FinalSuggestions$DewpointC=FinalSuggestions$WindSpeedKMH=FinalSuggestions$Year=FinalSuggestions$Day<-NULL
FinalSuggestions$Month=FinalSuggestions$Hour=FinalSuggestions$Capacity=FinalSuggestions$weekend<-NULL
FinalSuggestions$OH=FinalSuggestions$symferei<-NULL
FinalSuggestions$PV=FinalSuggestions$CHP<-NULL

#  plot(FinalSuggestions$Load,type="l",ylim=c(-15,350))
#  lines(simulated_data_OUT$Load,type="l",col="blue")
#  lines(FinalSuggestions$Grid,type="l",col="red")
#  lines(FinalSuggestions$RES,type="l",col="green")

dataOptimized<-heuristics(FinalSuggestions,dataOUT,DataReal,plott=FALSE)  # Ti kanw me ta heuristics
dataOptimized$FinalSuggestions=dataOptimized$Year=dataOptimized$Month<-NULL
dataOptimized$Day=dataOptimized$Hour<-NULL
#write.csv(FinalSuggestions, file=paste("C:/Users/vangelis spil/Desktop/LoadShift.csv"),row.names=FALSE)
#write.csv(dataOptimized, file=paste("C:/Users/vangelis spil/Desktop/ChooseSource.csv"),row.names=FALSE)
