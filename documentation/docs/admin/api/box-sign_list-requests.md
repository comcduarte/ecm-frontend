# Box Sign

## List Requests

```
php bin/cli.php box-sign:list-requests

+--------------------------------------+-----------------------------------------------------------------------------+------------------+--------------------------+--------------------------+
| id                                   | name                                                                        | status           | created_at               | finished_at              |
+--------------------------------------+-----------------------------------------------------------------------------+------------------+--------------------------+--------------------------+
| 4c06cfaf-3e70-4e6f-9fba-c5d1dff8a6cc | Purchase Order-25-0176 po (1).pdf                                           | error_converting | 2026-04-14T14:19:22.013Z |                          |
| 45c23096-7512-492a-86a9-0d923c0cb0c6 | 2026-0106 REPLACE 10 TON GAS ELECTRIC ROOFTOP@180 JOHNSON ST.pdf            | viewed           | 2026-04-08T18:57:45.982Z | 2026-04-08T19:01:30.347Z |
| 8b3b9ee4-044f-42a6-b737-0031476c2c72 | 2023-0081-AM0098 Conceptual Design Support for the Arcade Site.pdf          | viewed           | 2026-04-07T13:58:59.812Z | 2026-04-07T14:31:03.552Z |
| 07e6b296-453f-4b12-a028-057f0edb5883 | 2026-0105 BID #2025-019 - TURF CARE SERVICES.pdf                            | signed           | 2026-04-02T13:58:13.251Z | 2026-04-07T17:12:24.843Z |
| 5ad096f5-76ea-4ec3-8bbb-aebf07aeb102 | 2026-0079 DISPOSAL OF MUNICIPAL SOLID WASTE.pdf                             | viewed           | 2026-04-01T13:12:32.754Z | 2026-04-01T13:14:38.758Z |
| f5c206ae-4f43-4d6b-b57e-eae8e5ecf220 | Purchase Order-26-0079 murphy rd PO (1).pdf                                 | error_converting | 2026-03-31T20:50:16.083Z |                          |
| 988234ca-60bf-4fb2-ade7-4519c0b20974 | 2026-0103 CREATIVE RECREATION-PLAYGROUND, RECREATION AND PARK EQUIPMENT.pdf | viewed           | 2026-03-25T18:22:55.955Z |                          |
| 7592719c-66fa-4a8a-8474-b9a429edb9bc | Purchase Order-26-0074 PO TATA (1).pdf                                      | error_converting | 2026-03-23T14:27:40.076Z |                          |
| b0977ef2-2eed-4ee7-ae6b-29ab70270055 | 2026-0095 CLEAN HARBORS PCB SOIL DISPOSAL.pdf                               | viewed           | 2026-03-18T16:57:42.172Z | 2026-03-18T17:00:26.653Z |
| d13548dc-fc2c-464e-ac17-f0767671a861 | 2026-0080 2026 CHEVROLET COLORADO FOR PARKING.pdf                           | signed           | 2026-03-17T14:34:06.715Z | 2026-03-18T19:25:07.036Z |
+--------------------------------------+-----------------------------------------------------------------------------+------------------+--------------------------+--------------------------+
```

