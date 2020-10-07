#include<iostream>
using namespace std;
int main(){
	int i,n;
	long int fact=1;
	cout<<"Enter the number";
	cin>>n;
	for(i=1;i<=n;i++){
		fact=fact*i;
	}
	cout<<"factorial is "<<fact;
	return 0;
}
