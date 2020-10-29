#include<bits/stdc++.h>
using namespace std;

int findSubset (int arr1[] ,int arr2[] , int n , int  m) {
    for (int i=0;i<m;i++) {
	           int y = arr2[i];
	           int x = count(arr1 , arr1 + n , y);
	           if (x == 0) {
	               return 0;
	           }
    }
    return 1;
}



int main()
 {

	int t;
	cin >> t;
	while (t--) {
	    int n , m;
	    cin >> n >> m;
	    int arr1[n] , arr2[m];
	    
	    
	    for (int i=0;i<n;i++) { 
	        cin >> arr1[i];
	    }
	    for (int i=0;i<m;i++) { 
	        cin >> arr2[i];
	    }
	    
	    
	    int returnValue = findSubset (arr1 ,arr2 ,n ,m);
	    if (returnValue == 1) {
	        cout << "Yes";
	    }else {
	        cout << "No";
	    }
	    cout << "\n";
	}
	return 0;
}