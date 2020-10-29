#include<iostream>
using namespace std;
double power(double,int=2);
void main()
{
 clrscr();
 int p;
 double n,y;
 cout<<"Enter the number : ";
 cin>>n;
 cout<<"Enter the value of p in n^p : ";
 cin>>p;
 y=power(n,p);
 cout<<"The n^p is : "<<y;
 y=power(n);
 cout<<"\nThe result without passing the value of p is : "<<y<<" which is p^2";
 getch();
}
double power(double x, int z)
{
 double a=1;
 int i;
 for(i=0;i<z;i++)
 {
 a=a*x;
 };
 return(a);
}
