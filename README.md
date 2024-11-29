# Bill Payment API with Wallet System
This is a basic API focusing on bill payment(airtime) and also has a wallet functionality where users can fund their wallets. It uses paystack API to implement the wallet feature.

Features
1) Basic authentication using sanctum which includes registration, login and logout
2) Check wallet balance
3) View transaction history
4) Fund wallet
5) It also includes validation for all the input fields

Steps
1) Clone the repository
2)  Install all dependancies: composer install and npm install
3)  set environment variables .env
4)  run *php artisan migrate* to migrate the database
5) create the variable in your .env file *PAYSTACK_SECRET_TOKEN* which should store your paystack secret key which would be accessed during wallet funding and payment verification
6) run the tests with the command *php artisan test*
7) start your server with *php artisan serve*
