# Lab 1

IT350 Lab 1 by Wade Murdock.

## Functionality:

## (Intentional) Quirks:

### READ.PHP:

* Even though accepted by MySQL, a `LIMIT` query that contains values with leading 0's is **rejected**.

    (Example in URL: `limit=003,5`)

* Even though accepted by MySQL, a `LIMIT` query that contains values that are out-of-scope from the working table's # of rows is **rejected**.

    (Example in URL: `limit=1,4` when there are only 4 total rows in the table.)

**NOTE:** If this occurs, the error message will tell you how many rows are in the table so you can adjust.

### INSERT.PHP:

* `puppy_location` is the only required field.
