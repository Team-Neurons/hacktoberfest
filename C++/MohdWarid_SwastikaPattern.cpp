// C++ implementation for Swastika Pattern 
// Made by Mohd Warid

#include <iostream>
using namespace std; 

int main() 
{ 
start:
int row, col;
cin >> row;
if (row % 2 == 0) //To ensure odd number of rows and columns
goto start;
else 
{
col = row;

for (int i = 0; i < row; i++) { 
	for (int j = 0; j < col; j++) { 
		
	// checking if i < row/2 
	if (i < row / 2) { 
		
		// checking if j<col/2 
		if (j < col / 2) { 
			
		// print '*' if j=0 
		if (j == 0) 
			cout << "*"; 
			
		// else print space 
		else
			cout << "  "; 
		} 
		
		// check if j=col/2 
		else if (j == col / 2) 
		cout << " *"; 
		else
		{ 
		// if i=0 then first row will have '*' 
		if (i == 0) 
			cout << " *"; 
		} 
	} 
	else if (i == row / 2) 
		cout << "* "; 
	else { 
		
		// middle column and last column will have '*' 
		// after i > row/2 
		if (j == col / 2 || j == col - 1) 
		cout << "* "; 
		
		// last row 
		else if (i == row - 1) { 
			
		// last row will be have '*' if 
		// j <= col/2 or if it is last column 
		if (j <= col / 2 || j == col - 1) 
			cout << "* "; 
		else
			cout << "  "; 
		} 
		else
		cout << "  "; 
	} 
	} 
	cout << "\n"; 
} 
}
return 0; 
} 
