// { Driver Code Starts
//Initial Template for C++

/* Following program is a C implementation of 
Rabin Karp. Algorithm given in the CLRS book */
#include<bits/stdc++.h>

using namespace std;

bool search(string, string, int);

// d is the number of characters in the input alphabet 
#define d 256 

/* pat -> pattern 
	txt -> text 
	q -> A prime number 
*/

//Modified
bool search(string pat, string txt, int q) 
{ 
    //Hm basically 3 blocks bnaegein ismein 
    int n=txt.length();
    int m=pat.length();
    int h=1;
    for(int i=0; i<m-1; i++){   //yha hm d^(m-1) compute krenge taki weighted sum ajae
        h=(h*d)%q;        //ye value niche use hogi function mein
    }
    //is niche wale block mein hm hash function compute krenge aur q ko isliye use krhe taki value bhaut bdi na use ho jave
    int p=0;
    int t=0;
    //yha loop lgaingein pattern ki length tk
    for(int i=0; i<m; i++){
    //horner rule lgainge ismein see notes
        p=(p * d + pat[i]) % q;   //pat ka hash function
        t=(t * d + txt[i]) % q;   //txt ka hash function
    }
    bool flag=true;
    //is wale block mein hm search krenge sliding window ko agr match hogai to flag ko true krdenge agr nhi hoi to false krdenge
    //ye asli function he yha yha loop lgadenge m-1 tk
    for (int i = 0; i <= n-m; i++)   //is loop mein pattern checking hogi
    {
        if(p==t){         //agr p jo ki he pateern ka hash function eguall hoa text ke hash function se
            flag = true;    //flag ko true krdenge
            // aur loop mein chlejainge
            for(int j=0; j<m; j++){    //yha pe pattern ki lenght tk jaenge
               if(txt[i+j]!=pat[j]){    //agr pattern hr iter pe match na hoa to flag ko false nhi to true pe print krrdenge
                   flag=false;
                   break;
               }
            }
            if(flag==true){  
               return true;  
            }
        }
    
        //is wale block mein hmm next sling window that is txt(i+1) compute krenge
        //upr wali condition mein hmm last window ko nhi denkhegein
        //niche ek condition he ki jb value negative mein chli gai to value hm q add krdenge taki second block mein  dikat na ave
        if(i<n-m){
            t = ((d * (t - txt[i] * h) + txt[i+m]) % q);   //next sliding window pattern ki compute kri
        }
        if(t < 0){
                //agr t minus mein gya to add krdenge q ki value
            t = t + q;
        }
        
    } 
    return false;
}
int main() 
{ 
    int t;
    cin >> t;
    
    while(t--){
	    string s, p;
	    cin >> s >> p;
	    int q = 101; // A prime number 
	    if(search(p, s, q)) cout << "Yes" << endl;
	    else cout << "No" << endl;
    }
	return 0; 
} 
  // } Driver Code Ends
