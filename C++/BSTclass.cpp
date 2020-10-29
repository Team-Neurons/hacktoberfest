#include<iostream>
using namespace std;

template <typename T>
class BinaryTreeNode {
public:
    T data;
    BinaryTreeNode<T> *left;
    BinaryTreeNode<T> *right;

    BinaryTreeNode(T data) {
        this->data = data;
        this->left = NULL;
        this->right = NULL;
    }
};

class BST {
	// Complete this class
    public:
   BinaryTreeNode<int>* root;

    BST (){
        this-> root = NULL;
    }

    ~BST (){
        delete root;
    }

    private:

    BinaryTreeNode<int>* insertData(int data , BinaryTreeNode<int> * node ){
        if(node==NULL){
            BinaryTreeNode<int>* treeNode = new BinaryTreeNode<int>(data);
            return treeNode;

        }

       else if(node->data > data){
            node->left= insertData(data, node->left);
        }

        else {
            node ->right = insertData(data, node->right);
        }
          return node;

    }

     bool searchData(int data , BinaryTreeNode<int> * node ){
        if(node==NULL){
          return 0;

        }
        if(node->data== data){
            return 1;

        }

       else if(node->data > data){
           return searchData(data, node->left);
        }

        else {
            return searchData(data, node->right);
        }
    }

    BinaryTreeNode<int>* findMin(BinaryTreeNode<int>* node){
        while(node->left!=NULL){
            node=node->left;
        }
        return node;
    }

     BinaryTreeNode<int>* deleteData(int data , BinaryTreeNode<int>* node){
        if(node==NULL){
            return node;
        }

        if(node->data > data){
            node->left= deleteData(data, node->left);
        }

        else if (node-> data< data){
            node->right = deleteData(data, node->right);
        }

        else {
            if(node->left==NULL && node-> right ==NULL){
                delete node;
                node=NULL;

            }

           else if(node->left==NULL){
                BinaryTreeNode<int>* temp = node;
                node=node->right;
                delete temp;
            }

            else if(node->right==NULL){
                BinaryTreeNode<int>* temp= node;
                node=node->left;
                delete temp;
            }

            else {
                BinaryTreeNode<int> *  temp = findMin(node->right);
                node->data = temp->data;
                node->right= deleteData(temp->data, node->right);
            }
            return node;

        }
    }

    void printTree(BinaryTreeNode<int>* node){
        if(node==NULL){
            return ;
        }

        cout<<node->data<<":";

        if(node->left!=NULL){
            cout<<"L:"<<node->left->data<<",";
        }

         if(node->right!=NULL){
            cout<<"R:"<<node->right->data;
        }
        cout<<endl;
        printTree(node->left);
        printTree(node->right);


    }

    public:

   void insert (int data){
      this -> root =  insertData(data ,this-> root);
   }

    void deleteData(int data ){
        this-> root= deleteData(data, this->root);
    }

  bool hasData(int k){
    return searchData(k, this->root);
   }

    void printTree(){
        printTree(this->root);
    }


};


int main(){
    BST *tree = new BST();
    int choice, input;
    while(true){
        cin>>choice;
        switch(choice){
            case 1:
                cin >> input;
                tree->insert(input);
                break;
            case 2:
                cin >> input;
                tree->deleteData(input);
                break;
            case 3:
                cin >> input;
                if(tree->hasData(input)) {
                    cout << "true" << endl;
                }
                else {
                    cout << "false" << endl;
                }
                break;
            default:
                tree->printTree();
                return 0;
                break;
        }
    }
}
