# Updating Special Note

(This note exists in the README file, but it's important enough that it's also included as a separate file for this version as because of its importance.)

In version 1.3.0 there was a significant change that will BREAK your current working templates unless you read through this document. It's an easy fix for you, but it must be done after you install the update or you will not have images returned in your Pic Puller loops.

Initially Pic Puller's returned tags were based on the tag returned by Instagram. That meant if Instagram returned 'low_resolution' then your Pic Puller tag would be 'low_resolution'. The use of the default names was creating problems for a number of users unfortunately. Naming conflicts were causing data not to be rendered. 

To solve this naming conflict problem, starting with version 1.3.0, Pic Puller will prefix every tag returned with 'ig_' by default. The prefix can be set on a per site basis to whatever you like.

If your templates were working for you before version 1.3.0, you may choose to reset your Pic Puller application to using an empty string, i.e. no prefix at all. 

To edit the prefix, in the Pic Puller module visit the "Active Site App Info" tab and choose 'edit' under the "Prefix for all tags" column. 

Your existing templates should continue to work with no update needed to their code.

If you have any questions, please drop me a line at john@johnfmorton.com.

As always, favoriting and feedback at http://devot-ee.com/add-ons/pic-puller-for-instagram is greatly appreciated. 

-John