# Lab Extension 1

IT350 Lab 1 by Wade Murdock.

GitHub: https://github.com/wad11656/it350-lab1

API: http://40.117.58.200/it350site/

## Database Structure:

### Database: **puppies_unlimited**

`(pk)` = primary key

`(fk)` = foreign key

   Table: **adoption**

      ----------------------------------------------------------
     |adoption_id(pk)|customer_id(fk)|puppy_id(fk)|purchase_time|
      ----------------------------------------------------------

   Table: **breed**

      -----------------
     |id(pk)|breed_name|
      -----------------

   Table: **customer**

     --------------------------------------------
    |id(pk)|customer_name|age|address|phone|email|
     --------------------------------------------

   Table: **immunization**

      -------------------------------------------------
     |id(pk)|immunization_name|immunization_description|
      -------------------------------------------------

   Table: **parent**

      ---------------------------------------------------------------------------
     |relationship_id(pk)|puppy_parent_id(fk)|puppy_child_id(fk)|mother_or_father|
      ---------------------------------------------------------------------------

   Table: **puppy**

     ------------------------------------------------------
    |id(pk)|puppy_name|age|location|photos|date_time_posted|
     ------------------------------------------------------

   Table: **puppy_breed**

     --------------------------------------------
    |puppy_breed_id(pk)|puppy_id(fk)|breed_id(fk)|
     --------------------------------------------

   Table: **puppy_immunization**

      -----------------------------------------------------------------------------
     |immu_given_id(pk)|puppy_id(fk)|vet_id(fk)|immunization_id(fk)|immu_given_time|
      ------------------------------------------------------------------------------

   Table: **veterinarian**

      -------------------------------------
     |id(pk)|veterinarian_name|address|city|
      -------------------------------------

## Functionality:

<del>### READ.PHP Example Query:</del>

<del>`http://40.117.58.200/it350site/read.php?user=employee&secretkey=123654&table=puppy&order=puppy_age&limit=5&conditions=puppy_name%20LIKE%20%27Alfred%27`</del>

<del>### INSERT.PHP Example Query:</del>

<del>`http://40.117.58.200/it350site/insert.php?user=employee&secretkey=123654&table=customer&columns=customer_name,age,address,phone,email&values=%27George%27,%2735%27,%271234%27,%271234567%27,%27George@gmail.com%27`</del>

<del>### UPDATE.PHP Example Query:</del>

<del>`http://40.117.58.200/it350site/update.php?user=employee&secretkey=123654&table=customer&set=customer_name=%27Phil%27&conditions=id=%273%27`</del>

<del>### DELETE.PHP Example Query:</del>

<del>`http://40.117.58.200/it350site/delete.php?user=employeeadmin&secretkey=321456&table=customer&conditions=id=%272%27`</del>

<del>## (Intentional) Quirks:</del>

**UPDATE (2/15/19):** Functionality adheres to that found in the test driver.

### ALL:

<del>* Valid emails (i.e. `bob@gmail.com`), file names with extensions (i.e. `photo.jpg`), and phone numbers with hyphens (i.e. `555-4321`) are not accepted as URL parameters due to `jgiboney`'s binding parameter code's limitations. The biggest resulting limitation from this is that you therefore cannot `INSERT`/`UPDATE` a valid email into the `customer_email` column.</del>

   <del>`jgiboney` admits this is an error on his part, did not expect us to fully understand--nor, therefore, be able to modify--the binding parameter code he provided, and rather expected us to just copy and paste it where appropriate, so points are not to be taken off for our API not being able to accept valid emails (i.e. `bob@gmail.com`) or phone numbers with hyphens (i.e. `555-4321`).</del>

**UPDATE (2/15/19):** Emails and phone numbers with hyphens are now accepted and are checked for proper formatting.

### READ.PHP:

* Even though accepted by MySQL, a `LIMIT` query that contains values with leading 0's is **rejected**.

    (Example in URL: `limit=003,5`)

* Even though accepted by MySQL, a `LIMIT` query that contains values that are out-of-scope from the working table's # of rows is **rejected**.

    (Example in URL: `limit=1,4` when there are only 4 total rows in the table.)

    **NOTE:** If an out-of-scope query occurs, the error message will tell you how many rows are in the table so you can adjust.

### INSERT.PHP:

* `INSERT` queries for the table `puppy` need at least a `puppy_name`.

* `INSERT` queries for the table `customer` need at least a `customer_name`.

* Only **7- or 10- digit** `customer_phone` numbers are accepted in the `customer` table (i.e. `5554321`).

* Only **positive integer** `customer_age` and `puppy_age` values are accepted in the `customer` and `puppy` tables, respectively (i.e. `35`).