alp="abcdefghijklmnopqrstuvwxyz"

key = int(input("Enter the caesar key: \n"))

encrypt = ""

msg = input("Enter the message to encrypt: \n")

for char in msg:
    if char in alp:
        position = alp.find(char)
        newpos = (position + key)%26
        enchar = alp[newpos]
        encrypt += enchar
    else:
        encrypt += char

print("The encrypted message is: \n",encrypt)

