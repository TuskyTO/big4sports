# big4sports
Website with quizzes on all North American big 4 sports 

The teammates for this group are: Charlie Knapp and Elliot Goldman

The purpose of this code is to create a website that contains sports quizzes of all 4 major North-American sports. The quizzes will be mostly based on statistics. They will have a difficulty of easy, medium or hard. Eventually we will have accounts where users can keep track of their high scores. We will also implement a leaderboard to see who the all-time leaders are based on accuracy and time.

Our repository contains all of our issues, and a branch dedicated to each issue. Our index.html file is our main website. We also have 4 additional html files that highlights quizzes on each sports league for users (ex: NBA.html). These files are not "completed" because our focus was the main landing page and those files are just the idea of what they would be for. We also have a large and small image for each of our 4 images. (One for our mobile interface and one for the web). We also have a css file to style our main index page. 

Our code can be run by opening our github pages link or pulling our repo and opening index.html locally.

The work was completed 50/50 by both teammates Charlie and Elliot.

NOTE: Certain google chrome extensions negatively affect our websites lighthouse report. Please compelete the lighthouse report on an incognito tab to ensure accurate testing. 






_________________________________________________________________________________________________________________________________________________________________________________________
Hw2:



Below are screenshots of the local phpMyAdmin interfaces for each team member:

Elliot:

