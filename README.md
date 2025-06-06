# Davion Panel
Created using CodeIgniter 4 and Vue JS 3

## Alternative Versions
[Click Here](https://github.com/ghivarra/davion-panel-ts) - Davion Panel with CodeIgniter 4 & Vue JS 3 WITH Typescript

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
- Database Migrations using CodeIgniter Spark command

# Dependency
- CodeIgniter 4
- VueJS 3
- Vue Router
- Vue Draggable
- Font Awesome 6
- Axios
- Bootstrap 5.3
- SweetAlert
- Ramsey/UUID

# I forked but cannot run it?
One cannot run this app without the ignored files from DotEnv, App/Config, vendor, and node_modules folders. But don't worry, just follow the steps below to run this app (on Linux or UNIX environment):

- Run `bash install.sh` on root project location
- Modify the .env configuration files based on your server/host environment
- Run `php spark migrate:refresh` on root project location to migrate the database, make sure you already set the correct configurations on the database config in .env file.
- Run `npm run dev` for development environment
- Run `npm run build` and set `VITE_APP_ENV` in .env file to `production` for production

# FAQ
Email me at gsenandika@gmail.com for further questions.
