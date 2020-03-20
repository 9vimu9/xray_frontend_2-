from __future__ import absolute_import, division, print_function, unicode_literals

try:
    import tensorflow as tf
    from PIL import Image
    import os
    import numpy as np 
    import sys


    TEST_IMAGE_PATH = sys.argv[1]
    MODEL_PATH = sys.argv[2]
    IMG_SIZE = 250
    LABELS = ['BACTERIA','NORMAL','VIRUS']

    new_model = tf.keras.models.load_model(MODEL_PATH)

    def decode_img(image_path):
        im = Image.open(image_path).convert('RGB')
        new_size = (IMG_SIZE,IMG_SIZE)
        im = im.resize(new_size, Image.ANTIALIAS)
        data = np.asarray(im)
        image = np.array(data)
        image = np.divide(image , 255.0)
        return image


    img = decode_img(TEST_IMAGE_PATH)

    img = np.expand_dims(img, axis=0)

    result = new_model.predict(img)
    maxValue = result.argmax()
    values = result.flatten()
    print(values)
    print(maxValue)
    print(LABELS[maxValue])

    #print(result)
    # print(img.shape)

except Exception as e:
    print(os.getegid()+' Failed to open file: %s' % (e,))
