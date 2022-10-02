#include<stdio.h>
#include<graphics.h>
#include<conio.h>
#include<dos.h>

void main(){
    int gd=DETECT, gm; //gd=DETECT detects best available graphics driver, gm = graphics mode.
    initgraph(&gd, &gm, "C:\\TurboC3\\BGI"); //for initializing graph mode
    line(100,100,200,200); //draw a line segment
    getch();
}