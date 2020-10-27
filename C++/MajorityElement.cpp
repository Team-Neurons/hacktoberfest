// Title     : Majority element in an array (Boyer-Moore Majority Vote Algorithm)

#include<bits/stdc++.h>
using namespace std;

int boyerMooreMajorityVote(int arr[], int n) {

    int element = 0, count = 0;

    for (int i = 0; i < n; ++i) {
        if (count == 0)
            element = arr[i];
        if (element == arr[i])
            ++count;
        else
            --count;
    }

    count = 0;
    for (int i = 0; i < n; ++i) {
        if (element == arr[i])
            ++count;
        if (count > (n / 2)) {
            return element;
        }
    }

    return INT_MIN;
}

int main() {

    int n;
    cout << "Enter the number of elements : ";
    cin >> n;

    int arr[n];
    cout << "Enter " << n << " elements : ";
    for (int i = 0; i < n; ++i) {
        cin >> arr[i];
    }

    int majority = boyerMooreMajorityVote(arr, n);

    if (majority == INT_MIN)
        cout << "Majority element does not exist\n";
    else
        cout << "Majority element is : " << majority << '\n';

    return 0;
}