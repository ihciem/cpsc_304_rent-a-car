<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

<html>
    <head>
      <!-- Required meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">

      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
    <h2>Welcome to SuperRent!</h2>
    <hr />
    <form action="customer_search.php">
          <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->

          <p><input type="submit" value="I am a Customer"></p>
      </form>
      <form action="clerk_actions.php">
          <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
          
          <p><input type="submit" value="I am a Clerk"></p>
      </form>
	</body>
</html>
