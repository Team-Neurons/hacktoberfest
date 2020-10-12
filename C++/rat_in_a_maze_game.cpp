/* Author: Mohit Radadiya*/

#include<bits/stdc++.h>
using namespace std;

void BFS(int sx,int sy,int dx,int dy,vector<vector<int> > &dist, vector<vector<pair<int,int> > > &parent, vector<vector<int> > &arr,int n,int m){
	queue<pair<int,int> > q;
	q.push(make_pair(sx,sy));
	while(q.size()!=0){
		int currX = q.front().first;
		int currY = q.front().second;
		q.pop();
		if(currX == dx && currY == dy)
		break;
		if(currX+1<n && arr[currX+1][currY]!=0 && dist[currX+1][currY]>dist[currX][currY]+1){
			dist[currX+1][currY] = dist[currX][currY]+1;
			parent[currX+1][currY] = make_pair(currX,currY);
			q.push(make_pair(currX+1,currY));
		}
		if(currY+1<m && arr[currX][currY+1]!=0 && dist[currX][currY+1]>dist[currX][currY]+1){
			dist[currX][currY+1] = dist[currX][currY]+1;
			parent[currX][currY+1] = make_pair(currX,currY);
			q.push(make_pair(currX,currY+1));
		}
		if(currX-1>=0 && arr[currX-1][currY]!=0 && dist[currX-1][currY]>dist[currX][currY]+1){
			dist[currX-1][currY] = dist[currX][currY]+1;
			parent[currX-1][currY] = make_pair(currX,currY);
			q.push(make_pair(currX-1,currY));
		}
		if(currY-1>=0 && arr[currX][currY-1]!=0 && dist[currX][currY-1]>dist[currX][currY]+1){
			dist[currX][currY-1] = dist[currX][currY]+1;
			parent[currX][currY-1] = make_pair(currX,currY);
			q.push(make_pair(currX,currY-1));
		}
	}
	return ;
}


Rat_Maze_Driver(){
	int n,m;
	cout<<"Enter maze matrix dimensions"<<endl;
	cin>>n>>m;
	vector<int> cols(m);
	vector<vector<int> > arr(n,cols);
	cout<<"Enter a matrix of dimension "<<n<<" x "<<m<<" to represent state of cells"<<endl;
	int i,j;
	for(i=0;i<n;i++){
		for(j=0;j<m;j++){
			cin>>arr[i][j];
		}
	}
	
	cout<<"Enter source cell co-ordinates, 0 indexing"<<endl;
	int sx,sy;
	cin>>sx>>sy;
	while(sx>=n || sy>=m){
		cout<<"Invalid source cell co-ordinates, enter again!"<<endl;
		cin>>sx>>sy;
	}
	
	cout<<"Enter destination cell co-ordinates, 0 indexing"<<endl;
	int dx,dy;
	cin>>dx>>dy;
	while(dx>=n || dy>=m){
		cout<<"Invalid destination cell co-ordinates, enter again!"<<endl;
		cin>>dx>>dy;
	}
	
	vector<int> distCol(m);
	vector<vector<int> > dist(n,distCol);
	vector<pair<int,int> > parCol(m);
	vector<vector<pair<int,int> > > parent(n,parCol);
	
	for(i=0;i<n;i++){
		for(j=0;j<m;j++){
			dist[i][j]=INT_MAX;
			parent[i][j]=make_pair(-1,-1);
		}
	}
	if(arr[sx][sy]!=0 && arr[dx][dy]!=0)
	{
		dist[sx][sy]=0;
		parent[sx][sy]=make_pair(sx,sy);
	
		BFS(sx,sy,dx,dy,dist,parent,arr,n,m);
		vector<pair<int,int> > oppositePath;
		int tx=dx;
		int ty=dy;
		while(make_pair(tx,ty)!=parent[tx][ty]){
			oppositePath.push_back(make_pair(tx,ty));
			int nx=parent[tx][ty].first;
			int ny=parent[tx][ty].second;
			tx=nx;
			ty=ny;
		}
		oppositePath.push_back(make_pair(tx,ty));
		cout<<"Shortest Path is"<<endl;
		for(i=oppositePath.size()-1;i>=0;i--){
			cout<<oppositePath[i].first<<" "<<oppositePath[i].second<<endl;
		}
	}
	else{
		cout<<"No path exists!"<<endl;
	}
	return 0;
}
