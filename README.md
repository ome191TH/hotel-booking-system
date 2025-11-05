# Hotel Booking System

## Overview
The Hotel Booking System is a web application that allows users to browse available hotel rooms, make reservations, and manage bookings. The system is built using PHP, MySQL, HTML, CSS, and Bootstrap, ensuring a responsive and user-friendly interface.

## Features
1. Display a list of all available rooms from the database.
2. Show detailed information for each room.
3. Provide a booking form for users to select check-in and check-out dates.
4. Check room availability before confirming a booking.
5. Save booking information to the database.
6. Admin panel to view and manage all bookings.

## Project Structure
```
hotel-booking-system
├── assets
│   ├── css
│   │   └── style.css
│   ├── js
│   │   └── script.js
│   └── images
│       └── .gitkeep
├── config
│   └── db.php
├── public
│   ├── index.php
│   ├── room_detail.php
│   ├── booking_form.php
│   ├── booking_save.php
│   └── admin.php
├── sql
│   └── hotel_db.sql
└── README.md
```

## Setup Instructions
1. Clone the repository to your local machine.
2. Ensure you have XAMPP installed and running.
3. Import the `hotel_db.sql` file into your MySQL database to create the necessary tables.
4. Update the `config/db.php` file with your database credentials.
5. Access the application via your web browser at `http://localhost/hotel-booking-system/public/index.php`.

## Usage
- Navigate to the homepage to view available rooms.
- Click on a room to see its details.
- Fill out the booking form to reserve a room.
- Admins can access the admin panel to manage bookings.

## Technologies Used
- PHP
- MySQL
- HTML
- CSS
- Bootstrap

## License
This project is open-source and available for modification and distribution under the MIT License.