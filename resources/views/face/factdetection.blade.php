<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>



  <style>
        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        canvas {
        position: absolute;
        }
  </style>
</head>
<body>

  <video id="video" width="920" height="860" autoplay muted></video>

</body>
<script defer src="{{ asset('face-api.min.js') }}"></script>
<script defer src="{{ asset('script_face.js') }}"></script>


</html>
