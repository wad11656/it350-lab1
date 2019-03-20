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

=========================================

**Project 1**:

=========================================

### INSERT.PHP Example Query:

`http://192.168.50.92/it350site/insert.php?user=employee&secretkey=123654&table=location&columns=location_name&values='Downtown SLC'`

### DELETE.PHP Example Query:

`http://192.168.50.92/it350site/delete.php?user=employeeadmin&secretkey=321456&table=adoption&conditions=puppy_id>='0'`

### READ.PHP Example Query:

`http://192.168.50.92/it350site/read.php?user=employee&secretkey=123654&table=puppy&order=puppy_age&limit=5&conditions=puppy_name%20LIKE%20%Mutt%27`

### UPDATE.PHP Example Query:

`http://192.168.50.92/it350site/update.php?user=employee&secretkey=123654&table=customer&set=customer_name=%27Phil%27&conditions=person_id=%27937%27`

=========================================

**Extension 1**:

=========================================

### PEDIGREE.PHP Example Query:

`http://192.168.50.92/it350site/pedigree.php?user=employee&secretkey=123654&puppy=Jack`

### INSERT_JOIN.PHP Example Query:

`http://192.168.50.92/it350site/insert_join.php?user=employee&secretkey=123654&table=customer&columns=person_id&values='Joe'&fktables=person&fkcolumns=person_name`

### VETCITIES.PHP Example Query:

`http://192.168.50.92/it350site/vetcities.php?user=employee&secretkey=123654`

### UNADOPTEDBREEDCOUNTS.PHP Example Query:

`http://192.168.50.92/it350site/unadoptedbreedcounts.php?user=employee&secretkey=123654`

### CUSTOMERPUPS.PHP Example Query:

`http://192.168.50.92/it350site/customerpups.php?user=employee&secretkey=123654`

### NOIMMUS.PHP Example Query:

`http://192.168.50.92/it350site/noimmus.php?user=employee&secretkey=123654`

### POPULARCUSTOMERS.PHP Example Query:

`http://192.168.50.92/it350site/popularcustomers.php?user=employee&secretkey=123654`

### GIVE_IMMUNIZATION.PHP Example Query:

`http://192.168.50.92/it350site/give_immunization.php?user=employee&secretkey=123654&puppy=Jill&immunization=Rabies&veterinarian=Jane`

### DELETE_IMMUNIZATION.PHP Example Query:

`http://192.168.50.92/it350site/delete_immunizations.php?user=employeeadmin&secretkey=321456&immunization=Rabies`

=========================================

**Extension 2**:

=========================================

### SEARCH.PHP Example Query:

`http://192.168.50.92/it350site/search.php?user=employee&secretkey=123654&query=dog`

### LONGEST_WAITING.PHP Example Query:

`http://192.168.50.92/it350site/longest_waiting.php?user=employee&secretkey=123654`

### LOCATION_EMPLOYEES.PHP Example Query:

`http://192.168.50.92/it350site/location_employees.php?user=employee&secretkey=123654`

### EMPLOYEE_CUSTOMERS.PHP Example Query:

`http://192.168.50.92/it350site/employee_customers.php?user=employee&secretkey=123654`

### NOT_ALL_BREEDS.PHP Example Query:

`http://192.168.50.92/it350site/not_all_breeds.php?user=employee&secretkey=123654`

### WORKS_ALL.PHP Example Query:

`http://192.168.50.92/it350site/works_all.php?user=employee&secretkey=123654&manager=Jane`

## (Intentional) Quirks:

### READ.PHP:

* Even though accepted by MySQL, a `LIMIT` query that contains values with leading 0's is **rejected**.

    (Example in URL: `limit=003,5`)

* Even though accepted by MySQL, a `LIMIT` query that contains values that are out-of-scope from the working table's # of rows is **rejected**.

    (Example in URL: `limit=1,4` when there are only 4 total rows in the table.)

    **NOTE:** If an out-of-scope query occurs, the error message will tell you how many rows are in the table so you can adjust.

### INSERT.PHP:

* Only **7- or 10- digit** `phone` numbers are accepted (i.e. `555-4321`).

* Only **positive integer** `age` values are accepted (i.e. `35`).