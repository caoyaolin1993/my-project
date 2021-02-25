<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>用户登录</title>
  <link type="text/css" rel="styleSheet" href="./static/css/bootstrap.min.css" />
  <link type="text/css" rel="styleSheet" href="./static/css/bootstrap.min.css.map" />
</head>

<body>
  <div class="container">
    <form action="dologin.php" method="post">
      <div class="form-group row">
        <label for="staticEmail" class="col-sm-2 col-form-label">账号:</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="username">
        </div>
      </div>
      <div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label">密码:</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="password">
        </div>
      </div>
      <div class="form-group row">
       <button type="submit" class="btn btn-primary">用户登录</button>
      </div>
    </form>
  </div>
  <script src="./static/js/bootstrap.min.js"></script>
  <script src="./static/js/bootstrap.min.js.map"></script>
</body>

</html>