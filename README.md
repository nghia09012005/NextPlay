# NextPlay API Documentation

## Testing

### Running Tests

To run the test suite, use the following command:

```bash
php tests/run_all_service_tests.php
```

### Test Coverage

The test suite includes the following test cases:

- **GameService Tests**
  - `getAllGames()`: Tests retrieving all games
  - `getGameById()`: Tests retrieving a single game by ID

- **UserService Tests**
  - User registration and authentication tests
  - Password validation tests

- **CategoryService Tests**
  - `getAllCategories()`: Tests retrieving all categories
  - `getCategoryById()`: Tests retrieving a single category by ID
  - `createCategory()`: Tests creating a new category
  - `updateCategory()`: Tests updating an existing category

## Base URL

### WebServer url
```
localhost: http://localhost/Assignment/NextPlay/index.php/...
public url: https://nghiadz.alwaysdata.net/...
```
### Database url
```
mysql://root:zqrZpHByLbLHYozKGRrFCDYhQbINYpTe8@tramway.proxy.rlwy.net:42537/railway
```

## Quick Reference

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST   | /users/signin | User login | No |
| POST   | /users/register | Register new user | No |
| POST   | /users/logout | Logout user | No |
| GET    | /users/profile | Get current user profile with role | Yes |
| PUT    | /users | Update current user | Yes |
| PUT    | /users/password | Update user password | Yes |
| GET    | /admins | Get all admins | No |
| GET    | /admins/{uid} | Get admin by ID | No |
| GET    | /admins/stats | Get dashboard statistics | No |
| GET    | /admins/check/{uid} | Check if user is admin | No |
| GET    | /admins/non-admin-users | Get users who are not admins | Yes |
| POST   | /admins | Create new admin user | Yes |
| POST   | /admins/promote/{uid} | Promote user to admin | Yes |
| POST   | /admins/demote/{uid} | Demote admin to user | Yes |
| DELETE | /admins/{uid} | Delete admin | Yes |
| POST   | /publishers/register | Register new publisher | No |
| GET    | /users | Get all users | Yes |
| GET    | /users/{id} | Get user by ID | Yes |
| GET    | /publishers | Get all publishers | Yes |
| GET    | /publishers/{id} | Get publisher by ID | Yes |
| GET    | /categories | Get all categories | Yes |
| POST   | /categories | Create new category | Admin |
| GET    | /categories/{id} | Get category by ID | Yes |
| PUT    | /categories/{id} | Update category | Admin |
| DELETE | /categories/{id} | Delete category | Admin |
| GET    | /games | Get all games | No |
| GET    | /games/me | Get current publisher's games | Publisher |
| GET    | /games/{id} | Get game by ID | No |
| GET    | /publishers/{id}/games | Get games by publisher | No |
| POST   | /wishlists | Create a new wishlist | Yes |
| GET    | /wishlists/{wishlist_name}/games | Get games in a wishlist | Yes |
| POST   | /wishlists/{wishlist_name}/games | Add game to wishlist | Yes |
| POST   | /payments | Process payment and move games to library | Yes |
| GET    | /library | Get all games in user's library | Yes |
| POST   | /games | Create new game | Publisher |
| PUT    | /games/{id} | Update game | Publisher/Admin |
| DELETE | /games/{id} | Delete game | Publisher/Admin |
| GET    | /news | Get all news articles | No |
| GET    | /news/{id} | Get news article by ID | No |
| POST   | /news | Create new article | Admin/Publisher |
| PUT    | /news/{id} | Update article | Author/Admin |
| DELETE | /news/{id} | Delete article | Author/Admin |
| POST   | /news/comment | Add comment to article | Authenticated User |
| DELETE | /news/comment/{id} | Delete comment | Comment Author/Admin |
| GET    | /reviews/news/{news_id} | Get reviews for a news article | No |
| GET    | /reviews/customer/{customer_id} | Get reviews by customer | No |
| POST   | /reviews | Add/Update a review | Authenticated User |
| DELETE | /review/{customer_id}/{news_id} | Delete a review | Review Author/Admin |
| GET    | /reviews/average/{news_id} | Get average rating for news | No |
| GET    | /reviews/check/{customer_id}/{news_id} | Check if customer reviewed news | No |
| POST   | /feedback | Add a game review | Yes |
| GET    | /feedback/game/{id} | Get reviews for a game | No |
| PUT    | /feedback/game/{id} | Update a game review | Yes |
| DELETE | /feedback/game/{id} | Delete a game review | Yes |
| GET    | /faqs | Get all FAQs | No |
| POST   | /contact | Submit contact message | No |
| GET    | /content | Get page content | No |
| PUT    | /admin/content | Update page content | Admin |

