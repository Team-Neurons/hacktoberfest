import cv2 as cv
import numpy as np
import scipy as sp
import matplotlib.pyplot as plt
import pandas as pd
import math





cam = cv.VideoCapture(0)
face_cascade = cv.CascadeClassifier('haarcascade_frontalface_alt.xml')

eye_cascade = cv.CascadeClassifier('third-party/frontalEyes35x16.xml')
nose_cascade = cv.CascadeClassifier('third-party/Nose18x15.xml')

glasses= cv.imread("glasses.png",cv.IMREAD_UNCHANGED)
mooch= cv.imread("mustache.png",cv.IMREAD_UNCHANGED)

while True:
	ret,frame = cam.read()
	if ret==False:
		print("Something Went Wrong!")
		continue

	key_pressed = cv.waitKey(1)&0xFF #Bitmasking to get last 8 bits
	if key_pressed==ord('q'): #ord-->ASCII Value(8 bit)
		break

	faces = face_cascade.detectMultiScale(frame,1.3,5)
	eyes= eye_cascade.detectMultiScale(frame,1.3,5)
	noses= nose_cascade.detectMultiScale(frame,1.3,5)
	#print(faces)
	if(len(eyes)==0):
		cv.imshow("Video",frame)
		continue
		###########//face\\####################################
	for face in faces:
		x,y,w,h = face
		face_section = frame[y-10:y+h+10,x-10:x+w+10];
		face_section = cv.resize(face_section,(100,100))
		cv.rectangle(frame,(x,y),(x+w,y+h),(0,255,255),3)
		###################//eyes\\##########################
		for eye in eyes:
			x1,y1,w1,h1 =eye
			eye_section =frame[y1-10:y1+w1+10,x1-10:x1+h1+10];
			eye_section =cv.resize(eye_section,(100,100))
			cv.rectangle(frame,(x1,y1),(x1+h1,y1+w1),(0,0,255),1)
			###############filter_googles#################
			glasses = cv.resize(glasses,dsize = (h1,w1))
			glasses = cv.cvtColor(glasses,cv.COLOR_BGR2RGBA)
			y_offset =y1 # add or subtract integer to adjust position of filter based on your image
			x_offset= x1   
			y1, y2 = y_offset, y_offset + w1
			x1, x2 = x_offset, x_offset + h1
			alpha_s = glasses[:, :, 3] /255
			alpha_l = 1.0 - alpha_s
			for c in range(0, 3):
				frame[y1:y2, x1:x2, c] = (alpha_s * glasses[:, :, c] + alpha_l * frame[y1:y2, x1:x2, c])


					###################//nose\\########

		for nose in noses:
			x2,y2,w2,h2 =nose
			nose_section =frame[y2-10:y2+w2+10,x2-10:x2+h2+10];
			nose_section =cv.resize(nose_section,(100,100))
			cv.rectangle(frame,(x2,y2),(x2+h2,y2+w2),(255,0,0),1)
			##############mostach filter############
			mooch = cv.resize(mooch,dsize = (h2,w2))
			mooch = cv.cvtColor(mooch,cv.COLOR_BGR2RGBA)
			y1_offset =y2+15 # 15 to push mustach down..............change based on person or use mouth_haarcascade
			x1_offset= x2   
			y1a, y2a = y1_offset, y1_offset + w2
			x1a, x2a = x1_offset, x1_offset + h2
			alpha_sa = mooch[:, :, 3] /255
			alpha_la = 1.0 - alpha_sa
			for ca in range(0, 3):
				frame[y1a:y2a, x1a:x2a, ca] = (alpha_sa * mooch[:, :, ca] + alpha_la * frame[y1a:y2a, x1a:x2a, ca])





	cv.imshow("Video",frame)
	cv.imshow("Video1",face_section)
	cv.imshow("Video2",eye_section)
	cv.imshow("Video3",nose_section)
cam.release()
cv.destroyAllWindows()	        
    

