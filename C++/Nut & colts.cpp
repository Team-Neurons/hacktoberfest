#include<bits/stdc++.h>
using namespace std;

int main(){
    
    int n;
    cin>>n;
    
    char postion[]={'!','#','$','%','&','*','@','^','~'};

    while(n--){
        int number;
        cin>>number;

        char a;
        map<char,int> gquiz1;


        gquiz1.insert(pair<char, int>('!', 0));
        gquiz1.insert(pair<char, int>('#', 0));
        gquiz1.insert(pair<char, int>('$', 0));
        gquiz1.insert(pair<char, int>('%', 0));
        gquiz1.insert(pair<char, int>('&', 0));
        gquiz1.insert(pair<char, int>('*', 0));
        gquiz1.insert(pair<char, int>('@', 0));
        gquiz1.insert(pair<char, int>('^', 0));
        gquiz1.insert(pair<char, int>('~', 0));

        for(int i =0 ; i< number ;i++){ 
            cin>>a;
            gquiz1[a]=1;
        }
        
        char garbage;
        
        for(int i =0 ; i< number ;i++){
            cin>>garbage;
        }
        
        map<char , int>::iterator itr;
        
        for(itr=gquiz1.begin();itr!=gquiz1.end();++itr){
            if(itr->second==1){
            cout<<itr->first<<" ";
            }
        }
        
        cout<<endl;
        
        for(itr=gquiz1.begin();itr!=gquiz1.end();++itr){
            if(itr->second==1){
                cout<<itr->first<<" ";
            }
        }
        
        cout<<endl;
    }
    return 0;   
}