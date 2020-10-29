vector<int> sortByFreq(int arr[],int n){
    
    map<int,int>mp1;
    map<int,vector<int>>mp2;
    
    for(int i=0;i<n;i++){
        mp1[arr[i]]++;
    }
        
    for(auto x:mp1){
        mp2[x.second].push_back(x.first);
    }
    
    vector<int>vec;
    
    for(auto x=mp2.rbegin();x!=mp2.rend();x++){
        for(auto y:x->second){
            for(int i=0;i<x->first;i++){
                vec.push_back(y);
            }
        }
    }
    
    return vec;
}