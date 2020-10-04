# Sweeetch
Sweeetch's website. 

Launch the app locally

# launch

Open the console and : 

1. clone repository from GitHub

        $ git clone [repository url]
        
2. cd into the repository. Then run : 

        $ composer install 
        $ composer update 
        
3. then run :

        $ yarn install
        $ yarn self:update 
        $ yarn watch 

4. launch app : 

        $ symfony server:start 
        
5. go to localhost:8000 and naviguate on the app 


# connect to database 

1. this app uses MySQL with MAMP. So start MAMP 

2. a - Be sure to have a .env file and that DATABASE_URL refers to you database url
   
   b - If you use MAMP, go to config > packages > doctrine.yaml and paste this under "charset: utf8mb4" : 
   
        unix_socket: /Applications/MAMP/tmp/mysql/mysql.sock

3. from the terminal create new database : 

        $ bin/console doctrine:database:create
        
4. then load entites : 

        $ bin/console make:migration
        $ bin/console doctrine:migrations:migrate
        
5. load fixtures : 

         $ bin/console doctrine:fixtures:load 
        

you can now use the app ! 