![Image 2-26-25 at 10 03 PM](https://github.com/user-attachments/assets/d6550815-cc63-4b72-84f5-056e4c34483f)


Charlie:

<img width="1465" alt="Screenshot 2025-02-26 at 10 04 18 PM" src="https://github.com/user-attachments/assets/6b335513-9050-4268-b1c9-a48a38b1454c" />



Below are steps to install our app:

1. Clone this repository via "git clone https://github.com/TuskyTO/big4sports.git" in your terminal or download our sopurce code zip file from our v1.0.0 release and unzip it.

2.  We have a main folder "big4sports" which contains our front-end HTML files, as well as a subfolder "backend" which contains all of our php files such as index.php, db_connect.php, create_trivia.php, etc. The frontend in the big4sports folder is the scaffolding for the landing page and the additional html pages that you can access from it. Our backend folder contains all the files needed for our database and CRUD applications.
   
3. To test our app, the user needs to copy and paste the entire "big4sports" folder in the "htdocs" folder of their XAMPP.

4. The user must open their XAMPP control panel and make sure that Apache and MySQL are running.

5. We have provided the app-db.sql file with the existing databases. To use this, you must:
   a. create a database called "app-db" on your local phpMyAdmin
   b. select the app-db database on your phpMyAdmin, then select "import", then click the file app-db.sql that you have from our cloned repository. This will provide you with the most updated version of our database.

6. Final step: Open your browser, visit "localhost/”. From here should see the big4sports folder. Then, you can open our backend folder and access our index.php file. 

7. Once you have registered an account, you should be able to:
   a. Read the existing trivia questions from the existing users via the table and the "View" button on the furthers right column of the table
   b. Create your own trivia questions via add a "add a new trivia question"
   c. Update your own trivia questions (ONLY YOURS)
   d. Delete your own trivia questions (ONLY YOURS)
   e. Visit our landing page

NOTE: Instead of installing our app locally you can also access through our infinityfree website link below.

LINK TO OUR WEB APP ON INFINITY FREE: https://big4sports.kesug.com/

As you navigate through these steps, you should see the following pages:

<img width="1440" alt="Screenshot 2025-03-06 at 2 00 55 PM" src="https://github.com/user-attachments/assets/894198c4-6a0b-4a39-9dca-4a6ade1a49d7" />

<img width="1440" alt="Screenshot 2025-03-06 at 2 00 44 PM" src="https://github.com/user-attachments/assets/fe118bc6-796e-4b6a-bbb9-547bb8cf02ff" />


<img width="1438" alt="Screenshot 2025-03-06 at 1 45 55 PM" src="https://github.com/user-attachments/assets/0c42e098-0363-472a-b9d6-ec650285a4be" />


<img width="1436" alt="Screenshot 2025-03-06 at 1 46 13 PM" src="https://github.com/user-attachments/assets/a0d088fb-3734-4c3e-9e24-6110823a9c56" />

<img width="1440" alt="Screenshot 2025-03-06 at 1 46 29 PM" src="https://github.com/user-attachments/assets/d4fe0668-1b50-4937-907b-59036e8b0216" />

<img width="1440" alt="Screenshot 2025-03-06 at 1 55 25 PM" src="https://github.com/user-attachments/assets/71ddea68-94cf-4824-a3f2-9507080b249c" />

<img width="1440" alt="Screenshot 2025-03-06 at 1 55 07 PM" src="https://github.com/user-attachments/assets/494b44cf-f133-441f-b544-0ba54e90cf2d" />

Percentage Breakdown: 50/50 between Charlie and Elliot

A little more on our version control: We used main for production-ready code, and feature-specific branches for each big change or problem that we had with our existing code. We merged them via pull requests.







________________________________________________________________________________________________________________________________________________________________________________________
Hw3:

Some notes about our updated file management:

We have created 3 new folders:
- Images --> for all images on our landing page
- myApp --> for all files pertaining to our React Native mobile app
- Screens --> a folder within myApp that contains all of the frontend javascript files
* Backend --> This folder already existed and remains almost the exact same except for the changes in api_trivia.php to implement the REST API


HOW TO SETUP/RUN OUR APP:

- BACKEND:
The backend setup directions are the same from Hw2. They are as follows:
1. Clone this repository via "git clone https://github.com/TuskyTO/big4sports.git" in your terminal or download our sopurce code zip file from our v1.0.0 release and unzip it.

2.  We have a main folder "big4sports" which contains our front-end HTML files, as well as a subfolder "backend" which contains all of our php files such as index.php, db_connect.php, create_trivia.php, etc. The frontend in the big4sports folder is the scaffolding for the landing page and the additional html pages that you can access from it. Our backend folder contains all the files needed for our database and CRUD applications.
   
3. To test our app, the user needs to copy and paste the entire "big4sports" folder in the "htdocs" folder of their XAMPP.

4. The user must open their XAMPP control panel and make sure that Apache and MySQL are running.

5. We have provided the app-db.sql file with the existing databases. To use this, you must:
   a. create a database called "app-db" on your localhost/phpMyAdmin/
   b. select the app-db database on your phpMyAdmin, then select "import", then click the file app-db.sql that you have from our cloned repository. This will provide you with the most updated version of our database.

6. Final step: Open your browser, visit "localhost/”. From here should see the big4sports folder. Then, you can open our backend folder and access our index.php file. 

7. Once you have registered an account, you should be able to:
   a. Read the existing trivia questions from the existing users via the table and the "View" button on the furthers right column of the table
   b. Create your own trivia questions via add a "add a new trivia question"
   c. Update your own trivia questions (ONLY YOURS)
   d. Delete your own trivia questions (ONLY YOURS)
   e. Visit our landing page

- MOBILE FRONTEND:
1. Repeat steps 1-5 of the BACKEND instructions.
2. Make sure you have Android Studio, Node.js, and XAMPP downloaded
3. Open Android Studio and click the dropdown "More Actions" option
4. Open virtual device manager
5. Add a device (whichever you device you desire to test in)
6. Start (open) said device 
7. In terminal, make sure you are in the correct directory "myApp" (which is a subfolder in our main big4sports folder)
8. Enter command "npm install" in the terminal
9. Enter command "npm start" in the terminal
10. Click "a" as shown in terminal to activate app in Android emulator
11. You will now be pushed to a login page. If you have an existing account, login with those details. Or, create a new account by clicking "Register"
12. Once you are logged in, you will be able to see all of the existing trivia questions. You can only "update" and "delete" the trivia questions that your user originally created. You can "read" any of the trivia questions, even ones that the current user has not created. 
13. To update one of your existing trivia questions, you must select the update button, change what you desire, and then re-login. You will not see the changes unless you re-login (click the back arrow).
14. If you wish to create a new trivia question, the same re-login (click the back arrow) applies. You must re-login to see the newly created trivia question. 


Screenshots for Postman testing of POST, PUT, GET, DELETE:

Elliot screenshots:
![Image 3-24-25 at 11 44 AM](https://github.com/user-attachments/assets/07a9fa8b-15ec-49b9-85c9-78a9913c970f)

![Image 3-24-25 at 11 44 AM](https://github.com/user-attachments/assets/c26b4c20-cd94-4b26-a2ab-c9955b79786a)

![Image 3-24-25 at 11 47 AM](https://github.com/user-attachments/assets/2b606fbd-a1e6-4d52-bfb3-6a7740ac778f)

![Image 3-24-25 at 11 47 AM](https://github.com/user-attachments/assets/53fc8681-69e7-4c62-bf8b-645f69f2299c)

Charlie screenshots:
<img width="1470" alt="Screenshot 2025-03-25 at 8 55 50 PM" src="https://github.com/user-attachments/assets/6487d195-8727-4ad5-b7d2-7fcdd95a96af" />

<img width="1470" alt="Screenshot 2025-03-25 at 8 53 42 PM" src="https://github.com/user-attachments/assets/2a694645-fb80-4b87-bc32-de085a0907dd" />

REST API EXPLANATION:
   We have implemented a REST API into our backend in order to work with the React Native frontend. We have updated our backend php files to return data in JSON format rather than        pure HTML. This makes it easy for a React Native moble app to consume and display the data from our backend. 

   Specifically, we modified our existing php scripts to act as API endpoints:
      - A GET request to /api_trivia.php will return all trivia questions (or a specific trivia question if you give an id#).
      - A POST request to /api_trivia.php would create a new trivia question.
      - A PUT request to /api_trivia.php?id=5 would update the trivia record with ID 5.
      - A DELETE request to /api_trivia.php?id=5 would delete the trivia record with ID 5.

So the way our REST API works:

1. Receives the incoming HTTP request.

2. Determines the request method (GET, POST, etc.).

3. Processes the request (e.g., querying or updating the database).

4. Returns the data (or an error) as a JSON response.

A NOTE ON OUR CORS HEADERS:
For the purposes of this homework assignment, we're using the wildcard (*) for CORS during development. This allows our React Native app or any other client to access your backend without restriction. While this is fine for development, in a production environment we would want to restrict the allowed origins to only trusted domains (e.g., https://big4sports.kesug.com). This is to prevent security risks such as unauthorized access or data leaks.

THIS HOMEWORK WAS 50/50 BETWEEN CHARLIE AND ELLIOT 
