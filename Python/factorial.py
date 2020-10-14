# Calculates the factorials of numbers
# with a recursive function
def factorial(num):
    if num <= 1:
        return 1
    return num * factorial(num - 1)


if __name__ == "__main__":
    for i in range(10):
        print("Factorial of " + str(i) + " is " + str(factorial(i)))

    