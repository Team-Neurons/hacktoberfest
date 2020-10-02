package com.company;

import java.sql.SQLOutput;
import java.util.ArrayList;

public class TodoFunction {
    private ArrayList<String> todoList = new ArrayList<String>();

    public void addItem(String item){
        todoList.add(item);
    }

    public void removeItem(int index){
        String myItem =todoList.get(index);
        todoList.remove(index);
    }

    public void printTodoList(){
        System.out.println("Todo list consist of :"+todoList.size()+"item");
        for(int i=0;i<todoList.size();i++){
            System.out.println("Item at position "+(i+1)+" is "+todoList.get(i));
        }
    }
    public void updateTodo(int index,String list){
        todoList.set(index,list);
        System.out.println("Updated Sucessfuly" +index+1);
    }

    public String findItem(String searchItem){
        int index = todoList.indexOf(searchItem);

        if(index == -1){
            return null;
        }else{
            return todoList.get(index);
        }

    }

}
