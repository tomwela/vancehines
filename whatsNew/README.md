#What's New
Ruben J. Leon

##Instructions for Adding new content
The mobile apps provide a News section that describes the latest features upon each successive app release.  This section of the app is a list of links that will redirect the user to a web page where all the new features for the latest release are listed.


####HTML
The first thing to do in the whatsNew folder is to copy the existing index.html as a template to a new html file.  The name of this file is lower case and starts off with 3 characters for the month followed by a four digit year and ending with ".html"   For example, if a new app release takes place in September 2016 then the new html file name will be sep2016.html.  

When completed, a copy of this new html page will become the new whatsNew/index.html file.  This is done incase anyone tries to look around in the browser by editing the url.  Instead of seeing all the contents of the whatsNew folder, they will be redirected to the index.html page which is just an exact copy of the lastet features page!

**Images**

There are two image files, an svg file for the web page and a png file for the mobile app.

Management will determine the image file to use.

Marketing can provide an SVG file for the whatsNew html page.  This file is usally an icon used in the FP3 application representing a new or modified feature of the fp3.  The image will be scaled in the web page to about 64x64 pixels and will be stored in the whatsNew/img folder:
```html
<img alt="64x64" src="img/wheel_icon_target2017.svg" data-holder-rendered="true" style="width: 64px; height: 64px;">
```


The image for the mobile app must be edited to add padding around the image and saved into the png format.
Take the svg file and drop it onto the GIMP application which will then ask what size to convert  to. The image without any padding should be about 225x225 pixels in size.  **Note: this process converts an SVG file to an image.**

In GIMP, reset the Canvas size 350x350 (center the image) and export for the web as a png file.  
Copy the image to the prototype/stories folder for use by the mobile app.


####Database entry####
In the database, edit the topstories table to add a new entry with all the appropriate info. 