## Admin Endpoints

### Get All Admins
- **URL**: `/admins`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "uid": 1,
        "uname": "admin1",
        "email": "admin1@example.com",
        "avatar": "avatar.jpg"
      }
    ],
    "total": 1
  }
  ```

### Get Admin by ID
- **URL**: `/admins/{uid}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "uid": 1,
      "uname": "admin1",
      "email": "admin1@example.com",
      "avatar": "avatar.jpg"
    }
  }
  ```
- **Error Response (404 Not Found)**:
  ```json
  {
    "status": "error",
    "message": "Admin not found"
  }
  ```

### Check if User is Admin
- **URL**: `/admins/check/{uid}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "isAdmin": true
  }
  ```

### Get Dashboard Statistics
- **URL**: `/admins/stats`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "totalUsers": 150,
      "totalGames": 45,
      "totalOrders": 320,
      "totalRevenue": 15000.00
    }
  }
  ```

### Get Non-Admin Users
- **URL**: `/admins/non-admin-users`
- **Method**: `GET`
- **Authentication**: Required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "uid": 2,
        "uname": "user1",
        "email": "user1@example.com",
        "avatar": "avatar.jpg"
      }
    ],
    "total": 1
  }
  ```

### Create New Admin
- **URL**: `/admins`
- **Method**: `POST`
- **Authentication**: Required (Admin)
- **Request Body**:
  ```json
  {
    "uname": "newadmin",
    "email": "newadmin@example.com",
    "password": "securepassword123"
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Admin created successfully",
    "data": {
      "uid": 3,
      "uname": "newadmin",
      "email": "newadmin@example.com"
    }
  }
  ```
- **Error Response (400 Bad Request)**:
  ```json
  {
    "status": "error",
    "message": "Missing required fields"
  }
  ```

### Promote User to Admin
- **URL**: `/admins/promote/{uid}`
- **Method**: `POST`
- **Authentication**: Required (Admin)
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "User promoted to admin successfully"
  }
  ```
- **Error Response (400 Bad Request)**:
  ```json
  {
    "status": "error",
    "message": "User is already an admin"
  }
  ```

### Demote Admin to User
- **URL**: `/admins/demote/{uid}`
- **Method**: `POST`
- **Authentication**: Required (Admin)
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Admin demoted to user successfully"
  }
  ```
- **Error Response (400 Bad Request)**:
  ```json
  {
    "status": "error",
    "message": "User is not an admin"
  }
  ```

### Delete Admin
- **URL**: `/admins/{uid}`
- **Method**: `DELETE`
- **Authentication**: Required (Admin)
- **Query Parameters**:
  - `deleteUser` (optional): If `true`, also deletes the user account
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Admin deleted successfully"
  }
  ```
- **Error Response (404 Not Found)**:
  ```json
  {
    "status": "error",
    "message": "Admin not found"
  }
  ```

## Review Endpoints (News)

### Get Reviews for a News Article
- **URL**: `/reviews/news/{news_id}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "customerid": 1,
        "news_id": 5,
        "review_time": "2025-12-02 15:30:00",
        "content": "Great article!",
        "rating": 5,
        "uname": "username",
        "avatar": "avatar.jpg"
      }
    ]
  }
  ```

### Get Reviews by Customer
- **URL**: `/reviews/customer/{customer_id}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "customerid": 1,
        "news_id": 5,
        "review_time": "2025-12-02 15:30:00",
        "content": "Great article!",
        "rating": 5,
        "news_title": "Article Title"
      }
    ]
  }
  ```

### Add/Update Review
- **URL**: `/reviews`
- **Method**: `POST`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "customerid": 1,
    "news_id": 5,
    "content": "Great article!",
    "rating": 5
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Review saved successfully",
    "data": {
      "customerid": 1,
      "news_id": 5,
      "content": "Great article!",
      "rating": 5
    }
  }
  ```

### Delete Review
- **URL**: `/review/{customer_id}/{news_id}`
- **Method**: `DELETE`
- **Authentication**: Required (Review Author or Admin)
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Review deleted successfully"
  }
  ```

