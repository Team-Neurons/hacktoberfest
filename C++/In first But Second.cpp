#include<bits/stdc++.h>
using namespace std;

int main(){
    
    int t;
    cin>>t;
    
    while(t--){
        
        long long int m,n,x;
        int i;
        
        unordered_map<long long int,long long int>m1;
        cin>>m>>n;
        
        long long int arr[m];
        
        for(i=0;i<m;i++){
            cin>>arr[i];
            m1[arr[i]] = 0;
        }
        
        for(i=0;i<n;i++){
            cin>>x;
            m1[x] = 1;
        }
        
        for(i=0;i<m;i++){
            if(m1[arr[i]] == 0){
                cout<<arr[i]<<" ";
            }
        }
        
        cout<<"\n";
    }
    
    return 0;
    
}