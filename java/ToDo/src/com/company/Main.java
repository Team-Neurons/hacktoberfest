package com.company;

import java.util.Scanner;

public class Main {

    private static Scanner in = new Scanner(System.in);
    private static TodoFunction myTodoList = new TodoFunction();//object

    public static void main(String[] args) {
       int command = 0;
       boolean exit =false;

       printCommand();
       while (!exit){
           System.out.println("Enter your Choice :");
           command = in.nextInt();
           in.nextLine();
           switch (command){
               case 0:
                   printCommand();
                   break;
               case 1:
                   myTodoList.printTodoList();
                   break;
               case 2:
                   addItem();
                  break;
               case 3:
                   updateItem();
                   break;
               case 4:
                   removeItem();
                   break;
               case 5:
                   findItem();
                   break;
               case 6:
                   exit=true;
                   break;
                default:
                    System.out.println("invalid coice");
                    break;
           }
       }
    }

    public static void printCommand(){
        System.out.println("\n Commands :"+
                           "\n Press 0 : To Print instructions "+
                           "\n Press 1 : To Print all list"+
                           "\n Press 2 : To add list in todo "+
                           "\n Press 3 : To modify item in todo"+
                           "\n Press 4 : To remove item form todo"+
                           "\n Press 5 : To Search An Item from Todo"+
                           "\n Press 6 : To exit the app"
                          );
    }
    public static void addItem(){
        System.out.println("Enter item to be added in todo list ");
        myTodoList.addItem(in.nextLine());
    }
    public static void updateItem(){
        System.out.println("Enter the item number :");
        int index = in.nextInt();
        in.nextLine();
        System.out.println("Enter new item to be added :");
        String myNewItem=in.nextLine();
        myTodoList.updateTodo(index-1,myNewItem);
    }
    public static void removeItem(){
        System.out.println("Enter the item number to be deleted :");
        int index = in.nextInt();
        in.nextLine();
        myTodoList.removeItem(index-1);

    }
    public static void findItem(){
        System.out.println("Enter a String to be searched :");
        String searchItem=in.nextLine();
        if(myTodoList.findItem(searchItem)== null){
            System.out.println("item not found in your todo");
        }else {
            System.out.println(searchItem+"was found in your list ");
        }
    }
}
