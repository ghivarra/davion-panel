# Davion Panel
Created using CodeIgniter 4 and Vue JS 3

## URL Demo
https://davion.giberamedia.com/login

## Accounts
Username: admin\
Password: W2X63pL7Ty3CBbRfansR

The database and files keep resetting every 30 minutes, so it doesn't matter. Try it as much as you want.

# Features
- Advanced Role Level and Permissions
- Administrator CRUD
- Administrator Session Control
- Dynamic Menu
- Dynamic Website Informations (title, description, etc.)
- Menu Search Form

# Dependency
- CodeIgniter 4
- VueJS 3
- Vue Router
- Vue Draggable
- Font Awesome 6
- Axios
- Bootstrap 5.3
- SweetAlert 2

# I forked but cannot run it?
One cannot run this app without the ignored files from App/Config, vendor, and node_modules folders. But don't worry, just run steps below (on Linux or UNIX environment):

- Run `npm install` on project location
- Run `composer install` on project location
- Run `cp vendor/codeigniter4/framework/app/Config/{App.php,Cache.php,ContentSecurityPolicy.php,Cookie.php,Database.php,Encryption.php,Email.php,Session.php,Security.php} app/Config/` on project location
- Set the copied configuration files based on your server/host environment
- Run `npm run dev` for development environment
- Run `npm run build` and set `VITE_APP_ENV` in .env file to `production` for production

# FAQ
Email me at gsenandika@gmail.com for further questions.