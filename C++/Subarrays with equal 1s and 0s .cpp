long long nctwo(long long int n)
{
    return (n*(n-1))/2;
}
long long int countSubarrWithEqualZeroAndOne(int a[], int n)
{
    unordered_map<int,int>m;
    m[0]=1;
    int sum=0,i;
    long long int ans=0;
    for(i=0;i<n;i++)
    {
        if(a[i]==0)
        sum--;
        else
        sum++;
        m[sum]++;
    }
    for(auto itr=m.begin();itr!=m.end();itr++)
    ans+=nctwo(itr->second);
    return ans;
}