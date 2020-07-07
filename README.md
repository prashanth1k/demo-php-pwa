## What this is?

A quick demo of file handling using PWA on PHP pages.

It uses -

1. PHP
1. Basic PWA (which has been bypassed for tests - caching may or may not work as expected)
1. SQLite DB

## Installation Instructions

This is a fairly self-contained app.

1. Download repository
1. Host the entire folder. Ensure `images` folder is write-enabled
1. Enable `https` for your site. You can use self-certification on a tool like Laragon
1. Navigate to your domain name and test

## How it works?

This application provides a working demo of how PWA can be enabled on a plain PHP app. It also touches a bit of WebRTC to enable users to use any supported device to open a camera, take pictures and upload it to the server.

The components described below are key to the functionality offered by the app.

### Backend PHP Application

Consists of a couple of pages -

#### 1. `index.php`

Home page with the main form - just fill in the claim number, take at least one image and hit `Submit` to upload files.

If the device does not support taking images (or if user denies camera access), the application will show a simple file upload form.

Uploading files will do two things -

1. Create a record in SQLite DB
1. Capture file details (either in the `file` input or as data URLs)
1. `index` calls `upload.php` to upload files on button click

Depending on camera support, `index.php` shows a traditional file dialog where user can input files, or a live video feed from camera that allows user to click one or more photographs.

The files (or data URLs) need to be saved on server using `upload.php` called by `submit` action.

#### 1. `upload.php`

This is a mash of different functions since we started with a far simpler demo.

Depending on whether camera support is present, `upload.php` will -

1. Upload the multiple files supplied in the traditional format
1. Collect data URLs created by the camera app and create files from those URLs

## PWA

Uses a simple PWA plug using `sw.js` and a simple webmanifest - both get referred in the `index.php`. Browser will prompt installation of the app - both on desktop and mobile devices. This works like a normal PWA, nothing fancy.

Offline-first is not an objective of this demo - we are working with PHP and want files to reach the haven of our server for the most part.

## Known Issues

- Mobile may give low memory errors (this is from Chrome, not quite this app) - see [this post](http://bhijanneupane.blogspot.com/2018/01/100-working-solution-for-unable-to.html) for fix
