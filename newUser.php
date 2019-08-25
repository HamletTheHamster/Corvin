<?php

echo "    
<!-- 1 Header -->
<head>

    <title>Register | Corvin</title>
    <link href = 'newUser.css' type = 'text/css' rel = 'stylesheet' />

    <link
        rel = 'apple-touch-icon'
        sizes = '57x57'
        href = '../Art/Favicon/apple-icon-57x57.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '60x60'
        href = '../Art/Favicon/apple-icon-60x60.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '72x72'
        href = '../Art/Favicon/apple-icon-72x72.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '76x76'
        href = '../Art/Favicon/apple-icon-76x76.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '114x114'
        href = '../Art/Favicon/apple-icon-114x114.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '120x120'
        href = '../Art/Favicon/apple-icon-120x120.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '144x144'
        href = '../Art/Favicon/apple-icon-144x144.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '152x152'
        href = '../Art/Favicon/apple-icon-152x152.png'
            />
    <link
        rel = 'apple-touch-icon'
        sizes = '180x180'
        href = '../Art/Favicon/apple-icon-180x180.png'
            />
    <link
        rel = 'icon'
        type = 'image/png'
        sizes = '192x192'
        href = '../Art/Favicon/android-icon-192x192.png'
            />
    <link
        rel = 'icon'
        type = 'image/png'
        sizes = '32x32'
        href = '../Art/Favicon/favicon-32x32.png'
            />
    <link
        rel = 'icon'
        type = 'image/png'
        sizes = '96x96'
        href = '../Art/Favicon/favicon-96x96.png'
            />
    <link
        rel = 'icon'
        type = 'image/png'
        sizes = '16x16'
        href = '../Art/Favicon/favicon-16x16.png'
            />
    <link
        rel = 'manifest'
        href = '/manifest.json'
            />

    <meta
        name = 'msapplication-TileColor'
        content = '#ffffff'
            />
    <meta
        name = 'msapplication-TileImage'
        content = '/ms-icon-144x144.png'
            />
    <meta
        name = 'theme-color'
        content = '#ffffff'
            />

    <meta
        http-equiv = 'refresh'
        content = '855'
            />

    <meta
        name = 'google'
        content = 'notranslate'
            />

</head>    
";

//Corvin Header
echo "
<body>

<div class = 'Container'>

<div class = 'BackToLogin'>
    <form action = 'login.php'>
        <input type = 'submit' value = 'Back To Log In' class = 'BackToLoginButton' name = 'submit' />
    </form>
</div>

    <div class = 'Center'>
        <div class = 'TopBar'>
            <div class = 'Corvin'>
                Corvin
            </div>
        </div>
";

echo "<br /><br /><br /><br /><br /><br /><br /><br />";

//Create new user form
echo "
    <div class = 'Login'>
        <form action = 'registerNewUser.php' method = 'post' enctype = 'multipart/form-data'>
            <label>
                <input type = 'text' class = 'TextBox' name = 'firstName' style = 'width: 300px' placeholder = 'First Name' autofocus />
            </label>
            <br /> <br />
            <label>
                <input type = 'text' class = 'TextBox' name = 'lastName' style = 'width: 300px' placeholder = 'Last Name' />
            </label>
            <br /> <br />
            <label>
                <input type = 'email' class = 'TextBox' name = 'email' style = 'width: 300px' placeholder = 'Email' />
            </label>
            <br /> <br />
            <label>
                <input type = 'text' class = 'TextBox' name = 'username' style = 'width: 300px' placeholder = 'User Name' />
            </label>
            <br /> <br />
            <label>
                <input type = 'password' class = 'TextBox' name = 'password' style = 'width: 300px' placeholder = 'Password (at least 8 characters)' />
            </label>
            <br /> <br />
            <label>
                <input type = 'password' class = 'TextBox' name = 'password2' style = 'width: 300px' placeholder = 'Re-enter Password' />
            </label>
            <br /><br /><br />
            Account Tier
            <br /><br />
            <label>
                <input type = 'radio' name = 'accountTier' value = 'Free' />Free (250 MB)
                <br /><br />
                <input type = 'radio' name = 'accountTier' value = 'Plus' />Plus (100GB)
                <br /><br />
                <input type = 'radio' name = 'accountTier' value = 'Pro' />Pro (500GB)
                <br /><br />
                <input type = 'radio' name = 'accountTier' value = 'Referral' />I have a Referral Code
                <br /><br />
            </label>
            <br /><br />
            <label>
                <input type = 'text' class = 'TextBox' name = 'referralCode' style = 'width: 300px' placeholder = 'Referral Code' />
            </label>
            <br /><br /><br />
            <input type = 'submit' value = 'Register' class = 'LoginButton' />
        </form>
    </div>
";

//</div> Center & </div> Container
echo "
    <div class = 'Push'></div>
    </div>
</div>
";

//Footer
echo "
    <div class = 'Footer'>
        <div class = 'FootCenter'>&copy; Corvin, Inc.</div>
    </div>
</body>
";

