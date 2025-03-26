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

Screenshots for Postman testing of POST, PUT, GET, DELETE:
![Image 3-24-25 at 11 44 AM](https://github.com/user-attachments/assets/07a9fa8b-15ec-49b9-85c9-78a9913c970f)

![Image 3-24-25 at 11 44 AM](https://github.com/user-attachments/assets/c26b4c20-cd94-4b26-a2ab-c9955b79786a)

![Image 3-24-25 at 11 47 AM](https://github.com/user-attachments/assets/2b606fbd-a1e6-4d52-bfb3-6a7740ac778f)

![Image 3-24-25 at 11 47 AM](https://github.com/user-attachments/assets/53fc8681-69e7-4c62-bf8b-645f69f2299c)

REST API EXPLANATION:

Our data model (trivia) has the following fields:

- id (int) – Auto-increment primary key.

- username (string) – The user who created the trivia.

- trivia_question (string) – The question prompt.

- trivia_answer (string) – The correct answer to that question.

- difficulty (int) – An integer from 1 to 10.


