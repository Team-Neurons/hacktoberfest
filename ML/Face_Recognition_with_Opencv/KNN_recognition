import cv2
import numpy as np
import os

########## KNN CODE ############
def distance(v1, v2):
	# Eucledian 
	return np.sqrt(((v1-v2)**2).sum())

def knn(train, test, k=5):
	dist = []
	
	for i in range(train.shape[0]):
		# Get the vector and label
		ix = train[i, :-1] #trainset -->dataset
		iy = train[i, -1]  #trainset -->labels
		# Compute the distance from test point
		d = distance(test, ix)
		dist.append([d, iy])
	# Sort based on distance and get top k
	dk = sorted(dist, key=lambda x: x[0])[:k]
	# Retrieve only the labels
	labels = np.array(dk)[:, -1]
	
	# Get frequencies of each label
	output = np.unique(labels, return_counts=True)
	# Find max frequency and corresponding label
	index = np.argmax(output[1])
	return output[0][index]
################################

cam = cv2.VideoCapture(0)

face_cascade = cv2.CascadeClassifier("haarcascade_frontalface_alt.xml")

skip= 0
dataset_path ="./FaceData/"
class_id = 0 #label for given file
names = {}
face_data = []
labels = []

#data prepration
for fx in os.listdir(dataset_path):
	if fx.endswith(".npy"): #a numpy file
	#create a mapping between class
		names[class_id] = fx[:-4]
		print("Loading file ",fx)
		data_item = np.load(dataset_path+fx) #file name and .npy
		face_data.append(data_item)

		#Create Labels
		target = class_id*np.ones((data_item.shape[0],)) #matrix of ones
		class_id +=1
		labels.append(target)
		

face_dataset = np.concatenate(face_data,axis=0)
face_label= np.concatenate(labels,axis=0).reshape((-1,1))



print(face_dataset.shape)
print(face_label.shape)
#by here, no. of rows should be equal
trainset = np.concatenate((face_dataset,face_label),axis=1)
print(trainset.shape)

#by here, no. of rows is same and coloumn is added

while True:
	ret,frame = cam.read()
	if ret == False:
		continue
	faces = face_cascade.detectMultiScale(frame,1.3,5)

	if(len(faces)==0): #if no face is seen, the camera hangs up rather than clossing with empty()! error
		cv2.imshow("Faces",frame)
		continue 

	for face in faces:
		x,y,w,h = face

		offset = 10
		face_selection = frame[y-offset:y+h+offset,x-offset:x+w+offset]
		face_selection = cv2.resize(face_selection,(100,100))


		#predicted label
		out = knn(trainset,face_selection.flatten())
			#displahy
		pred_name = names[int(out)]	
		cv2.putText(frame,pred_name,(x,y-10),cv2.FONT_HERSHEY_SIMPLEX,1,(255,0,125),2,cv2.LINE_AA)
		cv2.rectangle(frame,(x,y),(x+w,y+h),(0,255,255),5)


	cv2.imshow("Faces",frame)

	key=cv2.waitKey(1) & 0xFF

	if key==ord('q'):
		break

cam.release()
cv2.destroyAllWindows()
