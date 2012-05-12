# Installation instructions

To install Pic Puller you will need to place 2 folders containing Pic Puller files into your ExpressionEngine installation. 

Alongside this README file, you will find 2 directories: "system" and "themes".

## Let's address the "system" folder first.

Navigate to the folder "ig_picpuller" in the "system" folder, system > expressionengine > third_party > ig_picpuller.

Place this "ig_picpuller" folder in your EE site in its "system > expressionengine > third_party" folder.

## Now for the themes folder.

Navigate to the folder "ig_picpuller" in the "themes" folder, themes > third_party > ig_picpuller.

Place this "ig_picpuller" folder in your EE site in its "themes > third_party" folder.

## In the control panel

Log in as an Super Admin to your ExpressionEngine control panel and install the module and the fieldtype. (The use of the fieldtype requires the installation of the module, but the module is not dependent on the fieldtype, so you may choose to only install the module.)

Follow the instructions within the module to set up your Instragram application.

# How to update from a previous version

(Before you update: Please do a backup of your database and files before updating. Moving to version Pic Puller 1.1.0 updates 2 tables in your ExpressionEngine database: exp_ig_picpuller_oauths and exp_ig_picpuller_credentials. Backups are the only way to go back to your previous data.)

To update Pic Puller, replace the "ig_picpuller" folder in your system > expressionengine > third_party folder with the most recent version. Do the same for the "themes" folder as well.

Visit the Pic Puller module page in your control panel which will trigger the update of the software. If you do not see the version number update, you may need to press the "Run Module Updates" button on the upper left corner of the Modules screen within the EE control panel.

If you are updating PicPuller from version 0.9.2 or lower, you will need to install the field type if you choose to use it. 

WARNING: ExpressionEngine's installation screen can be confusing if you have the module installed already and are trying to add the field type. Both radio buttons should be set to "install" in the status are. Do not accidentally uninstall the module. If you do, you will need to go through the Instagram app set up again. This will most likely mean you need to update your authorization URL at Instagram, but not create an entirely new application. Your users will also need to reauthorize with Pic Puller.

# Notes on MSM compatibility:

The major update to version 1.1.0 is compatibility with ExpressionEngine Multiple Site Manager. This allows Pic Puller to install multiple Instagram applications within an EE site with MSM installed. Each site you manage will be able to have its own Instagram application. Read the accompanying details in the accompanying documentation file for details.

--

If you have any questions, please drop me a line at john@johnfmorton.com.

Favoriting and feedback at http://devot-ee.com/add-ons/pic-puller-for-instagram is greatly appreciated. 

I hope you enjoy Pic Puller.

-John