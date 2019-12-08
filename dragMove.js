function drag(ev) {

  ev.dataTransfer.setData("text", ev.target.id);
}

function allowDrop(ev) {

  ev.preventDefault();

  if (o == "darkHome") {

    ev.target.style.backgroundColor = "rgba(0, 130, 140, .85)";
  }
  if (o == "lightHome") {

    ev.target.style.backgroundColor = "rgba(0, 130, 140, .60)";
  }

}

function dragLeave(ev) {

  ev.target.style.backgroundColor = "rgba(0, 130, 140, 0)";
}

function moveUp(ev, directoryPath) {

  ev.preventDefault();
  var directoryToMove = directoryPath + "/" + ev.dataTransfer.getData("text");
  var directoryTarget = ev.target.id + "/" + ev.dataTransfer.getData("text");

  //alert("ev.target.id: "+ev.target.id+"\nev.dataTransfer.getData('text'): "+ev.dataTransfer.getData("text")+"\ndirectoryPath: "+directoryPath);

  if (directoryPath !== ev.target.id) {

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

          //alert(message);
          location.reload();
        }
        else {

          //alert(message);
          ev.target.style.backgroundColor = "rgba(0, 130, 140, 0)";
        }
      }
    });
  }
  else {

    dragLeave(ev);
  }
}

function moveDown(ev, directoryPath) {

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

          //alert(message);
          location.reload();
        }
      }
    });
  }
  else {

    dragLeave(ev);
  }
}
