## Exchange Test Code

Trading sandbox built with Laravel 12 and Vue 3. Supports USD balance management, BTC/ETH asset balances, limit buy/sell orders, immediate matching, and real-time `OrderMatched` broadcasts to keep the UI in sync.

### Requirements
- PHP 8.2+
- Composer
- Node.js 20+ and npm
- Mysql

### Quick Setup
1) Install dependencies
```
composer install
npm install
```
2) Copy env and generate key
```
cp .env.example .env
php artisan key:generate
```
3) Database setup
- After setting up database credentials in the .env file run:
```
php artisan migrate --seed
```
5) Pusher
- make sure to setup the Pusher credentials in the .env file 
```
PUSHER_APP_ID
PUSHER_APP_KEY
PUSHER_APP_SECRET
PUSHER_APP_CLUSTER
```

5) Run the app
```
npm run dev
php artisan serve
```
