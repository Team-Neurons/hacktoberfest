
#include <bits/stdc++.h> 
using namespace std;

//Function to swap elements
void swap(int* a, int* b)
{
	int temp = *a;
	*a = *b;
	*b = temp;
}

//Function to implement bubble sort  
void bubbleSort(int arr[], int n)
{
	int i, j;
	for (i = 0; i < n - 1; i++)

		// The last i elements are properly placed already 
		for (j = 0; j < n - i - 1; j++)
			if (arr[j] > arr[j + 1])
				swap(&arr[j], &arr[j + 1]);
}