### Get Average Rating
- **URL**: `/reviews/average/{news_id}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "news_id": 5,
      "average_rating": 4.5
    }
  }
  ```

### Check if Customer Reviewed
- **URL**: `/reviews/check/{customer_id}/{news_id}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "has_reviewed": true
    }
  }
  ```

## Library Endpoints

### Get All Games in User's Library
- **URL**: `/library`
- **Method**: `GET`
- **Authentication**: Required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "Gid": 1,
        "name": "Game 1",
        "version": "1.0",
        "description": "Game description",
        "cost": "29.99",
        "adminid": 1,
        "publisherid": 1
      }
    ],
    "count": 1
  }
  ```
- **Error Responses**:
  - 401 Unauthorized: User not logged in
  - 500 Internal Server Error: Server error

## Payment & Wishlist Endpoints

### Create a New Wishlist
- **URL**: `/wishlists`
- **Method**: `POST`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "wishlist_name": "My Wishlist"
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Wishlist created successfully",
    "data": {
      "wishname": "My Wishlist"
    }
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing wishlist name
  - 401 Unauthorized: User not logged in
  - 409 Conflict: Wishlist with this name already exists
  - 500 Internal Server Error: Server error

### Get Games in Wishlist
- **URL**: `/wishlists/{wishlist_name}/games`
- **Method**: `GET`
- **Authentication**: Required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "wishlist_name": "My Wishlist",
      "games": [
        {
          "Gid": 123,
          "name": "Game Name",
          "version": "1.0",
          "description": "Game description",
          "cost": "29.99"
        }
      ]
    }
  }
  ```
- **Error Responses**:
  - 401 Unauthorized: User not logged in
  - 404 Not Found: Wishlist not found
  - 500 Internal Server Error: Server error

### Add Game to Wishlist
- **URL**: `/wishlists/{wishlist_name}/games`
- **Method**: `POST`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "game_id": 123
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Game added to wishlist successfully"
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing game ID
  - 401 Unauthorized: User not logged in
  - 404 Not Found: Wishlist not found
  - 409 Conflict: Game already in wishlist
  - 500 Internal Server Error: Server error

## Payment Endpoint

### Process Payment and Move Games to Library
- **URL**: `/payments`
- **Method**: `POST`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "wishlist_name": "wishlist1",
    "game_ids": [1, 2, 3]
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "2 games moved to library",
    "data": {
      "moved_to_library": [1, 2],
      "failed_to_remove_from_wishlist": [3],
      "library": "Payed"
    }
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Invalid input data
  - 401 Unauthorized: User not logged in
  - 402 Payment Required: Insufficient balance
  - 404 Not Found: Customer not found
  - 500 Internal Server Error: Server error

  **Insufficient Balance Response (402 Payment Required)**:
  ```json
  {
    "status": "error",
    "message": "Insufficient balance",
    "code": 402,
    "data": {
      "current_balance": 50.00,
      "total_cost": 99.99,
      "needed_amount": "49.99",
      "required_balance": 99.99
    }
  }
  ```

## Default Admin Account
- **Username**: admin
- **Password**: adminpass
- **Email**: admin@example.com
- **UID**: 3

**Note**: This is a default admin account. Please change the password after first login.



## News Endpoints

