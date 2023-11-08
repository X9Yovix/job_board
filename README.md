# Job Board Backend

## Team of the Project
<div align="center">
  <h3>Meet the Team</h3>
  <table>
    <tr>
      <td align="center">
        <a href="https://github.com/X9Yovix">
          <img src="https://avatars.githubusercontent.com/X9Yovix" width="100" height="100" alt="Contributor 1"><br />
          <sub><b>Karim Ouazzou</b></sub>
        </a>
      </td>
      <td align="center">
        <a href="https://github.com/nada203123">
          <img src="https://avatars.githubusercontent.com/nada203123" width="100" height="100" alt="Contributor 2"><br />
          <sub><b>Nada Ghribi</b></sub>
        </a>
      </td>
      <td align="center">
        <a href="https://github.com/yassineraddaoui">
          <img src="https://avatars.githubusercontent.com/yassineraddaoui" width="100" height="100" alt="Contributor 3"><br />
          <sub><b>Yassine Raddaoui</b></sub>
        </a>
      </td>
    </tr>
  </table>
</div>

## Info

Before building and running the Job Board App, ensure you have the following prerequisites installed:

- PHP 8 or higher
- Composer
- Symfony CLI

## Build

To get started with the Job Board, follow these steps:

1. Clone the repository:

```
git clone https://github.com/X9Yovix/job_board.git
```

2. Navigate to the project directory:

```
cd job_board
```

3. Install the dependencies using Composer:

```
composer install
```

4. Run project using Symfony CLI:

```
symfony server:start
```

## Database Configuration and Creation

1. Set up the database connection:
   - Open your `.env` file located in the root of the project.
   - Find the line that specifies `DATABASE_URL` and uncomment it.
   - Replace the default database parameters with your actual database username, password, and name. Ensure the database name is `job_board`.
   - Here is an example configuration with the default `root` user and no password:

```plaintext
# For a MySQL database
DATABASE_URL="mysql://root:@127.0.0.1:3306/job_board?serverVersion=8.0.32&charset=utf8mb4"
```

2. Create the `job_board` database by running this command:
```
php bin/console doctrine:database:create
```
