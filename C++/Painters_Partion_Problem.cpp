#include<iostream>
#include <climits>
#include <algorithm>
using namespace std;

bool isPossible(int arr[], int n, int k, int time) {

	int painter = 1;
	int painterTime = 0;

	for (int i = 0; i < n; i++) {

		painterTime += arr[i];

		if (painterTime > time) {
			painter += 1;
			painterTime = arr[i];

			if (arr[i] > time) return false;

			if (painter > k) {
				return false;
			}
		}

	}

	return true;
}

int minTime(int a[], int k, int n)
{
	// if(st > en)
	// {
	// 	return -1;
	// }

	int min_Time = INT_MIN;
	int max_Time = 0;

	for(int i =0; i < n; i++)
	{
		max_Time = max_Time + a[i];
		min_Time = max(min_Time, a[i]);
	}

	int ans = max_Time;

	int st = min_Time;
	int en = max_Time;

	while(st <= en)
	{
		int mid = st + (en - st) / 2;

		if(isPossible(a, n, k, mid))
		{
			ans = mid;
			en = mid - 1;
		}
		else 
		{
			st = mid + 1;
		}
	}

	return ans;
}


int main()
{
	int k;
	int n;

	cin>>k>>n;

	int a[n];

	for(int i =0; i<n; i++)
	{
		cin>>a[i];
	}

	cout<<minTime(a, k, n);
	return 0;
}
