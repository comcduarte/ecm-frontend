# Unable to Find Contract

There may be times when a contract becomes disjointed.  File and folders are stored in directories within box, but contracts and metadata information is displayed in ECM based on criteria.  I will walk you through some examples you may come across, how to identify the problem, and a proper resolution.

1. An Amendment is not linked to a Contract, and it is not showing up in my Workflow Queue.
    i. Verify that the Amendment should be in a workflow queue, and confirm it does not show up in the list.
    ii. Navigate the Box Folder for that workflow queue and confirm the amendment folder exists.
    iii. This means the metadata is the problem.  
        a. Open the amendment folder directly on box.com.  
        b. Under Details, update the ecm-application metadata template.
        c. If the queue field has the wrong value, insert the folder id for the workflow queue it belongs in.  This information should populate children objects due to the implemented cascade policy.
        * These functions may also be performed with the ecm-cli.
        d. If the queue field has no value, and/or you can manually confirm that it has been signed by all parties, the queue field does not have to be populated; you may simply move the amendment to a subfolder of the original contract in the cabinet.
        e. If the approvals metadata template is missing both purchasing and the mayors office, yet the contract is fully executed, you can put the contract back in the purchasing queue by filling in the queue field, and then let Purchasing and the Mayors Office Route the Contract as normal to populate those values.

