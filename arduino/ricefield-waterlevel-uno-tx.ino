//pengirim(arduino uno)
#include <SoftwareSerial.h>
#include <Servo.h>

SoftwareSerial mySerial(10, 11); // RX, TX

Servo myservo;

const int trigPin = 8;
const int echoPin = 9;

long duration;
int distance;

void setup() {
  myservo.attach(7);
  Serial.begin(9600);
  mySerial.begin(9600);

  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
}

void loop() {
  scan();
  mySerial.write(distance);
  Serial.print("Ketinggian Air = ");
  Serial.print(distance);
  Serial.println("cm dari SET POINT");
  if (distance < 20) {
    Serial.println("Palang Irigasi Tertutup");
    myservo.write(60);
  }
  else {
    Serial.println("Palang Irigasi Terbuka");
    myservo.write(20);
  }
  delay (2000);
  Serial.println();
}

void scan() {
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  duration = pulseIn(echoPin, HIGH);
  distance = duration * 0.034 / 2;
}
