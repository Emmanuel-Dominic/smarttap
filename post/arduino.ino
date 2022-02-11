//---------------------------------------------------------------
#include <sdtio.h>;
#include <sdtlib.h>;
#include <Wire.h>;
#include <LiquidCrystal_I2C.h>;
#include <SPI.h>;
#include <MFRC522.h>;
#include "NewPing.h";
#include <OneWire.h>;
#include <Servo.h>;
#include <DallasTemperature.h>;
#include <Wire.h>;
#include "SoftwareSerial.h";
#include <SoftwareSerial.h>;


//Define analog input
#define ANALOG_IN_PIN A6;

#define SS_PIN 53;
#define RST_PIN 5;

// Hook up HC-SR04 with Trig to Arduino Pin 9, Echo to Arduino pin 10
#define TRIGGER_PIN 15;
#define ECHO_PIN 14;
// Maximum distance we want to ping for (in centimeters).
#define MAX_DISTANCE 400;

#define TdsSensorPin A1;
#define VREF 5.0;  // analog reference voltage(Volt) of the ADC
#define SCOUNT 30; // sum of sample point

#define ONE_WIRE_BUS 12;

// Floats for ADC voltage & Input voltage
float adc_voltage = 0.0;
float in_voltage = 0.0;

// Floats for resistor values in divider (in ohms)
float R1 = 30000.0;
float R2 = 7500.0;

// Float for Reference Voltage
float ref_voltage = 5.0;

// Integer for ADC value
int adc_value = 0;

// Set the LCD address to 0x27 for a 16 chars and 2 line display
LiquidCrystal_I2C lcd(0x27, 16, 2);

int state = 1;
const float OffSet = 0.483;

float V, P;
int pushButton2 = 21;

MFRC522 mfrc522(SS_PIN, RST_PIN); // Create MFRC522 instance.
byte readCard[4];
String MasterTag = "5C6EF137"; // REPLACE this Tag ID with your Tag ID!!!
String tagID = "";

//............................
String rfid;

// NewPing setup of pins and maximum distance.
NewPing sonar(TRIGGER_PIN, ECHO_PIN, MAX_DISTANCE);
float duration, distance;
int buzer = 38;
//...........flowsensor1.................................
byte sensorInterrupt = 0; // 0 = digital pin 2
byte sensorP = 9;
float calibrationFactor = 4.5;
volatile byte pulseCount;
float flowRate;
unsigned int flowMilliLitres;
unsigned long totalMilliLitres;
unsigned long oldTime;
//............flowsensor2...................................
//...........................................................
byte sensorInterrupt1 = 0; // 0 = digital pin 2
byte sensorP1 = 2;
float calibrationFactor1 = 4.5;
volatile byte pulseCount1;
float flowRate1;
unsigned int flowMilliLitres1;
unsigned long totalMilliLitres1;
unsigned long oldTime1;

//......................temp.................................
String security;

OneWire oneWire(ONE_WIRE_BUS);

DallasTemperature sensors(&oneWire);

float Celcius = 0;
float Fahrenheit = 0;

//................................................................
int sensorPin = A2;
float volt;
float ntu;
int analogBuffer[SCOUNT]; // store the analog value in the array, read from ADC
int analogBufferTemp[SCOUNT];
int analogBufferIndex = 0, copyIndex = 0;
float averageVoltage = 0, tdsValue = 0, temperature = 25;

Servo myservo;  // create servo object to control client servo
Servo myservo1; // create servo object to control sub client servo
// twelve servo objects can be created on most boards
//..................................

int pos = 0;         // variable to store the servo position
int pushButton = 13; // this is proximity sensor(pir)

SoftwareSerial esp(11, 10); // RX3, TX2
String uri_short = "/smarttap/post/insert.php?data=1";
String uri;

