# Registering a New User Account

Once the user has registered themselves with the application, a pending user record is created.  It is the administrators' responsibility to assign that user to their respective department, and mark the user record as Active.

As of version 1, this function needs to be performed by a manual database entry.  Open phpMyAdmin and follow the following steps.

1. Open the production version of the database and navigate to the user_roles table.
2. Click the Insert toolbar button to add a new entry.
3. The values can be selected from the drop downs.  Only registered users will appear in the user dropdown, and only applicable departments will appear in the role dropdown.
    * A normal departmental user is added to the DEPT role only.
    * A department head, one that has the ability to route a contract to Risk to begin the workflow, must also be added to the ECM_DEPARTMENT role.
    * Workflow Reviewers, based on their stage of review, will also have roles to be assigned.
    * To make it easier, you can unchecked the ignore checkbox and added a second record automatically from one form submission.  This makes it convenient to added a department employee because you may assign them the proper department and/or department head role simultaneously.
4. Open the users table and verify that the user account has an active status, and not pending.  If so, change it to active.

The next time a user logs in they will see all the queues and function available to their roles.