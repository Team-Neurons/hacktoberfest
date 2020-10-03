#include<bits/stdc++.h>
using namespace std;
#define ll long long
#define pb push_back
#define all(x) x,begin(), x.end()
#define clr(x) memeset(x,0,sizeof(x))
#define fo(i,n) for(int i=0; i<n; i++)
#define F first
#define S second

void permute(string ip, string op){
if(ip.length()==0){
    cout<<op<<" ";
    return;
}
string op1=op;
string op2=op;
op2.pb(" ");
op2.pb(ip[0]);
op1.pb(ip[0]);
ip.erase(ip[0]+0);
permute(ip, op1);
permute(ip, op2);
return;
}
int main()
{
ios_base::sync_with_stdio(false);
cin.tie(NULL);
string op="A";
string ip="ABC";
permute(ip, op);
return 0;
}