void setup(){

  pinMode(buzer, OUTPUT);
  pinMode(pushButton2, INPUT_PULLUP);
  sensors.begin();
  myservo.attach(8);
  myservo.write(20);
  myservo.attach(7);
  myservo1.write(20);
  myservo.detach();
  myservo1.detach();

  pinMode(pushButton, INPUT);

  pinMode(8, INPUT);
  // myservo.write(0);
  esp.begin(9600);
  Serial.begin(9600);
  connectWifi();
  Serial.begin(9600);
  SPI.begin();        // Initiate  SPI bus
  mfrc522.PCD_Init(); // Initiate MFRC522
  Serial.println("Approximate your card to the reader...");
  Serial.println();
  pinMode(TdsSensorPin, INPUT);
  //....................................................
  pinMode(sensorP, INPUT);
  digitalWrite(sensorP, HIGH);
  pulseCount = 0;
  flowRate = 0.0;
  flowMilliLitres = 0;
  totalMilliLitres = 0;
  oldTime = 0;
  attachInterrupt(sensorInterrupt, pulseCounter, FALLING);
  //....................2....................................................
  pinMode(sensorP1, INPUT);
  digitalWrite(sensorP1, HIGH);
  pulseCount1 = 0;
  flowRate1 = 0.0;

  flowMilliLitres1 = 0;
  totalMilliLitres1 = 0;
  oldTime1 = 0;
  attachInterrupt(sensorInterrupt1, pulseCounter, FALLING);
  lcd.begin();
  lcd.backlight();
  lcd.print("Smart, Tap!");
  lcd.setCursor(0, 1);
  lcd.print("By");
  delay(5000);
  lcd.clear();
  lcd.print("Josephine");
  lcd.setCursor(0, 1);
  lcd.print("Malvin & Eric");
  delay(5000);
  lcd.clear();
  lcd.print("Supervised By");
  lcd.setCursor(0, 1);
  lcd.print("Mr Ben");
  delay(5000);
}

void loop(){
  int meter_no = 2026550143;
  int ph = 8.2;
  int temp = 25;
  int battery_life = 89;
  int push_button = 1;
  voltage();
  pressure();
  security1();
  rfid1();
  flowsensor();
  flowsensor1();
  temperatures();
  turbidity();
  tds();

  if ((ph > 6.4 && ph < 8.6) && (ntu > 5) && tdsValue > 600){
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Water Safe");
    myservo1.attach(7);
    myservo1.write(0); //open
    myservo.attach(8);
    myservo.write(0); //open client
    digitalWrite(38, 0);
    lcd.setCursor(2, 3);
    lcd.scrollDisplayRight();
    lcd.print("Units ");
    lcd.print(totalMilliLitres);
  }else{
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("water not Safe");
    lcd.setCursor(2, 3);
    lcd.scrollDisplayRight();
    lcd.print("Units: ");
    lcd.print(totalMilliLitres);
    // digitalWrite(38,1);
  }

  uri = uri_short + "&node1=" + ph + "&node2=" + ntu + "&node3=" + tdsValue + "&node4=" + Celcius + "&node5=" + security + "&node6=" + rfid + "&node7=" + totalMilliLitres + "&node8=" + totalMilliLitres1 + "&node9=" + meter_no + "&node10=" + P + "&node11=" + in_voltage;
  Serial.println(uri);
  httppost();
}

void voltage(){
  // Read the Analog Input
  adc_value = analogRead(ANALOG_IN_PIN);

  // Determine voltage at ADC input
  adc_voltage = (adc_value * ref_voltage) / 1024.0;

  // Calculate voltage at divider input
  in_voltage = adc_voltage / (R2 / (R1 + R2));
  in_voltage = map(in_voltage, 0, 13, 0, 100);
  // Print results to Serial Monitor to 2 decimal places
  Serial.print("Input Voltage = ");
  Serial.print(in_voltage);
  Serial.println("%");
  // Short delay
  //delay(500);
}

void pressure(){
  //Connect sensor to Analog 0
  V = analogRead(A7) * 5.00 / 1024; //Sensor output voltage
  P = (V - OffSet) * 400;           //Calculate water pressure

  Serial.print("Voltage:");
  Serial.print(V, 3);
  Serial.println("V");

  Serial.print("..................... Pressure:");
  Serial.print(P, 1);
  Serial.println(" KPa");
  Serial.println();

  //delay(500);
}

