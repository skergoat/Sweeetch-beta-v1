# Sweeetch
Sweeetch's website. 


# launch

Open the console and : 

1. clone repository from GitHub

        $ git clone [repository url]
        
2. cd into the repository. Then run : 

        $ composer install 
        
3. then run :

        $ yarn install
        $ yarn watch 

4. launch app : 

        $ symfony server:start 
        
5. go to localhost:8000 and naviguate on the app 


# connect to database 

1. this app uses MySQL. So install and start MAMP 

2. from the terminal create new database : 

        $ bin/console doctrine:database:create
        
3. then load entites : 

        $ bin/console make:migration
        $ bin/console doctrine:migrations:migrate
        
 4 load fixtures : 

         $ bin/console doctrine:fixtures:load 
        

you can now use the app ! 