### Get All News Articles
- **URL**: `/news`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  [
    {
      "id": 1,
      "title": "New Game Release",
      "content": "Content of the news article...",
      "thumbnail": "https://example.com/image.jpg",
      "author_id": 1,
      "author_name": "admin",
      "author_avatar": "path/to/avatar.jpg",
      "created_at": "2025-12-02 14:30:00",
      "views": 42
    }
  ]
  ```

### Get Single News Article
- **URL**: `/news/{id}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "id": 1,
    "title": "New Game Release",
    "content": "Content of the news article...",
    "thumbnail": "https://example.com/image.jpg",
    "author_id": 1,
    "author_name": "admin",
    "author_avatar": "path/to/avatar.jpg",
    "created_at": "2025-12-02 14:30:00",
    "views": 43,
    "comments": [
      {
        "id": 1,
        "news_id": 1,
        "user_id": 2,
        "content": "Great article!",
        "created_at": "2025-12-02 15:30:00",
        "uname": "user123",
        "avatar": "path/to/user/avatar.jpg"
      }
    ]
  }
  ```

### Create News Article
- **URL**: `/news`
- **Method**: `POST`
- **Authentication**: Required (Admin/Publisher)
- **Request Body**:
  ```json
  {
    "title": "New Article",
    "content": "Article content here...",
    "thumbnail": "https://example.com/image.jpg",
    "author_id": 1
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "message": "News created successfully"
  }
  ```

### Update News Article
- **URL**: `/news/{id}`
- **Method**: `PUT`
- **Authentication**: Required (Author/Admin)
- **Request Body**:
  ```json
  {
    "title": "Updated Article Title",
    "content": "Updated content...",
    "thumbnail": "https://example.com/new-image.jpg",
    "author_id": 1
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "message": "News updated successfully"
  }
  ```

### Delete News Article
- **URL**: `/news/{id}`
- **Method**: `DELETE`
- **Authentication**: Required (Author/Admin)
- **Request Body**:
  ```json
  {
    "author_id": 1
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "message": "News deleted successfully"
  }
  ```

### Add Comment to News Article
- **URL**: `/news/comment`
- **Method**: `POST`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "news_id": 1,
    "user_id": 2,
    "content": "This is a great article!"
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "message": "Comment added successfully"
  }
  ```

### Delete Comment
- **URL**: `/news/comment/{id}`
- **Method**: `DELETE`
- **Authentication**: Required (Comment Author/Admin)
- **Request Body**:
  ```json
  {
    "user_id": 2
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "message": "Comment deleted successfully"
  }
  ```

This document provides detailed information about the NextPlay API endpoints, request/response formats, and examples.



## Authentication
Most endpoints require authentication. Include the session token in subsequent requests after login.

## API Endpoints

### Users

#### Update Password
- **URL**: `/users/password`
- **Method**: `PUT`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "currentPassword": "current_password_123",
    "newPassword": "new_secure_password_123"
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Password updated successfully"
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing or invalid input, incorrect current password, or new password too short
  - 401 Unauthorized: User not logged in
  - 500 Internal Server Error: Server error

#### Update Current User
- **URL**: `/users` or `/users/me`
- **Method**: `PUT`
- **Authentication**: Required
- **Request Body** (all fields optional):
  ```json
  {
    "uname": "new_username",
    "email": "new.email@example.com",
    "DOB": "1990-01-01",
    "fname": "New First",
    "lname": "New Last",
    "avatar": "path/to/avatar.jpg"
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "message": "User updated successfully",
    "user": {
      "uid": 1,
      "uname": "new_username",
      "email": "new.email@example.com",
      "DOB": "1990-01-01",
      "fname": "New First",
      "lname": "New Last",
      "avatar": "path/to/avatar.jpg"
    }
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Invalid input data
  - 401 Unauthorized: User not logged in
  - 500 Internal Server Error: Server error

### Authentication

#### Login
- **URL**: `/users/signin`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "uname": "username",
    "password": "password"
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Login successful",
    "user": {
      "uid": 3,
      "uname": "admin",
      "email": "admin@example.com"
    }
  }
  ```

#### Register New User
- **URL**: `/users/register`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "uname": "newuser",
    "email": "newuser@example.com",
    "password": "password123",
    "DOB": "2000-01-01",
    "fname": "First",
    "lname": "Last"
  }
  ```

### Categories

#### Get All Categories
- **URL**: `/categories`
- **Method**: `GET`
- **Authentication**: Required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "catId": 1,
        "name": "Action",
        "description": "Action games"
      }
    ]
  }
  ```

#### Get Category by ID
- **URL**: `/categories/{id}`
- **Method**: `GET`
- **Authentication**: Required
- **URL Parameters**:
  - `id` (required): Category ID

#### Create New Category
- **URL**: `/categories`
- **Method**: `POST`
- **Authentication**: Required (Admin)
- **Request Body**:
  ```json
  {
    "name": "New Category",
    "description": "Category description"
  }
  ```

#### Update Category
- **URL**: `/categories/{id}`
- **Method**: `PUT`
- **Authentication**: Required (Admin)
- **Request Body**:
  ```json
  {
    "name": "Updated Name",
    "description": "Updated description"
  }
  ```

