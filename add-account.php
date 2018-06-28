<!DOCTYPE HTML>  
<html>
<head>
<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
</head>
<body>  
<div class="form-style">
<h2>Cashfree - Add Virtual Account</h2>
<form method="post" id="add-account">
  <input type="hidden" name="event" value="createVirtualAccount">

  Virtual Account Id:
  <input type="text" name="account[vAccountId]" value="">
  <br><br> 

  Name:
  <input type="text" name="account[name]" value="">
  <br><br>

  Email:
  <input type="text" name="account[email]" value="">
  <br><br>

  Phone:
  <input type="text" name="account[phone]" value="">
  <br><br>

  <input type="submit" name="submit" value="Submit">
</form>
</div>
</body>

<script type="text/javascript">
  $("document").ready(function () {

    $("#add-account").on('submit',function () {
      var data = $("#add-account").serialize();
      $.ajax({method: 'post', url: 'call.php', data: data, success: function(result){
        var resultObject = JSON.parse(result);
        alert("Status : " +resultObject["status"] +
              "\nMessage : "+ resultObject["message"]
             );
        }});
      return false;
    });

  });
</script>
<link href="style.css" rel="stylesheet">
</html>