Pilau Repair Meta
=======================

A WordPress plugin for one-click, object-by-object repairing of corrupted serialized data in metadata.

* Adds a _Repair meta_ option to the row actions for posts (including CPTs) and pages.
* Clicking this will search for serialized arrays in the post's meta, and repair them if possible.
* The replaced value(s) are stored in a file in the uploads folder, just in case. Note that this file's contents only apply to the last operation - it's overwritten the next time you click _Repair meta_.

Possible features:

* Support for Media Library and Users
* Add _Repair meta_ to bulk actions drop-down

I'll add these if a project I work on needs them. If you need new features for your project, feel free to add them and submit a pull request :)