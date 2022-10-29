# Currency rate getter

How to set up the project:
1. In the root folder run: 

    a. docker-compose up
    
    b. composer install

 

2. Add this line to the hosts file:

   127.0.0.1 fftest.loc

## How to get currency rates:
GET request example:

http://fftest.loc/currency-rate?date=2022-10-29&curr_in=RUR&curr_out=USD

Params:

      date - format yyyy-mm-dd

      curr_in - from what currency to convert (optional, default RUR)

      curr_out - to what currency to convert

Cache:

      The script automatically calculates currency rate for 1 day before the chosen date and stores data in Redis for 30 days

Available currencies:

      RUR
      USD
      EUR
      TRY

If more currencies are needed - change currency code mapping at App\Models\CurrencyInfoSrc\CBR:78

CBR currency code list: http://www.cbr.ru/scripts/XML_val.asp?d=0
