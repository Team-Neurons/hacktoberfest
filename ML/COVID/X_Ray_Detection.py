import cv2
import numpy as np
import keras
from keras.layers import *
from keras.models import Model , load_model
from keras.preprocessing import image
from keras.utils import np_utils
from keras.models import model_from_json
from keras.applications.resnet50 import ResNet50
from keras.optimizers import Adam
import matplotlib.pyplot as plt
import random
import os
from PIL import Image


model = load_model("model.h5")
# load json and create model

# json_file = open('model.json', 'r')
# loaded_model_json = json_file.read()
# json_file.close()
# loaded_model = model_from_json(loaded_model_json)
# # load weights into new model
# loaded_model.load_weights("model.h5")
# print("Loaded model from disk")
 
# # evaluate loaded model on test data
# loaded_model.compile(loss='binary_crossentropy', optimizer='rmsprop', metrics=['accuracy'])
# score = loaded_model.evaluate(X, Y, verbose=0)
# print("%s: %.2f%%" % (loaded_model.metrics_names[1], score[1]*100))




frame = cv2.imread("ax.jpeg")
# frame = np.array(frame)
# image = Image.fromarray(frame)
# img = image.resize((224, 224), Image.BILINEAR) 
# mlt.imshow(img)


# def image_to_feature_vector(image, size=(224, 224)):
# # resize the image to a fixed size, then flatten the image into
# # a list of raw pixel intensities
# 	return cv2.resize(image, size).flatten()


# if np.all(np.array(frame.shape)):
	# print(frame.shape)
# 	test_data = cv2.resize(frame, (224,224))




# test_data = image_to_feature_vector(frame, size=(224, 224))
test_data = cv2.resize(frame, (224,224))

test_data = np.array(test_data)
test_data.shape = (1,224,224,3)


zz = model.predict(test_data)
print(zz[0][0])


if zz[0][0]<0.24:
	pred = "COVID 19 POSITIVE"
else:
	pred = "COVID 19 NEGATIVE"
	continous_viol = 0

print("Prediction ---------->", pred)


cv2.destroyAllWindows()



