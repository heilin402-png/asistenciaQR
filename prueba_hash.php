<?php

$hash = '$2y$10$6RHhXtcXOSt/OGqIxCIrSuv0HQA/dHyZ/waUvw4xlQbxQNWLGB3nK';

if(password_verify("admin123", $hash)){
    echo "La contraseña SI corresponde";
}else{
    echo "La contraseña NO corresponde";
}

?>