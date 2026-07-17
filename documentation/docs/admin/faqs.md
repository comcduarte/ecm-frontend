# Frequently Asked Questions

   - I don't see my contract listed in the Department Dashboard.
      - Administrative Tasks
         - Open the contract folder object and verify that the "Permission" Metadata Template is applied, and the requesting department ID is populated.
         - If not, apply the new instance to the folder object, and set Department to the department's queue folder ID
         - ecm-cli metadata:apply-instance --data '{"department": "1234567890"}' --scope "enterprise" 1234567890 permission
            - ecm-cli metadata:apply-instance [-d|--data DATA] [-s|--scope SCOPE] [--] <folder_id> <template_key>
         
   - I need to update the vendor information in the metadata for my contract.
      - Administrative Tasks
         - Open the contract folder object and verify that the "Vendor" Metadata Template is applied and populated.
         - Verify what information needs to be changed. (For this instance we'll use just the vendor email address)
         - ecm-cli metadata:update-instance [-d|--data DATA] [-s|--scope SCOPE] [--] <folder_id> <template_key>
         - ecm-cli metadata-cascade-policy:list-policies [-m|--marker MARKER] [-o|--offset OFFSET] [-e|--owner_enterprise_id OWNER_ENTERPRISE_ID] [--] <folder_id>
         - ecm-cli metadata-cascade-policy:force-apply-policy MzU0ODc2NzY0OTc0I2U1NjM5NjAyNjYjdmVuZG9yLWEzM2UxNzliLTc1OTMtNDJkZC05N2U0LWFmNWM5ZTE1OTJjMg overwrite.
   - How do I resend a BoxSign Request?
   - I need to resend a BoxSign Request to a new email address; the first one was lost.
      - Policy
         - You may only resend a BoxSign Request to the existing email address. Purchasing has the permission to do so.  An administrator can also use the following command:
            - ecm-cli box-sign:list-requests
            - ecm-cli box-sign:resend-request 035b796c-c391-4d54-99a6-8bd7bab000de
         - In the event the vendor did receive the email, but clicking on the link reveals that it has expired, they have a small link on the bottom of that page which will allow them to resend the BoxSign Request themselves.
         - In the event you need to change to whom the email is being sent, you must cancel the original BoxSign Request, delete the Signed files, and Signing Log, and create a new BoxSign Request to the new email address.  You should update the email address in the Vendor Metadata Template before this is done.
            - ecm-cli box-sign:cancel-request 30f4f149-4396-4df9-b182-9c42e2d6776d --delete-sign-files --delete-signing-log
            - --delete-sign-files --delete-signing-log are optional arguments
   - I receive a 500 Error view opening a contract
      - Review Log File for specific error.
      - Cabinet Files may not have the proper metadata cascade policies applied
If the File does not contain a ecm-application metadata instance, apply cascade policy to folder instance and force apply to all children.