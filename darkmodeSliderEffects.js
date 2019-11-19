$("#darkmodeSlider").change(function() {

  var mode = $(this).prop("checked");

  $.ajax({
    type: "POST",
    dataType: "JSON",
    url: "updateAccountPreferences.php",
    data: {darkmode: mode},
    /*
    success: function(data) {

      var data = eval(data);
      message = data.message;
      success = data.success;
      alert("data: " + message + "\nsuccess: " + success);
    }
    */
  });
});

function manualModeSwitch() {

  if (document.getElementById("darkmodeSlider").checked) {

    // Body
    document.getElementById("wrapper").style.backgroundColor = "rgb(28, 29, 31)";
    document.getElementById("mainContent").style.backgroundColor = "rgb(28, 29, 31)";
    document.getElementById("push").style.backgroundColor = "rgb(28, 29, 31)";
    document.getElementById("footer").style.backgroundColor = "rgb(28, 29, 31)";
    document.body.style.color = "rgba(255, 255, 255, 0.85)";

    // Corvin C & Home Button
    document.getElementById("corvinHeader").style.color = "rgb(0, 130, 140)";
    document.getElementById("homeButton").style.color = "rgb(0, 130, 140)";

    // Workspaces Menu
    document.getElementById("workspacesButton").style.color = "rgb(0, 130, 140)";
    document.getElementById("workspacesMenuContent").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("workspacesMenuContent").style.borderColor = "rgba(255, 255, 255, 0.25)";
    document.getElementById("workspacesMenuContent").style.boxShadow = "0 2px 4px 0 rgba(255, 255, 255, 0)";
    /*<?php
    foreach ($workspaces as $key => $value) {
      if ($key > 0) {
    ?>
        document.getElementById('<?php echo $value;?>Workspace').style.backgroundColor = "rgb(53, 54, 56)";
        document.getElementById('<?php echo $value;?>Workspace').style.color = "rgba(255, 255, 255, 0.85)";
    <?php
      }
    }
    ?>*/
    document.getElementById("workspacesMenuHeath").style.backgroundColor = "rgb(18, 19, 21)";

    // Account Menu
    document.getElementById("accountButton").style.color = "rgb(0, 130, 140)";
    document.getElementById("accountMenuContent").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("accountMenuContent").style.borderColor = "rgba(255, 255, 255, 0.25)";
    document.getElementById("accountMenuContent").style.boxShadow = "0 2px 4px 0 rgba(255, 255, 255, 0)";
    document.getElementById("accountMenuName").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("diskSpace").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("accountMenuHeath").style.backgroundColor = "rgb(18, 19, 21)";
    document.getElementById("accountSettings").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("help").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("logout").style.color = "rgba(255, 255, 255, 0.85)";

    // Header Area
    document.getElementById("getMoreSpaceButton").style.borderColor = "rgb(28, 29, 31)";
    document.getElementById("settingsHeader").style.color = "rgb(255, 255, 255, 0.85)";

    // Bars
    for (bar = 1; bar < 20; bar++) {
      document.getElementById("heath"+bar).style.backgroundColor = "rgb(18, 19, 21)";
    }

    // Collapsed Account Details
    document.getElementById("name").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("nameValuep").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("profileImage").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("email").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("emailValuep").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("username").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("usernameValuep").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("password").style.color = "rgba(255, 255, 255, 0.85)";

    // Expanded Name
    document.getElementById("namePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("namePasswordCheck").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("firstNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("firstNameChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("lastNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("lastNameChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("nameSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("nameSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

    // Expanded Email
    document.getElementById("emailPasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("emailPasswordCheck").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("emailChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("emailChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("emailSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("emailSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

    // Expanded Username
    document.getElementById("usernamePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("usernamePasswordCheck").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("usernameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("usernameChangeTextBox").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("usernameSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("usernameSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

    // Expanded Password
    document.getElementById("passwordLengthRequirement").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("oldPassword").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("oldPassword").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("newPasswordTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("newPasswordTextBox").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("newPassword2TextBox").style.backgroundColor = "rgba(0, 130, 140, 0.85)";
    document.getElementById("newPassword2TextBox").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("passwordSaveChangesButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("passwordSaveChangesButton").style.borderColor = "rgba(255, 255, 255, 0)";

    // Preferences
    document.getElementById("darkmode").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("ledgerSize").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("smallerLedgerSizeButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("smallerLedgerSizeButton").style.borderColor = "rgba(255, 255, 255, 0)";
    document.getElementById("standardLedgerSizeButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("standardLedgerSizeButton").style.borderColor = "rgba(255, 255, 255, 0)";
    document.getElementById("largerLedgerSizeButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("largerLedgerSizeButton").style.borderColor = "rgba(255, 255, 255, 0)";
    document.getElementById("dateFormat").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("timeZone").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("language").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("corvinBetaProgram").style.color = "rgba(255, 255, 255, 0.85)";

    // Notifications
    document.getElementById("newSignIn").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("workspaceFileChanges").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("newWorkspaceMember").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("promotionalCorvinNews").style.color = "rgba(255, 255, 255, 0.85)";

    // Delete My Corvin
    document.getElementById("deleteMyCorvin").style.color = "rgba(255, 255, 255, 0.85)";
    document.getElementById("deleteMyCorvinButton").style.backgroundColor = "rgb(53, 54, 56)";
    document.getElementById("deleteMyCorvinButton").style.borderColor = "rgba(255, 255, 255, 0)";
  }
  else {

    // Body
    document.getElementById("wrapper").style.backgroundColor = "rgb(254, 254, 254)";
    document.getElementById("mainContent").style.backgroundColor = "rgb(254, 254, 254)";
    document.getElementById("push").style.backgroundColor = "rgb(254, 254, 254)";
    document.getElementById("footer").style.backgroundColor = "rgb(254, 254, 254)";
    document.body.style.color = "rgba(23, 23, 23, 0.85)";

    // Corvin C & Home Button
    document.getElementById("corvinHeader").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("homeButton").style.color = "rgba(23, 23, 23, 0.85)";

    // Workspaces Menu
    document.getElementById("workspacesButton").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("workspacesMenuContent").style.backgroundColor = "rgb(254, 254, 254)";
    document.getElementById("workspacesMenuContent").style.borderColor = "rgba(23, 23, 23, 0.25)";
    document.getElementById("workspacesMenuContent").style.boxShadow = "0 2px 4px 0 rgba(23, 23, 23, 0.25)";
    /*<?php
    foreach ($workspaces as $key => $value) {
      if ($key > 0) {
    ?>
        document.getElementById('<?php echo $value;?>Workspace').style.backgroundColor = "rgb(254, 254, 254)";
        document.getElementById('<?php echo $value;?>Workspace').style.color = "rgba(23, 23, 23, 0.85)";
    <?php
      }
    }
    ?>*/
    document.getElementById("workspacesMenuHeath").style.backgroundColor = "rgba(18, 19, 21, 0.25)";

    // Account Menu
    document.getElementById("accountButton").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("accountMenuContent").style.backgroundColor = "rgb(254, 254, 254)";
    document.getElementById("accountMenuContent").style.borderColor = "rgba(23, 23, 23, 0.25)";
    document.getElementById("accountMenuContent").style.boxShadow = "0 2px 4px 0 rgba(23, 23, 23, 0.25)";
    document.getElementById("accountMenuName").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("diskSpace").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("accountMenuHeath").style.backgroundColor = "rgba(18, 19, 21, 0.25)";
    document.getElementById("accountSettings").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("help").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("logout").style.color = "rgba(23, 23, 23, 0.85)";

    // Header Area
    document.getElementById("getMoreSpaceButton").style.borderColor = "rgb(254, 254, 254)";
    document.getElementById("settingsHeader").style.color = "rgba(23, 23, 23, 0.85)";

    // Bars
    for (bar = 1; bar < 20; bar++) {
      document.getElementById("heath"+bar).style.backgroundColor = "rgb(18, 19, 21, 0.25)";
    }

    // Collapsed Account Details
    document.getElementById("name").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("nameValuep").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("profileImage").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("email").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("emailValuep").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("username").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("usernameValuep").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("password").style.color = "rgba(23, 23, 23, 0.85)";

    // Expanded Name
    document.getElementById("namePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("namePasswordCheck").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("firstNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("firstNameChangeTextBox").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("lastNameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("lastNameChangeTextBox").style.color = "rgba(0, 23, 23, 0.85)";
    document.getElementById("nameSaveChangesButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
    document.getElementById("nameSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

    // Expanded Email
    document.getElementById("emailPasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("emailPasswordCheck").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("emailChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("emailChangeTextBox").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("emailSaveChangesButton").style.backgroundColor = "rgb(51, 204, 255, 0)";
    document.getElementById("emailSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

    // Expanded Username
    document.getElementById("usernamePasswordCheck").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("usernamePasswordCheck").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("usernameChangeTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("usernameChangeTextBox").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("usernameSaveChangesButton").style.backgroundColor = "rgb(51, 204, 255, 0)";
    document.getElementById("usernameSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

    // Expanded Password
    document.getElementById("passwordLengthRequirement").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("oldPassword").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("oldPassword").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("newPasswordTextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("newPasswordTextBox").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("newPassword2TextBox").style.backgroundColor = "rgba(0, 130, 140, 0.06)";
    document.getElementById("newPassword2TextBox").style.color = "rgba(0, 23, 23, 0.85)";
    document.getElementById("passwordSaveChangesButton").style.backgroundColor = "rgb(51, 204, 255, 0)";
    document.getElementById("passwordSaveChangesButton").style.borderColor = "rgba(23, 23, 23, 0.25)";

    // Preferences
    document.getElementById("darkmode").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("ledgerSize").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("smallerLedgerSizeButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
    document.getElementById("smallerLedgerSizeButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
    document.getElementById("standardLedgerSizeButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
    document.getElementById("standardLedgerSizeButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
    document.getElementById("largerLedgerSizeButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
    document.getElementById("largerLedgerSizeButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
    document.getElementById("dateFormat").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("timeZone").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("language").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("corvinBetaProgram").style.color = "rgba(23, 23, 23, 0.85)";

    // Notifications
    document.getElementById("newSignIn").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("workspaceFileChanges").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("newWorkspaceMember").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("promotionalCorvinNews").style.color = "rgba(23, 23, 23, 0.85)";

    // Delete My Corvin
    document.getElementById("deleteMyCorvin").style.color = "rgba(23, 23, 23, 0.85)";
    document.getElementById("deleteMyCorvinButton").style.backgroundColor = "rgba(51, 204, 255, 0)";
    document.getElementById("deleteMyCorvinButton").style.borderColor = "rgba(23, 23, 23, 0.25)";
  }
}
