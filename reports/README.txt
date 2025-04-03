Individual Pre-Project
Ellise Putnam
CSCI403
Due: 11/8/2023

---------------------------------------------------------------------------------------------------------

(1) Choose a dataset
The dataset I chose is called "120 years of Olympic history: athletes and results" from Kaggle.  
I chose this dataset because I am an athlete and always grew up watching the Olympics with my family,
so I thought it would be fun to see the data that dates back 120 years (up until 2018).  Within this
dataset there are 15 columns:

id, a unique number given to each athlete
name, the athlete's name
sex, either male (M) or female (F)
age, the age of the athlete
height, height of the athlete in centimeters
weight, weight of the athlete in kilograms
team, the country in which the athlete is competing For
NOC, the abbriviation for the country also called National Olympic Committee
games, contains the year and season the athlete competed in
year, the year that the athlete competed
season, the season the athlete competed (Summer or Winter)
city, the city in which the olympics was hosted
sport, the sport that the athlete competed in
event, the event the athlete competed in, if there was an event
medal, the medal that the athlete recieved (Gold, Silver, Bronze, NA)

Source: Griffin, R. “120 Years of Olympic History: Athletes and Results.” Kaggle, 15 June 2018, 
        www.kaggle.com/datasets/heesoo37/120-years-of-olympic-history-athletes-and-results/. 

 id |   name    | sex | age | height | weight | team  | noc |    games    | year | season |   city    |   sport    |            event             | medal 
----+-----------+-----+-----+--------+--------+-------+-----+-------------+------+--------+-----------+------------+------------------------------+-------
  1 | A Dijiang | M   |  24 |    180 |     80 | China | CHN | 1992 Summer | 1992 | Summer | Barcelona | Basketball | Basketball Men's Basketball  | NA
  2 | A Lamusi  | M   |  23 |    170 |     60 | China | CHN | 2012 Summer | 2012 | Summer | London    | Judo       | Judo Men's Extra-Lightweight | NA

---------------------------------------------------------------------------------------------------------

(2) Load and Clarence
In order to load the data that I found, I first copied the csv file into the same directory as 
the sql file.  Next I used the copy feature to copy the table that I created (see sql statements 
below).  I did have some issues with some rows not having values, such that their values were 
literally 'NA' when the column was height or weight for example (a column that would make sense
as a NUMERIC variable).  However I was able to fix this issue (see the queries that ensure the
data is clean below).

CREATE TABLE athlete_events (
  id NUMERIC(6,0),
  name TEXT,
  sex TEXT,
  age TEXT,
  height TEXT,
  weight TEXT,
  team TEXT,
  NOC TEXT,
  games TEXT,
  year NUMERIC(4,0),
  season TEXT,
  city TEXT,
  sport TEXT,
  event TEXT,
  medal TEXT
);

Queries that ensure the data is clean:
    1. I created a few queries that change the NA values in some of the columns to just be null
       and then change them to be NUMERIC values after removing the NA values.  For example, some
       of the rows did not have a height or weight value or they had NA as the value, so I first 
       set the variable height and weight to be TEXT, then removed the NA values and made them null
       and then changed the variable type to be NUMERIC so that it would be easier to manipulate the 
       values.  I did this for height, weight, age, and medal (however I did not change medal to be
       NUMERIC because it is not a numeric value).

    UPDATE athlete_events
    SET age = NULL
    WHERE age = 'NA';

    ALTER TABLE athlete_events
    ALTER COLUMN age TYPE NUMERIC(2)
    USING (age);

    2. The next thing I did to help clean up the data was deleted the column 'games'.  This column
       essentially just contained information of two columns already populated in the table as the 
       games column contained the year and season of the given olympics.  I figured that information 
       would be easier to manipulate if they were seprate columns (which they are) so I decied to 
       delete games to reduce the amount of unnecessary data.  The command I used is below.

    ALTER TABLE athlete_events
    DROP COLUMN games;

    3. In addition to the first data cleaning process, I did further cleaning on the age, height,
       and weight columns so that they could be viewed in a VIEW.  For this data, I just removed
       the rows that had null values (which we established as null from NA in query 1) in order to
       clean the information up more.  Below is my sql statement.

    CREATE VIEW clean_athlete_data AS
    SELECT *
    FROM athlete_events
    WHERE age IS NOT NULL 
    AND height IS NOT NULL 
    AND weight IS NOT NULL;

---------------------------------------------------------------------------------------------------------

(3) Analyze

  1. Which countries have won the most gold medals in the dataset, and how many gold medals have 
     they won? (Aggregate function)

  SELECT NOC, COUNT(*) AS gold_medals
  FROM athlete_events
  WHERE medal = 'Gold'
  GROUP BY NOC
  ORDER BY gold_medals DESC
  LIMIT 5;

 noc | gold_medals 
-----+-------------
 USA |        2638
 URS |        1082
 GER |         745
 GBR |         678
 ITA |         575

The question asks about which countries have the most gold medals, in the output of the query we 
can see which country has the most, how many they have, as well as the top counties that follow the 
leading gold medalist county.  I think this information is interesting because we live in the United 
State and it is interesting to see that we have won the most gold medals in the last 120 years.

  2. What is the average age of gold medalist in different sports? (Common table expression)

  WITH avg_age_by_sport AS (
    SELECT sport, AVG(age) AS avg_age
    FROM clean_athlete_data
    WHERE medal = 'Gold' AND age IS NOT NULL
    GROUP BY sport
    ORDER BY avg_age DESC
)
SELECT sport, avg_age
FROM avg_age_by_sport LIMIT 5;

      sport       |       avg_age       
------------------+---------------------
 Art Competitions | 38.0000000000000000
 Equestrianism    | 34.9174311926605505
 Curling          | 32.7826086956521739
 Sailing          | 30.1061946902654867
 Beach Volleyball | 29.9565217391304348

The question asks about the average age of gold medalist in each sport, as displayed in the table, we
have the sport on the left and then the average age in which those athletes are winning gold medals. 
I think that this information is interesting because as we can see (for the listed sports) 38 appears 
to be the oldest average age and it is continuously decreases.

  3. Which athletes are over 30 and have won a gold medal?

  SELECT name, age
  FROM clean_athlete_data
  WHERE medal = 'Gold' 
  AND age > 30
  LIMIT 5;

              name              | age 
--------------------------------+-----
 Nicola Virginia Adams          |  33
 Marilyn Agliotti               |  33
 Charles Benedict "Ben" Ainslie |  31
 Charles Benedict "Ben" Ainslie |  35
 Artur Surenovych Aivazian      |  35

In the above output we can see the name of the athlete and their age.  While we limited this output at 5,
we are able to see all of the athletes that are over 30 years old and have won a gold medal as asked in
the question.  I think this is not only interesting but also amazing that these people are able to compete
in high level sports at ages where competeing is not always doable at that age.

  4. Who are the tallest athletes and what sport do they play?

  SELECT DISTINCT name, sport, height
  FROM clean_athlete_data
  WHERE height IS NOT NULL
  ORDER BY height DESC
  LIMIT 5;

          name          |   sport    | height 
------------------------+------------+--------
 Yao Ming               | Basketball |    226
 Arvydas Romas Sabonis  | Basketball |    223
 Tommy Loren Burleson   | Basketball |    223
 Gunther Behnke         | Basketball |    221
 Roberto Dueas Hernndez | Basketball |    221

In the above question, we are asked who the tallest athletes are, the sport that they play, and what their
height is.  As we can see, the table shows just that!  Not only is this cool to see that the tallest person
is 3cm taller than the next, but also how the top 5 tallest athletes all play basketball (shocker)!
