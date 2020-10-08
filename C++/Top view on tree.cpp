#include<bits/stdc++.h>
using namespace std;
class Node {
    public:
        int data;
        Node *left;
        Node *right;
        Node(int d) {
            data = d;
            left = NULL;
            right = NULL;
        }
};
class Solution {
    public:
  		Node* insert(Node* root, int data) {
            if(root == NULL) {
                return new Node(data);
            } else {
                Node* cur;
                if(data <= root->data) {
                    cur = insert(root->left, data);
                    root->left = cur;
                } else {
                    cur = insert(root->right, data);
                    root->right = cur;
                }

               return root;
           }
        }

/*
class Node {
    public:
        int data;
        Node *left;
        Node *right;
        Node(int d) {
            data = d;
            left = NULL;
            right = NULL;
        }
};

*/

struct item
{
    Node* n;
    int hd;
    item(Node* n, int hd) {
        this->n = n;
        this->hd = hd;
    }
};
struct less_than_key
{
    inline bool operator() (const item& struct1, const item& struct2)
    {
        return (struct1.hd < struct2.hd);
    }
};
void topView(Node * root)
{
    std::queue<item*> q;
    std::set<int> s;
    std::vector<item> tops;
    q.push(new item(root, 1));
    while(!q.empty()) {
        item* top = q.front();
        q.pop();
        if(s.find(top->hd) == s.end()) {
            s.insert(top->hd);
            tops.push_back(*top);
        }
        if(top->n->left)
            q.push(new item(top->n->left, top->hd - 1));
        if(top->n->right)
            q.push(new item(top->n->right, top->hd + 1));
    }
    std::sort(tops.begin(), tops.end(), less_than_key());
    for(std::vector<item>::iterator it = tops.begin(); it != tops.end(); ++it) {
        std::cout << (*it).n->data << " ";
    }
    std::cout << std::endl;
}

};

int main() {
    
    Solution myTree;
    Node* root = NULL;
    
    int t;
    int data;

    std::cin >> t;

    while(t-- > 0) {
        std::cin >> data;
        root = myTree.insert(root, data);
    }
  
    myTree.topView(root);

    return 0;
}
