//--------------------------------
//﻿ Zalora FILE REST API v1.0
//--------------------------------


Author: Mauricio Ashimine
---------------------------------



Index 

1. API Requirements
2. Summary
3. Technologies/Tools used
4. Setup
5. Routes
6. API docs
7. Basic Usage




//---------------------------------
// 1. API Objectives
//---------------------------------


Write a program that provides HTTP API to store and retrieve files. Features:

    - Upload a new file
    - Retrieve an uploaded file by name
    - Delete an uploaded file by name
    - Bonus point: Include a nix module
    - More bonus point: If multiple files have similar contents, reuse the contents somehow to save space


//---------------------------------
// 2. Summary
//---------------------------------


This is a REST API for managing files. Before using the proper API, user must authenticate via credentials and use a valid token (JWT)

Basically this application uploads a new file, retrieves an uploaded file and deletes an uploaded file. In order to save some disk space, the application detects name, mime_type and the file's content (hashing it's content). If file content is identically to some other file stored in database , API rejects user's file (Sends a json response "The content you're trying to upload already exist").

Another vague approach to fulfill this design requirement, could be opening every file chunking lines and comparing those against all stored files, save the line's position in db and reconstruct every file. But file will be different to user's original file and this method would use a lot of computing resources due to database querys and opening multiple files (syscalls).

I know this is not the best way to provide reuse of content. I think it could be done with some other filtering and indexing tools like elastic search or map reduce framework. But unfortunately due to some time issues, i couldn't finish my research.

Requirements explicit says: "delete and retrieve files by name". Actually I based my API on Google's Drive API structure and Google's Drive "retrieves files by id", so I developed my app this way. Otherwise I design other routes for retrieving and deleting files by id as you request.


//----------------------------------
// 3. Technologies/Tools used
//----------------------------------


- API was developed in PHP7 with Laravel v5.3
- JWT protocol was used for Auth's purpose (tymon/jwt-auth)
- LEMP stack was used (Linux, Nginx, MySQL, PHP) in a vagrant box (Homestead vbox)


//----------------------------------
// 4. Setup
//----------------------------------


To install the API, follow the instructions:

- Please configure your web server to project's document root (public folder).
- Copy an .env file form .env_example.
- Run these commands:
    - php artisan key:generate
    - php artisan migrate --seed
    - php artisan storage:link
    - composer install


//----------------------------------
// 5. Routes
//----------------------------------


API is conformed by 8 routes:

+--------+--------------------------------+--------------------------------------------------+
| Method |          URI                   |                 ACTION                           |
+--------+----------+---------------------+--------------------------------------------------+
| POST   | api/v1/auth                    | User send credentials, app returns a valid token |
| GET    | api/v1/auth/me                 | Shows user's information                         |
| GET    | api/v1/files                   | Shows all the files stored                       |
| POST   | api/v1/files                   | Uploads a certain file                           |
| GET    | api/v1/files/{file}            | Retrieves a certain file by id                   |
| DELETE | api/v1/files/{file}            | Deletes a certain file by id                     |
+--------|--------------------------------+ -------------------------------------------------+
| GET    | api/v1/fil3s/{fil3}            | Retrieves a certain file by name                 |
| DELETE | api/v1/fil3s/{fil3}            | Deletes a certain file by name                   |
+--------|--------------------------------+--------------------------------------------------+

For more detailed information, please read "6. API DOCS"


//----------------------------------
// 6. API Docs
//----------------------------------


Detailed API documentation was developed in Swagger. The swagger YAML file is included in the swagger directory (root project folder).

You can use it in your swagger-ui application or use the online swagger editor (http://swagger.io/swagger-editor/). If you choose online swagger editor, please paste the swagger.yaml content into the editor. API will be displayed in HTML form.


//----------------------------------
// 7. Basic Usage
//----------------------------------

For basic usage you can use CURL or some webapp as POSTMAN.

Credentials:

email: admin@admin.com
password: secret

---------------------------------------

1. AUTHENTICATION: Use "auth" method for authentication

- your_host_name/api/v1/auth
- use "credentials" as form-data key-values pairs

Response: A valid token. e.g 

{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL3phbG9yYS5hcHBcL2FwaVwvdjFcL2F1dGgiLCJpYXQiOjE0ODM4MTYzODksImV4cCI6MTQ4MzgxOTk4OSwibmJmIjoxNDgzODE2Mzg5LCJqdGkiOiJjZjRkMjI0OGU2MTFjMTEyYjRjYThiNTRiNzExNWNlNSJ9.ukXA40FQMfs6EVrdBngxhI5w-JJWkWQOF2N5N3__3e4"
}

----------------------------------------

2. GET FILES: Use "files" method for listing all files

- your_host_name/api/v1/fil3s/php?token=eyJ0eXAiOiJKV1Qi
- use your valid token as url parameter.

Response: All stored files.

{
    "data": [

        {
            "id": 20,
            "name": "test.txt",
            "mime_type": "text/plain"
        },
        {
            "id": 21,
            "name": "2",
            "mime_type": "text/plain"
        },
        {
            "id": 22,
            "name": "php",
            "mime_type": "text/x-c++"
        }

    ]
}

Note: Use the other methods with the corresponding parameters to perform the desired actions

// -------------------------------------

