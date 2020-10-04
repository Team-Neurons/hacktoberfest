x=[1,2,4,6,7,4,6,11,2]
y=[2,1,5,7,3,5,7,1,3]
#Resultant values for x and y
z=[0,0,1,0,1,0,1,1,0]

def dist(ax,by,tx,ty):
    d=(ax-tx)**2+(by-ty)**2
    return(d**(1/2))

res=[]
tx=1
ty=2

for i in range(0,len(x)):
    res.append(dist(x[i],y[i],tx,ty))

#print(res)

res2=[[res[e],z[e]] for e in range(0,len(res))]

#print(res2)

res.sort()
#print(res)
res3=[]
for i in res:
    for j in res2:
        if j[0]==i:
            res3.append(j)
            break

#print(res3)
lable=[]
for k in range(0,6):
    lable.append(res3[k][1])

#print(lable)
count0=0
count1=0
for w in lable:
    if w==0:
        count0=count0+1
    else:
        count1=count1+1

#print(count0)
#print(count1)

r=[count1,count0]

Q=max(r)
print("Answer:-")
if Q==count0:
    print(0)
else:
    print(1)


