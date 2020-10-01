#include <bits/stdc++.h>
using namespace std;

int main()
{
    int N, space = 2;
    cin>>N;
    int ctr = N/2;
    for(int i = 1; i <= N/2+1; i++)
    {
        if(i == 1)
            for(int j = 1; j <= N; j++)
                cout << "* ";
        else
        {
            for(int j = 1; j <= ctr; j++)
                cout << "* ";
            for(int j = 1; j <= space; j++)
                cout << " ";
            space += 4;
            for(int j = 1; j <= ctr; j++)
                cout << "* ";
            ctr--;
        }
        cout << endl;
    }
    ctr = 2;
    space -= 8;
    for(int i = 1; i <= N/2; i++)
    {
        if(i == N/2)
            for(int j = 1; j <= N; j++)
                cout << "* ";
        else
        {
            for(int j = 1; j <= ctr; j++)
                cout << "* ";
            for(int j = 1; j <= space; j++)
                cout << " ";
            space -= 4;
            for(int j = 1; j <= ctr; j++)
                cout << "* ";
            ctr++;
        }
        cout << endl;
    }
    return 0;
}