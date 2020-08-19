function abonnement(){
    var maCheckBox = document.getElementById("_remember_me");
        if (maCheckBox.checked)
        {
            document.getElementById("txtCookie").style.display="block";
        }
        else
        {
            document.getElementById("txtCookie").style.display="none";
        }
};
