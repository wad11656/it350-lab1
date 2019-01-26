# Lab 1

IT350 Lab 1 by Wade Murdock.

## Database Structure:

### Database: **puppies_unlimited**

`(pk)` = primary key

`(fk)` = foreign key

   Table: **customer**

         -----------------------------------------------------------------------------------------
        |customer_id(pk)|customer_name|customer_age|customer_address|customer_phone|customer_email|
         -----------------------------------------------------------------------------------------

   Table: **puppy**

         -------------------------------------------------------------------------------------------------------------------
        |puppy_id(pk)|puppy_name|puppy_age|puppy_breed|puppy_location|puppy_immunizationrecords|puppy_photo|puppy_timeposted|
         -------------------------------------------------------------------------------------------------------------------

   Table: **customer_puppy**

         -------------------------------------------------
        |customerpuppy_id(pk)|customer_id(fk)|puppy_id(fk)|
         -------------------------------------------------

   Table: **purchase** (empty)

         ----------------------------------------------------------
        |purchase_id(pk)|customer_id(fk)|puppy_id(fk)|purchase_time|
         ----------------------------------------------------------

## Functionality:

### READ.PHP Example Query:

`http://192.168.50.92/it350site/read.php?user=employee&secretkey=123654&table=puppy&order=puppy_age&limit=5&conditions=puppy_name%20LIKE%20%27Alfred%27`

### INSERT.PHP Example Query:

`http://192.168.50.92/it350site/insert.php?user=employee&secretkey=123654&table=customer&columns=customer_name,customer_age,customer_address,customer_phone,customer_email&values=%27George%27,%2735%27,%271234%27,%271234567%27,%271234%27`

### UPDATE.PHP Example Query:

`http://192.168.50.92/it350site/update.php?user=employee&secretkey=123654&table=customer&set=customer_name=%27Phil%27&conditions=customer_id=%273%27`

### DELETE.PHP Example Query:

`http://192.168.50.92/it350site/delete.php?user=employeeadmin&secretkey=321456&table=customer&conditions=customer_id=%272%27`

## (Intentional) Quirks:

### ALL:

* Valid emails (i.e. `bob@gmail.com`), file names with extensions (i.e. `.jpg`), and phone numbers with hyphens (i.e. `-`) are not accepted as URL parameters due to `jgiboney`'s binding parameter code's limitations. The biggest resulting limitation from this is that you therefore cannot `INSERT`/`UPDATE` a valid email into the `customer_email` column.

    `jgiboney` admits this is an error on his part, did not expect us to fully understand--nor, therefore, be able to modify--the binding parameter code he provided, and rather expected us to just copy and paste it where appropriate, so points are not to be taken off for our API not being able to accept valid emails (i.e. `bob@gmail.com`) or phone numbers with hyphens (i.e. `555-4321`).

### READ.PHP:

* Even though accepted by MySQL, a `LIMIT` query that contains values with leading 0's is **rejected**.

    (Example in URL: `limit=003,5`)

* Even though accepted by MySQL, a `LIMIT` query that contains values that are out-of-scope from the working table's # of rows is **rejected**.

    (Example in URL: `limit=1,4` when there are only 4 total rows in the table.)

    **NOTE:** If an out-of-scope query occurs, the error message will tell you how many rows are in the table so you can adjust.

### INSERT.PHP:

* `INSERT` queries for the table `puppy` need one of the following minimum criteria:

    `puppy_location`,`puppy_photo`

    OR
    
    `puppy_name`,`puppy_location`

    ...because otherwise, there is not enough identifying information per table entry to distinguish one dog from another.

* `INSERT` queries for the table `customer` need one of the following minimum criteria:

    `customer_name`,`customer_address`

    OR

    `customer_name`,`customer_phone`

    OR

    `customer_name`,`customer_email`

    ...because at the very least, Puppies Unlimitied&trade; needs to know a customer's name and have a way to contact them.

* Only **7- or 10- digit** `customer_phone` numbers are accepted in the `customer` table (i.e. `5554321`).

* Only **positive integer** `customer_age` and `puppy_age` values are accepted in the `customer` and `puppy` tables, respectively (i.e. `35`).