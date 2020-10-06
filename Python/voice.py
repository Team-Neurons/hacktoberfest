from pyttsx3 import *

engine=init('sapi5')
voices=engine.getProperty('voices')
#print(voices[0].id)
engine.setProperty('voice',voices[0].id)

def speak(audio):
    engine.say(audio)
    engine.runAndWait()


if __name__ == "__main__":
    speak("..........Hello .......User ")
    print("Enter the statement :-")
    s=input()
    speak(s)
