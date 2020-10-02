import cv2
import numpy as np
import os
#Read a Video Stream and Display It

#Camera Object
cam = cv2.VideoCapture(0)
face_cascade = cv2.CascadeClassifier('haarcascade_frontalface_alt.xml')
face_data = []
cnt = 0 

user_name = input("enter your name")


while True:
	ret,frame = cam.read()
	if ret==False:
		print("Something Went Wrong!")
		continue

	key_pressed = cv2.waitKey(1)&0xFF #Bitmasking to get last 8 bits
	if key_pressed==ord('q'): #ord-->ASCII Value(8 bit)
		break

	faces = face_cascade.detectMultiScale(frame,1.3,5)
	#print(faces)
	if(len(faces)==0):
		cv2.imshow("Video",frame)
		continue
	for face in faces:
		x,y,w,h = face
		face_section = frame[y-10:y+h+10,x-10:x+w+10];
		face_section = cv2.resize(face_section,(100,100))
		cv2.rectangle(frame,(x,y),(x+w,y+h),(0,255,255),2)
		if cnt%10==0:
			print("Taking picture ",int(cnt/10))
			face_data.append(face_section)
		cnt +=1 

	gray = cv2.cvtColor(frame,cv2.COLOR_BGR2GRAY)
	cv2.imshow("Video",frame)
	cv2.imshow("Video Gray",face_section)

#Save the face data in a numpy file
print("Total Faces" ,len(face_data))
face_data = np.array(face_data)
face_data = face_data.reshape((face_data.shape[0],-1))

np.save("FaceData/"+user_name+".npy",face_data)
print("Saved at FaceData/"+user_name+".npy")
print(face_data.shape)
cam.release()
cv2.destroyAllWindows()	

