#include <stdio.h>

int main() {
  int i,j,k,l,n;
  printf("Enter no of rows: ");
  scanf("%d",&n);
  for(i=1;i<=n;i++){
    for(k=1;k<=(n-i);k++){
        printf(" ");
    }
    for(j=1;j<=i;j++){
      printf("*");
    }
    for(l=2;l<=i;l++){
      printf("*");
    }
    
    printf("\n");
  }
  for(i=1;i<=n;i++){
    for(k=1;k<=i;k++){
      printf(" ");
    }
    for(l=1;l<=(n-i);l++){
      printf("*");
    }
    for(j=2;j<=(n-i);j++){
      printf("*");
    }
        
    printf("\n");
  }
}
