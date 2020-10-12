#include <bits/stdc++.h>
using namespace std;

int main() {
	//code
	int t;
	cin>>t;
	while(t--){
	    int n;  cin>>n;
	    int arr[n];
	    for(int i=0;i<n;i++){
	        cin>>arr[i];
	    }
	    stack <int> s;
      int arr1[n]; 
      
        for (int i = n - 1; i >= 0; i--)  
        { 
            while (!s.empty() && s.top() <= arr[i]) 
                s.pop(); 
           
            if (s.empty())  
                arr1[i] = -1;          
            else 
                arr1[i] = s.top();         
      
            s.push(arr[i]); 
        }
        
	    for(int i=0;i<n;i++){
	        cout<<arr1[i]<<" ";
	    }
	    cout<<endl;
	}
	return 0;
}
