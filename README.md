# 🍎 Fruits and 🥕 Vegetables REST API using Symfony

## 🎯 Goal
We want to build a service which will take a `request.json` sample file location: `data/request.json` and:
- Process the file and create two separate collections for `Fruits` and `Vegetables`
- Each collection has methods like `add()`, `remove()`, `list()`;
- Units have to be stored as grams;
- Store the collections in a storage engine of your choice. (e.g. Database)
- Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
- Provide another API endpoint to add new items to the collections (i.e., your storage engine).
- As a bonus you might:
  - consider giving option to decide which units are returned (kilograms/grams);
  - how to implement `search()` method collections;
  - use latest version of Symfony's to embed your logic 

## Load json file
Json file loaded via Symfony Console Command
```bash
bin/console app:import-fruit-vegetable path/file.json
```

## Running the tests
```shell
$ cd /path/to/base/directory
$ bin/phpunit tests
```
