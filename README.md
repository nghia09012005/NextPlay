# NextPlay API Documentation

## Quick Reference

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST   | /users/signin | User login | No |
| POST   | /users/register | Register new user | No |
| PUT    | /users | Update current user | Yes |
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
| POST   | /games | Create new game | Publisher |
| PUT    | /games/{id} | Update game | Publisher/Admin |
| DELETE | /games/{id} | Delete game | Publisher/Admin |

## Wishlist Endpoints

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
    "wishlist_name": "My Wishlist",
    "game_ids": [123, 456, 789]
  }
  ```
- **Success Response (200 OK)**:
  ```json
  {
    "status": "success",
    "message": "2 games moved to library",
    "data": {
      "moved_to_library": [123, 789],
      "failed_to_remove_from_wishlist": [456],
      "library": "Payed"
    },
    "warnings": [
      "Failed to remove game 456 from wishlist"
    ]
  }
  ```
- **Error Responses**:
  - 400 Bad Request: Missing required fields or invalid input
  - 401 Unauthorized: User not logged in
  - 500 Internal Server Error: Server error during payment processing

## Default Admin Account
- **Username**: admin
- **Password**: adminpass
- **Email**: admin@example.com
- **UID**: 3

**Note**: This is a default admin account. Please change the password after first login.



This document provides detailed information about the NextPlay API endpoints, request/response formats, and examples.

## Base URL
```
http://localhost/Assignment/NextPlay/index.php/...
```

## Authentication
Most endpoints require authentication. Include the session token in subsequent requests after login.

## API Endpoints

### Users

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
