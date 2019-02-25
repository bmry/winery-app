**WINERY APP**

This is a application developed to model the communication between Customers, Waiter and Sommelier

**TECHNOLOGY STACK**

>1.  Symfony 4 Framework <br/>
>2.  SQL-LITE Database` <br/>
>3. RABBITMQ`<br/>



**HOW TO INSTALL** <br/>
**NOTE**: Please download RabbitMQ from [Rabbit MQ ](https://www.rabbitmq.com/download.html) and install.

>ENSURE RABBITMQ IS RUNNING ON Port **5672** <br/>

``1.    Run composer install``  <br/>
**Running the above command will download the following:<br/>**
>- Install application dependencies<br/>
>-   Create Queue and Exchange<br/>
>-   Start consumers.


**Application URL**

**Book Wine**
-   **Run on your browser** `http://127.0.0.1:8000`

**Waiter Admin Dashboard:**
-   `http://127.0.0.1/waiter`
 
**Sommelier Admin Dashboard:**
-   `http://127.0.0.1/sommelier`

    
**FEATURE**

- **Request Form** - This is where customer pick their wine of choice
    Customer can request for multiple wines
    
    ![Wine Request Fome](https://doc-14-b4-docs.googleusercontent.com/docs/securesc/2vcipqvrreusg54jhe6jlm78akbvpm4k/59luqaecs2ji8nt8q504eq3qaqev9jsa/1551124800000/15860053536152095633/15860053536152095633/1JaYXxEMk_hJD44-wHAQqEvPPwjvyZtd5)
    
- **Waiter Dashboard** - On the  waiter dashboard the waiter can see orders.
    - The waiter can do the following on their dashboard:
        -   List Orders
        -   View Order Logs
        -   View Order Items/ Status
  - View Order Item 
- **Sommelier Dashboard**
  - The sommelier can do the following:
    -   List Wine
    -   Create New Wine Record
    -   Update Wine Publish Date

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


