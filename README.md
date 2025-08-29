# Clinic Management System

A comprehensive clinic management system built with Laravel 12, designed to handle all aspects of medical clinic operations.

## Features

### Team Member Modules

#### Wesley: System Administration & Staff Management
- **Staff Management**: Add/Edit/Delete staff profiles with photos and ID
- **Role-Based Access Control (RBAC)**: Define user roles and permissions
- **Announcement Management**: Create, edit, delete announcements with internal/public visibility

#### Chris: Appointment & Patient Management
- **Appointment Scheduling**: Real-time calendar with time-slot locking
- **Booking, Rescheduling, Canceling**: Manage appointments with conflict handling
- **Patient Registration**: Add/Edit/Delete patient profiles with webcam photos
- **Medical History Management**: View patient visit history and records
- **Document Upload**: Upload and manage patient documents

#### Yong: Consultation & Medical Records
- **Medical Consultation Records**: Add consultation notes with timestamps
- **E-Prescriptions and Reports**: Upload and manage digital prescriptions
- **Patient History**: Quick access to patient visit history during consultation
- **Doctor Dashboard**: Today's appointments and patient quick search

#### Calvin: Billing & Reporting
- **Billing and Invoicing**: Automatic cost calculation and PDF invoice generation
- **Payment Tracking**: Track payment statuses (Paid, Partial, Unpaid)
- **Reports and Analytics**: Income summary and patient flow reports
- **Export and Email**: Export reports in CSV format

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd clinic-management-system
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Configure your database in `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinic_management_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Seed the database:
```bash
php artisan db:seed
```

8. Install and build frontend assets:
```bash
npm install
npm run build
```

9. Create storage link:
```bash
php artisan storage:link
```

10. Start the development server:
```bash
php artisan serve
```

## Default Login Credentials

The system comes with pre-seeded users:

- **Admin**: admin@clinic.com / password
- **Doctor**: doctor@clinic.com / password
- **Receptionist**: receptionist@clinic.com / password

## User Roles

- **Admin**: Full system access, staff management, reports
- **Doctor**: Patient consultations, medical records, appointment management
- **Nurse**: Patient care, medical records access
- **Receptionist**: Patient registration, appointment scheduling, billing
- **Pharmacist**: Prescription management, medication dispensing

## Database Structure

The system includes the following main tables:
- `users` - User accounts with role-based access
- `staff` - Staff member profiles
- `doctors` - Doctor-specific information
- `patients` - Patient profiles and medical history
- `appointments` - Appointment scheduling and management
- `consultations` - Medical consultation records
- `bills` - Billing and invoicing
- `services` - Available medical services
- `announcements` - System announcements
- `medical_records` - Patient medical records
- `patient_documents` - Uploaded patient documents

## Technologies Used

- **Backend**: Laravel 12 (PHP)
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **File Storage**: Laravel Storage (public disk)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team.
