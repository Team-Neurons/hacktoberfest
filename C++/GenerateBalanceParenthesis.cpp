void solve(int open, int close, string op, vector<string> &v)
{
    //base condtion jb open and close ka count hora 0
    if(open==0 && close==0){
        v.push_back(op);
        return;
    }
    //open ki choice jb tk milrhi jbtk count open ka 0 na hojae
    if(open!=0){
        //yha pe phli optput to same hi rkhndete he root wali
        string op1=op;
        op1.push_back('(');
        solve(open-1, close, op1, v);  //open use krke count -1
    }
    //dusri condtion closing bracket ki milrhi
    if(close>open){
        string op2=op;
        op2.push_back(')');
        solve(open, close-1, op2, v);  //close use krke count -1
    }
}
vector<string> Solution::generateParenthesis(int n) {
    vector<string> v;
    //2 open and close bracket leliye n ke hisab se
    int close=n;
    int open=n;
    string op=""; //output string leliya
    //ab apna recursive count krliya
    solve(open, close, op, v);
    return v;
}