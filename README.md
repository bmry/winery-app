**NOTE: 

-   **In Production I will decompose the application into two services and deploy each service per container so that they can scale independently when requests increases**
-   **I planned to start the application with two instance of waiter service and one instance of sommelier in production**
-   **Due to limited time I was not able to add enough functional and unit Test. But there are few test cases.**
-   **Please know that I would not deploy this application into production without fully functional and unit tests in place.**

**WINERY APP**

This is an application is developed to model the communication between Customers, Waiter and Sommelier.

**TECHNOLOGY STACK**

>1.  Symfony 4 Framework <br/>
>2.  SQL-LITE Database` <br/>
>3. RABBITMQ`<br/>
>4. PHP 7
>5. DOCKER
>6. DOCTRINE


**HOW TO INSTALL** <br/>
1. Run composer install

2. START RABBITMQ

    **WINDOW USER**
            
        start-rabbit.bat
        
    **LINUX USER**
        
        ./start-rabbit.sh 
           
>ENSURE RABBITMQ IS RUNNING ON Port **5672** <br/>

3.  **START APPLICATION**

    i.  Change To App Root Directory.
    
    ii. **WINDOW USER**
    
    -   Run ``start-app.bat``
    -   This will open the application on your browser automatically.
    
    **LINUX USER**
         
        ./start-app.sh

**Running the above command will download the following:<br/>**
>- Install application dependencies<br/>
>-   Create Queue and Exchange<br/>
>-   Start consumers.
>-  Get Wine From RSS Feed and Insert into DB


**\*NOTE\* YOU CAN MANUALLY GET WINE INVENTORY BY RUNNING**

Run the command in the project folder root directory
> php bin/console app:load_rss_feed


HOW TO TEST IF  APP IS WORKING
-   Request for a wine or multiple wine on **http://127.0.0.1:8000**
-   Then make go to **http://l27.0.0.1:8080/waiter.
-   If order has been processed the order status will be **PROCESSED**
-   Click on View Order Item button to see if requested wine is available or not.
-   You can make wine available for the day by editing wine publish date on **http://127.0.0.1:8000/sommelier**



**Application URL**

**Book Wine**
-       http://127.0.0.1:8000

**Waiter Admin Dashboard:**
-       http://127.0.0.1/waiter
 
**Sommelier Admin Dashboard:**
-       http://127.0.0.1/sommelier
    
**FEATURE**

- **Request Form** - This is where customer pick their wine of choice
    Customer can request for multiple wines
    ![Wine Request Fome](https://docs.google.com/uc?id=1fV-WsBSjfM-n2HdbPTKhud0hhT-y-nOn)
    ![Wine Request Fome](https://docs.google.com/uc?id=1I-DJBAzUJ6I2oDXvnS2NmfhYsnW-MUUr)
    
- **Waiter Dashboard** - On the  waiter dashboard the waiter can see orders.
    - The waiter can do the following on their dashboard:
        -   List Orders
        -   View Order Logs
        -   View Order Items/ Status
       
     ![Wine Request Fome](https://docs.google.com/uc?id=184Ut-NOVCgVAYqc6LgkhlB-rvjX34udG) 
     ![Wine Request Fome](https://docs.google.com/uc?id=1Y1ybnWPVCNVvi0rfAMm6_1d_JjFG39YQ)   
       
  - View Order Item 
- **Sommelier Dashboard**
  - The sommelier can do the following:
    -   List Wine
    -   Create New Wine Record
    -   Update Wine
    -   Easy Make Wine Available 
  ![Wine Request Fome](https://docs.google.com/uc?id=1iA64WBH1qVpgSLVcIf5ZALkHrbBpEaJM)   
  ![Wine Request Fome](https://docs.google.com/uc?id=1sHU2zkxmvOsGt2QTMQ3hzpjjyitWk4iF)   
  ![Wine Request Fome](https://docs.google.com/uc?id=1hT_hUAp9nLSnGLH_dOZwiYDoNtRDyt8A)   
  ![Wine Request Fome](https://docs.google.com/uc?id=1zrLZnbwWr9P0hMZPOTd2osiEQIp8Krau)   
  ![Wine Request Fome](https://docs.google.com/uc?id=19RCKKvt9sttSs2KCuHwXE_9eddc6a8-i)   

**ADDITIONAL FEATURE:**
-   If a customer request for a wine and the wine is unavailable on that day, next time it is available the customer get notified of the wine availability.
 This happens whenever the sommelier update the wine's available date.
            
       
      
**HOW THE APPLICATION WORKS**

-  The communication process start when a customer request for a wine with waiters.<br/>
-  The waiter transfer the wine to a queue where from where the sommellier can access it.
-  The sommellier pick the wine from the queue and check if the wine is available for the day or not.
-  The somellier service send drop the response for the waiter and the waiter pick from there 
  and send a response back to the user
  
**APPLICATION INTERNAL COMMUNICATION**

- When the user submit a request form, an *OrderCreateEvent* is fired. The OrderCreateEventListener respond to the event by calling the *WaiterService CustomerRequestHandler* 
which publish the request to the *Order Request Queue*.

- The Sommelier Service WaiterRequestHandler Picks the message from the queue and process it by checking wine availability for the day and drop the response in a Response Queue.

 - The SommerlierService ResponseHandler then pick the response and send it to back to the WaiterService's ResponseSender which notifies of the user of wine availability.


>NOTE: In case you expected an RPC communication between the services instead of using a request and a response queue.

>This is what I think that would mean: It means the waiter will wait for a response from the Sommelier thereby making it unavailable to receieve other customer request.


