# Vibe-Verse API Documentation

## Background

This document provides the API endpoint specifications for `Vibe-Verse`. The application includes three main user roles: Admin, Organizer, and User, each with designated API endpoints for various tasks such as event management, user administration, and payment processing.

## Requirements

The API documentation outlines:
- The available endpoints for Admin, Organizer, and User roles.
- The HTTP methods used (GET, POST, DELETE).
- Expected input parameters and JSON response structures for each endpoint.

## API Endpoints

### Common Files
- `connect.php`: Database connection settings.
- `header.php`: Common header settings for API requests.
- `session.php`: Session management.

## Admin Endpoints

### Admin Endpoints Table

| Endpoint                       | HTTP Method | Description                              | Parameters                        | Response Example                                               |
|--------------------------------|-------------|------------------------------------------|-----------------------------------|--------------------------------------------------------------|
| `/Apis/Admin/acceptOrder.php` | `POST`      | Accepts an order.                        | `order_id`: ID of the order      | `{ "status": "success", "message": "Order accepted successfully" }` |
| `/Apis/Admin/activate.php`    | `POST`      | Activates a user account.               | `user_id`: ID of the user        | `{ "status": "success", "message": "User activated" }`      |
| `/Apis/Admin/allOrganizer.php` | `GET`       | Retrieves all organizers.                | -                                 | `[ { "organizer_id": 1, "name": "Organizer 1" }, ... ]`    |
| `/Apis/Admin/allRejected.php`  | `GET`       | Fetches all rejected entries.           | -                                 | `[ { "entry_id": 1, "reason": "Rejected due to..." }, ... ]` |
| `/Apis/Admin/allRequest.php`   | `GET`       | Lists all requests from users.          | -                                 | `[ { "request_id": 1, "description": "Request details" }, ... ]` |
| `/Apis/Admin/allTransaction.php`| `GET`       | Retrieves all transactions.              | -                                 | `[ { "transaction_id": 1, "amount": 100, "status": "completed" }, ... ]` |
| `/Apis/Admin/delete.php`       | `DELETE`    | Deletes a specified entity.             | `entity_id`: ID of the entity    | `{ "status": "success", "message": "Entity deleted" }`      |
| `/Apis/Admin/order.php`        | `POST`      | Manages orders (e.g., updating order status). | `order_id`, `status`: New status for the order | `{ "status": "success", "message": "Order updated" }`        |
| `/Apis/Admin/organizer.php`    | `GET`       | Fetches organizer details.               | `organizer_id`: ID of the organizer | `{ "organizer_id": 1, "name": "Organizer Name", ... }`      |
| `/Apis/Admin/rejectOrder.php`  | `POST`      | Rejects an order.                       | `order_id`, `reason`: Reason for rejection | `{ "status": "success", "message": "Order rejected" }`       |
| `/Apis/Admin/suspend.php`      | `POST`      | Suspends a user or organizer.           | `user_id` or `organizer_id`     | `{ "status": "success", "message": "User suspended" }`      |

## Organizer Endpoints

### Organizer Endpoints Table

| Endpoint                                 | HTTP Method | Description                                      | Parameters                        | Response Example                                               |
|------------------------------------------|-------------|--------------------------------------------------|-----------------------------------|--------------------------------------------------------------|
| `/Apis/Organizer/createPin.php`         | `POST`      | Creates a secure PIN for the organizer.         | `organizer_id`, `pin`            | `{ "status": "success", "message": "PIN created" }`        |
| `/Apis/Organizer/editProfile.php`       | `POST`      | Edits organizer profile details.                 | `organizer_id`, `profile_data`   | `{ "status": "success", "message": "Profile updated" }`    |
| `/Apis/Organizer/forget.php`            | `POST`      | Handles forgotten credentials.                   | `email`: Organizer's email       | `{ "status": "success", "message": "Recovery email sent" }`|
| `/Apis/Organizer/logout.php`            | `POST`      | Logs the organizer out of the system.           | `organizer_id`                   | `{ "status": "success", "message": "Logged out" }`         |
| `/Apis/Organizer/register.php`          | `POST`      | Registers a new organizer.                       | `organizer_data`                 | `{ "status": "success", "message": "Organizer registered" }`|
| `/Apis/Organizer/event/allEvents.php`   | `GET`       | Lists all events.                               | -                                 | `[ { "event_id": 1, "name": "Event 1", ... }, ... ]`      |
| `/Apis/Organizer/event/create.php`      | `POST`      | Creates a new event.                             | `event_data`                     | `{ "status": "success", "message": "Event created" }`      |
| `/Apis/Organizer/event/event.php`       | `GET`       | Retrieves details of a specific event.         | `event_id`                       | `{ "event_id": 1, "name": "Event Name", ... }`             |
| `/Apis/Organizer/event/registerScan.php`| `POST`      | Registers a scan for event attendance.           | `event_id`, `scan_data`         | `{ "status": "success", "message": "Scan registered" }`    |
| `/Apis/Organizer/event/scan.php`        | `POST`      | Processes a scan for event entry.               | `event_id`, `user_id`           | `{ "status": "success", "message": "Scan successful" }`    |
| `/Apis/Organizer/event/scanLogin.php`   | `POST`      | Handles login using scan.                        | `scan_data`                     | `{ "status": "success", "message": "Login via scan successful" }` |
| `/Apis/Organizer/payments/allRequest.php`| `GET`       | Retrieves all payment requests.                  | -                                 | `[ { "request_id": 1, "amount": 100, ... }, ... ]`        |
| `/Apis/Organizer/payments/payMethod.php`| `POST`      | Sets up a payment method.                        | `organizer_id`, `payment_data`  | `{ "status": "success", "message": "Payment method set" }` |
| `/Apis/Organizer/payments/requestPay.php`| `POST`     | Requests a payment.                              | `organizer_id`, `amount`        | `{ "status": "success", "message": "Payment requested" }`  |

## User Endpoints

### User Endpoints Table

| Endpoint                      | HTTP Method | Description                                | Parameters            | Response Example                                                 |
|-------------------------------|-------------|--------------------------------------------|--------------------------------------------------------------------------------------|
| `/Apis/User/allEvents.php`    | `GET`       | Retrieves all available events.            | -                     | `[ { "event_id": 1, "name": "Event 1", ... }, ... ]`         |
| `/Apis/User/event.php`        | `GET`       | Retrieves details of a specific event for the user. | `event_id`           | `{ "event_id": 1, "name": "Event Name", "details": "..." }`  |
| `/Apis/User/register.php`     | `POST`      | Registers a new user.                      | `user_data`          | `{ "status": "success", "message": "User registered" }`      |

## Implementation

The following steps are recommended to implement each endpoint:
1. **Database Connection**: Use `connect.php` to manage connections.
2. **Session Management**: Use `session.php` to manage user sessions across requests.
3. **Authentication and Authorization**: Implement checks to ensure appropriate access levels for Admin, Organizer, and User.

## Milestones

1. **Milestone 1**: Implement all Admin endpoints.
2. **Milestone 2**: Implement Organizer endpoints.
3. **Milestone 3**: Implement User endpoints.
4. **Milestone 4**: Complete session management and security checks.
5. **Milestone 5**: Conduct API testing for validation and performance.

## Gathering Results

- Perform testing to verify that each endpoint meets functional requirements.
- Evaluate system performance under expected load conditions.
- Assess endpoint security and data integrity through testing.
