# Lab: Extension 2

IT350 Lab: Extension 2 by Wade Murdock.

GitHub: https://github.com/wad11656/it350-lab1

API: http://192.168.50.92/it350site/

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

     --------------------
    |id(pk)|person_id(fk)|
     ---------------------
   
   Table: **employee**

      -----------------------------------------------
     |id(pk)|person_id(fk)|hire_date|termination_date|
      -----------------------------------------------   
   
   Table: **employee_manages**

      --------------------------------------
     |id(pk)|employee_id(fk)|location_id(fk)|
      --------------------------------------   
   
   Table: **employee_works_at**

      --------------------------------------
     |id(pk)|employee_id(fk)|location_id(fk)|
      --------------------------------------    
   
   Table: **immunization**

      -------------------------------------------------
     |id(pk)|immunization_name|immunization_description|
      -------------------------------------------------
   
   Table: **location**

      --------------------
     |id(pk)|location_name|
      --------------------
   
   Table: **parent**

      ---------------------------------------------------------------------------
     |relationship_id(pk)|puppy_parent_id(fk)|puppy_child_id(fk)|mother_or_father|
      ---------------------------------------------------------------------------

   Table: **person**

     ------------------------------------------
    |id(pk)|person_name|age|address|phone|email|
     ------------------------------------------
   
   Table: **puppy**

     ----------------------------------------------------------------------
    |id(pk)|puppy_name|age|location(fk)|photos|date_time_posted|description|
     ----------------------------------------------------------------------

   Table: **puppy_breed**

     --------------------------------------------
    |puppy_breed_id(pk)|puppy_id(fk)|breed_id(fk)|
     --------------------------------------------

   Table: **puppy_immunization**

      -----------------------------------------------------------------------------
     |immu_given_id(pk)|puppy_id(fk)|vet_id(fk)|immunization_id(fk)|immu_given_time|
      ------------------------------------------------------------------------------

   Table: **veterinarian**

      -------------------------
     |id(pk)|person_id(fk)|city|
      -------------------------

## Functionality:

<del>### SEARCH.PHP Example Query:</del>

<del>`http://192.168.50.92/it350site/search.php?user=employee&secretkey=123654&query=dog`</del>

<del>### LONGEST_WAITING.PHP Example Query:</del>

<del>`http://192.168.50.92/it350site/longest_waiting.php?user=employee&secretkey=123654`</del>

<del>### LOCATION_EMPLOYEES.PHP Example Query:</del>

<del>`http://192.168.50.92/it350site/location_employees.php?user=employee&secretkey=123654`</del>

<del>### EMPLOYEE_CUSTOMERS.PHP Example Query:</del>

<del>`http://192.168.50.92/it350site/employee_customers.php?user=employee&secretkey=123654`</del>

<del>### NOT_ALL_BREEDS.PHP Example Query:</del>

<del>`http://192.168.50.92/it350site/not_all_breeds.php?user=employee&secretkey=123654`</del>

<del>### WORKS_ALL.PHP Example Query:</del>

<del>`http://192.168.50.92/it350site/works_all.php?user=employee&secretkey=123654&manager=Jane`</del>

<del>## (Intentional) Quirks:</del>

**UPDATE (2/15/19):** Functionality adheres to that found in the test driver.

## (Intentional) Quirks:

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

* Only **7- or 10- digit** `phone` numbers are accepted (i.e. `555-4321`).

* Only **positive integer** `age` values are accepted (i.e. `35`).