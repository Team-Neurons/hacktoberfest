#include<iostream>
using namespace std;
int main(){
	int n[1000],i=0,size;
	cout<<"Enter the size of array";
	cin>>size;
	cout<<"Enter the values";
	for(i=0;i<size;i++){
		cin>>n[i];
	}
	cout<<"Output :- \n";
for(i=0;i<size;i++){
	if(n[i]>=0){
		cout<<n[i]<<endl;
	}
	else{
		break;
	}
}
	return 0;
}
