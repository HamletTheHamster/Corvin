function drag(ev) {

  ev.dataTransfer.setData("text", ev.target.id);
}

function allowDrop(ev) {

  ev.preventDefault();
}

function drop(ev, directoryPath) {

  ev.preventDefault();
  var directoryToMove = directoryPath + "/" + ev.dataTransfer.getData("text");
  var directoryTarget = directoryPath + "/" + ev.target.id + "/" + ev.dataTransfer.getData("text");

  //alert(directoryToMove + "\n" + directoryTarget);

  $.ajax({
    type: 'POST',
    dataType: 'JSON',
    url: 'dragMove.php',
    data: {
      directoryToMove: directoryToMove,
      directoryTarget: directoryTarget
    },
    success: function(data) {

      var data = eval(data);
      message = data.message;

      if (message == 'true') {

        location.reload();
      }
      else {

        alert(message);
      }
    }
  });
}
