## Koombea Assesment, Import CSV in laravel

Project showing how to import data from __CSV file__, also matching CSV columns with database columns, validate fields and set status to contacts and __CSV files__ uploaded.

You must to __register__, once did, you go to __login__, put your credentials and you will see the interface.

To upload a __CSV File__ you must to go to __Import Contacts__ menu link, then select a __CSV file__ and click on __Parse CSV__ button.

The partieal result of the upload is shown, you must to match the correct name of column with the select at bottom of each one.

Once you matched the columns click on __Import Data__ button.

The result of the import is shown, you can consult the contacts at __"View Contacts"__ link above at menu and consult the files uploades at __"View Files"__, where you can filter by status in each one.

---

### How to Install

- Clone the repository with __git clone__
- Copy __.env.example__ file to __.env__ and edit database credentials there
- Run __composer install__
- Run __php artisan key:generate__
- Run __php artisan migrate__
- That's it - load the homepage

---
### License

Please use and re-use however you want.
