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
  - consider giving option to decide which units are returned (kilograms/grams)
  - how to implement `search()` method collections
  - use latest version of Symfony's to embed your logic 

## Prerequisites
```
php (>=8.2)
composer
Symfony 6.4
MySQL 8.0
Docker (28.0.4) & Docker Compose (v2.34.0) (Optional for Containerized Development)
```

## Containerized Development Environment Configuration (`.env`)
Modify Symfony's `.env` file for MySQL connection
```code
DATABASE_URL="mysql://local:secret@mysql:3306/food"
```

## Steps to Run the Script using Docker Compose
`docker-compose.yml` file is provided, run the following command:
```bash
docker compose up -d
```

## Accessing Symfony Console Commands
After running the containers, use:
```bash
docker exec -it food_api bash
php bin/console doctrine:migrations:migrate
php bin/console debug:router
```
To enter the MySQL container:
```bash
docker exec -it food_mysql mysql -u local -psecret food
```

##  Stopping and Cleaning Up
```bash
docker compose down
```

## Load json file
Json file loaded via Symfony Console Command
```bash
php bin/console app:import-fruit-vegetable path/file.json
```

## API Documentation for `fruits`
```code
Create Resource Endpoint
POST /api/fruits
Request Body
{
  "name": "Apples",
  "quantity": 20,
  "unit": "kg"
}
Response Example
{
    "id": 7,
    "name": "Apples",
    "alias": "apples",
    "gram": 20000,
    "createdAt": "2025-03-22T21:04:47+00:00"
}
```
```code
Get Resource Endpoint
GET /api/fruits
Response Example
{
    "fruits": [
        {
            "id": 6,
            "name": "Apples",
            "alias": "apples",
            "gram": 20000,
            "createdAt": "2024-11-18T22:48:58+00:00"
        },
        {
            "id": 4,
            "name": "Berries",
            "alias": "berries",
            "gram": 10000,
            "createdAt": "2024-11-06T01:24:48+00:00"
        }
    ],
    "pager": {
        "currentPage": 1,
        "previousPage": null,
        "nextPage": 2,
        "totalPages": 3,
        "totalItems": 6,
        "offset": 0,
        "limit": 2
    }
}
```
```code
Get Resource Endpoint
GET /api/fruits?page=2&unit=kilogram
Response Example
{
    "fruits": [
        {
            "id": 6,
            "name": "Apples",
            "alias": "apples",
            "kilogram": 20,
            "createdAt": "2024-11-18T22:48:58+00:00"
        },
        {
          "id": 4,
          "name": "Berries",
          "alias": "berries",
          "kilogram": 10,
          "createdAt": "2024-11-06T01:24:48+00:00"
        }
    ],
    "pager": {
        "currentPage": 1,
        "previousPage": null,
        "nextPage": 2,
        "totalPages": 3,
        "totalItems": 6,
        "offset": 0,
        "limit": 2
    }
}
```
```code
Delete Resource Endpoint
DELETE /api/fruits/{id}
204 No Content
```

## API Documentation for `vegetables`
```code
Create Resource Endpoint
POST /api/vegetables
Request Body
{
    "name": "Tomatoes",
    "quantity": 5,
    "unit": "kg"
}
Response Example
{
    "id": 2,
    "name": "Tomatoes",
    "alias": "tomatoes",
    "gram": 5000,
    "createdAt": "2025-03-22T21:28:01+00:00"
}
```
```code
Get Resource Endpoint
GET /api/vegetables
Response Example
{
    "vegetables": [
        {
            "id": 2,
            "name": "Beans",
            "alias": "beans",
            "gram": 65000,
            "createdAt": "2024-11-06T01:24:48+00:00"
        },
        {
            "id": 3,
            "name": "Beetroot",
            "alias": "beetroot",
            "gram": 950,
            "createdAt": "2024-11-06T01:24:48+00:00"
        }
    ],
    "pager": {
        "currentPage": 1,
        "previousPage": null,
        "nextPage": 2,
        "totalPages": 3,
        "totalItems": 5,
        "offset": 0,
        "limit": 2
    }
}
```
```code
Get Resource Endpoint
GET /api/vegetables?page=2&unit=kilogram
Response Example
{
    "vegetables": [
        {
            "id": 1,
            "name": "Carrot",
            "alias": "carrot",
            "kilogram": 10.922,
            "createdAt": "2024-11-06T01:24:48+00:00"
        },
        {
            "id": 6,
            "name": "Celery",
            "alias": "celery",
            "kilogram": 20,
            "createdAt": "2024-11-06T01:24:48+00:00"
        }
    ],
    "pager": {
        "currentPage": 2,
        "previousPage": 1,
        "nextPage": 3,
        "totalPages": 3,
        "totalItems": 5,
        "offset": 2,
        "limit": 2
    }
}
```
```code
Delete Resource Endpoint
DELETE /api/vegetables/{id}
204 No Content
```

### HTTP Response Codes Used
- `200 OK`: The request was successful.
- `201 Created`: The resource was successfully created.
- `204 No Content`: The request was successfully processed, but there is no content to return.
- `400 Bad Request`: The request was malformed or missing necessary information.
- `404 Not Found`: The resource was not found.
- `422 Unprocessable Entity`: The request is well-formed but contains semantic errors that prevent processing.
- `500 Internal Server Error`: An unexpected error occurred on the server.
