1) run "composer install" command
2) create a file named '.env' and copy .env.example file and paste it .env
3) rename "APP_NAME" and run "php artisan key:generate" command
4) make sure to set DB settings in .env file and run "php artisan migrate" to create tables
5) run "php artisan db:seed" to create dummy products and users
6) run "php artisan serve" command to execute the project

-- Authentication --
- There are three endpoints in this section.

APP_URL/api/register (POST)  :  
  
   - There are already 3 users created in the system by using seeder.
   
   - If you want to create a new user, you're able use this endpoint.
   
   - This endpoint accepts 3 parameters: name, email, password
    
    Sample response:
      
        {
            "data": {
                "name": "Dogu",
                "email": "dogu@hotmail.com",
                "updated_at": "2021-11-14T06:44:22.000000Z",
                "created_at": "2021-11-14T06:44:22.000000Z",
                "id": 4
                },
            "access_token": "1|YWm5SRvaoEdsaiI5yAoK43jFrOI2iadGrQ4zjWub",
            "token_type": "Bearer"
        }
    
   - This endpoint does not require any token
    
    
APP_URL/api/login (POST)  :  
    - If you want to log in to system by using 3 users created with seeder, you need to fill password
    field with user's email.
      
        {
            email: "roel47@example.org",
            password: "roel47@example.org"
        }
    
   - This endpoint accepts 2 parameters: email, password
    
    Sample response:
       
       
         {
             "message": "Hi Manuel Schuster, welcome to system",
             "access_token": "2|htfCioTAAjDtySIZMYBUh7N9IVywF4LJaUC14wSq",
             "token_type": "Bearer"
         }
    
    
   - This endpoint does not require any token

APP_URL/api/logout (POST)  : 

   - Bearer token should be set in the Authorization section.
    
    
     Authorization: Bearer 907c762e069589c2cd2a229cdae7b8778caa9f07
     
     Sample response:
     
        {
             "message": "You have successfully logged out and the token was successfully deleted"
        }

-- Product --

   - There are 10 products created in the system by using seeder.
   - There is one endpoint to list products.
   
APP_URL/api/products (GET)  :

   - Bearer token should be set in the Authorization section.
       
    Authorization: Bearer 907c762e069589c2cd2a229cdae7b8778caa9f07
    
    

   - This endpoint returns 5 products by default. It accepts 'page' query parameter.
   (APP_URL/api/products?page=2)
   
        
     Sample response:
     
        [
            {
                "id": 1,
                "name": "Product-Q",
                "price": 384,
                "stock": 50
            }
        ]

-- Order --

   - There are 3 endpoints in this section for create, update and list orders.
   
APP_URL/api/orders:id? (GET)  :
    
   - Bearer token should be set in the Authorization section.
           
         Authorization: Bearer 907c762e069589c2cd2a229cdae7b8778caa9f07


   - This endpoint returns 5 orders belonging to logged user by default. It accepts 'page' query parameter
   (APP_URL/api/orders?page=2)
   
   - 'id' parameter is optional. To get details of a specific order, 'id' parameter should be sent.
    (APP_URL/api/orders/1)

        
        Sample response: 
        
        {
            "data": [
                {
                    "id": 1,
                    "customer_id": 1,
                    "total": 2304,
                    "shipping_date": "2021-11-17",
                    "products": [
                        {
                            "id": 1,
                            "order_id": 1,
                            "product_id": 1,
                            "quantity": 6,
                            "unit_price": 384,
                            "total_price": 2304,
                            "created_at": "2021-11-14T07:16:39.000000Z",
                            "updated_at": "2021-11-14T07:16:39.000000Z"
                        }
                    ],
                    "address": {
                        "id": 1,
                        "order_id": 1,
                        "first_name": "dogu",
                        "last_name": "yildiz",
                        "email": "cansu@hotmail.com",
                        "phone": "11111111",
                        "address": "test",
                        "zipcode": "33133",
                        "city": "izmir",
                        "country": "türkiye",
                        "created_at": "2021-11-14T07:16:39.000000Z",
                        "updated_at": "2021-11-14T07:16:39.000000Z"
                    }
                }
            ]
        }
        
APP_URL/api/order (POST)  :

   - Bearer token should be set in the Authorization section.
           
         Authorization: Bearer 907c762e069589c2cd2a229cdae7b8778caa9f07
         
         
   Sample request: 
   
          "address": {
                         "first_name": "dogu",
                         "last_name": "yildiz",
                         "email": "cansu@hotmail.com",
                         "phone": "11111111",
                         "address": "test",
                         "zipcode": "33133",
                         "city": "izmir",
                         "country": "türkiye",
                      }
          "products": [
                  {
                      "product_id": 1,
                      "quantity": 6,
                  }
              ],
    

- All fields are required.

APP_URL/api/order (PUT)  :

   - Bearer token should be set in the Authorization section.
               
         Authorization: Bearer 907c762e069589c2cd2a229cdae7b8778caa9f07
             
   Sample request: 
       
              "address": {
                             "first_name": "test",
                          }
              "products": [
                      {
                          "product_id": 2,
                          "quantity": 1,
                      }
                  ],
 


- Fields are not required. However if products array is sent, product_id and quantity fields are 
necessary.
