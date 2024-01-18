<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Access Denied</title>
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,700" rel="stylesheet">
  </head>
  <style type="text/css">
    * {
      -webkit-box-sizing: border-box;
              box-sizing: border-box;
    }

    body {
      padding: 0;
      margin: 0;
    }

    #notfound {
      position: relative;
      height: 100vh;
    }

    #notfound .notfound {
      position: absolute;
      left: 50%;
      top: 50%;
      -webkit-transform: translate(-50%, -50%);
          -ms-transform: translate(-50%, -50%);
              transform: translate(-50%, -50%);
    }

    .notfound {
      max-width: 460px;
      width: 100%;
      text-align: center;
      line-height: 1.4;
    }

    .notfound .notfound-404 {
      position: relative;
      width: 180px;
      height: 180px;
      margin: 0px auto 50px;
    }

    .notfound .notfound-404>div:first-child {
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      background: #ffa200;
      -webkit-transform: rotate(45deg);
          -ms-transform: rotate(45deg);
              transform: rotate(45deg);
      border: 5px dashed #000;
      border-radius: 5px;
    }

    .notfound .notfound-404>div:first-child:before {
      content: '';
      position: absolute;
      left: -5px;
      right: -5px;
      bottom: -5px;
      top: -5px;
      -webkit-box-shadow: 0px 0px 0px 5px rgba(0, 0, 0, 0.1) inset;
              box-shadow: 0px 0px 0px 5px rgba(0, 0, 0, 0.1) inset;
      border-radius: 5px;
    }

    .notfound .notfound-404 h1 {
      font-family: 'Cabin', sans-serif;
      color: #000;
      font-weight: 700;
      margin: 0;
      font-size: 90px;
      position: absolute;
      top: 50%;
      -webkit-transform: translate(-50%, -50%);
          -ms-transform: translate(-50%, -50%);
              transform: translate(-50%, -50%);
      left: 50%;
      text-align: center;
      height: 40px;
      line-height: 40px;
    }

    .notfound h2 {
      font-family: 'Cabin', sans-serif;
      font-size: 33px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 7px;
    }

    .notfound p {
      font-family: 'Cabin', sans-serif;
      font-size: 16px;
      color: #000;
      font-weight: 400;
    }

    .notfound a {
      font-family: 'Cabin', sans-serif;
      display: inline-block;
      padding: 10px 25px;
      background-color: #8f8f8f;
      border: none;
      border-radius: 40px;
      color: #fff;
      font-size: 14px;
      font-weight: 700;
      text-transform: uppercase;
      text-decoration: none;
      -webkit-transition: 0.2s all;
      transition: 0.2s all;
    }

    .notfound a:hover {
      background-color: #2c2c2c;
    }
  </style>
  <body>
    <div id="notfound">
      <div class="notfound">
        <div class="notfound-404">
          <div></div>
          <h1>403</h1>
          </div>
          <h2>Access Denied</h2>
          <p>You do not have permission to view this page.</p>
          <a href="{{url('/')}}">Go To Home Page</a>
        </div>
    </div>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13" type="41ec0fa4eda1bac2fb3fdb93-text/javascript"></script>
    <script type="41ec0fa4eda1bac2fb3fdb93-text/javascript">
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-23581568-13');
    </script>
    <script src="https://ajax.cloudflare.com/cdn-cgi/scripts/7089c43e/cloudflare-static/rocket-loader.min.js"   
      data-cf-settings="41ec0fa4eda1bac2fb3fdb93-|49" defer="">
    </script>
  </body>
</html>
