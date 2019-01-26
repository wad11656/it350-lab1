# Lab 1

IT350 Lab 1 by Wade Murdock.

## Database Structure:

### Database: **puppies_unlimited**

    Table: **customer**

         -------------------------------------------------------------------------------------
        |customer_id|customer_name|customer_age|customer_address|customer_phone|customer_email|
         -------------------------------------------------------------------------------------

    Table: **puppy**

         ---------------------------------------------------------------------------------------------------------------
        |puppy_id|puppy_name|puppy_age|puppy_breed|puppy_location|puppy_immunizationrecords|puppy_photo|puppy_timeposted|
         ---------------------------------------------------------------------------------------------------------------

## Functionality:

### READ.PHP Example Query:



## (Intentional) Quirks:

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