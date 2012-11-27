f2v is an open system for voting - more info at f2v.org 
or email admin -a-t- f2v.org


f2v has 3 main components:
* the database.
* the server: carries out db operations through http api's. Only the client
  should be able to access the server's api.
* the client: provides the public api's to access the database.


Requirements: 
------------
* [PDO]: http://php.net/manual/en/book.pdo.php
* [Slim Framework]: http://slimframework.com/ 
* [rest-client-php]: https://github.com/shurikk/rest-client-php


Usage:
-----
1. run `$ php composer.phar install` in the directory where installed f2v. This
   will retrieve and install the required packages. See [composer]:
   http://getcomposer.org/ for more info.
2. Configure your http server to run the server on a virtual site and point it 
to the f2v server's api directory `$root_dir/lib/server/api`.
3. Configure your http server to run the client on a virtual site and point it 
to the f2v client's api directory `$root_dir/lib/client/api`.
4. Edit 'config/server.php' and set your database connection parameters.
5. Edit 'config/client.php' and point `$server_url` to the url where your
   f2v server is running (step 2). 