void security1(){
  //String security;
  int ldr = analogRead(A4);
  int buttonState2 = digitalRead(pushButton2);
  Serial.print(ldr);
  Serial.println("............ldr");
  Serial.print(buttonState2);
  Serial.println("...................................................not tempered.............................................................");
  if (ldr < 490 && buttonState2 == 0){
    security = "Notamper";
    //digitalWrite(buzer,0);
  }else{
    Serial.println("...................................................tempered.............................................................");
    security = "tampered";
    //digitalWrite(buzer,1);
  }
}

void temperatures(){
  sensors.requestTemperatures();
  Celcius = sensors.getTempCByIndex(0);
  Fahrenheit = sensors.toFahrenheit(Celcius);
  Serial.print(" C ............................................................................... ");
  Serial.println(Celcius);
  Serial.print(" F  ");
  Serial.println(Fahrenheit);
  //delay(1000);
}

void rfid1(){
  int buttonState = digitalRead(pushButton);
  getID();
  Serial.print("-----state-- = ");
  Serial.println(state);

  if (state == 1 || state == 2){

    Serial.println("main valve open");
    myservo.attach(8);
    myservo.write(90); //open client
    //myservo1.attach(7);
    //myservo1.write(90);//open subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (state == 3){

    Serial.println("main valve close");
    myservo.attach(8);
    myservo.write(0); //close client
    //myservo1.attach(7);
    //myservo1.write(90);//open subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (tagID == "69B4375A" && buttonState == 0 && state == 1){

    Serial.println("money right tag near");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(90); //open subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (tagID == "69B4375A" && buttonState == 0 && state == 2){

    Serial.println("no money right tag near");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (tagID == "69B4375A" && buttonState == 0 && state == 3){

    Serial.println("no money at all right tag near");
    //myservo.attach(8);
    //myservo.write(0); //close client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (!tagID == "69B4375A" && buttonState == 0 && state == 1){

    Serial.println("wrong tag money near");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (!tagID == "69B4375A" && buttonState == 0 && state == 2){

    Serial.println("wrong tag no moneysubclient near");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (!tagID == "69B4375A" && buttonState == 0 && state == 3){

    Serial.println("wrong tag poor near");
    //myservo.attach(8);
    //myservo.write(0); //close client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (tagID == "69B4375A" && buttonState == 1 && state == 1){

    Serial.println("right tag money not near");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (tagID == "69B4375A" && buttonState == 0 && state == 2){

    Serial.println("not near wrong tag no money subclient");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (tagID == "69B4375A" && buttonState == 0 && state == 3){

    Serial.println("wrong tag not near poor");
    //myservo.attach(8);
    //myservo.write(0); //close client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (!tagID == "69B4375A" && buttonState == 1 && state == 1){

    Serial.println("unkown tag and client not near");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (!tagID == "69B4375A" && buttonState == 1 && state == 2){

    Serial.println("no credit and unkown tag");
    //myservo.attach(8);
    //myservo.write(90); //open client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }

  if (!tagID == "69B4375A" && buttonState == 1 && state == 3){

    Serial.println("poor clients");
    //myservo.attach(8);
    //myservo.write(0); //close client
    myservo1.attach(7);
    myservo1.write(0); //close subclient
    // delay(1);
    //myservo.detach();
    //rfid="69B4375A";
  }
}

boolean getID(){
  // Getting ready for Reading PICCs
  if (!mfrc522.PICC_IsNewCardPresent()){
    //If a new PICC placed to RFID reader continue
    return false;
  }
  
  if (!mfrc522.PICC_ReadCardSerial()){
    //Since a PICC placed get Serial and continue
    return false;
  }

  tagID = "";
  for (uint8_t i = 0; i < 4; i++){
    // The MIFARE PICCs that we use have 4 byte UID
    //readCard[i] = mfrc522.uid.uidByte[i];
    tagID.concat(String(mfrc522.uid.uidByte[i], HEX)); // Adds the 4 bytes in a single String variable
  }

  tagID.toUpperCase();
  mfrc522.PICC_HaltA(); // Stop reading
  return true;
}

void flowsensor(){
  
  // Only process counters once per second
  if ((millis() - oldTime) > 1000){
    detachInterrupt(sensorInterrupt);

    flowRate = ((1000.0 / (millis() - oldTime)) * pulseCount) / calibrationFactor;
    oldTime = millis();

    flowMilliLitres = (flowRate / 60) * 1000;

    // Add the millilitres passed in this second to the cumulative total
    totalMilliLitres += flowMilliLitres;

    unsigned int frac;

    // Print the flow rate for this second in litres / minute
    Serial.print("Flow rate: ");
    Serial.print(int(flowRate)); // Print the integer part of the variable
    Serial.print("L/min");
    Serial.print("\t"); // Print tab space

    // Print the cumulative total of litres flowed since starting
    Serial.print("*****************************************************************Output Liquid Quantity: ");
    Serial.print(totalMilliLitres);
    Serial.println("mL");
    Serial.print("\t"); // Print tab space
    Serial.print(totalMilliLitres / 1000);
    Serial.print("L");
    // Reset the pulse counter so we can start incrementing again
    pulseCount = 0;

    // Enable the interrupt again now that we've finished sending output
    attachInterrupt(sensorInterrupt, pulseCounter, FALLING);
  }
}

void flowsensor1(){
 
  // Only process counters once per second
  if ((millis() - oldTime1) > 1000){
    detachInterrupt(sensorInterrupt1);

    flowRate1 = ((1000.0 / (millis() - oldTime1)) * pulseCount1) / calibrationFactor1;
    oldTime1 = millis();

    flowMilliLitres = (flowRate1 / 60) * 1000;

    // Add the millilitres passed in this second to the cumulative total
    totalMilliLitres1 += flowMilliLitres1;

    unsigned int frac1;

    // Print the flow rate for this second in litres / minute
    Serial.print("Flow rate1: ");
    Serial.print(int(flowRate1)); // Print the integer part of the variable
    Serial.print("L/min1");
    Serial.print("\t"); // Print tab space

    // Print the cumulative total of litres flowed since starting
    //delay(5000);
    Serial.print("***********************************************************Output Liquid Quantity1: ");
    Serial.print(totalMilliLitres1);
    Serial.println("mL1");
    Serial.print("\t"); // Print tab space
    Serial.print(totalMilliLitres1 / 1000);
    Serial.print("L1");

    // Reset the pulse counter so we can start incrementing again
    pulseCount1 = 0;

    // Enable the interrupt again now that we've finished sending output
    attachInterrupt(sensorInterrupt1, pulseCounter, FALLING);
  }
}

/*
Insterrupt Service Routine
 */
void pulseCounter(){
  // Increment the pulse counter
  pulseCount++;
}

void turbidity(){

  volt = 0;
  for (int i = 0; i < 800; i++){
    volt += ((float)analogRead(sensorPin) / 1023) * 5;
  }

  volt = volt / 800;
  volt = round_to_dp(volt, 2);
  
  if (volt < 2.5){
    ntu = 3000;
  }else{
    ntu = (-1120.4 * square(volt) + 5742.3 * volt - 4353.8) / 1000;
  }

  Serial.print(ntu);
  Serial.println("...................... NTU");
  //delay(10);
}

float round_to_dp(float in_value, int decimal_place){
  float multiplier = powf(10.0f, decimal_place);
  in_value = roundf(in_value * multiplier) / multiplier;
  return in_value;
}

void tds(){
  static unsigned long analogSampleTimepoint = millis();
  //every 40 milliseconds,read the analog value from the ADC
  if (millis() - analogSampleTimepoint > 40U){
    analogSampleTimepoint = millis();
    analogBuffer[analogBufferIndex] = analogRead(TdsSensorPin); //read the analog value and store into the buffer
    analogBufferIndex++;
    if (analogBufferIndex == SCOUNT)
      analogBufferIndex = 0;
  }
  
  static unsigned long printTimepoint = millis();
  if (millis() - printTimepoint > 800U){
    printTimepoint = millis();

    for (copyIndex = 0; copyIndex < SCOUNT; copyIndex++)
      analogBufferTemp[copyIndex] = analogBuffer[copyIndex];
    averageVoltage = getMedianNum(analogBufferTemp, SCOUNT) * (float)VREF / 1024.0;                                                                                                  // read the analog value more stable by the median filtering algorithm, and convert to voltage value
    float compensationCoefficient = 1.0 + 0.02 * (temperature - 25.0);                                                                                                               //temperature compensation formula: fFinalResult(25^C) = fFinalResult(current)/(1.0+0.02*(fTP-25.0));
    float compensationVolatge = averageVoltage / compensationCoefficient;                                                                                                            //temperature compensation
    tdsValue = (133.42 * compensationVolatge * compensationVolatge * compensationVolatge - 255.86 * compensationVolatge * compensationVolatge + 857.39 * compensationVolatge) * 0.5; //convert voltage value to tds value
    //Serial.print("voltage:");
    //Serial.print(averageVoltage,2);
    //Serial.print("V   ");
    Serial.print("TDS Value:...................");
    Serial.print(tdsValue, 0);
    Serial.println("ppm");
  }
}

int getMedianNum(int bArray[], int iFilterLen){
  int bTab[iFilterLen];
  for (byte i = 0; i < iFilterLen; i++)
    bTab[i] = bArray[i];
  int i, j, bTemp;
  for (j = 0; j < iFilterLen - 1; j++){
    for (i = 0; i < iFilterLen - j - 1; i++){
      if (bTab[i] > bTab[i + 1]){
        bTemp = bTab[i];
        bTab[i] = bTab[i + 1];
        bTab[i + 1] = bTemp;
      }
    }
  }

  if ((iFilterLen & 1) > 0)
    bTemp = bTab[(iFilterLen - 1) / 2];
  else
    bTemp = (bTab[iFilterLen / 2] + bTab[iFilterLen / 2 - 1]) / 2;
  return bTemp;
}

void connectWifi(){
  String ssid = "smarttap";
  String password = "password";

  Serial.println("Trying to connect");
  esp.println("AT+CWMODE=3");
  delay(100);
  String cmd = "AT+CWJAP=\"" + ssid + "\",\"" + password + "\"";
  esp.println(cmd);
  esp.println("AT");
  String inData = esp.readStringUntil('\r');
  Serial.println("Got reponse from ESP8266: " + inData);

  delay(4000);
  if (esp.find("OK")){
    Serial.println("Connected!");
  }else{
    // connectWifi();
    Serial.println("Cannot connect to wifi");
  }
}

void httppost(){
  String data;

  String server = "192.168.43.79"; //ip adress of
  Serial.println("Starting Conn");
  esp.println("AT+CIPSTART=\"TCP\",\"" + server + "\",80"); //start a TCP connection.

  if (esp.find("OK")){
    Serial.println("TCP connection ready");
  }

  delay(1000);

  String postRequest =
      "POST " + uri +
      " HTTP/1.0\r\n" +
      "Host: " + server + "\r\n" +
      "Accept: *" + "/" + "*\r\n" +
      "Content-Length: " + data.length() + "\r\n" +
      "Content-Type: application/x-www-form-urlencoded\r\n" +
      "\r\n" + data;

  String sendCmd = "AT+CIPSEND="; //determine the number of caracters to be sent.

  esp.print(sendCmd);

  esp.println(postRequest.length());

  delay(500);

  if (esp.find(">")){
    Serial.println("Sending..");
    esp.print(postRequest);

    if (esp.find("SEND OK")){
      Serial.println("Packet sent");

      while (esp.available()){

        String tmpResp = esp.readString();
        Serial.println("tmpResp=");

        Serial.println(tmpResp);
        if (tmpResp.indexOf("allclosed") > -1){
          Serial.println("allclosed..........");
          state = 3;
        }

        if (tmpResp.indexOf("clientopenSubclientclose") > -1){
          Serial.println("clientopenSubclientclose..........");
          state = 2;
        }

        if (tmpResp.indexOf("allservoopen") > -1){
          Serial.println("allservoopen..........");
          state = 1;
        }
      }

      esp.println("AT+CIPCLOSE"); // close the connection
    }
  }
}
