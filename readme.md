

## Koombea Assesment
## _Import CSV in laravel_

Project showing how to import data from __CSV file__, also matching CSV columns with database columns, validate fields and set status to contacts and __CSV files__ uploaded.

- You must to __register__, once did, you go to __login__, put your credentials and you will see the interface.

- To upload a __CSV File__ you must to go to __Import Contacts__ menu link, then select a __CSV file__ and click on __Parse CSV__ button.

- The partieal result of the upload is shown, you must to match the correct name of column with the select at bottom of each one.

- Once you matched the columns click on __Import Data__ button.

- The result of the import is shown, you can consult the contacts at __"View Contacts"__ link above at menu and consult the files uploades at __"View Files"__, where you can filter by status in each one.
- ✨Enjoy ✨

---

### How to Install

- Clone the repository with __git clone__
- Edit database credentials in __.env__ file
- Run
    ```sh
    composer install
    php artisan key:generate
    php artisan migrate
    ```
- That's it - load the homepage
---
### Demo

You can test the demo here [CSV File in Heroku](http://nameless-scrubland-32903.herokuapp.com)
- __User__: _test_
- __Password__: _test&csv%demo1_

Generated CSV files

- [1000 records](https://api.mockaroo.com/api/24afcc20?count=1000&key=24826610) - headers match
- [500 records](https://api.mockaroo.com/api/d1c8b5c0?count=500&key=24826610) - headers doesn't match
- [300 records](https://api.mockaroo.com/api/24afcc20?count=300&key=24826610) - headers match

Generate CSV files with this format and random info [here](https://www.mockaroo.com/24afcc20)

---
### License

Please use and re-use however you want.
