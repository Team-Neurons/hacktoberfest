    vector<int>maxLeft(vector<int>v){
        vector<int>left;
        left.push_back(v[0]);
        for(int i=1;i<v.size();i++){
            left.push_back(std::max(left[i-1],v[i]));
        }
        return left;
    }
    vector<int>maxRight(vector<int>v){
        int n=v.size();
        vector<int>right;
        for(int i=0;i<n;i++){
            right.push_back(0);
        }
        right[n-1]=v[n-1];
        for(int i=n-2;i>=0;i--){
            right[i]=std::max(right[i+1],v[i]);
        }
        return right;
    }
public:
    int trap(vector<int>& height) {
        int n=height.size();
        if(n==0){
            return 0;
        }
        vector<int>right=maxRight(height);
        vector<int>left=maxLeft(height);
        vector<int>cont;
        for(int i=0;i<n;i++){
            int min=std::min(left[i],right[i]);
            cont.push_back(min-height[i]);
        }
        int sum=0;
        return accumulate(cont.begin(),cont.end(),sum);
    }
