/* 3x3x3 LED Cube
  Connection Setup:
  Columns
  [(x,y)-Pin]
  (0,0)-10
  (0,1)-9
  (0,2)-8
  (1,0)-7
  (1,1)-6
  (1,2)-5
  (2,0)-4
  (2,1)-3
  (2,2)-2
  Layers
  [layer-Pin]
  a-11
  b-12
  c-13
*/ 
int column[9] = { 10, 9, 8, 7, 6, 5, 4, 3, 2};
int layer[3] = { 13, 12, 11};

int time = 250;
int i;
int j;
int k;

void setup() {
  for (i = 0; i < 9; i++) {
    pinMode(column[i], OUTPUT);
  }

  for (i = 0; i < 3; i++) {
    pinMode(layer[i], OUTPUT);
  }
}

void loop() {
  turnEverythingOn();
  flickerOn();
  turnEverythingOff();
  delay(time);

  turnOnAndOffAllByLayerUpAndDownNotTimed();
  layerstompUpAndDown();
  turnOnAndOffAllByColumnSideways();
}

void turnEverythingOn() {
  for (i = 0; i < 9; i++) {
    digitalWrite(column[i], HIGH);
  }
  for (i = 0; i < 3; i++) {
    digitalWrite(layer[i], HIGH);
  }
}
void turnEverythingOff() {
  for (i = 0; i < 9; i++) {
    digitalWrite(column[i], LOW);
  }
  for (i = 0; i < 3; i++) {
    digitalWrite(layer[i], LOW);
  }
}
void columnsOn() {
  for (i = 0; i < 9; i++) {
    digitalWrite(column[i], HIGH);
  }
}
void columnsOff() {
  for (i = 0; i < 9; i++) {
    digitalWrite(column[i], LOW);
  }
}
void layersOn() {
  for (i = 0; i < 3; i++) {
    digitalWrite(layer[i], HIGH);
  }
}
void layersOff() {
  for (i = 0; i < 3; i++) {
    digitalWrite(layer[i], LOW);
  }
}
void flickerOn() {
  j = 150;
  while (j != 0) {
    turnEverythingOff();
    delay(j);
    turnEverythingOn();
    delay(j); j = j - 5;
  }
}
void flickerOff() {
  turnEverythingOff();
  for (k = 0;
       k != 150;
       k = k + 5) {
    turnEverythingOn();
    delay(k + 50);
    turnEverythingOff();
    delay(k);
  }
}
void turnOnAndOffAllByLayerUpAndDownNotTimed()
{ int x = 75;
  for (int i = 5;
       i != 0;
       i--) {
    turnEverythingOn();
    for (int i = 3; i != 0; i--)
    { digitalWrite(layer[i - 1], LOW);
      delay(x);
    }
    for (int i = 0;
         i < 3; i++) {
      digitalWrite(layer[i], HIGH);
      delay(x);
    }
    for (int i = 0;
         i < 3; i++) {
      digitalWrite(layer[i], LOW);
      delay(x);
    }
    for (int i = 3;
         i != 0;
         i--) {
      digitalWrite(layer[i - 1], HIGH);
      delay(x);
    }
  }
}

void layerstompUpAndDown() {
  int x = 75;
  for (int i = 0;
       i < 3; i++) {
    digitalWrite(layer[i], HIGH);
  }
  for (int y = 0;
       y < 5; y++) {
    for (int count = 0;
         count < 1;
         count++) {
      for (int i = 0;
           i < 3; i++) {
        digitalWrite(layer[i], LOW);
        delay(x);
        digitalWrite(layer[i], HIGH);
      }
      for (int i = 3;
           i != 0; i--) {
        digitalWrite(layer[i - 1], LOW);
        delay(x);
        digitalWrite(layer[i - 1], HIGH);
      }
    }
    for (int i = 0;
         i < 3; i++) {
      digitalWrite(layer[i], LOW);
      delay(x);
    }
    for (int i = 3;
         i != 0; i--) {
      digitalWrite(layer[i - 1], HIGH);
      delay(x);
    }
  }
}

void turnOnAndOffAllByColumnSideways() {
  int x = 75;
  layersOn();
  for (int y = 0; y < 3; y++) {
    for (int i = 0; i < 9; i++) {
      digitalWrite(column[i], HIGH);
      delay(x);
    }

    for (int i = 0;
         i < 9; i++) {
      digitalWrite(column[i], LOW);
      delay(x);
    }
  }
}