```
php bin/cli.php box-sign:get-request 4c06cfaf-3e70-4e6f-9fba-c5d1dff8a6cc

{
    "id": "8b3b9ee4-044f-42a6-b737-0031476c2c72",
    "type": "sign-request",
    "_version": "2024.0",
    "collaborator_level": "owner",
    "name": "2023-0081-AM0098 Conceptual Design Support for the Arcade Site.pdf",
    "parentFolder": {
        "id": "371747826261",
        "type": "folder"
    },
    "sender_email": "automationuser_2420059_gmqqphrkqt@boxdevedition.com",
    "sender_id": 44191335913,
    "sign_files": {
        "files": [
            {
                "id": "2190542490227",
                "etag": "1",
                "type": "file",
                "sequence_id": "1",
                "name": "2023-0081-AM0098 Conceptual Design Support for the Arcade Site.pdf",
                "sha1": "1f81619b1056308bf7a19916946921d019275bd0",
                "file_version": {
                    "id": "2422208007827",
                    "type": "file_version",
                    "sha1": "26e9a15b01c5ed526daf6218813dcfd2c0aa414e"
                }
            }
        ],
        "is_ready_for_download": true
    },
    "signers": [
        {
            "email": "automationuser_2420059_gmqqphrkqt@boxdevedition.com",
            "role": "final_copy_reader",
            "is_in_person": false,
            "order": 0,
            "verification_phone_number": null,
            "embed_url_external_user_id": null,
            "redirect_url": null,
            "declined_redirect_url": null,
            "login_required": false,
            "has_viewed_document": false,
            "signer_decision": null,
            "signer_group_id": null,
            "inputs": [],
            "embed_url": null,
            "iframeable_embed_url": null,
            "suppress_notifications": false,
            "smart_card_required_type": null,
            "attachments": [],
            "language": null
        },
        {
            "email": "mayor@middletownct.gov",
            "role": "signer",
            "is_in_person": false,
            "order": 0,
            "verification_phone_number": null,
            "embed_url_external_user_id": null,
            "redirect_url": null,
            "declined_redirect_url": null,
            "login_required": false,
            "has_viewed_document": true,
            "signer_decision": {
                "type": "signed",
                "finalized_at": "2026-04-07T14:31:03.552Z",
                "additional_info": null
            },
            "signer_group_id": null,
            "inputs": [
                {
                    "document_tag_id": null,
                    "text_value": "Nocera",
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "last_name",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": "Gene Nocera",
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "signature",
                    "content_type": "signature",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": "Apr 7, 2026",
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "date",
                    "content_type": "date",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": " City of Middletown     ",
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "company",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": "Mayor",
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "title",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": "Gene",
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "first_name",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                }
            ],
            "embed_url": null,
            "iframeable_embed_url": null,
            "suppress_notifications": false,
            "smart_card_required_type": null,
            "attachments": [],
            "language": "en"
        },
        {
            "email": "srossignol@crosskey.com",
            "role": "signer",
            "is_in_person": false,
            "order": 0,
            "verification_phone_number": null,
            "embed_url_external_user_id": null,
            "redirect_url": null,
            "declined_redirect_url": null,
            "login_required": false,
            "has_viewed_document": false,
            "signer_decision": null,
            "signer_group_id": null,
            "inputs": [
                {
                    "document_tag_id": null,
                    "text_value": null,
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "first_name",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": null,
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "date",
                    "content_type": "date",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": null,
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "title",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": null,
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "company",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": null,
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "signature",
                    "content_type": "signature",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                },
                {
                    "document_tag_id": null,
                    "text_value": null,
                    "checkbox_value": null,
                    "date_value": null,
                    "type": "text",
                    "content_type": "last_name",
                    "page_index": 1,
                    "read_only": false,
                    "validation": null,
                    "reason": null,
                    "is_validated": null
                }
            ],
            "embed_url": null,
            "iframeable_embed_url": null,
            "suppress_notifications": false,
            "smart_card_required_type": null,
            "attachments": [],
            "language": null
        }
    ],
    "signingLog": {
        "id": "2190565111050",
        "type": "file"
    },
    "source_files": [
        {
            "id": "2169004261812",
            "etag": "1",
            "type": "file",
            "sequence_id": "1",
            "name": "2023-0081-AM0098 Conceptual Design Support for the Arcade Site.docx",
            "sha1": "6445fdb39b3a0cfb1b135e0d258b94244a394782",
            "file_version": {
                "id": "2398104918958",
                "type": "file_version",
                "sha1": "6445fdb39b3a0cfb1b135e0d258b94244a394782"
            }
        }
    ],
    "status": "viewed"
}
```
