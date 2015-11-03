# Pic Puller for Instagram

Pic Puller was written for ExpressionEngine 2. It is not currently compatibile with ExpressionEngine 3 and I don't have a project that would prompt me to do that update but Pic Puller is not disappearing entirely. It is currently maintained for Craft CMS. 

This code base for the EE 2 version is here primarialy for anyone who is on EE2 and would like to use it but can't because it is no long for sale on the Devotee store. 

# Installation instructions

To install Pic Puller you will need to place 2 folders containing Pic Puller files into your ExpressionEngine 2 installation. 

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

(Before you update: Please do a backup of your database and files before updating. Moving to version Pic Puller 1.1.0 and Pic Puller 1.3.0 updates 2 tables in your ExpressionEngine database: exp_ig_picpuller_oauths and exp_ig_picpuller_credentials. Backups are the only way to go back to your previous data.)

To update Pic Puller, replace the "ig_picpuller" folder in your system > expressionengine > third_party folder with the most recent version. Do the same for the "themes" folder as well.

Visit the Pic Puller module page in your control panel which will trigger the update of the software. If you do not see the version number update, you may need to press the "Run Module Updates" button on the upper left corner of the Modules screen within the EE control panel.

If you are updating PicPuller from version 0.9.2 or lower, you will need to install the field type if you choose to use it. 

WARNING: ExpressionEngine's installation screen can be confusing if you have the module installed already and are trying to add the field type. Both radio buttons should be set to "install" in the status are. Do not accidentally uninstall the module. If you do, you will need to go through the Instagram app set up again. This will most likely mean you need to update your authorization URL at Instagram, but not create an entirely new application. Your users will also need to reauthorize with Pic Puller.

# Note for those update from a version before 1.3.0

In version 1.3.0 there was a significant change that will BREAK your current working templates unless you read through this section. It's an easy fix for you, but it must be done after you install the update or you will not have images returned in your Pic Puller loops.

Initially Pic Puller's returned tags were based on the tag returned by Instagram. That meant if Instagram returned 'low_resolution' then your Pic Puller tag would be 'low_resolution'. The use of the default names was creating problems for a number of users unfortunately. Naming conflicts were causing data not to be rendered. 

To solve this naming conflict problem, starting with version 1.3.0, Pic Puller will prefix every tag returned with 'ig_' by default. The prefix can be set on a per site basis to whatever you like.

If your templates were working for you before version 1.3.0, you may choose to reset your Pic Puller application to using an empty string, i.e. no prefix at all. 

To edit the prefix, in the Pic Puller module visit the "Active Site App Info" tab and choose 'edit' under the "Prefix for all tags" column. 

Your existing templates should continue to work with no update needed to their code.

# Notes on MSM compatibility:

The major update to version 1.1.0 is compatibility with ExpressionEngine Multiple Site Manager. This allows Pic Puller to install multiple Instagram applications within an EE site with MSM installed. Each site you manage will be able to have its own Instagram application. Read the details in the accompanying documentation file for details.

-John