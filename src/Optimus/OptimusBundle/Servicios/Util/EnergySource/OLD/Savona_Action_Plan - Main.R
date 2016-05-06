setwd("C:/Users/vangelis spil/Google Drive/OPTIMUS/Data and Scripts/Savona Campus Action Plan - DSS version - v2")
library(forecast)
library(MASS)
library(timeDate)
source("Load_Savona_Campus.R")
source("PV_Production_Savona_Campus.R")
source("energycost.R")
source("simulateData.R")
source("runningpeaks.R")
source("Kostologish.R")
source("heuristics.R")

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
