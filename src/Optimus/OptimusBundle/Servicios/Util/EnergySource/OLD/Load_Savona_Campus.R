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
