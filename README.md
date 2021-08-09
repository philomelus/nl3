Newsledger 3.0

This is a complete rewrite of Newsledger (sf.net/projects/newsledger), using python.

This file is VERY EARLY ON and INCOMPLETE:  You can email me if you need help at russg AT rnstech.com.

If you want to run this on a local system (Windows, Linux, or MacOS will work, as all three support python), follow the instructions below.  Note that these directions ARE NOT FOR PRODUCTION USE, as they would provide little to no security, among other issues.  If you want a production server (always running, public internet interface), check for Flask hosting (start at https://flask.palletsprojects.com/en/2.0.x/deploying/).

Newsleger?
==========

16 years ago I wrote newsledger 1 for my parents to use.  They have been newspaper dealers most of their adult lives, and their latest delearship was so large that they could no longer do it manually (as in pen/paper!  ugh!).  I volunteered to set something up (that was more than just a simple database as I had done before).  Newsledger is what ended up from this, and they've been using it for the whole 16 years since.  I have made minor changes here and there to add functionality or resolve bugs, but I'd done nothing major on it.

Version 1 used PHP 5 (originally, last it was used was with 7.2), as in the early 2000's that was the up and coming web tool.  Persoanlly, I never cared for it, but there wasn't really anything else I wanted to use (NO PERL! Python didn't have any usable web frameworks yet).  At the time, MySQL was still independant, and part of the LAMP stack that was easy to get set up and working.  Thus, I use MySQL 5 and PHP 5 for the project.

The code for nl1/2 is quite fragile.  If you don't use it exactly as it expects to be used, weird things can happen.  For the most part, its easy to use it as designed, as long as you have used a traditional database backed system before.  The whole idea was based on how the newspaper dealerships I had been exposed to worked.  As for capabilities, it was a full stack:

- Generate bills for customers
- Automatically handle periodic stops/restarts for customers.
- Handle notes for delivering (instructions on where to deliver a paper, for example)
- Provide driver change lists daily (to notify the actual newspaper deliverers on customers that are on vacation, etc.)
- Provide drive routes lists, that are in order of delivery.
- Provides unlimited delivery types (for example daily+sunday delivery, or sunday only delivery).
- Provides unlimited customer rates support, so as paper costs increase, customer subscriptions can be increased.
- Several different reports for detailing routes, driver tips, customer's who've paid ahead or are behind, etc.
- Handle customer payments
- etc.

I could go on, but I think the point is made.

So, the question is, why am I rewriting this now?

Within 2 or 3 years of getting nl1 functional, I really got to dislike PHP.  This was before the PHP database objects existed, and so the code was all calling the mysql extension directly (well, sort of anyway).  I did some abstracting, as I really wanted to use PostgreSQL at one point, but then the SQL dialect issues became more than I was willing to do.

What I really wanted was to use python to reimplement it.

I actually got somewhat far down the road of using Flask and Bootstrap 3 to reimplement it, before I got involved in other projects and never finished it.  This is what I call nl2, even though it was never really finished.

Then my mother died.  My dad has never been real good at detail work;  As a result, he did most of the dealing with drivers for dealership, and my mom did the financial and dealing with customer sides.  When she died, my dad had to step into doing those roles ...

And that forced me to add more stuff to nl1.  In php.  Ugh.  There was no way I was going to be able to provide directions my dad could follow for all of the stuff my mom used to do.  I did what I could, but in the end my wife ended up taking over some of the things my dad just wasn't able to do.

It was adding the new php code that finally made me decide to rewrite it.  Using my favorite web framework (me and django have had some problems with databases, and some of those issues still exist last I checked), Flask.  I wanted to have a modern web site under my belt, as I have always been a low level software person, and nl1 is/was a simplly architected beast.  Lots of popup windows.  Very sparse (and ugly) UI design (a main menu bar and then pages of content below), etc.

I no longer care for bootstrap, as a styling platform.  It has become so complex as to be unusable IMO.  BS4 was a significant update, but BS5 is just too much.  For nl3, I am using W3.CSS.  I didn't even know about it what I started looking for what to use.  Its fairly simple, once you get the idea behind it, and I found myself liking the looks of it.  More importantly, I was able to figure out how to ADD TO IT (try that with BS5, starting from scratch ...).  Because it uses standard web css styling, I was able to make changes without having to learn a new language or set of terms.

That brings me to now (mostly;  Lots of PHP refactoring left out, like converting my pages to smarty templates ...).

With python, there is normally one way to do something.  Once I know it, I KNOW IT.  With PHP (just like perl), there are so many ways to do the same thing, and most of them provide different results that aren't compatible with each other.  Yes, this is being addressed with newer versions (finally), but I have 170k of legacy to deal with.  I don't want to learn 5 ways to get the current local time (yes, I know!), etc.

Every time I tried to fix a bug related to accepting unexpected input or the like, PHP just got in the way.  Eventually I just started using perl regexes everywheree.  I have to give PHP credit there, though, as the implementation is fast enough for web usage.  I want to use python, and I am.

With Flask, the code for responding is so, well, stragiht forward (never use 'straight forward' to talk about coding ... alas), that I can go back a fix things even when its been months since I last looked at the code.  With PHP, I was always having to get back into figuring out how I'd implemented this or that (like the menuing system, which wasn't a bad design, just complicated), and then it'd never work the first time.  With Flask and sqlalchemy, I can follow what is going on with much less effort.

One of the things I was surprised about was how I was able to use my old nl2 code (that hasn't been touched in, literally, years) and understand exactly what I was doing back then.  I think most of that is about it being in python, but not all of it.

Anyway, I am rambling and am ready to do something else.  I'll come back when I am up to it.


Local Development Style Hosting
===============================

1.  Install the latest version of Python 3.  Python 3.9.6 was used during development, and is known to work well.

2.  Grab a copy of the source code for Newsledger from github (https://github.com/philomelus/nl3).  If there is a release, use it.  If not, use the newest source from the "code" button (if you need help with git, github has good documentation on how/what/why for git).

3.  Extract the source somewhere on your computer.  I use a dedicated src directoy in my home folder, thus the full path to my instance, on Windows is 'c:\Users\{name}\src\nl3'.  For Linux, its '/home/{name}/src/nl3'.

4.  Open a command prompt that can use the python 3 from step 1 (For windows, if you have the installer add python to your path, then a regular command prompt will work).

5.  Change your shell working directoy to the location of the source from step 3.

6.  Create an environment for nl3 to execute from.  The following command will do this:
    > python3 -m venv venv

7.  When step 6 complete, you should have a new folder named venv within your source folder.  Now we need to install the libraries that newsledger relies on.  Python's PIP program can install them.  Before we do that, though, we need to activate the environment we just had python create:
    Windows > venv/bin/activate.bat
    *nix/MacOS > . venv/bin/activate

8.  Your prompt from the command shell should have changed to show the environment you are working in.  Now we can get PIP to install the libraries needed.  One of the following commands should work:
    > python3 -m pip install -r requirements.txt
    > pip3 install -r requirements.txt

8.  If all goes well, you are almost done.  Since this instance of newsledger will be hosted locally, we will use a sqlite3 database, which requires no further configuration.  If you want to use a different database, you'll need to consult documentation elsewhere (I have tested with MySQL/MariaDB and PostgreSQL and had no problems once it was set up).

9.  HERE THERE BE DRAGONS!  WARNING!  DOCUMENTATION INCOMPLETE HERE!
    Scared? :-)

    At this point, I don't have instructions for creating a default database.  Development has been done using an old Newsledger 1.x/2.x database originally hosted on MySQL/MariaDB (since that's the ONLY database nl1/2 supported).  I will eventually add a way to do so, as well as provide directions here for doing so.  At that point, though, I may also actually have a way to install it via installer or such.  In the mean time, email me at russg AT rnstech.com and I can help you get a default sqlite3 database ready.

10.  You should now be albe to start nl3.  To do so, run the following, from a command shell, from within the source directory of nl3:
     > flask run

You should see a local development server start on localhost:5000.  Use your system internet browser (I recommend Firefox!) to open http://localhost:5000 and you should see a login prompt.


