#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>
#include <SoftwareSerial.h>

#define POWER_PIN  8
#define SIGNAL_PIN A4
#define sensorPower 7
#define sensorPin A0
#define DHTPIN 4
#define DHTTYPE    DHT11     // DHT 11

DHT_Unified dht(DHTPIN, DHTTYPE);
uint32_t delayMS;
int value = 0; // variable to store the sensor value

SoftwareSerial mySerial(3, 2); // SIM800L Tx & Rx connected to Arduino pins #3 & #2

void setup() {
  Serial.begin(9600);
  pinMode(POWER_PIN, OUTPUT);   // configure D7 pin as an OUTPUT
  digitalWrite(POWER_PIN, LOW); // turn the sensor OFF
  pinMode(sensorPower, OUTPUT);
  digitalWrite(sensorPower, LOW); // Initially keep the sensor OFF
  dht.begin();
  sensor_t sensor;
  delayMS = sensor.min_delay / 1000;

  mySerial.begin(9600);
  Serial.println("Initializing..."); 
  delay(1000);
  mySerial.println("AT"); // Handshake test, should return "OK" on success
  updateSerial();
  mySerial.println("AT+CMGF=1"); // Configuring TEXT mode
  updateSerial();
}

void loop() {
  digitalWrite(sensorPower, HIGH); // turn the soil moisture sensor ON
  delay(100);                        // wait 10 milliseconds
  int moisture = analogRead(sensorPin); // read the analog value from soil moisture sensor
  digitalWrite(sensorPower, LOW);  // turn the soil moisture sensor OFF

  Serial.print("Soil Moisture: ");
  Serial.println(moisture);
  
  digitalWrite(POWER_PIN, HIGH);  // turn the water sensor ON
  delay(100);                      // wait 10 milliseconds
  value = analogRead(SIGNAL_PIN); // read the analog value from water sensor
  digitalWrite(POWER_PIN, LOW);   // turn the water sensor OFF

  Serial.print("Water Sensor value: ");
  Serial.println(value);
  
  sensors_event_t event;
  dht.temperature().getEvent(&event);
  Serial.print(F("Temperature: "));
  Serial.print(event.temperature);
  Serial.println(F("°C"));
  dht.humidity().getEvent(&event);
  Serial.print(F("Humidity: "));
  Serial.print(event.relative_humidity);
  Serial.println(F("%"));

  // Sending SMS
  mySerial.println("AT+CMGS=\"+919553584470\""); // Change ZZ with the country code and xxxxxxxxxxx with the phone number to send an SMS to
  updateSerial();
  mySerial.print("Soil Moisture: ");
  mySerial.println(moisture);
  mySerial.print("Water Sensor value: ");
  mySerial.println(value);
  mySerial.print("Temperature: ");
  mySerial.print(event.temperature);
  mySerial.println("°C");
  mySerial.print("Humidity: ");
  mySerial.print(event.relative_humidity);
  mySerial.println("%");
  mySerial.write(26); // Send the CTRL+Z character to terminate the SMS

  delay(10000);
}

void updateSerial() {
  delay(500);
  while (Serial.available()) {
    mySerial.write(Serial.read()); // Forward data from Serial to Software Serial Port
  }
  while (mySerial.available()) {
    Serial.write(mySerial.read()); // Forward data from Software Serial to Serial Port
  }
}
