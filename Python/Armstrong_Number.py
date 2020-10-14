#Program to check whether the number is an armstrong number or not
#Ask user to enter the number
number=int(input("Enter the number you want to check armstrong:  "))

#To calculate the length of number entered.
order=len(str(number))

#Initialise sum to 0
sum=0

temp=number
while temp>0:
    num=temp%10
    sum+=num**order
    temp//=10

if (number==sum):
    print("The number you have entered is an Armstrong number.")
else:
    print("The number you have entered is not an Armstrong number.")





#OUTPUT:
#Enter the number you want to check armstrong:  1634
#The number you have entered is an Armstrong number.
