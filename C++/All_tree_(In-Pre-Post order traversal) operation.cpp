#include<bits/stdc++.h>
using namespace std;
struct node{
	struct node *lc;
	int data;
	struct node *rc;
};
int a[]={ 3,5,9,6,8,20,10,0,0,9,0,0,0,0,0,0,0,0,0,0,0};
struct node *buildtree(int n)
{
	struct node *temp = 0;
	if(a[n] != 0){
		temp = (struct node *)malloc(sizeof(struct node));
		temp->lc = buildtree(2*n+1);
		temp->data = a[n];
		temp->rc = buildtree(2*n+2);
	}
	return temp;
}

void inorder(struct node *root){
	if(root != NULL){
		if(root != NULL){
			inorder(root->lc);
			cout << root->data << " ";
			inorder(root->rc);
		}
	}
}
void postorder(struct node *root){
	if(root != NULL){
		if(root != NULL){
			postorder(root->lc);
			postorder(root->rc);
			cout << root->data << " ";
		}
	}
}
void preorder(struct node *root){
	if(root != NULL){
		if(root != NULL){
			cout << root->data << " ";
			preorder(root->lc);
			preorder(root->rc);
		}
	}
}
int main(){
	struct node *root;
	root = buildtree(0);
	cout << "Inorder Traversal:\n";
	inorder(root);
	cout << "\n";
	cout << "Postorder Traversal:\n";
	postorder(root);
	cout << "\n";
	cout << "Preorder Traversal:\n";
	preorder(root);
	cout << "\n";
	return 0;
}
