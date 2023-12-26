
import base64 
image = open('tew.map', 'rb') #open binary file in read mode
image_read = image.read()
image_64_encode = base64.encodestring(image_read)
print(image_64_encode)