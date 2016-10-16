# Evaporate
Evaporate is a system for easily including download codes with physical products

Welcome to Evaporate

Evaporate is a system for easily adding a unique download code to a physical product. Examples include:

A CD, vinyl record or cassette tape with a download code included for a zip of the music as mp3s/Flac
A book or magazine with a download code for a PDF or eBook of the material
A band issuing tickets for a show and including a unique download code on each ticket

Installation:

You will need:

A web hosting package including PHP version 5.3 or higher and MySQL.
Access to the MySQL database, for example by PHP myadmin.

Firstly, download or clone the Evaporate files and open the file database/Credentials.php. You will need to add
a username and password by which the application can access your MySQL install, along with the database host 
address, inserted where indicated by the comments. You will need to set dbName to the name of a database which 
you have added using something like phpMyAdmin. This database should be empty. Finally, for the session fingerprint, 
just use a random alphanumeric string. For automatedEmail, use something like "info@yourdomain.com" or 
"no-reply@yourdomain.com". That will be the email address used when sending things like "Welcome" and "Forgotten 
Password" emails.

With these parameters set, FTP the entire contents of the source folder up to a folder in your website's public
HTML folder. After this, you will spend most of your time visting the system via two index pages:

Assuming, from here on, that you have installed the system in a folder called "evaporate" in your website's public
HTML folder.

www.yourdomain.com/evaporate will list your products to end users, and allow them to redeem download codes.
www.yourdomain.com/evaporate/admin will allow you to add, remove and manage your products.

Visit the admin section immediately after you have FTP'd the files up. You will be asked to set your admin 
user name and password. After this, the admin panel will ask you to log in.