#### Delete Category
- **URL**: `/categories/{id}`
- **Method**: `DELETE`
- **Authentication**: Required (Admin)

## Game Management

#### Get Current Publisher's Games
- **URL**: `/games/me`
- **Method**: `GET`
- **Authentication**: Required (Publisher)
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "Gid": 1,
        "name": "My Game",
        "version": "1.0",
        "description": "Game description",
        "cost": 29.99,
        "publisherid": 1
      }
    ]
  }
  ```
- **Error Responses**:
  - 401 Unauthorized: Not authenticated
  - 403 Forbidden: User is not a publisher
  - 500 Internal Server Error: Server error

#### Get All Games
- **URL**: `/games`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "Gid": 1,
        "name": "Game 1",
        "version": "1.0",
        "description": "Game description",
        "cost": 29.99,
        "publisherid": 1,
        "publisher_name": "Publisher Name"
      }
    ]
  }
  ```

#### Get Game by ID
- **URL**: `/games/{id}`
- **Method**: `GET`
- **Authentication**: Not required
- **URL Parameters**:
  - `id` (required): Game ID
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "Gid": 1,
      "name": "Game 1",
      "version": "1.0",
      "description": "Game description",
      "cost": 29.99,
      "publisherid": 1,
      "publisher_name": "Publisher Name"
    }
  }
  ```
- **Error Responses**:
  - 404 Not Found: Game not found
  - 500 Internal Server Error: Server error

#### Get Games by Publisher
- **URL**: `/publishers/{id}/games`
- **Method**: `GET`
- **Authentication**: Not required
- **URL Parameters**:
  - `id` (required): Publisher ID
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "Gid": 1,
        "name": "Game 1",
        "version": "1.0",
        "description": "Game description",
        "cost": 29.99
      }
    ]
  }
  ```

#### Create New Game
- **URL**: `/games`
- **Method**: `POST`
- **Authentication**: Required (Publisher)
- **Request Body**:
  ```json
  {
    "name": "New Game",
    "version": "1.0",
    "description": "Game description",
    "cost": 29.99
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Game created successfully",
    "gameId": 1
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing or invalid fields
  - 401 Unauthorized: Not authenticated
  - 500 Internal Server Error: Server error

#### Update Game
- **URL**: `/games/{id}`
- **Method**: `PUT`
- **Authentication**: Required (Publisher of the game or Admin)
- **URL Parameters**:
  - `id` (required): Game ID to update
- **Request Body**:
  ```json
  {
    "name": "Updated Game Name",
    "version": "2.0",
    "description": "Updated game description",
    "cost": 39.99
  }
  ```
  Note: All fields are optional - only include fields that need to be updated.
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Game updated successfully",
    "data": {
      "Gid": 1,
      "name": "Updated Game Name",
      "version": "2.0",
      "description": "Updated game description",
      "cost": 39.99,
      "publisherid": 2
    }
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Invalid input data
  - 401 Unauthorized: Not authenticated
  - 403 Forbidden: Not authorized to update this game
  - 404 Not Found: Game not found
  - 500 Internal Server Error: Server error

## API Endpoints (Legacy)


## Game Feedback Endpoints

### Get Reviews for a Game
- **URL**: `/feedback/game/{id}`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "customerid": 1,
        "Gid": 12,
        "feedback_time": "2025-12-05",
        "publisherid": 2,
        "content": "Great game!",
        "rating": 5,
        "uname": "player1",
        "avatar": "avatar.jpg"
      }
    ]
  }
  ```

### Add Game Review
- **URL**: `/feedback`
- **Method**: `POST`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "Gid": 12,
    "content": "Awesome gameplay!",
    "rating": 5
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Review added successfully"
  }
  ```

### Update Game Review
- **URL**: `/feedback/game/{id}`
- **Method**: `PUT`
- **Authentication**: Required
- **Request Body**:
  ```json
  {
    "content": "Updated review content",
    "rating": 4
  }
  ```

### Delete Game Review
- **URL**: `/feedback/game/{id}`
- **Method**: `DELETE`
- **Authentication**: Required

## FAQ Endpoints

### Get All FAQs
- **URL**: `/faqs`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": "general",
        "name": "Chung",
        "icon": "bi-info-circle",
        "questions": [
           { "id": 1, "title": "Question?", "answer": "Answer..." }
        ]
      }
    ]
  }
  ```

## Contact Endpoints

