# Atarim - Short URL creator

## Installation

### Run composer install
`CD` into project and install dependencies:
```
composer install
```

Copy contents of `.env.example` and create an `.env` file and paste contents

Generate Laravel app key:
```
php artisan key:generate
```

### Run Sail docker command and start
```
./vendor/bin/sail up -d
```

## POST request to endpoints
App url is `http://localhost` and endpoints are as follows:

```
POST request for encode: http://localhost/api/v1/encode
POST request for decode: http://localhost/api/v1/decode
```

### Encode
Make `POST` request to encode endpoint with following using `Postman` as an example:

```
{
    "url" : "https://www.linkedin.com/in/ufb007"
}
```

Should receieve a `201` status with short url response data:
```
{
    "short_url": "http://localhost/pZK8pG"
}
```

Copy link `http://localhost/pZK8pG` and check on browser. Should redirect to original URL (https://www.linkedin.com/in/ufb007)

### Decode
Make `POST` request to decode `http://localhost/api/v1/decode` with `Postman` as example:

```
{
    "code": "pZK8pG"
}
```

Should receieve `200` status with original URL initially added:
```
{
    "url": "https://www.linkedin.com/in/ufb007"
}
```

Testing `not found` error should result in response status `404` when making `POST` request:

```
{
    "error": "code not found"
}
```

## Run Unit & Integration tests
Unit tests:
 - encode url returns shortened url
 - decode short url returns original url

Integration tests:
 - encode create short url
 - decode get original url
 - decode get original url not found
 - url redirect to original url
```
php artisan test
```