# Ecommerce Backend 🛠️

A robust API backend for an ecommerce platform built with Laravel 12, using PostgreSQL as the database and Laravel Sanctum for token-based authentication. This backend supports user authentication, product management, cart functionality, and transactions.

## 🚀 Features

* User Authentication: Secure login and registration with token-based authentication.
* Product Management: CRUD operations for products with category support.
* Cart & Transactions: Manage user carts and process transactions.
* API-First: RESTful API endpoints for frontend integration.
* Database Migrations: Structured schema with PostgreSQL.

## 🛠️ Tech Stack

* Laravel 12: Backend framework.
* PostgreSQL: Relational database for data storage.
* Laravel Sanctum: For API token authentication.
* PHP 8.4: Language version.
* Composer: Dependency management.

## 📦 Installation
### Prerequisites

* PHP: v8.4 or later
* Composer: v2.x
* PostgreSQL: v16.x
* Node.js & npm (for frontend assets, if needed)

### Steps

1. #### Clone the Repository:
<pre>
git clone https://github.com/your-username/ecommerce-backend.git
cd ecommerce-backend
</pre>


2. #### Install Dependencies:
<pre>
composer install
</pre>


3. #### Configure Environment:

* Copy .env.example to .env:
<pre>
cp .env.example .env
</pre>


* Update .env with your database credentials:
<pre>
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ecommerce_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
</pre>




4. #### Generate Application Key:
<pre>
php artisan key:generate
</pre>


5. #### Run Migrations:
<pre>
php artisan migrate
</pre>


6. #### Seed the Database (Optional):
<pre>
php artisan db:seed
</pre>


7. #### Run the Development Server:
<pre>
php artisan serve
</pre>


* The API will be available at http://localhost:8000.



## 🖥️ API Endpoints



| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/login | Authenticate user |
| POST | /api/register | Register new user |
| GET | /api/products | List all products |
| POST | /api/cart | Add to cart |
| GET | /api/transactions | List transactions | 

### Example Request (Login)
<pre>
curl -X POST http://localhost:8000/api/login \
-H "Content-Type: application/json" \
-d '{"email":"test@example.com","password":"password"}'
</pre>

 ### Example Response
<pre>
{
    "token": "1|random-token-string",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com"
    }
}

## 🧪 Testing
<pre>
Run tests with:
php artisan test
</pre>pre>   

## 🚀 Deployment

1. Set up your production environment (e.g., Nginx, Apache).
2. Update .env with production database credentials.
3. Run migrations:
<pre>
php artisan migrate --force
</pre>
4. Optimize for production:
<pre>
php artisan optimize
</pre>



## 📜 License
This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

1. Fork the repository.
2. Create a new branch (git checkout -b feature/your-feature).
3. Commit your changes (git commit -m 'Add your feature').
4. Push to the branch (git push origin feature/your-feature).
5. Open a Pull Request.

## 📧 Contact
For questions or feedback, reach out at math.knowledge458@gmail.com.

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Built with 💻 by Knowledge Mathebula.
