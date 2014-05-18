CUPWEB API
=======

This is an unofficial PHP API for the 'Contact Uren Planner' better known as CUPWEB. It uses [PHP's curl](https://php.net/curl) function to get the contents of a CUPWEB site.

**This CUPWEB API does not support the plan function, I've made this just for getting the timetable because my school does not use the plan function.**

##Using
The CUPWEB API is pretty straight forward, just look at [api-example.php](api-example.php), however because of some limitations of the CUPWEB website (and just ASPX's EventValidation) it must call the CUPWEB website once to save a cookie before you can get a timetable. You must search for a (last) name you will be using for login and fetching the timetable, not a random other name!

**Before you use this API, first edit api.php and edit the viewstate in the `getTimetable` function (line 109) to the right viewstate you can find on your (CUPWEB-SITE).nl where you enter the password after you've searched for a name. If you don't have any idea how to do this, just email me at the address on my profile: https://github.com/MegaCookie**

**If PHP fails to save the cookie file than just make sure your PHP has the right rights for saving the cookie file.**

**Because of another strange bug in the CUPWEB website, some students DO have TWO spaces between their last name and their prefix in their full username. Keep in mind that PHP does not display two spaces normally in the HTML.**

##Code examples
Take a look at [api-example.php](api-example.php) for a basic overview what you can do with the CUPWEB API.

###General information
- CUPWEB API's functions can be called easily like this: `cupphp\getTimeTable()`. As you can see it uses `cupphp` as namespace.
- Currently there is only one exception: ``CUPphp:X:invalid_server_response, server returned invalid response``.

###Classes
- `Session` holds the data for the session and the array of the names class you've searched for returned by `getNames()`.
  - **Usage**: It holds the important `sessionId` and the `names` class array. Look at the `getNames()` function for usage informations.
  - **Properties**: `sessionId` `names` `eventvalidation`.
  - **Functions**: `__constructor()` mirrors `set()`, `set($sessionId,$names,eventvalidation)`.

- `Names` holds the data for students you've searched for returned by `getNames()`.
  - **Usage**: It has an array with the student's `name`, `class`, `number`, `username`  Look at the `getNames()` function for usage information. 
  - **Properties**: `name`, `class`, `number`, `username`.
  - **Functions**: `__constructor()` mirrors `set()`, `set($name,$class,$number,$username)`.

- `Lesson` holds the data for the TimeTable lessons returned by `getTimeTable()`.
  - **Usage**: It has an array with the lesson's `date`, `day`, `hour`, `teacher`,`subject`,`classroom`  Look at the `getTimeTable()` function for usage information. 
    
    **If the API fails to login, it will just return an empty array for now. Maybe I will change this in the future.**
  - **Properties**: `date`, `day`, `hour`, `teacher`,`subject`,`classroom`.
  - **Functions**: `__constructor()` mirrors `set()`, `set($date,$day,$hour,$teacher,$subject,$classroom)`.

###Functions
- `getNames($filter,$schoolUrl)`: searches for students matching the filter and using the school's CUP Website.
  - **Returns**: a `Session` array with a `Names` array within it.
    - `Session`: array of the session and the `Names` array.
      - `session`: session needed for reading the cookie for logging in.
      - `Names`: an array with all the student's information matching the filter.
        - `name`: the last name and first name of the student.
        - `class`: the student's class.
        - `number`: the student's unique number identifier.
        - `username`: the full username used by CUP to login like this: `last name prefix first name (class) [number]`.
      - `eventvalidation`: the eventValidation needed for login aspx files.        
**Note: some students do have an extra space between their last name and their prefix. PHP will not display that in HTML but it exsists in the variable**
  - **Parameters**: 
    - `filter`: the search string to search students for. Because of ASPX's stupid EventValidation you need to provide a (last) name you're going to use for the login and timetable procedure, not a random other name.
    - `schoolUrl`: the school's CUP website URL.
- `getTimeTable($username,$password,$session,$schoolUrl,$eventvalidation)`: Login and get the student's timetable.
  **IMPORTANT: `$eventvalidation` needs to be encoded (encode URL) before you call this function in order to get the timetable!**
  - **Returns**: a `Lesson` array with all the lessons in a week.
        **If the API fails to login, it will just return an empty array for now. Maybe I will change this in the future.**
    - `date`: the date of the lesson like this: `2014-05-12` `Year-Month-Day`.
    -  `day`: two letters of the day of the week (In Dutch of course).
    -  `hour`: the hour of the lesson (Two numbers like this: `01`).
    -  `teacher`: the name of the teacher who teaches.
    -  `subject`: the subject of the lesson.
    -  `classroom`: the lesson's classroom.
  - **Parameters:**:
    - `username`: the full username of the student like this: `last name prefix first name (class) [number]`.
    - `password`: for logging in at the CUP website.
    - `session`: needed for reading CUP's cookie for logging in, you could have got from `getNames()`.
    - `schoolUrl`the school's CUP website URL.


##Help
Don't you understand anything about this? Just send me a email at the address on my profille: https://github.com/MegaCookie

##To do
Being able to change the time period for the timetable.

##Thanks to
**MegaCookie**: For the *core* CUPWEB API