### Submit Contact Message
- **URL**: `/contact`
- **Method**: `POST`
- **Authentication**: Not required
- **Request Body**:
  ```json
  {
    "name": "User Name",
    "email": "user@example.com",
    "subject": "Inquiry",
    "message": "Message content..."
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Message sent successfully"
  }
  ```

## Content Management Endpoints

### Get Page Content
- **URL**: `/content`
- **Method**: `GET`
- **Authentication**: Not required
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "home_hero_title": "Welcome",
      "contact_address": "123 Street"
    }
  }
  ```

### Update Page Content (Admin)
- **URL**: `/admin/content`
- **Method**: `PUT`
- **Authentication**: Required (Admin)
- **Request Body**:
  ```json
  {
    "section": "contact",
    "key": "address",
    "value": "New Address"
  }
  ```



### User Management

#### Register a New User
- **URL**: `/users/register`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "uname": "username",
    "email": "user@example.com",
    "password": "securepassword123",
    "DOB": "1990-01-01",
    "lname": "Last",
    "fname": "First"
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "User registered successfully",
    "uid": 1
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing or invalid fields
  - 409 Conflict: Username or email already exists
  - 500 Internal Server Error: Server error

#### User Login
- **URL**: `/users/signin`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "uname": "username",
    "password": "securepassword123"
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Login successful",
    "user": {
      "uid": 1,
      "uname": "username",
      "email": "user@example.com",
      "DOB": "1990-01-01",
      "lname": "Last",
      "fname": "First"
    }
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing username or password
  - 401 Unauthorized: Invalid credentials
  - 500 Internal Server Error: Server error

#### Get All Users
- **URL**: `/users`
- **Method**: `GET`
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "uid": 1,
        "uname": "user1",
        "email": "user1@example.com",
        "DOB": "1990-01-01",
        "lname": "Doe",
        "fname": "John"
      },
      ...
    ]
  }
  ```

#### Get User by ID
- **URL**: `/users/{uid}`
- **Method**: `GET`
- **URL Parameters**:
  - `uid` (required): User ID
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "uid": 1,
      "uname": "username",
      "email": "user@example.com",
      "DOB": "1990-01-01",
      "lname": "Last",
      "fname": "First"
    }
  }
  ```
- **Error Responses**:
  - 404 Not Found: User not found
  - 500 Internal Server Error: Server error

### Publisher Management

#### Register a New Publisher
- **URL**: `/publishers/register`
- **Method**: `POST`
- **Request Body**:
  ```json
  {
    "uname": "publisher1",
    "email": "publisher@example.com",
    "password": "securepassword123",
    "DOB": "1990-01-01",
    "lname": "Publisher",
    "fname": "Game",
    "description": "Leading game publisher",
    "taxcode": "TAX123456",
    "location": "New York, USA"
  }
  ```
- **Success Response (201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Publisher registered successfully",
    "uid": 2
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing or invalid fields
  - 409 Conflict: Username or email already exists
  - 500 Internal Server Error: Server error

#### Get All Publishers
- **URL**: `/publishers`
- **Method**: `GET`
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "uid": 2,
        "uname": "publisher1",
        "email": "publisher@example.com",
        "description": "Leading game publisher",
        "taxcode": "TAX123456",
        "location": "New York, USA"
      },
      ...
    ]
  }
  ```

#### Get Publisher by ID
- **URL**: `/publishers/{uid}`
- **Method**: `GET`
- **URL Parameters**:
  - `uid` (required): Publisher ID
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "uid": 2,
      "uname": "publisher1",
      "email": "publisher@example.com",
      "description": "Leading game publisher",
      "taxcode": "TAX123456",
      "location": "New York, USA"
    }
  }
  ```
- **Error Responses**:
  - 404 Not Found: Publisher not found
  - 500 Internal Server Error: Server error

## Error Handling
All error responses follow this format:
```json
{
  "status": "error",
  "message": "Error description"
}
```

## Status Codes
- 200 OK: Request successful
- 201 Created: Resource created successfully
- 400 Bad Request: Invalid request data
- 401 Unauthorized: Authentication required
- 403 Forbidden: Insufficient permissions
- 404 Not Found: Resource not found
- 405 Method Not Allowed: HTTP method not supported
- 409 Conflict: Resource already exists
- 500 Internal Server Error: Server error
