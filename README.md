CUP-PHP
=======

This is an unofficial PHP API for the 'Contact Uren Planner' better known as CUP. It uses [PHP's curl](https://php.net/curl) function to get the contents of a CUP site.

**This API does not support the plan function, I've made this just for getting the timetable because my school does not use the plan function**

##Using
The code is pretty straight forward, just look at [api-example.php](api-example.php), however because of some limitations of the CUP website it must call the CUP website once to save a cookie before you can get a timetable. The best you can do is then to use this for searching for users.

**If PHP fails to save the cookie file than just make sure your PHP has the right rights for saving the cookie file**

**Because of another strange bug in the CUP website, some students DO have TWO spaces between their last name and their prefix in their full username**

##Code examples
Take a look at [api-example.php](api-example.php) for a basic overview what you can do with the CUP-PHP API. 

**If the API fails to login, it will just return an empty array for now. Maybe I will change this in the future.**

*More Coming soon...*

##To do
Be able to change the time period for the timetable.

##Thanks to
**MegaCookie**: For the *core* API
