#include<iostream>
using namespace std;

double power(double a,int b = 2)
{
    double x=1;
    for(int i=1;i<=b;i++)
         x=x*a;

      return x;   
}

int main()
{
    int p;
    double n,r;
    cout<<" Enter number: ";
    cin>>n;
    cout<<"Enter exponent: ";
    cin>>p;
    r = power(n,p);
    cout<<"\nresult without passing exponent is "<<r;

    return 0;
}
