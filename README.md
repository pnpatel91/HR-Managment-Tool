# Project Management System Admin

This is laravel admin project sample include below list

- <i class="fas fa-users nav-icon"></i> Team Members
- roles and permission
- image uploading
- company 
  - One To Many Branches (With company_id in branch table)
- branch
  - One Company To Many (With company_id in branch table)
  - Many Branches To Many Users (With user_has_branches table)
- department
  - Many Departments To Many Users (With user_has_departments table)
- Holiday
  - Many Holidays To Many Branches (With holidays_has_branches table)
- attendance (punch-in & punch-out)
  - One attendance To Many branchs (With branch_id in attendance table)
  - One attendance To One User (With created_by in attendance table)
- rota (employee job time schedule)
  - One rota To One User (With user_id in attendance table)
- Leave
  - One leave To Many branchs (With branch_id in leave table)
  - One leave To One User (With employee_id in leave table)
  - One leave To One User (With approved_by in leave table)
- Eloquent: Relationships
  - One To Many
  - Many To Many
  - Polymorphic Relationships

## Dependencies and Plugins

- [spatie/laravel-permission](https://github.com/spatie/laravel-permission)
- [AdminLTE3](https://adminlte.io/themes/v3/)
- [stevebauman/location](https://github.com/stevebauman/location)

## Installation

- git clone
- composer install
- cp .env.example .env
- php artisan key:generate
- setup database in .env
- php artisan migrate --seed
- php artisan serve

The site will run localhost:8000

## Default Users

```cmd
//Super Admin User (View all data)
username - superadmin@gmail.com
password - password

// Admin User (View only his branch data)
username - admin@gmail.com
password - password

// Management User
username - management@gmail.com
password - password

// Staff User
username - staff@gmail.com
password - password

// Accounting User
username - accounting@gmail.com
password - password
```
## Create New Module [For Developer]

- ```cmd 
  $ php artisan make:model Model_name --all
  ```
- Update database/migrations, database/seeds & database/factories files
- Add this model seed in DatabaseSeeder
- Add this model Permission in app/http/Permission.php
- Creat Requests file in app/http/Requests folder for validation
- Update model, view, controller 

## License

MIT
