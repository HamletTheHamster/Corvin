$("#darkmodeSlider").change(function() {

  var mode = $(this).prop("checked");

  $.ajax({
    type: "POST",
    dataType: "JSON",
    url: "updateAccountPreferences.php",
    data: {darkmode: mode},
    success: function(data) {

      var data = eval(data);
      message = data.message;

      if (message == true) {

        location.reload();
      }
      else {

        alert(message);
      }
    }
  });
});
