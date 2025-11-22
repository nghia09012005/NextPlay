# NextPlay API Documentation

This document provides detailed information about the NextPlay API endpoints, request/response formats, and examples.

## Base URL
```
http://localhost/Assignment/NextPlay/index.php/...
```

## Authentication
Most endpoints require authentication. Include the session token in subsequent requests after login.

## API Endpoints

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
