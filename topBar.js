// Workspaces
function workspacesDropDownMenu() {
  if (document.getElementById("workspacesMenuContent").classList.contains(o+"Show")) {
    document.getElementById("workspacesButton").classList.remove(o+"Active");
  }
  else {
    document.getElementById("workspacesButton").classList.add(o+"Active");
    document.getElementById("accountMenuContent").classList.remove(o+"Show");
    document.getElementById("accountButton").classList.remove(o+"Active");
  }
  document.getElementById("workspacesMenuContent").classList.toggle(o+"Show");
}

function createWorkspacePopup() {
  document.getElementById("createWorkspacePopup").classList.toggle(o+"Show");
  document.getElementById("newWorkspaceNameTextField").focus();
}

function cancelCreateWorkspace() {
  document.getElementById("createWorkspacePopup").classList.toggle(o+"Show");
}

function joinWorkspacePopup() {
    document.getElementById("joinWorkspacePopup").classList.toggle(o+"Show");
    document.getElementById("joinWorkspaceCodeTextField").focus();
}

function cancelJoinWorkspace() {
  document.getElementById("joinWorkspacePopup").classList.toggle(o+"Show");
}

// Account
function accountDropDownMenu() {
  if (document.getElementById("accountMenuContent").classList.contains(o+"Show")) {
    document.getElementById("accountButton").classList.remove(o+"Active");
  }
  else {
    document.getElementById("accountButton").classList.add(o+"Active");
    document.getElementById("workspacesMenuContent").classList.remove(o+"Show");
    document.getElementById("workspacesButton").classList.remove(o+"Active");
  }
  document.getElementById("accountMenuContent").classList.toggle(o+"Show");
}

// Click Off
window.onclick = function(event) {
  if (document.getElementById("accountMenuContent").classList.contains(o+"Show")) {
    if (!event.target.matches("."+o+"AccountButton")) {
      document.getElementById("accountMenuContent").classList.remove(o+"Show");
      document.getElementById("accountButton").classList.remove(o+"Active");
    }
  }
  else if (document.getElementById("workspacesMenuContent").classList.contains(o+"Show")) {
    if (!event.target.matches("."+o+"WorkspacesButton")) {
      document.getElementById("workspacesMenuContent").classList.remove(o+"Show");
      document.getElementById("workspacesButton").classList.remove(o+"Active");
    }
  }
}
