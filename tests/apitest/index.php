<?php
require ($_SERVER['DOCUMENT_ROOT'] ."/engine.php");
?>
<!DOCTYPE html>
<html>
  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <script src="/assets/jquery/jquery-2.1.4.min.js"></script>
    <script src="/assets/jquery/jquery.transit.min.js"></script>
    <script src="script.js"></script>
    <style>
      * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }

      input {
        padding: 10px;
        margin-bottom: 5px;
      }

      input[type=button], input[type=submit], input[type=reset] {
        padding: 5px 10px;
        font-size: 12px;
        -webkit-appearance: none;
        border-radius: 0;
      }

      body {
        background: #000;
        margin-top: 2vh;
      }

      .menu {
        display: inline;
        position: relative;
        margin-left: 2vw;
        margin-right: 2vw;
        padding: 20px;
        width: 30vw;
        height: 190vh;
        background-color: #fff;
        float: left;
        overflow: scroll;
      }

      .content {
        display: inline;
        background-color: #a3a3a3;
        float: left;
        width: 32vw;
        margin-right: 2vw;
        height: 50vh;
        position: relative;
        padding: 20px;
        overflow: scroll;
      }

      .task {
        font-size: 20px;
        display: inline-block;
        font-weight: bold;
        color: #777;
        padding-top: 20px;
      }

      .mlink {
        margin-left: 20px;
        padding: 10px;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        width: 150px;
      }

      .mlink:hover {
        cursor: pointer;
        background-color: #f0ecec;
        color: rgb(69, 176, 210);
        text-align: center;
        border-radius: 15px;
      }

      .jwt {
        display: inline;
        float: left;
        width: 30vw;
        height: 50vh;
        position: relative;
        padding: 20px;
        background-color: #bdbdbd;
      }

      .method {
        display: inline;
        position: relative;
        float: left;
        background-color: #696767;
        color: #fff;
        padding: 20px;
        width: 64vw;
        height: 8vh;
        margin-top: 2vh;
        font-size: 14px;
      }

      .api {
        display: inline;
        position: relative;
        float: left;
        background-color: #3b3a3a;
        color: #fff;
        padding: 20px;
        width: 64vw;
        height: 8vh;
        margin-top: 2vh;
        font-size: 14px;
      }

      .results {
        display: inline;
        position: relative;
        float: left;
        background-color: #222;
        color: #fff;
        padding: 20px;
        width: 64vw;
        height: 118vh;
        margin-top: 2vh;
        overflow: scroll;
      }
    </style>
  </head>
  <body>
    <div class="menu">
      <span class="task">Task 1</span><br><div class="mlink" data-url="/user-create">Create User</div>
      <span class="task">Task 2</span><br><div class="mlink" data-url="/user-validate">Validate User</div>
      <span class="task">Task 3</span><br><div class="mlink" data-url="/user-friendship">Friendship</div>
      <span class="task">Task 4</span><br><div class="mlink" data-url="/user-profile">User Profile</div>
      <span class="task">Task 5</span><br><div class="mlink" data-url="/user-photos">User Photos</div>
    </div>
    <div class="content">content</div>
    <div class="jwt">
      <h3>JWT</h3><br>
      <div style="margin-left: 30px">
        <input type="radio" id="none" name="jwt" value="none"> None<br><br>
        <input type="radio" id="admin" name="jwt" value="admin" checked="checked"> Admin<br>
        <input type="radio" id="steve" name="jwt" value="steve"> Steve<br>
        <input type="radio" id="susan" name="jwt" value="susan"> Susan<br><br>
        <input type="radio" id="custom" name="jwt" value="custom"> Custom<br>
        <textarea style="width: 200px; height: 125px"></textarea>
      </div>
    </div>
    <div class="method">
      Method:&nbsp;&nbsp;
      <input type="radio" id="get" name="method" value="get"> GET&nbsp;&nbsp;
      <input type="radio" id="post" name="method" value="post" checked="checked"> POST&nbsp;&nbsp;
      <input type="radio" id="put" name="method" value="put"> PUT&nbsp;&nbsp;
      <input type="radio" id="head" name="method" value="head"> HEAD&nbsp;&nbsp;
      <input type="radio" id="delete" name="method" value="delete"> DELETE
    </div>
    <div class="api">
      <input type="submit" name="submit" value="id.conoda.com">
      &nbsp;&nbsp;&nbsp;
      <input type="submit" name="submit" value="api.conoda.com">
      &nbsp;&nbsp;&nbsp;
      <input type="submit" name="submit" disabled="disabled" value="proapi.conoda.com">
      &nbsp;&nbsp;&nbsp;
      <input type="submit" name="submit" disabled="disabled" value="api.loonies.app">
    </div>
    <div class="results">
      <h3>Results</h3><br>
      <div class="result-dump"></div>
    </div>
  </body>
</html> 