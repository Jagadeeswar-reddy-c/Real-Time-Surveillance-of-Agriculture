#include <SoftwareSerial.h>
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>

#define RX 4
#define TX 3
#define POWER_PIN 8
#define SIGNAL_PIN A4
#define sensorPower 7
#define sensorPin A0
#define DHTPIN 2
#define DHTTYPE DHT11 // DHT 11

String AP = "BBB"; // AP NAME
String PASS = "00000000"; // AP PASSWORD
String API = "PH512TJQHF6T577L"; // Write API KEY
String HOST = "api.thingspeak.com";
String PORT = "80";
String field1 = "field1";
String field2 = "field2";
String field3 = "field3";
String field4 = "field4";
String field5 = "field5";
String field6 = "field6";

int countTrueCommand;
int countTimeCommand; 
boolean found = false; 
SoftwareSerial esp8266(RX,TX); 

DHT_Unified dht(DHTPIN, DHTTYPE);
uint32_t delayMS;

void setup() {
  Serial.begin(9600);
  esp8266.begin(115200);
  
  pinMode(POWER_PIN, OUTPUT);
  digitalWrite(POWER_PIN, LOW);
  
  pinMode(sensorPower, OUTPUT);
  digitalWrite(sensorPower, LOW);
  
  dht.begin();
  sensor_t sensor;
  delayMS = sensor.min_delay / 1000;
  
  sendCommand("AT", 5, "OK");
  sendCommand("AT+CWMODE=1", 5, "OK");
  sendCommand("AT+CWJAP=\"" + AP + "\",\"" + PASS + "\"", 20, "OK");
}

void loop() {
  int moisture = getMoistureData();
  int waterLevel = getWaterSensorData();
  float temperature = getTemperatureData();
  float humidity = getHumidityData();
  
  String getData = "GET /update?api_key=" + API + "&" + field1 + "=" + String("AAT001") + "&" + field2 + "=" + String(humidity) + "&" + field3 + "=" + String(moisture) + "&" + field4 + "=" + String(waterLevel) + "&" + field5 + "=" + String(temperature) + "&" + field6 + "=" + String("01");
  sendCommand("AT+CIPMUX=1", 5, "OK");
  sendCommand("AT+CIPSTART=0,\"TCP\",\"" + HOST + "\"," + PORT, 15, "OK");
  sendCommand("AT+CIPSEND=0," + String(getData.length() + 4), 4, ">");
  esp8266.println(getData);
  delay(1500);
  countTrueCommand++;
  sendCommand("AT+CIPCLOSE=0", 5, "OK");
  
  delay(10000);
}

int getMoistureData() {
  digitalWrite(sensorPower, HIGH);
  delay(100);
  int moisture = analogRead(sensorPin);
  digitalWrite(sensorPower, LOW);
  Serial.print("Soil Moisture: ");
  Serial.println(moisture);
  return moisture;
}

int getWaterSensorData() {
  digitalWrite(POWER_PIN, HIGH);
  delay(100);
  int waterLevel = analogRead(SIGNAL_PIN);
  digitalWrite(POWER_PIN, LOW);
  Serial.print("Water Sensor value: ");
  Serial.println(waterLevel);
  return waterLevel;
}

float getTemperatureData() {
  sensors_event_t event;
  dht.temperature().getEvent(&event);
  Serial.print(F("Temperature: "));
  Serial.print(event.temperature);
  Serial.println(F("°C"));
  return event.temperature;
}

float getHumidityData() {
  sensors_event_t event;
  dht.humidity().getEvent(&event);
  Serial.print(F("Humidity: "));
  Serial.print(event.relative_humidity);
  Serial.println(F("%"));
  return event.relative_humidity;
}

void sendCommand(String command, int maxTime, char readReplay[]) {
  Serial.print(countTrueCommand);
  Serial.print(". at command => ");
  Serial.print(command);
  Serial.print(" ");
  while (countTimeCommand < (maxTime * 1)) {
    esp8266.println(command);
    if (esp8266.find(readReplay)) {
      found = true;
      break;
    }
    countTimeCommand++;
  }

  if (found == true) {
    Serial.println("OYI");
    countTrueCommand++;
    countTimeCommand = 0;
  } else {
    Serial.println("Fail");
    countTrueCommand = 0;
    countTimeCommand = 0;
  }
  found = false;
}
