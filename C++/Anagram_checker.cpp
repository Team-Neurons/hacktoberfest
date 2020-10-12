 #include <iostream>
using namespace std;
int main()
{
    //Initializing variables.
    char str1[100],str2[100];
    int first[26]={0}, second[26]={0}, c=0, flag=0;
    
    //Accepting inputs.
    cout<<"Enter First String: ";
    gets(str1);
    cout<<"Enter Second String: ";
    gets(str2);
    
    //Calculating frequencies of characters in first string.
    while(str1[c] != '\0')
    {
        first[str1[c]-'a']++;
        c++;
    }
     
    c=0;
    //Calculating frequencies of characters in second string. 
    while(str2[c] != '\0')
    {
        second[str2[c]-'a']++;
        c++;
    }
    //Checking if frequencies of both the strings are same or not.
    for(c=0;c<26;c++)
    {
        if(first[c] != second[c])
            flag=1;
    }
    //Priting result.
    if(flag == 0)
    {
        cout<<"Strings are anagram.";
    }
    else
    {
        cout<<"Strings are not anagram.";
    }
    return 0;
  
}
