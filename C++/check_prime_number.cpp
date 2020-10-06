#include<bits/stdc++.h>
using namespace std;

bool isPrime(int num)
{
	if (num <= 1)
		return false;

	for (int i=2; i<=num/2; i++)
	{
		if (num%i == 0)
		{
			return false;
		}
	}
	return true;
}

int main()
{
	int num;
	cout << "CHECK IF A GIVEN NUMBER IS PRIME OR NOT" << endl << "Enter your number: ";
	cin >> num;
	(isPrime(num)) ? (cout << "Yes") : (cout << "No");
	cout << endl;
}
