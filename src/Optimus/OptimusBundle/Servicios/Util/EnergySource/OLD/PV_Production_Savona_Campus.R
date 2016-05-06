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