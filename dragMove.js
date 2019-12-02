function drag(ev) {

  ev.dataTransfer.setData("text", ev.target.id);
}

function allowDrop(ev) {

  ev.preventDefault();
  ev.target.style.backgroundColor = "rgba(0, 130, 140, .85)";
}

function dragLeave(ev) {

  ev.target.style.backgroundColor = "rgba(0, 130, 140, 0)";
}

function drop(ev, directoryPath) {

  ev.preventDefault();
  var directoryToMove = directoryPath + "/" + ev.dataTransfer.getData("text");
  var directoryTarget = directoryPath + "/" + ev.target.id + "/" + ev.dataTransfer.getData("text");


  if (ev.dataTransfer.getData("text") !== ev.target.id) {

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
}
